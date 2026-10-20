<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\Evaluacion;
use App\Models\IntentoEvaluacion;
use App\Models\Sede;
use App\Models\Usuario;
use App\Support\Seguimiento;

class DashboardController extends Controller
{
    public function index()
    {
        $filas = Seguimiento::progreso();

        $usuarios = [
            'total' => Usuario::count(),
            'activos' => Usuario::where('estado', true)->count(),
            'empleados' => Usuario::where('rol', 'EMPLEADO')->where('estado', true)->count(),
            'jefes' => Usuario::where('rol', 'JEFE_AREA')->where('estado', true)->count(),
            'admins' => Usuario::where('rol', 'ADMINISTRADOR')->where('estado', true)->count(),
        ];

        $intentos = IntentoEvaluacion::count();
        $aprobados = IntentoEvaluacion::where('estado', 'APROBADO')->count();

        $kpis = [
            'sedes' => Sede::where('estado', true)->count(),
            'sedes_total' => Sede::count(),
            'capacitaciones' => Capacitacion::where('estado', true)->count(),
            'capacitaciones_total' => Capacitacion::count(),
            'evaluaciones' => Evaluacion::where('estado', true)->count(),
            'areas' => Area::where('estado', true)->count(),
            'certificados' => Certificado::count(),
            'intentos' => $intentos,
            'tasa_aprobacion' => $intentos > 0 ? (int) round(($aprobados / $intentos) * 100) : 0,
            'avance_global' => $filas->isNotEmpty() ? (int) round($filas->avg('porcentaje')) : 0,
            'completadas' => $filas->where('estado', 'COMPLETADA')->count(),
            'en_progreso' => $filas->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count(),
            'pendientes' => $filas->where('estado', 'PENDIENTE')->count(),
        ];

        // 1. Estadísticas Detalladas por SEDE
        $sedesDb = Sede::with(['areas' => fn ($q) => $q->where('estado', true)->withCount(['usuarios as empleados_count' => fn ($u) => $u->where('rol', 'EMPLEADO')->where('estado', true)])])
            ->orderBy('nombre')
            ->get();

        $porSede = $sedesDb->map(function ($sede) use ($filas) {
            $asignacionesSede = $filas->filter(function ($f) use ($sede) {
                $sedeIdUsuario = $f->usuario->sede_id ?? $f->usuario->area?->sede_id;
                return $sedeIdUsuario == $sede->id;
            });

            $totalAsignaciones = $asignacionesSede->count();
            $completadas = $asignacionesSede->where('estado', 'COMPLETADA')->count();
            $enProgreso = $asignacionesSede->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $pendientes = $asignacionesSede->where('estado', 'PENDIENTE')->count();
            $promedio = $totalAsignaciones > 0 ? (int) round($asignacionesSede->avg('porcentaje')) : 0;
            $certificadosSede = $asignacionesSede->where('certificado', true)->count();

            // Desglose de áreas dentro de la sede
            $areasDetalle = $sede->areas->map(function ($area) use ($asignacionesSede) {
                $asigArea = $asignacionesSede->filter(fn ($f) => $f->usuario->area_id == $area->id);
                $totalArea = $asigArea->count();
                $compArea = $asigArea->where('estado', 'COMPLETADA')->count();
                $promArea = $totalArea > 0 ? (int) round($asigArea->avg('porcentaje')) : 0;

                return (object) [
                    'id' => $area->id,
                    'nombre' => $area->nombre,
                    'empleados' => $area->empleados_count ?? 0,
                    'asignaciones' => $totalArea,
                    'completadas' => $compArea,
                    'promedio' => $promArea,
                ];
            })->sortByDesc('promedio')->values();

            return (object) [
                'id' => $sede->id,
                'nombre' => $sede->nombre,
                'ciudad' => $sede->ciudad,
                'direccion' => $sede->direccion,
                'estado' => $sede->estado,
                'areas_count' => $sede->areas->count(),
                'colaboradores_count' => $sede->areas->sum('empleados_count'),
                'total_asignaciones' => $totalAsignaciones,
                'completadas' => $completadas,
                'en_progreso' => $enProgreso,
                'pendientes' => $pendientes,
                'promedio' => $promedio,
                'tasa_cumplimiento' => $totalAsignaciones > 0 ? (int) round(($completadas / $totalAsignaciones) * 100) : 0,
                'certificados_count' => $certificadosSede,
                'areas_detalle' => $areasDetalle,
            ];
        })->sortByDesc('promedio')->values();

        // 2. Estadísticas Detalladas por ÁREA (discriminadas por sede institucional)
        $areasDb = Area::with(['sede'])
            ->withCount(['usuarios as empleados_count' => fn ($u) => $u->where('rol', 'EMPLEADO')->where('estado', true)])
            ->where('estado', true)
            ->orderBy('nombre')
            ->get();

        $porArea = $areasDb->map(function ($area) use ($filas) {
            $asigs = $filas->where('usuario.area_id', $area->id);
            $total = $asigs->count();
            $completadas = $asigs->where('estado', 'COMPLETADA')->count();
            $enProgreso = $asigs->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $pendientes = $asigs->where('estado', 'PENDIENTE')->count();
            $promedio = $total > 0 ? (int) round($asigs->avg('porcentaje')) : 0;

            return (object) [
                'id' => $area->id,
                'area_id' => $area->id,
                'nombre' => $area->nombre,
                'sede_id' => $area->sede_id,
                'sede_nombre' => $area->sede?->nombre ?? 'Sin Sede',
                'sede_ciudad' => $area->sede?->ciudad ?? '',
                'empleados' => $area->empleados_count ?? 0,
                'asignaciones' => $total,
                'completadas' => $completadas,
                'en_progreso' => $enProgreso,
                'pendientes' => $pendientes,
                'promedio' => $promedio,
            ];
        })
        ->sortByDesc('promedio')
        ->values();

        // 3. Estadísticas por Capacitación
        $porCapacitacion = $filas->groupBy(fn ($f) => $f->capacitacion->id)
            ->map(fn ($grupo) => (object) [
                'capacitacion' => $grupo->first()->capacitacion,
                'participantes' => $grupo->count(),
                'promedio' => (int) round($grupo->avg('porcentaje')),
                'completadas' => $grupo->where('estado', 'COMPLETADA')->count(),
            ])
            ->sortByDesc('participantes')
            ->take(6)
            ->values();

        $ultimosCertificados = Certificado::with('capacitacion:id,titulo')
            ->orderByDesc('fecha_emision')
            ->limit(5)
            ->get();

        $ultimosIntentos = IntentoEvaluacion::with(['usuario:id,nombre_completo', 'evaluacion:id,titulo'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // =========================================================================
        // ESTADÍSTICAS PRIORITARIAS: TASA DE FINALIZACIÓN Y PENDIENTES
        // =========================================================================

        // 1. Tasa de finalización de capacitaciones por empleados asignados
        $empleadosConAsignaciones = $filas->groupBy(fn ($f) => $f->usuario->id);
        $totalEmpleadosAsignados = $empleadosConAsignaciones->count();
        $empleadosCompletaronTodo = $empleadosConAsignaciones->filter(function ($asigs) {
            return $asigs->isNotEmpty() && $asigs->every(fn ($a) => $a->estado === 'COMPLETADA');
        });
        $totalEmpleadosCompletados = $empleadosCompletaronTodo->count();
        $tasaFinalizacion = $totalEmpleadosAsignados > 0 
            ? (int) round(($totalEmpleadosCompletados / $totalEmpleadosAsignados) * 100) 
            : 0;

        // 2. Empleados pendientes de capacitación (cobertura total de sedes y áreas)
        $todosEmpleados = Usuario::where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->with(['area.sede', 'sede'])
            ->orderBy('nombre_completo')
            ->get();

        $listaPendientes = [];
        $pendientesPorSede = [];
        $pendientesPorArea = [];

        foreach ($todosEmpleados as $emp) {
            $asigs = $filas->where('usuario.id', $emp->id);
            $totalAsigs = $asigs->count();
            $completadas = $asigs->where('estado', 'COMPLETADA')->count();
            $pendientes = $asigs->whereIn('estado', ['PENDIENTE', 'EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $promedio = $totalAsigs > 0 ? (int) round($asigs->avg('porcentaje')) : 0;

            // Es pendiente si tiene asignaciones y no todas están completadas, o si es un empleado sin cursos completados
            $esPendiente = ($totalAsigs > 0 && $pendientes > 0) || ($totalAsigs === 0);

            if ($esPendiente) {
                $sedeNombre = $emp->sedeActual()?->nombre ?? 'Sin Sede';
                $areaNombre = $emp->area?->nombre ?? 'Sin Área';
                $areaLabel = $areaNombre . ' (' . $sedeNombre . ')';

                $pendientesPorSede[$sedeNombre] = ($pendientesPorSede[$sedeNombre] ?? 0) + 1;
                $pendientesPorArea[$areaLabel] = ($pendientesPorArea[$areaLabel] ?? 0) + 1;

                $listaPendientes[] = (object) [
                    'id' => $emp->id,
                    'nombre' => $emp->nombre_completo,
                    'identificacion' => $emp->identificacion,
                    'sede' => $sedeNombre,
                    'area' => $areaNombre,
                    'total_asignadas' => $totalAsigs,
                    'completadas' => $completadas,
                    'pendientes' => $totalAsigs > 0 ? $pendientes : 0,
                    'promedio' => $promedio,
                    'sin_asignar' => $totalAsigs === 0,
                ];
            }
        }

        // Estructura completa de sedes y áreas para el bloque de pendientes (soporta sedes y áreas actuales y futuras)
        $sedesDbParaPendientes = Sede::where('estado', true)
            ->with(['areas' => fn ($q) => $q->where('estado', true)->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $sedesPendientesDetalle = $sedesDbParaPendientes->map(function ($sede) use ($todosEmpleados, $filas) {
            $areasSede = $sede->areas->map(function ($area) use ($todosEmpleados, $filas) {
                $empleadosArea = $todosEmpleados->filter(fn ($u) => $u->area_id == $area->id);
                $pendientesCount = 0;

                foreach ($empleadosArea as $emp) {
                    $asigs = $filas->where('usuario.id', $emp->id);
                    $totalAsigs = $asigs->count();
                    $pendientes = $asigs->whereIn('estado', ['PENDIENTE', 'EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
                    if (($totalAsigs > 0 && $pendientes > 0) || ($totalAsigs === 0)) {
                        $pendientesCount++;
                    }
                }

                return (object) [
                    'id' => $area->id,
                    'nombre' => $area->nombre,
                    'pendientes' => $pendientesCount,
                    'total_empleados' => $empleadosArea->count(),
                ];
            });

            $empleadosSede = $todosEmpleados->filter(function ($u) use ($sede) {
                return ($u->sede_id ?? $u->area?->sede_id) == $sede->id;
            });
            $pendientesSedeCount = 0;
            foreach ($empleadosSede as $emp) {
                $asigs = $filas->where('usuario.id', $emp->id);
                $totalAsigs = $asigs->count();
                $pendientes = $asigs->whereIn('estado', ['PENDIENTE', 'EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
                if (($totalAsigs > 0 && $pendientes > 0) || ($totalAsigs === 0)) {
                    $pendientesSedeCount++;
                }
            }

            return (object) [
                'id' => $sede->id,
                'nombre' => $sede->nombre,
                'ciudad' => $sede->ciudad,
                'total_pendientes' => $pendientesSedeCount,
                'total_empleados' => $empleadosSede->count(),
                'areas' => $areasSede,
            ];
        });

        $pendientesDistribucion = [
            'total' => count($listaPendientes),
            'por_sede' => $pendientesPorSede,
            'por_area' => $pendientesPorArea,
            'sedes_detalle' => $sedesPendientesDetalle,
            'lista' => $listaPendientes,
        ];

        // 3. Evolución mensual de capacitaciones (últimos 6 meses)
        $evolucionMensual = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subMonths($i);
            $clave = $fecha->format('Y-m');
            $label = ucfirst($fecha->translatedFormat('M Y'));

            $asignadas = \App\Models\UsuarioCapacitacion::whereRaw("DATE_FORMAT(fecha_asignacion, '%Y-%m') = ?", [$clave])->count();

            $participantes = \App\Models\UsuarioCapacitacion::where(function ($q) use ($clave) {
                $q->whereRaw("DATE_FORMAT(fecha_asignacion, '%Y-%m') = ?", [$clave])
                  ->orWhereRaw("DATE_FORMAT(fecha_inicio, '%Y-%m') = ?", [$clave]);
            })->distinct('usuario_id')->count('usuario_id');

            $completadas = \App\Models\UsuarioCapacitacion::where('estado', 'COMPLETADA')
                ->where(function ($q) use ($clave) {
                    $q->whereRaw("DATE_FORMAT(fecha_finalizacion, '%Y-%m') = ?", [$clave])
                      ->orWhereRaw("DATE_FORMAT(fecha_asignacion, '%Y-%m') = ?", [$clave]);
                })
                ->count();

            $evolucionMensual[] = [
                'mes' => $label,
                'clave' => $clave,
                'asignadas' => $asignadas,
                'participantes' => $participantes,
                'completadas' => $completadas,
            ];
        }

        // 5. Capacitaciones vencidas o próximas a vencer
        $hoy = \Carbon\Carbon::now();
        $en30Dias = \Carbon\Carbon::now()->addDays(30);

        $capsConFecha = Capacitacion::where('estado', true)
            ->whereNotNull('fecha_limite')
            ->with('areas:id,nombre')
            ->get();

        $vencidasCaps = $capsConFecha->filter(fn ($c) => \Carbon\Carbon::parse($c->fecha_limite)->isPast());
        $proximasCaps = $capsConFecha->filter(fn ($c) => !\Carbon\Carbon::parse($c->fecha_limite)->isPast() && \Carbon\Carbon::parse($c->fecha_limite)->lte($en30Dias));
        $alDiaCaps = $capsConFecha->filter(fn ($c) => \Carbon\Carbon::parse($c->fecha_limite)->gt($en30Dias));

        $asignacionesVencidasCount = $filas->filter(function ($f) {
            return !empty($f->capacitacion->fecha_limite)
                && \Carbon\Carbon::parse($f->capacitacion->fecha_limite)->isPast()
                && $f->estado !== 'COMPLETADA';
        })->count();

        $asignacionesProximasCount = $filas->filter(function ($f) use ($en30Dias) {
            if (empty($f->capacitacion->fecha_limite)) return false;
            $fl = \Carbon\Carbon::parse($f->capacitacion->fecha_limite);
            return !$fl->isPast() && $fl->lte($en30Dias) && $f->estado !== 'COMPLETADA';
        })->count();

        $vencimientos = [
            'vencidas_count' => $vencidasCaps->count(),
            'proximas_count' => $proximasCaps->count(),
            'al_dia_count' => $alDiaCaps->count(),
            'sin_limite_count' => Capacitacion::where('estado', true)->whereNull('fecha_limite')->count(),
            'asignaciones_vencidas_count' => $asignacionesVencidasCount,
            'asignaciones_proximas_count' => $asignacionesProximasCount,
            'vencidas' => $vencidasCaps->values(),
            'proximas' => $proximasCaps->values(),
        ];

        $usuario = auth()->user();

        return view('admin.dashboard', compact(
            'usuario',
            'usuarios',
            'kpis',
            'porSede',
            'porArea',
            'porCapacitacion',
            'ultimosCertificados',
            'ultimosIntentos',
            'tasaFinalizacion',
            'totalEmpleadosAsignados',
            'totalEmpleadosCompletados',
            'pendientesDistribucion',
            'evolucionMensual',
            'vencimientos'
        ));
    }
}
