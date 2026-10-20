<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Sede;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::query()
            ->with('sede:id,nombre,ciudad')
            ->withCount([
                'usuarios as empleados_count' => fn ($q) => $q->where('rol', 'EMPLEADO')->where('estado', true),
                'usuarios as jefes_count' => fn ($q) => $q->where('rol', 'JEFE_AREA')->where('estado', true),
                'capacitaciones',
            ])
            ->with([
                'usuarios' => fn ($q) => $q->where('estado', true)->select('id', 'identificacion', 'nombre_completo', 'rol', 'area_id')->orderBy('nombre_completo'),
                'capacitaciones' => fn ($q) => $q->select('capacitaciones.id', 'titulo', 'duracion_estimada', 'porcentaje_aprobacion', 'capacitaciones.estado')->orderBy('titulo'),
            ])
            ->orderBy('nombre')
            ->get();

        $sedes = Sede::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'ciudad']);

        return view('admin.areas.index', compact('areas', 'sedes'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $area = Area::create($datos + ['estado' => true]);

        if ($request->expectsJson() || $request->ajax()) {
            $area->load('sede:id,nombre,ciudad');
            return response()->json([
                'success' => true,
                'mensaje' => "Área \"{$area->nombre}\" creada y vinculada a la sede {$area->sede?->nombre} correctamente.",
                'area' => [
                    'id' => $area->id,
                    'nombre' => $area->nombre,
                    'sede_id' => $area->sede_id,
                    'sede_nombre' => $area->sede?->nombre ?? 'Sin sede',
                    'sede_ciudad' => $area->sede?->ciudad ?? '',
                ],
            ]);
        }

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
        if ($request->filled('nombre')) {
            $request->merge([
                'nombre' => Area::formatearNombre($request->input('nombre')),
            ]);
        }

        return $request->validate([
            'sede_id' => ['required', 'integer', 'exists:sedes,id'],
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('areas', 'nombre')
                    ->where('sede_id', $request->input('sede_id'))
                    ->ignore($area?->id)
            ],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'sede_id.required' => 'Debes seleccionar la sede a la que pertenece el área.',
            'sede_id.exists' => 'La sede seleccionada no es válida.',
            'nombre.required' => 'El nombre del área es obligatorio.',
            'nombre.unique' => 'Ya existe un área con ese nombre en la sede seleccionada.',
        ]);
    }
}
