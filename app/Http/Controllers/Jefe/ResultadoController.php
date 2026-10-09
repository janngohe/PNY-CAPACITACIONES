<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\IntentoEvaluacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Consulta de resultados de evaluaciones (submenú de "Crear Evaluación").
 */
class ResultadoController extends Controller
{
    public function index()
    {
        $usuario = $this->jefe();

        $evaluaciones = Evaluacion::query()
            ->whereHas('capacitacion', fn ($q) => $q->where('creado_por', $usuario->id))
            ->with('capacitacion')
            ->withCount('preguntas')
            ->orderByDesc('id')
            ->get();

        $stats = collect();
        if ($evaluaciones->isNotEmpty()) {
            $stats = IntentoEvaluacion::query()
                ->whereIn('evaluacion_id', $evaluaciones->pluck('id'))
                ->selectRaw("evaluacion_id, count(*) as intentos, count(distinct usuario_id) as participantes, sum(case when estado = 'APROBADO' then 1 else 0 end) as aprobados, avg(porcentaje) as promedio")
                ->groupBy('evaluacion_id')
                ->get()
                ->keyBy('evaluacion_id');
        }

        $empleadosArea = $usuario->area_id
            ? Usuario::where('rol', 'EMPLEADO')->where('estado', true)->where('area_id', $usuario->area_id)->count()
            : 0;

        foreach ($evaluaciones as $evaluacion) {
            $fila = $stats->get($evaluacion->id);
            $evaluacion->intentos_total = (int) ($fila->intentos ?? 0);
            $evaluacion->participantes = (int) ($fila->participantes ?? 0);
            $evaluacion->aprobados_total = (int) ($fila->aprobados ?? 0);
            $evaluacion->promedio = $fila ? round((float) $fila->promedio, 1) : null;
        }

        return view('jefe.resultados.index', compact('usuario', 'evaluaciones', 'empleadosArea'));
    }

    public function show(Request $request, Evaluacion $evaluacion)
    {
        $usuario = $this->jefe();
        $evaluacion->load('capacitacion.areas');

        abort_unless(
            $evaluacion->capacitacion && (int) $evaluacion->capacitacion->creado_por === (int) $usuario->id,
            403,
            'Esta evaluación pertenece a una capacitación que no publicaste.'
        );

        $areaIds = $evaluacion->capacitacion->areas->pluck('id')->all();

        $empleados = Usuario::query()
            ->where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->whereIn('area_id', $areaIds)
            ->orderBy('nombre_completo')
            ->get();

        $intentos = IntentoEvaluacion::where('evaluacion_id', $evaluacion->id)
            ->orderBy('numero_intento')
            ->get()
            ->groupBy('usuario_id');

        $filas = $empleados->map(function (Usuario $empleado) use ($intentos, $evaluacion) {
            $propios = $intentos->get($empleado->id, collect());
            $ultimo = $propios->last();
            $aprobado = $propios->contains(fn ($i) => $i->estado === 'APROBADO');

            return (object) [
                'usuario' => $empleado,
                'intentos' => $propios->count(),
                'restantes' => max(0, $evaluacion->intentos_permitidos - $propios->count()),
                'mejor' => $propios->isNotEmpty() ? (float) $propios->max('porcentaje') : null,
                'ultimo' => $ultimo ? (float) $ultimo->porcentaje : null,
                'fecha' => $ultimo?->fecha_finalizacion ?? $ultimo?->fecha_inicio,
                'estado' => $propios->isEmpty() ? 'SIN_PRESENTAR' : ($aprobado ? 'APROBADO' : 'NO_APROBADO'),
            ];
        });

        $resumen = [
            'empleados' => $filas->count(),
            'presentaron' => $filas->where('estado', '!=', 'SIN_PRESENTAR')->count(),
            'aprobaron' => $filas->where('estado', 'APROBADO')->count(),
            'no_aprobaron' => $filas->where('estado', 'NO_APROBADO')->count(),
            'promedio' => $filas->whereNotNull('mejor')->isNotEmpty()
                ? round($filas->whereNotNull('mejor')->avg('mejor'), 1)
                : null,
        ];

        $filtro = $request->query('estado');
        if (in_array($filtro, ['APROBADO', 'NO_APROBADO', 'SIN_PRESENTAR'], true)) {
            $filas = $filas->where('estado', $filtro)->values();
        } else {
            $filtro = null;
        }

        return view('jefe.resultados.show', compact('usuario', 'evaluacion', 'filas', 'resumen', 'filtro'));
    }

    private function jefe(): Usuario
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return $usuario;
    }
}
