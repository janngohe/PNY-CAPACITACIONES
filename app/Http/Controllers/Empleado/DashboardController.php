<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\CertificadoExterno;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Vista principal del empleado: Capacitaciones e Inducciones asignadas reales.
     */
    public function capacitaciones()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Si no está autenticado, redirigir al login
        if (!$usuario) {
            return redirect()->route('login');
        }

        // Cargar relación de área
        $usuario->loadMissing('area');

        // Obtener capacitaciones asignadas directamente al usuario con datos del pivot
        $capacitaciones = $usuario->capacitaciones()
            ->with(['modulos' => function ($query) {
                $query->where('estado', true)->orderBy('numero_seccion');
            }])
            ->where('capacitaciones.estado', true)
            ->get();

        // Si no tiene asignaciones directas, verificar si su área tiene capacitaciones asignadas
        if ($capacitaciones->isEmpty() && $usuario->area) {
            $capacitaciones = $usuario->area->capacitaciones()
                ->where('capacitaciones.estado', true)
                ->with(['modulos' => function ($query) {
                    $query->where('estado', true)->orderBy('numero_seccion');
                }])
                ->get();
        }

        // Si aún está vacío, cargar las capacitaciones generales activas del sistema
        if ($capacitaciones->isEmpty()) {
            $capacitaciones = Capacitacion::where('estado', true)
                ->with(['modulos' => function ($query) {
                    $query->where('estado', true)->orderBy('numero_seccion');
                }])
                ->get();
        }

        // Calcular progreso real para cada capacitación del usuario
        foreach ($capacitaciones as $cap) {
            $modulosIds = $cap->modulos->pluck('id');
            $totalModulos = $modulosIds->count();

            if ($totalModulos > 0) {
                $modulosCompletados = ProgresoModulo::where('usuario_id', $usuario->id)
                    ->whereIn('modulo_id', $modulosIds)
                    ->where('completado', true)
                    ->count();

                $cap->modulos_completados_count = $modulosCompletados;
                $cap->porcentaje_calculado = round(($modulosCompletados / $totalModulos) * 100);
            } else {
                $cap->modulos_completados_count = 0;
                $cap->porcentaje_calculado = 0;
            }

            // Estado del pivot si existe
            $cap->estado_usuario = $cap->pivot->estado ?? ($cap->porcentaje_calculado > 0 ? 'EN_PROGRESO' : 'PENDIENTE');
        }

        // Estadísticas reales calculadas desde la base de datos
        $completadasCount = $usuario->capacitaciones()
            ->wherePivot('estado', 'COMPLETADA')
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

        // Consulta real de certificados emitidos para este usuario
        $certificados = $usuario->certificados()
            ->with(['capacitacion', 'plantillaCertificado'])
            ->latest('fecha_emision')
            ->get();

        return view('empleado.certificados', compact('usuario', 'certificados'));
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

        // Capacitaciones finalizadas reales
        $finalizadas = $usuario->capacitaciones()
            ->wherePivot('estado', 'COMPLETADA')
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

        // Consulta real de certificados externos subidos por el usuario
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
