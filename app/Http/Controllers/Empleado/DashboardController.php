<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\CertificadoExterno;
use App\Models\Evaluacion;
use App\Models\IntentoEvaluacion;
use App\Models\Modulo;
use App\Models\PlantillaCertificado;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Vista principal del empleado: Capacitaciones e Inducciones asignadas reales.
     */
    public function capacitaciones()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('area');

        $capacitaciones = Capacitacion::query()
            ->where('estado', true)
            ->where(function ($q) {
                $q->whereNull('fecha_disponibilidad')->orWhere('fecha_disponibilidad', '<=', now());
            })
            ->where(function ($q) use ($usuario) {
                $q->whereHas('usuarios', fn ($u) => $u->where('usuarios.id', $usuario->id));

                if ($usuario->area_id) {
                    $q->orWhereHas('areas', fn ($a) => $a->where('areas.id', $usuario->area_id));
                }
            })
            ->with(['modulos' => function ($query) {
                $query->where('estado', true)->orderBy('numero_seccion')->with('contenidos');
            }])
            ->orderByDesc('id')
            ->get();

        $asignaciones = $usuario->asignaciones()->get()->keyBy('capacitacion_id');
        $progresosUsuario = ProgresoModulo::where('usuario_id', $usuario->id)->get()->keyBy('modulo_id');

        foreach ($capacitaciones as $cap) {
            $modulos = $cap->modulos;
            $totalModulos = $modulos->count();
            $modulosCompletados = 0;
            $moduloAnteriorCompletado = true;

            foreach ($modulos as $idx => $mod) {
                $progreso = $progresosUsuario->get($mod->id);
                $mod->completado_usuario = (bool) ($progreso?->completado);
                $mod->bloqueado_usuario = ! $moduloAnteriorCompletado;

                if ($mod->completado_usuario) {
                    $modulosCompletados++;
                }

                $moduloAnteriorCompletado = $mod->completado_usuario;
            }

            $cap->modulos_completados_count = $modulosCompletados;
            $cap->porcentaje_calculado = $totalModulos > 0 ? (int) round(($modulosCompletados / $totalModulos) * 100) : 0;

            $cap->estado_usuario = $asignaciones->get($cap->id)?->estado
                ?? ($cap->porcentaje_calculado >= 100 ? 'MODULOS_COMPLETOS' : ($cap->porcentaje_calculado > 0 ? 'EN_PROGRESO' : 'PENDIENTE'));
        }

        $completadasCount = $usuario->capacitaciones()
            ->wherePivotIn('estado', ['COMPLETADA', 'MODULOS_COMPLETOS'])
            ->count();

        $certificadosCount = $usuario->certificados()->count();

        $enProgresoCount = $capacitaciones->filter(function ($c) {
            return ($c->porcentaje_calculado > 0 && $c->porcentaje_calculado < 100) || ($c->estado_usuario === 'EN_PROGRESO');
        })->count();

        $estadisticas = [
            'activas' => $capacitaciones->count(),
            'en_progreso' => $enProgresoCount,
            'completadas' => $completadasCount,
            'certificados' => $certificadosCount,
        ];

        return view('empleado.capacitaciones', compact('usuario', 'capacitaciones', 'estadisticas'));
    }

    /**
     * Inicia / se une a una capacitación por parte del empleado.
     */
    public function iniciarCapacitacion(Capacitacion $capacitacion)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        abort_unless($capacitacion->estado, 404, 'La capacitación no está disponible.');

        $pivotExistente = $usuario->capacitaciones()->where('capacitacion_id', $capacitacion->id)->first()?->pivot;

        $usuario->capacitaciones()->syncWithoutDetaching([
            $capacitacion->id => [
                'fecha_asignacion' => $pivotExistente?->fecha_asignacion ?: now(),
                'fecha_inicio' => $pivotExistente?->fecha_inicio ?: now(),
                'estado' => 'EN_PROGRESO',
            ]
        ]);

        return redirect()->route('empleado.capacitaciones.ver', $capacitacion)
            ->with('success_modulo', '¡Te has unido a la capacitación "' . $capacitacion->titulo . '"! Puedes comenzar con el estudio de los módulos.');
    }

    /**
     * Finaliza formalmente una capacitación cuando todos los módulos y la evaluación (si existe) se han completado.
     */
    public function finalizarCapacitacion(Capacitacion $capacitacion)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        abort_unless($capacitacion->estado, 404, 'La capacitación no está disponible.');

        $modulosIds = $capacitacion->modulos()->where('estado', true)->pluck('id');
        $completadosCount = ProgresoModulo::where('usuario_id', $usuario->id)
            ->whereIn('modulo_id', $modulosIds)
            ->where('completado', true)
            ->count();

        if ($modulosIds->count() > 0 && $completadosCount < $modulosIds->count()) {
            return back()->with('error', 'Debes completar todos los módulos de estudio antes de finalizar la capacitación.');
        }

        $evaluaciones = $capacitacion->evaluaciones()->where('estado', true)->get();
        if ($evaluaciones->isNotEmpty()) {
            foreach ($evaluaciones as $eval) {
                $aprobado = IntentoEvaluacion::where('usuario_id', $usuario->id)
                    ->where('evaluacion_id', $eval->id)
                    ->where('estado', 'APROBADO')
                    ->exists();

                if (!$aprobado) {
                    return back()->with('error', 'Debes aprobar la evaluación de aprendizaje "' . $eval->titulo . '" para poder finalizar esta capacitación.');
                }
            }
        }

        $usuario->capacitaciones()->syncWithoutDetaching([
            $capacitacion->id => [
                'estado' => 'COMPLETADA',
                'fecha_finalizacion' => now(),
            ]
        ]);

        $plantilla = PlantillaCertificado::first();

        Certificado::firstOrCreate(
            [
                'usuario_id' => $usuario->id,
                'capacitacion_id' => $capacitacion->id,
            ],
            [
                'plantilla_certificado_id' => $plantilla?->id,
                'codigo' => 'PNY-' . strtoupper(Str::random(8)),
                'nombre_empleado' => $usuario->nombre_completo,
                'identificacion' => $usuario->identificacion,
                'nombre_capacitacion' => $capacitacion->titulo,
                'area_nombre' => $usuario->area->nombre ?? 'Producción Piscícola',
                'porcentaje' => 100,
                'fecha_emision' => now(),
            ]
        );

        return redirect()->route('empleado.certificados')
            ->with('success', '¡Felicitaciones! Has finalizado exitosamente la capacitación "' . $capacitacion->titulo . '" y tu certificado ha sido emitido.');
    }

    /**
     * Vista de detalle del curso para el empleado:
     * Permite visualizar el contenido libremente, bloquea módulos de forma secuencial
     * e integra la Evaluación de Aprendizaje en la misma vista.
     */
    public function verCapacitacion(Capacitacion $capacitacion)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        abort_unless($capacitacion->estado, 404, 'La capacitación no está disponible.');

        $capacitacion->load([
            'areas',
            'modulos' => fn ($q) => $q->where('estado', true)->orderBy('numero_seccion')->with('contenidos'),
            'evaluaciones' => fn ($q) => $q->where('estado', true)->with(['preguntas.opciones']),
        ]);

        $progresosUsuario = ProgresoModulo::where('usuario_id', $usuario->id)
            ->whereIn('modulo_id', $capacitacion->modulos->pluck('id'))
            ->get()
            ->keyBy('modulo_id');

        $totalModulos = $capacitacion->modulos->count();
        $modulosCompletados = 0;
        $moduloAnteriorCompletado = true;

        foreach ($capacitacion->modulos as $idx => $mod) {
            $progreso = $progresosUsuario->get($mod->id);
            $mod->completado_usuario = (bool) ($progreso?->completado);
            $mod->bloqueado_usuario = ! $moduloAnteriorCompletado;

            if ($mod->completado_usuario) {
                $modulosCompletados++;
            }

            $moduloAnteriorCompletado = $mod->completado_usuario;
        }

        $porcentajeProgreso = $totalModulos > 0 ? (int) round(($modulosCompletados / $totalModulos) * 100) : 0;
        $todosModulosCompletados = $totalModulos > 0 && $modulosCompletados >= $totalModulos;

        $evaluaciones = $capacitacion->evaluaciones;
        foreach ($evaluaciones as $eval) {
            $intentos = IntentoEvaluacion::where('usuario_id', $usuario->id)
                ->where('evaluacion_id', $eval->id)
                ->orderByDesc('id')
                ->get();

            $eval->intentos_usuario = $intentos;
            $eval->intentos_realizados = $intentos->count();
            $eval->intentos_restantes = max(0, $eval->intentos_permitidos - $intentos->count());
            $eval->mejor_intento = $intentos->sortByDesc('porcentaje')->first();
            $eval->aprobada = $intentos->contains(fn ($i) => $i->estado === 'APROBADO');
        }

        return view('empleado.ver_capacitacion', compact(
            'usuario',
            'capacitacion',
            'porcentajeProgreso',
            'modulosCompletados',
            'totalModulos',
            'todosModulosCompletados',
            'evaluaciones'
        ));
    }

    /**
     * Marca un módulo como completado por el empleado y desbloquea el siguiente módulo secuencial.
     */
    public function completarModulo(Modulo $modulo)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $modulo->loadMissing('capacitacion');
        $capacitacion = $modulo->capacitacion;

        if (!$capacitacion || !$capacitacion->estado) {
            return back()->with('error', 'La capacitación no está disponible.');
        }

        $modulosOrdenados = $capacitacion->modulos()->where('estado', true)->orderBy('numero_seccion')->get();
        $currentIndex = $modulosOrdenados->search(fn ($m) => $m->id === $modulo->id);

        if ($currentIndex > 0) {
            $prevModulo = $modulosOrdenados[$currentIndex - 1];
            $prevCompletado = ProgresoModulo::where('usuario_id', $usuario->id)
                ->where('modulo_id', $prevModulo->id)
                ->where('completado', true)
                ->exists();

            if (!$prevCompletado) {
                return back()->with('error', 'Debes completar el módulo anterior "' . $prevModulo->titulo . '" antes de avanzar.');
            }
        }

        $progresoExistente = ProgresoModulo::where('usuario_id', $usuario->id)
            ->where('modulo_id', $modulo->id)
            ->first();

        ProgresoModulo::updateOrCreate(
            ['usuario_id' => $usuario->id, 'modulo_id' => $modulo->id],
            [
                'porcentaje' => 100,
                'completado' => true,
                'fecha_finalizacion' => now(),
                'fecha_inicio' => $progresoExistente?->fecha_inicio ?: now(),
            ]
        );

        $totalModulos = $modulosOrdenados->count();
        $completadosCount = ProgresoModulo::where('usuario_id', $usuario->id)
            ->whereIn('modulo_id', $modulosOrdenados->pluck('id'))
            ->where('completado', true)
            ->count();

        $nuevoEstado = ($completadosCount >= $totalModulos) ? 'MODULOS_COMPLETOS' : 'EN_PROGRESO';

        $pivotExistente = $usuario->capacitaciones()->where('capacitacion_id', $capacitacion->id)->first()?->pivot;

        $usuario->capacitaciones()->syncWithoutDetaching([
            $capacitacion->id => [
                'fecha_asignacion' => $pivotExistente?->fecha_asignacion ?: now(),
                'estado' => $nuevoEstado,
            ]
        ]);

        return back()->with('success_modulo', '¡Módulo "' . $modulo->titulo . '" completado con éxito! El siguiente contenido se ha desbloqueado.');
    }

    /**
     * Procesa la entrega de respuestas de una Evaluación por parte del empleado,
     * liquida el resultado (%) automáticamente y genera el Certificado al aprobar.
     */
    public function rendirEvaluacion(Request $request, Evaluacion $evaluacion)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $evaluacion->loadMissing('capacitacion', 'preguntas.opciones');
        $capacitacion = $evaluacion->capacitacion;

        $intentosPrevios = IntentoEvaluacion::where('usuario_id', $usuario->id)
            ->where('evaluacion_id', $evaluacion->id)
            ->count();

        if ($intentosPrevios >= $evaluacion->intentos_permitidos) {
            return back()->with('error_evaluacion', 'Has alcanzado el límite máximo de ' . $evaluacion->intentos_permitidos . ' intentos permitidos para esta evaluación.');
        }

        $preguntas = $evaluacion->preguntas;
        $totalPreguntas = $preguntas->count();

        if ($totalPreguntas === 0) {
            return back()->with('error_evaluacion', 'La evaluación no contiene preguntas configuradas aún.');
        }

        $correctas = 0;
        $respuestasEnviadas = $request->input('respuestas', []);

        foreach ($preguntas as $pregunta) {
            $opcionSeleccionadaId = $respuestasEnviadas[$pregunta->id] ?? null;

            if ($opcionSeleccionadaId) {
                $opcion = $pregunta->opciones->firstWhere('id', (int) $opcionSeleccionadaId);
                if ($opcion && $opcion->es_correcta) {
                    $correctas++;
                }
            }
        }

        $porcentajeObtenido = round(($correctas / $totalPreguntas) * 100, 2);
        $aprobado = $porcentajeObtenido >= $evaluacion->porcentaje_aprobacion;
        $estado = $aprobado ? 'APROBADO' : 'NO_APROBADO';

        IntentoEvaluacion::create([
            'usuario_id' => $usuario->id,
            'evaluacion_id' => $evaluacion->id,
            'numero_intento' => $intentosPrevios + 1,
            'porcentaje' => $porcentajeObtenido,
            'respuestas_correctas' => $correctas,
            'total_preguntas' => $totalPreguntas,
            'estado' => $estado,
            'fecha_inicio' => now(),
            'fecha_finalizacion' => now(),
        ]);

        if ($aprobado) {
            $usuario->capacitaciones()->syncWithoutDetaching([
                $capacitacion->id => [
                    'fecha_asignacion' => now(),
                    'estado' => 'COMPLETADA',
                ]
            ]);

            $plantilla = PlantillaCertificado::first();

            Certificado::firstOrCreate(
                [
                    'usuario_id' => $usuario->id,
                    'capacitacion_id' => $capacitacion->id,
                ],
                [
                    'plantilla_certificado_id' => $plantilla?->id,
                    'codigo' => 'PNY-' . strtoupper(Str::random(8)),
                    'nombre_empleado' => $usuario->nombre_completo,
                    'identificacion' => $usuario->identificacion,
                    'nombre_capacitacion' => $capacitacion->titulo,
                    'area_nombre' => $usuario->area->nombre ?? 'Producción Piscícola',
                    'porcentaje' => $porcentajeObtenido,
                    'fecha_emision' => now(),
                ]
            );

            return back()->with('success_evaluacion', '🎉 ¡Felicidades! Has aprobado la evaluación con ' . $porcentajeObtenido . '%. Tu certificado digital ha sido emitido y está listo para descargar.');
        } else {
            $intentosRestantes = $evaluacion->intentos_permitidos - ($intentosPrevios + 1);
            return back()->with('error_evaluacion', 'Obtuviste ' . $porcentajeObtenido . '% (mínimo requerido: ' . $evaluacion->porcentaje_aprobacion . '%). Te quedan ' . $intentosRestantes . ' intentos disponibles.');
        }
    }

    /**
     * Vista de Certificados emitidos reales del usuario.
     */
    public function certificados()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('area');

        // Auto-emitir certificado para cualquier capacitación completada que no tenga aún registro de certificado
        $capacitacionesCompletadas = $usuario->capacitaciones()
            ->wherePivotIn('estado', ['COMPLETADA', 'MODULOS_COMPLETOS'])
            ->get();

        if ($capacitacionesCompletadas->isNotEmpty()) {
            $plantilla = PlantillaCertificado::first() ?? PlantillaCertificado::create([
                'nombre' => 'Plantilla Oficial C.I. Piscícola New York',
                'descripcion' => 'Plantilla corporativa predeterminada para emisión de certificados de capacitación',
                'ruta_plantilla' => 'plantillas/default.pdf',
                'nombre_organizacion' => 'C.I. Piscícola New York S.A.S.',
                'texto_certificado' => 'Certifica que el colaborador ha completado y aprobado satisfactoriamente la capacitación.',
                'firma_1_nombre' => 'Ing. Carlos Mendoza',
                'firma_1_cargo' => 'Director de Gestión Humana & Bioseguridad',
                'firma_2_nombre' => 'Dra. Elena Ramos',
                'firma_2_cargo' => 'Gerente de Calidad C.I. Piscícola New York',
                'estado' => true,
            ]);

            foreach ($capacitacionesCompletadas as $cap) {
                Certificado::firstOrCreate(
                    [
                        'usuario_id' => $usuario->id,
                        'capacitacion_id' => $cap->id,
                    ],
                    [
                        'plantilla_certificado_id' => $plantilla?->id,
                        'codigo' => 'PNY-' . strtoupper(Str::random(8)),
                        'nombre_empleado' => $usuario->nombre_completo,
                        'identificacion' => $usuario->identificacion,
                        'nombre_capacitacion' => $cap->titulo,
                        'area_nombre' => $usuario->area->nombre ?? 'Producción Piscícola',
                        'porcentaje' => 100,
                        'fecha_emision' => now(),
                    ]
                );
            }
        }

        $certificados = $usuario->certificados()
            ->with(['capacitacion', 'plantillaCertificado'])
            ->latest('fecha_emision')
            ->get();

        return view('empleado.certificados', compact('usuario', 'certificados'));
    }

    /**
     * Muestra la vista oficial imprimible del Certificado Digital.
     */
    public function verCertificado(Certificado $certificado)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        if ($certificado->usuario_id !== $usuario->id && !in_array($usuario->rol, ['JEFE_AREA', 'ADMINISTRADOR', 'TH'])) {
            abort(403, 'No tienes permiso para visualizar este certificado.');
        }

        $certificado->loadMissing('capacitacion', 'usuario.area', 'plantillaCertificado');

        return view('empleado.ver_certificado', compact('usuario', 'certificado'));
    }

    /**
     * Descarga el certificado como archivo PDF generado en el servidor.
     */
    public function descargarCertificadoPdf(Certificado $certificado)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        if ($certificado->usuario_id !== $usuario->id && !in_array($usuario->rol, ['JEFE_AREA', 'ADMINISTRADOR', 'TH'])) {
            abort(403, 'No tienes permiso para descargar este certificado.');
        }

        $certificado->loadMissing('plantillaCertificado');

        return Pdf::loadView('empleado.certificado_pdf', compact('certificado'))
            ->setPaper('letter', 'landscape')
            ->download('Certificado_' . $certificado->codigo . '.pdf');
    }

    /**
     * Vista de Capacitaciones Finalizadas / Historial real.
     */
    public function finalizadas()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('area');

        $finalizadas = $usuario->capacitaciones()
            ->wherePivotIn('estado', ['COMPLETADA', 'MODULOS_COMPLETOS'])
            ->with(['certificados' => function ($q) use ($usuario) {
                $q->where('usuario_id', $usuario->id);
            }])
            ->get();

        return view('empleado.finalizadas', compact('usuario', 'finalizadas'));
    }

    /**
     * Vista de Anexo de Certificados externos del usuario.
     */
    public function anexoCertificados()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $usuario->loadMissing('area');

        $certificadosExternos = $usuario->certificadosExternos()
            ->orderByDesc('id')
            ->get();

        return view('empleado.anexo-certificados', compact('usuario', 'certificadosExternos'));
    }

    /**
     * Almacena un nuevo certificado externo radicado por el empleado.
     */
    public function guardarAnexo(Request $request)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect()->route('login');
        }

        $request->validate([
            'entidad_emisora' => ['required', 'string', 'max:255'],
            'fecha_emision' => ['required', 'date'],
            'archivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'entidad_emisora.required' => 'La entidad emisora es requerida.',
            'fecha_emision.required' => 'La fecha de emisión es requerida.',
            'archivo.required' => 'Debes adjuntar el archivo de soporte.',
            'archivo.mimes' => 'El archivo debe ser formato PDF, JPG o PNG.',
            'archivo.max' => 'El archivo no puede exceder los 10MB.',
        ]);

        $rutaArchivo = $request->file('archivo')->store('certificados_externos', 'public');

        CertificadoExterno::create([
            'usuario_id' => $usuario->id,
            'entidad_emisora' => $request->entidad_emisora,
            'fecha_emision' => $request->fecha_emision,
            'ruta_archivo' => $rutaArchivo,
            'estado' => 'PENDIENTE',
        ]);

        return back()->with('success_anexo', 'Certificado externo radicado exitosamente. Pendiente de revisión por Talento Humano.');
    }
}
