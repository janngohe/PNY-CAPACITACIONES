<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaInduccion;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\IntentoEvaluacion;
use App\Models\Sede;
use App\Models\Usuario;
use App\Models\UsuarioCapacitacion;
use App\Support\Seguimiento;
use Carbon\Carbon;
use Illuminate\View\View;

class EstadisticaController extends Controller
{
    /**
     * Muestra el panel interactivo de estadísticas del área para el Jefe de Área,
     * con gráficos detallados de capacitaciones, inducciones y desglose por sedes.
     */
    public function index(): View
    {
        /** @var Usuario $jefe */
        $jefe = auth()->user();
        $jefe->loadMissing(['area.sede', 'sede']);
        $area = $jefe->area;
        $areaId = $jefe->area_id;
        $areaNombre = $area?->nombre ?? 'Mi Área';

        // 1. Colaboradores del Área
        $colaboradores = Usuario::query()
            ->with(['sede:id,nombre,ciudad', 'area.sede:id,nombre,ciudad'])
            ->where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->when($areaId, fn ($q) => $q->where('area_id', $areaId))
            ->orderBy('nombre_completo')
            ->get();

        $colaboradoresIds = $colaboradores->pluck('id');
        $totalColaboradores = $colaboradores->count();

        // 2. Progreso de Capacitaciones (Exclusivo del área)
        $filasProgreso = Seguimiento::progreso([
            'area_id' => $areaId,
        ]);

        $totalAsignaciones = $filasProgreso->count();
        $completadas = $filasProgreso->where('estado', 'COMPLETADA')->count();
        $enProgreso = $filasProgreso->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
        $pendientes = $filasProgreso->where('estado', 'PENDIENTE')->count();
        $noAprobadas = $filasProgreso->where('estado', 'NO_APROBADA')->count();
        $promedioAvanceGlobal = $totalAsignaciones > 0 ? (int) round($filasProgreso->avg('porcentaje')) : 0;
        $tasaCumplimiento = $totalAsignaciones > 0 ? (int) round(($completadas / $totalAsignaciones) * 100) : 0;

        // 3. Evaluaciones y Certificados del Área
        $certificadosCount = Certificado::whereIn('usuario_id', $colaboradoresIds)->count();

        $intentosArea = IntentoEvaluacion::whereIn('usuario_id', $colaboradoresIds)->get();
        $totalIntentos = $intentosArea->count();
        $intentosAprobados = $intentosArea->where('estado', 'APROBADO')->count();
        $tasaAprobacion = $totalIntentos > 0 ? (int) round(($intentosAprobados / $totalIntentos) * 100) : 0;
        $promedioCalificacion = $totalIntentos > 0 ? round((float) $intentosArea->avg('porcentaje'), 1) : 0;

        // 4. Inducciones del Área
        $asistenciasInduccion = AsistenciaInduccion::query()
            ->where(function ($q) use ($areaId, $areaNombre, $colaboradoresIds) {
                $q->whereIn('usuario_id', $colaboradoresIds);
                if ($areaNombre) {
                    $q->orWhere('area', $areaNombre);
                }
            })
            ->with(['sesion.induccion', 'sesion.sede'])
            ->latest('fecha_registro')
            ->get();

        $totalAsistenciasInduccion = $asistenciasInduccion->count();
        $sesionesInduccionParticipadas = $asistenciasInduccion->pluck('sesion_id')->unique()->count();
        $colaboradoresInducidos = $asistenciasInduccion->pluck('identificacion')->unique()->count();
        $conFirmaDigitalCount = $asistenciasInduccion->filter(fn ($a) => !empty($a->firma_digital))->count();

        // 5. Estadísticas Desglosadas por SEDE (solo colaboradores y actividad de esta área)
        $sedesDb = Sede::where('estado', true)->orderBy('nombre')->get();

        $porSede = $sedesDb->map(function ($sede) use ($colaboradores, $filasProgreso, $asistenciasInduccion) {
            $colabsSede = $colaboradores->filter(function ($c) use ($sede) {
                $sedeIdColab = $c->sede_id ?: $c->area?->sede_id;
                return $sedeIdColab == $sede->id;
            });
            $colabIdsSede = $colabsSede->pluck('id')->toArray();
            $totalColabs = $colabsSede->count();

            // Capacitaciones
            $asigSede = $filasProgreso->filter(fn ($f) => in_array($f->usuario->id, $colabIdsSede));
            $totalAsig = $asigSede->count();
            $compSede = $asigSede->where('estado', 'COMPLETADA')->count();
            $promedioSede = $totalAsig > 0 ? (int) round($asigSede->avg('porcentaje')) : 0;
            $certificadosSede = $asigSede->where('certificado', true)->count();

            // Inducciones
            $asistIndSede = $asistenciasInduccion->filter(function ($a) use ($colabIdsSede, $sede) {
                return in_array($a->usuario_id, $colabIdsSede) ||
                       ($a->sesion?->sede_id == $sede->id && in_array($a->usuario_id, $colabIdsSede));
            });
            $totalInduccionesSede = $asistIndSede->count();

            return (object) [
                'id' => $sede->id,
                'nombre' => $sede->nombre,
                'ciudad' => $sede->ciudad,
                'colaboradores_count' => $totalColabs,
                'asignaciones_count' => $totalAsig,
                'completadas_count' => $compSede,
                'promedio_avance' => $promedioSede,
                'tasa_cumplimiento' => $totalAsig > 0 ? (int) round(($compSede / $totalAsig) * 100) : 0,
                'certificados_count' => $certificadosSede,
                'inducciones_count' => $totalInduccionesSede,
            ];
        })
        ->filter(fn ($s) => $s->colaboradores_count > 0 || $s->asignaciones_count > 0 || $s->inducciones_count > 0)
        ->sortByDesc('colaboradores_count')
        ->values();

        // Si ninguna sede coincidió (por ejemplo, empleados sin sede asignada aún), incluimos un resumen general
        if ($porSede->isEmpty()) {
            $porSede = collect([(object) [
                'id' => 0,
                'nombre' => 'Sede Principal / General',
                'ciudad' => 'Huila',
                'colaboradores_count' => $totalColaboradores,
                'asignaciones_count' => $totalAsignaciones,
                'completadas_count' => $completadas,
                'promedio_avance' => $promedioAvanceGlobal,
                'tasa_cumplimiento' => $tasaCumplimiento,
                'certificados_count' => $certificadosCount,
                'inducciones_count' => $totalAsistenciasInduccion,
            ]]);
        }

        // 6. Evolución Mensual de Capacitaciones e Inducciones del Área (Últimos 6 meses)
        $evolucionMensual = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $clave = $fecha->format('Y-m');
            $label = ucfirst($fecha->translatedFormat('M Y'));

            // Capacitaciones completadas en el mes por colaboradores del área
            $completadasMes = UsuarioCapacitacion::whereIn('usuario_id', $colaboradoresIds)
                ->where('estado', 'COMPLETADA')
                ->where(function ($q) use ($clave) {
                    $q->whereRaw("DATE_FORMAT(fecha_finalizacion, '%Y-%m') = ?", [$clave])
                      ->orWhereRaw("DATE_FORMAT(fecha_asignacion, '%Y-%m') = ?", [$clave]);
                })
                ->count();

            // Inducciones asistidas en el mes por colaboradores del área
            $induccionesMes = $asistenciasInduccion->filter(function ($a) use ($clave) {
                return $a->fecha_registro && $a->fecha_registro->format('Y-m') === $clave;
            })->count();

            // Intentos de evaluación aprobados en el mes
            $aprobadosMes = $intentosArea->filter(function ($it) use ($clave) {
                $f = $it->fecha_finalizacion ?? $it->fecha_inicio;
                return $f && $f->format('Y-m') === $clave && $it->estado === 'APROBADO';
            })->count();

            $evolucionMensual[] = [
                'mes' => $label,
                'clave' => $clave,
                'completadas' => $completadasMes,
                'inducciones' => $induccionesMes,
                'aprobados' => $aprobadosMes,
            ];
        }

        // 7. Ranking de Capacitaciones Asignadas a esta Área con su Avance Promedio
        $rankingCapacitaciones = $filasProgreso->groupBy('capacitacion_id')->map(function ($filasCap) {
            $primera = $filasCap->first();
            $total = $filasCap->count();
            $comp = $filasCap->where('estado', 'COMPLETADA')->count();
            $prom = $total > 0 ? (int) round($filasCap->avg('porcentaje')) : 0;

            return (object) [
                'id' => $primera->capacitacion->id,
                'titulo' => $primera->capacitacion->titulo,
                'total_colaboradores' => $total,
                'completadas' => $comp,
                'promedio_avance' => $prom,
                'tasa_cumplimiento' => $total > 0 ? (int) round(($comp / $total) * 100) : 0,
            ];
        })->sortByDesc('promedio_avance')->values()->take(8);

        // Agrupación de KPIs compactos
        $kpis = [
            'colaboradores_total' => $totalColaboradores,
            'sedes_con_presencia' => $porSede->count(),
            'promedio_avance' => $promedioAvanceGlobal,
            'completadas' => $completadas,
            'en_progreso' => $enProgreso,
            'pendientes' => $pendientes,
            'no_aprobadas' => $noAprobadas,
            'total_asignaciones' => $totalAsignaciones,
            'tasa_cumplimiento' => $tasaCumplimiento,
            'certificados_count' => $certificadosCount,
            'intentos_total' => $totalIntentos,
            'tasa_aprobacion' => $tasaAprobacion,
            'promedio_calificacion' => $promedioCalificacion,
            'inducciones_asistencias' => $totalAsistenciasInduccion,
            'inducciones_sesiones' => $sesionesInduccionParticipadas,
            'inducciones_colaboradores' => $colaboradoresInducidos,
            'inducciones_firmas' => $conFirmaDigitalCount,
        ];

        return view('jefe.estadisticas.index', compact(
            'jefe',
            'area',
            'areaNombre',
            'kpis',
            'porSede',
            'evolucionMensual',
            'rankingCapacitaciones',
            'asistenciasInduccion'
        ));
    }
}

