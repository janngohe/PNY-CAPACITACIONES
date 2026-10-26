<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    /**
     * Listado del personal (empleados) perteneciente al área del Jefe de Área,
     * con soporte de filtrado por sedes y por capacitaciones creadas/asignadas.
     */
    public function index(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $area = $usuario->area;

        if (! $usuario->area_id) {
            return view('jefe.personal.index', [
                'usuario' => $usuario,
                'area' => null,
                'empleados' => collect(),
                'sedes' => collect(),
                'capacitaciones' => collect(),
                'capacitacionSeleccionada' => null,
                'estadisticas' => [
                    'total' => 0,
                    'activos' => 0,
                    'inactivos' => 0,
                    'con_certificados' => 0,
                    'completadas' => 0,
                    'en_progreso' => 0,
                    'sin_iniciar' => 0,
                    'porcentaje' => 0,
                ],
                'buscar' => '',
                'filtroEstado' => 'todos',
                'filtroSede' => '',
                'filtroCapacitacion' => '',
                'filtroParticipacion' => 'todos',
                'empleadosPorSede' => collect(),
            ]);
        }

        $buscar = trim((string) $request->query('buscar', ''));
        $filtroEstado = (string) $request->query('estado', 'todos');
        $filtroSede = $request->query('sede_id');
        $filtroCapacitacion = $request->query('capacitacion_id');
        $filtroParticipacion = (string) $request->query('participacion', 'todos');

        // 1. Sedes institucionales activas
        $sedes = \App\Models\Sede::where('estado', true)->orderBy('nombre')->get();

        // 2. Capacitaciones creadas por el Jefe de Área o vinculadas a su área institucional
        $capacitaciones = \App\Models\Capacitacion::query()
            ->where(function ($q) use ($usuario) {
                $q->where('creado_por', $usuario->id)
                  ->orWhereHas('areas', fn ($a) => $a->where('areas.id', $usuario->area_id));
            })
            ->where('estado', true)
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'creado_por', 'puntos', 'duracion_estimada']);

        $capacitacionSeleccionada = $filtroCapacitacion
            ? $capacitaciones->firstWhere('id', (int) $filtroCapacitacion)
            : null;

        $capacitacionIds = $capacitaciones->pluck('id');

        // 3. Conteo de empleados del área agrupados por sede institucional
        $empleadosPorSede = Usuario::where('area_id', $usuario->area_id)
            ->where('rol', 'EMPLEADO')
            ->selectRaw('coalesce(sede_id, ?) as s_id, count(*) as total', [$usuario->area?->sede_id ?? 1])
            ->groupBy('s_id')
            ->pluck('total', 's_id');

        // 4. Query base de los colaboradores participantes del área
        $query = Usuario::query()
            ->where('area_id', $usuario->area_id)
            ->where('rol', 'EMPLEADO')
            ->with([
                'sede:id,nombre,ciudad',
                'area.sede:id,nombre,ciudad',
                'asignaciones' => function ($q) use ($capacitacionIds) {
                    if ($capacitacionIds->isNotEmpty()) {
                        $q->whereIn('capacitacion_id', $capacitacionIds);
                    }
                    $q->with('capacitacion:id,titulo,estado,puntos,porcentaje_aprobacion');
                },
                'certificados' => function ($q) use ($capacitacionIds) {
                    if ($capacitacionIds->isNotEmpty()) {
                        $q->whereIn('capacitacion_id', $capacitacionIds);
                    }
                    $q->select('id', 'usuario_id', 'capacitacion_id', 'codigo', 'fecha_emision');
                }
            ]);

        // A. Filtro por Sede institucional
        if (!empty($filtroSede)) {
            $query->where(function ($q) use ($filtroSede) {
                $q->where('sede_id', $filtroSede)
                  ->orWhere(function ($sub) use ($filtroSede) {
                      $sub->whereNull('sede_id')
                          ->whereHas('area', fn ($a) => $a->where('sede_id', $filtroSede));
                  });
            });
        }

        // B. Búsqueda por texto (nombre completo o identificación)
        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('identificacion', 'like', "%{$buscar}%");
            });
        }

        // C. Filtro por Estado del colaborador (activo / inactivo)
        if ($filtroEstado === 'activos') {
            $query->where('estado', true);
        } elseif ($filtroEstado === 'inactivos') {
            $query->where('estado', false);
        }

        // D. Filtro por estado de participación en la capacitación seleccionada
        if ($capacitacionSeleccionada) {
            $capId = $capacitacionSeleccionada->id;
            if ($filtroParticipacion === 'completadas') {
                $query->whereHas('asignaciones', fn ($a) => $a->where('capacitacion_id', $capId)->where('estado', 'COMPLETADA'));
            } elseif ($filtroParticipacion === 'en_progreso') {
                $query->whereHas('asignaciones', fn ($a) => $a->where('capacitacion_id', $capId)->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS']));
            } elseif ($filtroParticipacion === 'sin_iniciar') {
                $query->where(function ($q) use ($capId) {
                    $q->whereHas('asignaciones', fn ($a) => $a->where('capacitacion_id', $capId)->where('estado', 'PENDIENTE'))
                      ->orWhereDoesntHave('asignaciones', fn ($a) => $a->where('capacitacion_id', $capId));
                });
            }
        }

        // Contadores agregados para el área y capacitaciones creadas
        $query->withCount([
            'asignaciones as total_asignaciones_count' => function ($q) use ($capacitacionIds) {
                if ($capacitacionIds->isNotEmpty()) {
                    $q->whereIn('capacitacion_id', $capacitacionIds);
                }
            },
            'asignaciones as completadas_count' => function ($q) use ($capacitacionIds) {
                if ($capacitacionIds->isNotEmpty()) {
                    $q->whereIn('capacitacion_id', $capacitacionIds);
                }
                $q->where('estado', 'COMPLETADA');
            },
            'asignaciones as en_progreso_count' => function ($q) use ($capacitacionIds) {
                if ($capacitacionIds->isNotEmpty()) {
                    $q->whereIn('capacitacion_id', $capacitacionIds);
                }
                $q->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS']);
            },
            'certificados as certificados_count' => function ($q) use ($capacitacionIds) {
                if ($capacitacionIds->isNotEmpty()) {
                    $q->whereIn('capacitacion_id', $capacitacionIds);
                }
            },
        ]);

        $empleados = $query->orderBy('nombre_completo')->get();

        // 5. Mapear estado específico de participación si se filtró por una capacitación
        if ($capacitacionSeleccionada) {
            foreach ($empleados as $emp) {
                $asignacion = $emp->asignaciones->firstWhere('capacitacion_id', $capacitacionSeleccionada->id);
                $emp->participacion_estado = $asignacion ? $asignacion->estado : 'PENDIENTE';
                $emp->participacion_inicio = $asignacion?->fecha_inicio;
                $emp->participacion_fin = $asignacion?->fecha_finalizacion;
                $emp->tiene_certificado = $emp->certificados->contains('capacitacion_id', $capacitacionSeleccionada->id);
            }
        }

        // 6. Obtener última actividad registrada para cada colaborador
        if ($empleados->isNotEmpty()) {
            $ultimasActividades = ProgresoModulo::query()
                ->whereIn('usuario_id', $empleados->pluck('id'))
                ->selectRaw('usuario_id, max(coalesce(fecha_finalizacion, fecha_inicio)) as ultima_fecha')
                ->groupBy('usuario_id')
                ->pluck('ultima_fecha', 'usuario_id');

            foreach ($empleados as $emp) {
                $emp->ultima_actividad = $ultimasActividades->get($emp->id);
            }
        }

        // 7. Estadísticas consolidadas calculadas sobre el universo de la sede seleccionada (o todas)
        $baseStatsQuery = Usuario::query()
            ->where('area_id', $usuario->area_id)
            ->where('rol', 'EMPLEADO')
            ->when(!empty($filtroSede), function ($q) use ($filtroSede) {
                $q->where(function ($sub) use ($filtroSede) {
                    $sub->where('sede_id', $filtroSede)
                        ->orWhere(function ($s2) use ($filtroSede) {
                            $s2->whereNull('sede_id')
                               ->whereHas('area', fn ($a) => $a->where('sede_id', $filtroSede));
                        });
                });
            });

        $totalEmpleadosSede = (clone $baseStatsQuery)->count();
        $activosSede = (clone $baseStatsQuery)->where('estado', true)->count();
        $inactivosSede = (clone $baseStatsQuery)->where('estado', false)->count();

        if ($capacitacionSeleccionada) {
            $capId = $capacitacionSeleccionada->id;
            $empleadoIdsSede = (clone $baseStatsQuery)->pluck('id');

            $participacionesCap = \App\Models\UsuarioCapacitacion::where('capacitacion_id', $capId)
                ->whereIn('usuario_id', $empleadoIdsSede)
                ->get();

            $completadasCap = $participacionesCap->where('estado', 'COMPLETADA')->count();
            $enProgresoCap = $participacionesCap->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $sinIniciarCap = max(0, $totalEmpleadosSede - ($completadasCap + $enProgresoCap));

            $porcentajeCumplimiento = $totalEmpleadosSede > 0
                ? (int) round(($completadasCap / $totalEmpleadosSede) * 100)
                : 0;

            $estadisticas = [
                'total' => $totalEmpleadosSede,
                'activos' => $activosSede,
                'inactivos' => $inactivosSede,
                'completadas' => $completadasCap,
                'en_progreso' => $enProgresoCap,
                'sin_iniciar' => $sinIniciarCap,
                'porcentaje' => $porcentajeCumplimiento,
                'con_certificados' => \App\Models\Certificado::where('capacitacion_id', $capId)->whereIn('usuario_id', $empleadoIdsSede)->count(),
            ];
        } else {
            // Estadísticas generales sobre todas las capacitaciones del área
            $empleadoIdsSede = (clone $baseStatsQuery)->pluck('id');
            $certificadosCount = \App\Models\Certificado::query()
                ->when($capacitacionIds->isNotEmpty(), fn ($q) => $q->whereIn('capacitacion_id', $capacitacionIds))
                ->whereIn('usuario_id', $empleadoIdsSede)
                ->distinct('usuario_id')
                ->count('usuario_id');

            $asignacionesGlobales = \App\Models\UsuarioCapacitacion::query()
                ->when($capacitacionIds->isNotEmpty(), fn ($q) => $q->whereIn('capacitacion_id', $capacitacionIds))
                ->whereIn('usuario_id', $empleadoIdsSede)
                ->get();

            $completadasTotal = $asignacionesGlobales->where('estado', 'COMPLETADA')->count();
            $enProgresoTotal = $asignacionesGlobales->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $numCaps = max(1, $capacitaciones->count());

            $estadisticas = [
                'total' => $totalEmpleadosSede,
                'activos' => $activosSede,
                'inactivos' => $inactivosSede,
                'con_certificados' => $certificadosCount,
                'completadas' => $completadasTotal,
                'en_progreso' => $enProgresoTotal,
                'sin_iniciar' => max(0, ($totalEmpleadosSede * $numCaps) - ($completadasTotal + $enProgresoTotal)),
                'porcentaje' => ($totalEmpleadosSede > 0 && $capacitaciones->count() > 0)
                    ? (int) round(($completadasTotal / ($totalEmpleadosSede * $capacitaciones->count())) * 100)
                    : 0,
            ];
        }

        return view('jefe.personal.index', compact(
            'usuario',
            'area',
            'empleados',
            'sedes',
            'capacitaciones',
            'capacitacionSeleccionada',
            'estadisticas',
            'buscar',
            'filtroEstado',
            'filtroSede',
            'filtroCapacitacion',
            'filtroParticipacion',
            'empleadosPorSede'
        ));
    }
}
