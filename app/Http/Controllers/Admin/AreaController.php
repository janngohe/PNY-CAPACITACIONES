<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::query()
            ->withCount([
                'usuarios as empleados_count' => fn ($q) => $q->where('rol', 'EMPLEADO')->where('estado', true),
                'usuarios as jefes_count' => fn ($q) => $q->where('rol', 'JEFE_AREA')->where('estado', true),
                'capacitaciones',
            ])
            ->orderBy('nombre')
            ->get();

        return view('admin.areas.index', compact('areas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        Area::create($datos + ['estado' => true]);

        return redirect()->route('admin.areas.index')->with('success', 'Área creada correctamente.');
    }

    public function update(Request $request, Area $area): RedirectResponse
    {
        $area->update($this->validar($request, $area));

        return redirect()->route('admin.areas.index')->with('success', 'Área actualizada correctamente.');
    }

    public function toggleEstado(Area $area): RedirectResponse
    {
        $area->update(['estado' => ! $area->estado]);

        return back()->with('success', $area->estado ? 'Área activada.' : 'Área desactivada (se conserva su historial).');
    }

    private function validar(Request $request, ?Area $area = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('areas', 'nombre')->ignore($area?->id)],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => 'El nombre del área es obligatorio.',
            'nombre.unique' => 'Ya existe un área con ese nombre.',
        ]);
    }
}
