<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Jefe\CapacitacionController as JefeCapacitacionController;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Administración global de capacitaciones.
 * Reutiliza el estudio de creación (módulos, contenidos y archivos) del Jefe de Área,
 * pero el administrador define a qué áreas se asigna cada capacitación.
 */
class CapacitacionController extends JefeCapacitacionController
{
    public function index()
    {
        $capacitaciones = Capacitacion::query()
            ->where('estado', true)
            ->with(['areas:id,nombre', 'creador:id,nombre_completo,rol'])
            ->withCount([
                'modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true),
                'evaluaciones',
            ])
            ->orderByDesc('id')
            ->get();

        $participacion = ProgresoModulo::query()
            ->join('modulos', 'modulos.id', '=', 'progreso_modulos.modulo_id')
            ->where('modulos.estado', true)
            ->selectRaw('modulos.capacitacion_id as capacitacion_id, count(distinct progreso_modulos.usuario_id) as participantes')
            ->groupBy('modulos.capacitacion_id')
            ->pluck('participantes', 'capacitacion_id');

        foreach ($capacitaciones as $cap) {
            $cap->participantes_count = (int) ($participacion[$cap->id] ?? 0);
            $cap->empleados_objetivo = Usuario::where('rol', 'EMPLEADO')->where('estado', true)
                ->whereIn('area_id', $cap->areas->pluck('id'))->count();
        }

        $totalBorradores = Capacitacion::where('estado', false)->count();

        $estadisticas = [
            'total' => $capacitaciones->count() + $totalBorradores,
            'activas' => $capacitaciones->count(),
            'borradores' => $totalBorradores,
            'inactivas' => $totalBorradores,
            'sin_area' => $capacitaciones->filter(fn ($c) => $c->areas->isEmpty())->count(),
        ];

        return view('admin.capacitaciones.index', compact('capacitaciones', 'estadisticas'));
    }

    public function borradores()
    {
        $capacitaciones = Capacitacion::query()
            ->where('estado', false)
            ->with(['areas:id,nombre', 'creador:id,nombre_completo,rol'])
            ->withCount([
                'modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true),
                'evaluaciones',
            ])
            ->orderByDesc('id')
            ->get();

        foreach ($capacitaciones as $cap) {
            $cap->participantes_count = 0;
            $cap->empleados_objetivo = Usuario::where('rol', 'EMPLEADO')->where('estado', true)
                ->whereIn('area_id', $cap->areas->pluck('id'))->count();
        }

        $totalPublicadas = Capacitacion::where('estado', true)->count();

        $estadisticas = [
            'total' => $totalPublicadas + $capacitaciones->count(),
            'activas' => $totalPublicadas,
            'borradores' => $capacitaciones->count(),
            'inactivas' => $capacitaciones->count(),
            'sin_area' => $capacitaciones->filter(fn ($c) => $c->areas->isEmpty())->count(),
        ];

        return view('admin.capacitaciones.borradores', compact('capacitaciones', 'estadisticas'));
    }

    public function create()
    {
        return view('jefe.capacitaciones.form', $this->datosFormulario(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $areas = $this->validarAreas($request);
        $datos = $this->validar($request);
        $esBorrador = $request->input('accion') === 'borrador' || ! $request->boolean('estado_publicacion', true);

        DB::transaction(function () use ($request, $datos, $areas, $esBorrador) {
            $capacitacion = Capacitacion::create([
                ...$this->atributosCapacitacion($datos, null),
                'ruta_imagen' => $this->guardarImagen($request, null),
                'creado_por' => $request->user()->id,
                'estado' => ! $esBorrador,
            ]);

            $capacitacion->areas()->sync($areas);
            $this->sincronizarModulos($capacitacion, $datos['modulos'], $request);
        });

        if ($esBorrador) {
            return redirect()->route('admin.capacitaciones.borradores')
                ->with('success', 'Capacitación guardada en borradores correctamente.');
        }

        return redirect()->route('admin.capacitaciones.index')
            ->with('success', 'Capacitación creada y asignada a las áreas seleccionadas.');
    }

    public function edit(Capacitacion $capacitacion)
    {
        return view('jefe.capacitaciones.form', $this->datosFormulario($capacitacion));
    }

    public function update(Request $request, Capacitacion $capacitacion): RedirectResponse
    {
        $areas = $this->validarAreas($request);
        $datos = $this->validar($request);
        $accion = $request->input('accion');
        $esBorrador = $accion === 'borrador';
        $esPublicar = $accion === 'publicar';

        DB::transaction(function () use ($request, $datos, $capacitacion, $areas, $esBorrador, $esPublicar) {
            $atributos = [
                ...$this->atributosCapacitacion($datos, $capacitacion),
                'ruta_imagen' => $this->guardarImagen($request, $capacitacion->ruta_imagen),
            ];

            if ($esBorrador) {
                $atributos['estado'] = false;
            } elseif ($esPublicar) {
                $atributos['estado'] = true;
            }

            $capacitacion->update($atributos);

            $capacitacion->areas()->sync($areas);
            $this->sincronizarModulos($capacitacion, $datos['modulos'], $request);
        });

        if ($esBorrador || ! $capacitacion->fresh()->estado) {
            return redirect()->route('admin.capacitaciones.borradores')
                ->with('success', 'Borrador actualizado correctamente.');
        }

        return redirect()->route('admin.capacitaciones.index')->with('success', 'Capacitación actualizada correctamente.');
    }

    public function publicar(Capacitacion $capacitacion): RedirectResponse
    {
        $capacitacion->update(['estado' => true]);

        return redirect()->route('admin.capacitaciones.index')
            ->with('success', '¡La capacitación "' . $capacitacion->titulo . '" ha sido publicada exitosamente!');
    }

    public function destroy(Capacitacion $capacitacion): RedirectResponse
    {
        if ($capacitacion->estado) {
            return back()->with('error', 'No puedes eliminar una capacitación publicada. Puedes desactivarla.');
        }

        $titulo = $capacitacion->titulo;
        $capacitacion->delete();

        return redirect()->route('admin.capacitaciones.borradores')
            ->with('success', 'El borrador "' . $titulo . '" ha sido descartado.');
    }

    public function toggleEstado(Capacitacion $capacitacion): RedirectResponse
    {
        $capacitacion->update(['estado' => ! $capacitacion->estado]);

        return back()->with('success', $capacitacion->estado
            ? 'Capacitación activada: vuelve a estar visible para las áreas asignadas.'
            : 'Capacitación desactivada: ya no se muestra a los empleados (se conserva el historial).');
    }

    private function datosFormulario(?Capacitacion $capacitacion): array
    {
        return [
            'usuario' => $this->jefe(),
            'panel' => 'admin',
            'capacitacion' => $capacitacion,
            'modulosForm' => $this->modulosParaFormulario($capacitacion),
            'tiposContenido' => self::TIPOS_CONTENIDO,
            'areas' => Area::where('estado', true)
                ->when($capacitacion, fn ($q) => $q->orWhereIn('id', $capacitacion->areas()->pluck('areas.id')))
                ->orderBy('nombre')
                ->get(),
            'areasSeleccionadas' => $capacitacion ? $capacitacion->areas()->pluck('areas.id')->all() : [],
        ];
    }

    private function validarAreas(Request $request): array
    {
        return $request->validate([
            'areas' => ['required', 'array', 'min:1'],
            'areas.*' => ['integer', 'exists:areas,id'],
        ], [
            'areas.required' => 'Selecciona al menos un área a la que se asignará la capacitación.',
            'areas.min' => 'Selecciona al menos un área a la que se asignará la capacitación.',
        ])['areas'];
    }
}
