<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Sede;
use App\Support\Seguimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SedeController extends Controller
{
    public function index()
    {
        $sedes = Sede::query()
            ->withCount([
                'areas as areas_activas_count' => fn ($q) => $q->where('estado', true),
                'areas',
                'usuarios as empleados_count' => fn ($q) => $q->where('rol', 'EMPLEADO')->where('estado', true),
                'usuarios as jefes_count' => fn ($q) => $q->where('rol', 'JEFE_AREA')->where('estado', true),
            ])
            ->with([
                'areas' => fn ($q) => $q->select('id', 'sede_id', 'nombre', 'estado')
                    ->withCount(['usuarios as empleados_count' => fn ($u) => $u->where('rol', 'EMPLEADO')->where('estado', true)])
                    ->orderBy('nombre'),
            ])
            ->orderBy('nombre')
            ->get();

        // Calcular avances por sede a través de Seguimiento
        $filas = Seguimiento::progreso();

        foreach ($sedes as $sede) {
            $asignacionesSede = $filas->filter(function ($f) use ($sede) {
                $sedeUsuarioId = $f->usuario->sede_id ?? $f->usuario->area?->sede_id;
                return $sedeUsuarioId == $sede->id;
            });

            $sede->total_asignaciones = $asignacionesSede->count();
            $sede->completadas = $asignacionesSede->where('estado', 'COMPLETADA')->count();
            $sede->en_progreso = $asignacionesSede->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count();
            $sede->pendientes = $asignacionesSede->where('estado', 'PENDIENTE')->count();
            $sede->promedio_avance = $sede->total_asignaciones > 0 ? (int) round($asignacionesSede->avg('porcentaje')) : 0;
        }

        return view('admin.sedes.index', compact('sedes'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $sede = Sede::create($datos + ['estado' => true]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => "Sede \"{$sede->nombre}\" registrada correctamente.",
                'sede' => [
                    'id' => $sede->id,
                    'nombre' => $sede->nombre,
                    'ciudad' => $sede->ciudad,
                ],
            ]);
        }

        return redirect()->route('admin.sedes.index')->with('success', 'Sede registrada correctamente.');
    }

    public function update(Request $request, Sede $sede): RedirectResponse
    {
        $sede->update($this->validar($request, $sede));

        return redirect()->route('admin.sedes.index')->with('success', 'Sede actualizada correctamente.');
    }

    public function toggleEstado(Sede $sede): RedirectResponse
    {
        $sede->update(['estado' => ! $sede->estado]);

        return back()->with('success', $sede->estado ? 'Sede activada con éxito.' : 'Sede desactivada (se conserva su historial).');
    }

    private function validar(Request $request, ?Sede $sede = null): array
    {
        if ($request->filled('nombre')) {
            $request->merge([
                'nombre' => Sede::formatearNombre($request->input('nombre')),
            ]);
        }

        return $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('sedes', 'nombre')->ignore($sede?->id)],
            'ciudad' => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ], [
            'nombre.required' => 'El nombre de la sede es obligatorio.',
            'nombre.unique' => 'Ya existe una sede con ese nombre.',
            'ciudad.required' => 'La ciudad o municipio de la sede es obligatoria.',
        ]);
    }
}
