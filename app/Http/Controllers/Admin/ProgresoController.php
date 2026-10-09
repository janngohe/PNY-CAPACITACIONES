<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Capacitacion;
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
            'capacitacion_id' => $request->query('capacitacion_id'),
            'estado' => $request->query('estado'),
        ];

        $filas = Seguimiento::progreso($filtros);

        $resumen = [
            'registros' => $filas->count(),
            'participantes' => $filas->pluck('usuario.id')->unique()->count(),
            'completadas' => $filas->where('estado', 'COMPLETADA')->count(),
            'promedio' => $filas->isNotEmpty() ? (int) round($filas->avg('porcentaje')) : 0,
        ];

        $porPagina = 15;
        $pagina = LengthAwarePaginator::resolveCurrentPage();
        $paginado = new LengthAwarePaginator(
            $filas->forPage($pagina, $porPagina)->values(),
            $filas->count(),
            $porPagina,
            $pagina,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.progreso.index', [
            'filas' => $paginado,
            'resumen' => $resumen,
            'filtros' => $filtros,
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre']),
            'capacitaciones' => Capacitacion::orderBy('titulo')->get(['id', 'titulo']),
            'estados' => Seguimiento::ESTADOS,
        ]);
    }
}
