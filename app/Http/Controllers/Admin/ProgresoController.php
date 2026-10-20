<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\Sede;
use App\Support\Seguimiento;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProgresoController extends Controller
{
    public function index(Request $request)
    {
        $filtros = [
            'q' => trim((string) $request->query('q')),
            'area_id' => $request->query('area_id'),
            'sede_id' => $request->query('sede_id'),
            'capacitacion_id' => $request->query('capacitacion_id'),
            'estado' => $request->query('estado'),
        ];

        $colaboradores = Seguimiento::progresoPorColaborador($filtros);
        $todasLasAsignaciones = $colaboradores->flatMap(fn ($c) => $c->asignaciones);

        $resumen = [
            'registros' => $todasLasAsignaciones->count(),
            'participantes' => $colaboradores->count(),
            'completadas' => $todasLasAsignaciones->where('estado', 'COMPLETADA')->count(),
            'promedio' => $colaboradores->isNotEmpty() ? (int) round($colaboradores->avg('porcentaje_global')) : 0,
        ];

        $porPagina = 15;
        $pagina = LengthAwarePaginator::resolveCurrentPage();
        $paginado = new LengthAwarePaginator(
            $colaboradores->forPage($pagina, $porPagina)->values(),
            $colaboradores->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.progreso.index', [
            'filas' => $paginado,
            'resumen' => $resumen,
            'filtros' => $filtros,
            'sedes' => Sede::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'ciudad']),
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'capacitaciones' => Capacitacion::orderBy('titulo')->get(['id', 'titulo']),
            'estados' => Seguimiento::ESTADOS,
        ]);
    }
}
