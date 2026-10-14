<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\Contenido;
use App\Models\IntentoEvaluacion;
use App\Models\Modulo;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use App\Models\UsuarioCapacitacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CapacitacionController extends Controller
{
    protected const TIPOS_CONTENIDO = ['TEXTO', 'VIDEO', 'IMAGEN', 'PDF', 'ENLACE'];

    /**
     * Vista por defecto del Jefe de Área: capacitaciones publicadas por él.
     */
    public function index()
    {
        $usuario = $this->jefe();

        $capacitaciones = $usuario->capacitacionesCreadas()
            ->where('estado', true)
            ->with('areas')
            ->withCount(['modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true)])
            ->orderByDesc('id')
            ->get();

        $totalBorradores = $usuario->capacitacionesCreadas()->where('estado', false)->count();
        $empleadosArea = $usuario->area_id ? $this->empleadosDelArea([$usuario->area_id])->count() : 0;

        // Participación agregada por capacitación (empleados que ya iniciaron y módulos completados)
        $participacion = collect();
        if ($capacitaciones->isNotEmpty()) {
            $participacion = ProgresoModulo::query()
                ->join('modulos', 'modulos.id', '=', 'progreso_modulos.modulo_id')
                ->whereIn('modulos.capacitacion_id', $capacitaciones->pluck('id'))
                ->where('modulos.estado', true)
                ->selectRaw('modulos.capacitacion_id as capacitacion_id, count(distinct progreso_modulos.usuario_id) as participantes, sum(case when progreso_modulos.completado = 1 then 1 else 0 end) as completados')
                ->groupBy('modulos.capacitacion_id')
                ->get()
                ->keyBy('capacitacion_id');
        }

        foreach ($capacitaciones as $cap) {
            $fila = $participacion->get($cap->id);
            $cap->participantes_count = (int) ($fila->participantes ?? 0);

            $posibles = $empleadosArea * $cap->modulos_activos_count;
            $cap->progreso_promedio = $posibles > 0
                ? (int) round((($fila->completados ?? 0) / $posibles) * 100)
                : 0;
        }

        $estadisticas = [
            'total' => $capacitaciones->count() + $totalBorradores,
            'activas' => $capacitaciones->count(),
            'borradores' => $totalBorradores,
            'inactivas' => $totalBorradores,
            'empleados' => $empleadosArea,
        ];

        return view('jefe.capacitaciones.index', compact('usuario', 'capacitaciones', 'estadisticas'));
    }

    /**
     * Listado de borradores de capacitaciones pendientes de publicar.
     */
    public function borradores()
    {
        $usuario = $this->jefe();
        $usuario->loadMissing('area');

        $capacitaciones = $usuario->capacitacionesCreadas()
            ->where('estado', false)
            ->with(['areas', 'evaluaciones'])
            ->withCount([
                'modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true),
                'evaluaciones',
            ])
            ->orderByDesc('id')
            ->get();

        $totalPublicadas = $usuario->capacitacionesCreadas()->where('estado', true)->count();
        $empleadosArea = $usuario->area_id ? $this->empleadosDelArea([$usuario->area_id])->count() : 0;

        $estadisticas = [
            'total' => $totalPublicadas + $capacitaciones->count(),
            'activas' => $totalPublicadas,
            'borradores' => $capacitaciones->count(),
            'inactivas' => $capacitaciones->count(),
            'empleados' => $empleadosArea,
        ];

        return view('jefe.capacitaciones.borradores', compact('usuario', 'capacitaciones', 'estadisticas'));
    }

    public function create()
    {
        $usuario = $this->jefe();
        $usuario->loadMissing('area');

        return view('jefe.capacitaciones.form', [
            'usuario' => $usuario,
            'capacitacion' => null,
            'modulosForm' => $this->modulosParaFormulario(null),
            'tiposContenido' => self::TIPOS_CONTENIDO,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $usuario = $this->jefe();

        if (! $usuario->area_id) {
            return back()->withInput()->with('error', 'Tu usuario no tiene un área asignada. Solicita a administración que te asigne un área para publicar capacitaciones.');
        }

        $datos = $this->validar($request);
        $esBorrador = $request->input('accion') === 'borrador' || ! $request->boolean('estado_publicacion', true);

        DB::transaction(function () use ($request, $datos, $usuario, $esBorrador) {
            $capacitacion = Capacitacion::create([
                ...$this->atributosCapacitacion($datos, null),
                'ruta_imagen' => $this->guardarImagen($request, null),
                'creado_por' => $usuario->id,
                'estado' => ! $esBorrador,
            ]);

            // La capacitación solo se habilita para el área del jefe que la publica
            $capacitacion->areas()->sync([$usuario->area_id]);

            $this->sincronizarModulos($capacitacion, $datos['modulos'], $request);
        });

        if ($esBorrador) {
            return redirect()->route('jefe.capacitaciones.borradores')
                ->with('success', 'Capacitación guardada en borradores correctamente. Puedes completarla y publicarla cuando desees.');
        }

        return redirect()->route('jefe.capacitaciones.index')
            ->with('success', 'Capacitación publicada correctamente. Ya está disponible para los empleados de tu área.');
    }

    public function show(Capacitacion $capacitacion)
    {
        $usuario = $this->jefe();
        $this->autorizar($capacitacion, $usuario);

        $capacitacion->load('areas');
        $modulos = $capacitacion->modulos()->where('estado', true)->get();
        $totalModulos = $modulos->count();

        $areaIds = $capacitacion->areas->pluck('id')->all();
        $empleados = $this->empleadosDelArea($areaIds)->with('area')->orderBy('nombre_completo')->get();

        $progresos = ProgresoModulo::whereIn('modulo_id', $modulos->pluck('id'))
            ->whereIn('usuario_id', $empleados->pluck('id'))
            ->get()
            ->groupBy('usuario_id');

        $asignaciones = UsuarioCapacitacion::where('capacitacion_id', $capacitacion->id)
            ->get()
            ->keyBy('usuario_id');

        $certificados = Certificado::where('capacitacion_id', $capacitacion->id)
            ->pluck('usuario_id')
            ->flip();

        $mejoresNotas = IntentoEvaluacion::query()
            ->join('evaluaciones', 'evaluaciones.id', '=', 'intentos_evaluacion.evaluacion_id')
            ->where('evaluaciones.capacitacion_id', $capacitacion->id)
            ->selectRaw('intentos_evaluacion.usuario_id as usuario_id, max(intentos_evaluacion.porcentaje) as mejor')
            ->groupBy('intentos_evaluacion.usuario_id')
            ->pluck('mejor', 'usuario_id');

        $participantes = $empleados->map(function (Usuario $empleado) use ($progresos, $asignaciones, $certificados, $mejoresNotas, $totalModulos) {
            $registros = $progresos->get($empleado->id, collect());
            $completados = $registros->where('completado', true)->count();
            $porcentaje = $totalModulos > 0 ? (int) round(($completados / $totalModulos) * 100) : 0;
            $asignacion = $asignaciones->get($empleado->id);

            $estado = $asignacion?->estado
                ?? ($porcentaje >= 100 ? 'MODULOS_COMPLETOS' : ($registros->isNotEmpty() ? 'EN_PROGRESO' : 'PENDIENTE'));

            return (object) [
                'usuario' => $empleado,
                'completados' => $completados,
                'porcentaje' => $porcentaje,
                'estado' => $estado,
                'mejor_nota' => $mejoresNotas->get($empleado->id),
                'certificado' => $certificados->has($empleado->id),
                'ultima_actividad' => $registros->max('fecha_finalizacion') ?? $registros->max('fecha_inicio'),
            ];
        });

        $resumen = [
            'empleados' => $participantes->count(),
            'iniciaron' => $participantes->filter(fn ($p) => $p->porcentaje > 0 || $p->estado !== 'PENDIENTE')->count(),
            'completaron' => $participantes->filter(fn ($p) => $p->estado === 'COMPLETADA' || $p->porcentaje >= 100)->count(),
            'promedio' => $participantes->isNotEmpty() ? (int) round($participantes->avg('porcentaje')) : 0,
        ];

        return view('jefe.capacitaciones.show', compact('usuario', 'capacitacion', 'modulos', 'participantes', 'resumen'));
    }

    public function edit(Capacitacion $capacitacion)
    {
        $usuario = $this->jefe();
        $this->autorizar($capacitacion, $usuario);
        $usuario->loadMissing('area');

        return view('jefe.capacitaciones.form', [
            'usuario' => $usuario,
            'capacitacion' => $capacitacion,
            'modulosForm' => $this->modulosParaFormulario($capacitacion),
            'tiposContenido' => self::TIPOS_CONTENIDO,
        ]);
    }

    public function update(Request $request, Capacitacion $capacitacion): RedirectResponse
    {
        $usuario = $this->jefe();
        $this->autorizar($capacitacion, $usuario);

        $datos = $this->validar($request);
        $accion = $request->input('accion');
        $esBorrador = $accion === 'borrador';
        $esPublicar = $accion === 'publicar';

        DB::transaction(function () use ($request, $datos, $capacitacion, $esBorrador, $esPublicar) {
            $atributos = [
                ...$this->atributosCapacitacion($datos, $capacitacion),
                'ruta_imagen' => $this->guardarImagen($request, $capacitacion->ruta_imagen),
            ];

            if ($esBorrador) {
                $atributos['estado'] = false;
            } elseif ($esPublicar) {
                $atributos['estado'] = true;
            }

            $capacitacion->update($atributos);

            $this->sincronizarModulos($capacitacion, $datos['modulos'], $request);
        });

        if ($esBorrador || ! $capacitacion->fresh()->estado) {
            return redirect()->route('jefe.capacitaciones.borradores')
                ->with('success', 'Borrador actualizado correctamente.');
        }

        return redirect()->route('jefe.capacitaciones.index')
            ->with('success', 'Capacitación actualizada correctamente.');
    }

    /**
     * Publica directamente una capacitación que estaba en borrador.
     */
    public function publicar(Capacitacion $capacitacion): RedirectResponse
    {
        $this->autorizar($capacitacion, $this->jefe());

        $capacitacion->update(['estado' => true]);

        return redirect()->route('jefe.capacitaciones.index')
            ->with('success', '¡La capacitación "' . $capacitacion->titulo . '" ha sido publicada exitosamente! Ya está visible para tus colaboradores.');
    }

    /**
     * Elimina definitivamente una capacitación que esté en borrador (no publicada).
     */
    public function destroy(Capacitacion $capacitacion): RedirectResponse
    {
        $this->autorizar($capacitacion, $this->jefe());

        if ($capacitacion->estado) {
            return back()->with('error', 'No puedes eliminar una capacitación que ya está publicada. Puedes desactivarla.');
        }

        $titulo = $capacitacion->titulo;
        $capacitacion->delete();

        return redirect()->route('jefe.capacitaciones.borradores')
            ->with('success', 'El borrador "' . $titulo . '" ha sido descartado.');
    }

    /**
     * Alternar estado activo / inactivo de una capacitación publicada.
     */
    public function toggleEstado(Capacitacion $capacitacion): RedirectResponse
    {
        $this->autorizar($capacitacion, $this->jefe());

        $capacitacion->update(['estado' => ! $capacitacion->estado]);

        return back()->with('success', $capacitacion->estado
            ? 'Capacitación activada: vuelve a estar visible para los empleados del área.'
            : 'Capacitación desactivada: ya no se muestra a los empleados (se conserva el historial).');
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    protected function jefe(): Usuario
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return $usuario;
    }

    protected function autorizar(Capacitacion $capacitacion, Usuario $usuario): void
    {
        abort_unless((int) $capacitacion->creado_por === (int) $usuario->id, 403, 'Esta capacitación no fue publicada por ti.');
    }

    protected function empleadosDelArea(array $areaIds)
    {
        return Usuario::query()
            ->where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->whereIn('area_id', $areaIds);
    }

    protected function atributosCapacitacion(array $datos, ?Capacitacion $capacitacion = null): array
    {
        $tieneFechaFin = ($datos['tiene_fecha_fin'] ?? 'no') === 'si';
        $fechaLimite = ($tieneFechaFin && ! empty($datos['fecha_limite']))
            ? Carbon::parse($datos['fecha_limite'])->endOfDay()
            : null;

        return [
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'porcentaje_aprobacion' => $datos['porcentaje_aprobacion'] ?? $capacitacion?->porcentaje_aprobacion ?? 80.00,
            'intentos_permitidos' => $datos['intentos_permitidos'] ?? $capacitacion?->intentos_permitidos ?? 3,
            'duracion_estimada' => $datos['duracion_estimada'] ?? null,
            'incentivo' => $datos['incentivo'] ?? null,
            'fecha_disponibilidad' => ! empty($datos['fecha_disponibilidad']) ? Carbon::parse($datos['fecha_disponibilidad'])->startOfDay() : null,
            'fecha_limite' => $fechaLimite,
        ];
    }

    protected function validar(Request $request): array
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'porcentaje_aprobacion' => ['nullable', 'numeric', 'between:1,100'],
            'intentos_permitidos' => ['nullable', 'integer', 'between:1,20'],
            'duracion_estimada' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'incentivo' => ['nullable', 'string', 'max:2000'],
            'tiene_fecha_fin' => ['nullable', 'in:si,no'],
            'fecha_disponibilidad' => ['nullable', 'date'],
            'fecha_limite' => [
                'nullable',
                'required_if:tiene_fecha_fin,si',
                'date',
                $request->filled('fecha_disponibilidad') ? 'after_or_equal:fecha_disponibilidad' : 'date',
            ],

            'modulos' => ['required', 'array', 'min:1'],
            'modulos.*.id' => ['nullable', 'integer'],
            'modulos.*.titulo' => ['required', 'string', 'max:255'],
            'modulos.*.descripcion' => ['required', 'string', 'max:5000'],
            'modulos.*.contenidos' => ['nullable', 'array'],
            'modulos.*.contenidos.*.id' => ['nullable', 'integer'],
            'modulos.*.contenidos.*.titulo' => ['required', 'string', 'max:255'],
            'modulos.*.contenidos.*.tipo' => ['required', 'in:' . implode(',', self::TIPOS_CONTENIDO)],
            'modulos.*.contenidos.*.contenido' => ['nullable', 'string', 'max:20000'],
            'modulos.*.contenidos.*.archivo' => ['nullable', 'file', 'max:262144'],
            'modulos.*.contenidos.*.ruta_archivo_actual' => ['nullable', 'string', 'max:255'],
        ], [
            'titulo.required' => 'El título de la capacitación es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'imagen.image' => 'La portada debe ser una imagen (JPG, PNG o WEBP).',
            'imagen.max' => 'La portada no puede superar los 4 MB.',
            'porcentaje_aprobacion.between' => 'El porcentaje de aprobación debe estar entre 1 y 100.',
            'intentos_permitidos.between' => 'Los intentos permitidos deben estar entre 1 y 20.',
            'fecha_limite.required_if' => 'Indica la fecha límite de finalización o selecciona que no tiene fecha de fin.',
            'fecha_limite.after_or_equal' => 'La fecha límite no puede ser anterior a la fecha de disponibilidad.',
            'modulos.required' => 'Agrega al menos un módulo a la capacitación.',
            'modulos.min' => 'Agrega al menos un módulo a la capacitación.',
            'modulos.*.titulo.required' => 'Cada módulo debe tener un título.',
            'modulos.*.descripcion.required' => 'Cada módulo debe tener una descripción.',
            'modulos.*.contenidos.*.titulo.required' => 'Cada contenido debe tener un título.',
            'modulos.*.contenidos.*.archivo.max' => 'El archivo no puede superar los 250 MB.',
        ]);

        // Validación específica por tipo de contenido
        $errores = [];
        foreach ($datos['modulos'] as $key => $modulo) {
            foreach ($modulo['contenidos'] ?? [] as $ckey => $contenido) {
                $tipo = $contenido['tipo'] ?? 'TEXTO';
                $archivoSubido = $request->file("modulos.$key.contenidos.$ckey.archivo");
                $rutaActual = $contenido['ruta_archivo_actual'] ?? null;
                $textoContenido = trim($contenido['contenido'] ?? '');
                $tituloContenido = $contenido['titulo'] ?? 'sin título';

                if ($tipo === 'VIDEO') {
                    if ($archivoSubido) {
                        $ext = strtolower($archivoSubido->getClientOriginalExtension());
                        $validExts = ['mp4', 'mov', 'webm', 'm4v'];
                        if (! in_array($ext, $validExts, true)) {
                            $errores["modulos.$key.contenidos.$ckey.archivo"] = 'El video debe estar en formato MP4, MOV o WEBM (recomendado MP4 con codec H.264 + AAC).';
                        }
                        if ($archivoSubido->getSize() > 250 * 1024 * 1024) {
                            $errores["modulos.$key.contenidos.$ckey.archivo"] = 'El archivo de video supera el límite de 250 MB.';
                        }
                    }

                    if (! $archivoSubido && empty($rutaActual) && empty($textoContenido)) {
                        $errores["modulos.$key.contenidos.$ckey.contenido"] = 'Para el video "' . $tituloContenido . '", adjunta un archivo de video (máx. 250 MB) o proporciona una URL.';
                    }
                } elseif (in_array($tipo, ['IMAGEN', 'PDF'], true)) {
                    if ($archivoSubido) {
                        $ext = strtolower($archivoSubido->getClientOriginalExtension());
                        $validExts = $tipo === 'PDF' ? ['pdf'] : ['jpg', 'jpeg', 'png', 'webp'];
                        if (! in_array($ext, $validExts, true)) {
                            $errores["modulos.$key.contenidos.$ckey.archivo"] = $tipo === 'PDF'
                                ? 'El documento debe ser un archivo PDF.'
                                : 'La imagen debe ser JPG, PNG o WEBP.';
                        }
                        if ($archivoSubido->getSize() > 20 * 1024 * 1024) {
                            $errores["modulos.$key.contenidos.$ckey.archivo"] = 'El archivo no puede superar los 20 MB.';
                        }
                    } elseif (empty($rutaActual)) {
                        $errores["modulos.$key.contenidos.$ckey.archivo"] = 'Adjunta el archivo del contenido "' . $tituloContenido . '".';
                    }
                } elseif (in_array($tipo, ['TEXTO', 'ENLACE'], true)) {
                    if (empty($textoContenido)) {
                        $errores["modulos.$key.contenidos.$ckey.contenido"] = 'Completa el ' . ($tipo === 'ENLACE' ? 'enlace' : 'texto') . ' del contenido "' . $tituloContenido . '".';
                    }
                }
            }
        }

        if ($errores) {
            throw ValidationException::withMessages($errores);
        }

        return $datos;
    }

    protected function guardarImagen(Request $request, ?string $rutaActual): ?string
    {
        if (! $request->hasFile('imagen')) {
            return $rutaActual;
        }

        $this->borrarArchivoPublico($rutaActual);

        return 'storage/' . $request->file('imagen')->store('capacitaciones', 'public');
    }

    protected function borrarArchivoPublico(?string $ruta): void
    {
        if ($ruta && str_starts_with($ruta, 'storage/')) {
            Storage::disk('public')->delete(substr($ruta, strlen('storage/')));
        }
    }

    /**
     * Crea / actualiza módulos y contenidos respetando el orden enviado.
     * Los módulos retirados del formulario se desactivan (nunca se eliminan, para conservar el progreso).
     */
    protected function sincronizarModulos(Capacitacion $capacitacion, array $modulosForm, Request $request): void
    {
        $existentes = $capacitacion->modulos()->get()->keyBy('id');
        $mantenidos = [];

        $posicion = 0;
        foreach ($modulosForm as $key => $datosModulo) {
            $posicion++;
            $modulo = isset($datosModulo['id']) ? $existentes->get((int) $datosModulo['id']) : null;

            $atributos = [
                'titulo' => $datosModulo['titulo'],
                'descripcion' => $datosModulo['descripcion'],
                'numero_seccion' => $posicion,
                'estado' => true,
            ];

            if ($modulo) {
                $modulo->update($atributos);
            } else {
                $modulo = $capacitacion->modulos()->create($atributos);
            }

            $mantenidos[] = $modulo->id;
            $this->sincronizarContenidos($modulo, $datosModulo['contenidos'] ?? [], $request, $key);
        }

        // Desactivar módulos que el jefe retiró del formulario
        $capacitacion->modulos()->whereNotIn('id', $mantenidos)->update(['estado' => false]);
    }

    protected function sincronizarContenidos(Modulo $modulo, array $contenidosForm, Request $request, int|string $moduloKey): void
    {
        $existentes = $modulo->contenidos()->get()->keyBy('id');
        $mantenidos = [];

        $orden = 0;
        foreach ($contenidosForm as $ckey => $datos) {
            $orden++;
            $contenido = isset($datos['id']) ? $existentes->get((int) $datos['id']) : null;
            $tipo = $datos['tipo'];
            $esArchivo = in_array($tipo, ['IMAGEN', 'PDF', 'VIDEO'], true);

            $ruta = $contenido?->ruta_archivo;
            if ($esArchivo && $request->hasFile("modulos.$moduloKey.contenidos.$ckey.archivo")) {
                $this->borrarArchivoPublico($ruta);
                $ruta = 'storage/' . $request->file("modulos.$moduloKey.contenidos.$ckey.archivo")->store('contenidos', 'public');
            } elseif (! $esArchivo) {
                $this->borrarArchivoPublico($ruta);
                $ruta = null;
            }

            $atributos = [
                'titulo' => $datos['titulo'],
                'tipo' => $tipo,
                'contenido' => $datos['contenido'] ?? null,
                'ruta_archivo' => $ruta,
                'orden' => $orden,
                'obligatorio' => true,
            ];

            if ($contenido) {
                $contenido->update($atributos);
            } else {
                $contenido = $modulo->contenidos()->create($atributos);
            }

            $mantenidos[] = $contenido->id;
        }

        // Los contenidos no tienen historial propio: se pueden retirar del módulo
        $modulo->contenidos()->whereNotIn('id', $mantenidos)->get()->each(function (Contenido $c) {
            $this->borrarArchivoPublico($c->ruta_archivo);
            $c->delete();
        });
    }

    /**
     * Estructura de módulos/contenidos para repoblar el formulario (old input o datos guardados).
     */
    protected function modulosParaFormulario(?Capacitacion $capacitacion): array
    {
        // Se conservan las llaves originales para poder mapear los errores de validación
        $old = old('modulos');
        if (is_array($old)) {
            return $old;
        }

        if (! $capacitacion) {
            return [[
                'id' => null,
                'titulo' => '',
                'descripcion' => '',
                'contenidos' => [],
            ]];
        }

        return $capacitacion->modulos()->where('estado', true)->with('contenidos')->get()
            ->map(fn (Modulo $m) => [
                'id' => $m->id,
                'titulo' => $m->titulo,
                'descripcion' => $m->descripcion,
                'contenidos' => $m->contenidos->map(fn (Contenido $c) => [
                    'id' => $c->id,
                    'titulo' => $c->titulo,
                    'tipo' => $c->tipo,
                    'contenido' => $c->contenido,
                    'ruta_archivo_actual' => $c->ruta_archivo,
                ])->all(),
            ])->all();
    }
}
