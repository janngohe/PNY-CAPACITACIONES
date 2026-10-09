<?php

namespace App\Http\Controllers\Admin;

use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Asignación de capacitaciones a las áreas: todos los empleados activos de un área
 * asignada ven y pueden realizar la capacitación.
 */
class AsignacionController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $areas = Area::where('estado', true)->orderBy('nombre')->get(['id', 'nombre']);

        $empleadosPorArea = Usuario::where('rol', 'EMPLEADO')->where('estado', true)
            ->selectRaw('area_id, count(*) as total')->groupBy('area_id')->pluck('total', 'area_id');

        $capacitaciones = Capacitacion::with('areas:id')
            ->withCount(['modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true)])
            ->orderByDesc('estado')
            ->orderBy('titulo')
            ->get();

        return view('admin.asignaciones.index', compact('areas', 'capacitaciones', 'empleadosPorArea'));
    }

    public function update(Request $request, Capacitacion $capacitacion): RedirectResponse
    {
        $datos = $request->validate([
            'areas' => ['nullable', 'array'],
            'areas.*' => ['integer', 'exists:areas,id'],
        ]);

        $capacitacion->areas()->sync($datos['areas'] ?? []);

        return redirect()->route('admin.asignaciones.index')
            ->with('success', "Áreas de «{$capacitacion->titulo}» actualizadas correctamente.");
    }
}
