<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Jefe\CapacitacionController as JefeCapacitacionController;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\ProgresoModulo;
use App\Models\Sede;
use App\Models\Usuario;
use App\Models\UsuarioCapacitacion;
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
    public function index(?Request $request = null)
    {
        $request = $request ?? request();
        $q = trim((string) $request->query('q', ''));
        $areaId = $request->query('area_id');
        $estado = $request->query('estado', '1');
        $creadoPor = $request->query('creado_por');

        $query = Capacitacion::query()
            ->with(['areas:id,nombre', 'creador:id,nombre_completo,rol'])
            ->withCount([
                'modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true),
                'evaluaciones',
            ]);

        // Buscador predictivo / de texto
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        // Filtro por Área
        if (!empty($areaId)) {
            if ($areaId === 'sin_area') {
                $query->whereDoesntHave('areas');
            } else {
                $query->whereHas('areas', fn ($a) => $a->where('areas.id', $areaId));
            }
        }

        // Filtro por Estado (1 = Activas/Publicadas, 'vencidas' = Vencidas, 'proximas' = Próximas a vencer, 0 = Inactivas/Borradores, 'todos' = Todos)
        if ($estado === 'vencidas' || $estado === 'vencida') {
            $query->where('estado', true)
                ->whereNotNull('fecha_limite')
                ->where('fecha_limite', '<', now());
        } elseif ($estado === 'proximas' || $estado === 'proxima') {
            $query->where('estado', true)
                ->whereNotNull('fecha_limite')
                ->where('fecha_limite', '>=', now())
                ->where('fecha_limite', '<=', now()->addDays(30));
        } elseif ($estado === '1') {
            $query->where('estado', true);
        } elseif ($estado === '0') {
            $query->where('estado', false);
        }

        // Filtro por Creado por
        if (!empty($creadoPor)) {
            $query->where('creado_por', $creadoPor);
        }

        $capacitaciones = $query->orderByDesc('id')->get();
        $capIds = $capacitaciones->pluck('id');

        // Participantes únicos por capacitación
        $participacion = ProgresoModulo::query()
            ->join('modulos', 'modulos.id', '=', 'progreso_modulos.modulo_id')
            ->whereIn('modulos.capacitacion_id', $capIds)
            ->where('modulos.estado', true)
            ->selectRaw('modulos.capacitacion_id as capacitacion_id, count(distinct progreso_modulos.usuario_id) as participantes')
            ->groupBy('modulos.capacitacion_id')
            ->pluck('participantes', 'capacitacion_id');

        // Módulos completados por capacitación
        $modulosCompletados = ProgresoModulo::query()
            ->join('modulos', 'modulos.id', '=', 'progreso_modulos.modulo_id')
            ->whereIn('modulos.capacitacion_id', $capIds)
            ->where('modulos.estado', true)
            ->where('progreso_modulos.completado', 1)
            ->selectRaw('modulos.capacitacion_id as capacitacion_id, count(*) as total_completados')
            ->groupBy('modulos.capacitacion_id')
            ->pluck('total_completados', 'capacitacion_id');

        // Empleados que culminaron la capacitación completa
        $usuariosCompletaron = UsuarioCapacitacion::query()
            ->whereIn('capacitacion_id', $capIds)
            ->whereIn('estado', ['COMPLETADA', 'MODULOS_COMPLETOS'])
            ->selectRaw('capacitacion_id, count(distinct usuario_id) as total')
            ->groupBy('capacitacion_id')
            ->pluck('total', 'capacitacion_id');

        // Empleados activos por área sin N+1
        $empleadosPorArea = Usuario::where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->whereNotNull('area_id')
            ->selectRaw('area_id, count(*) as total')
            ->groupBy('area_id')
            ->pluck('total', 'area_id');

        foreach ($capacitaciones as $cap) {
            $cap->participantes_count = (int) ($participacion[$cap->id] ?? 0);
            $cap->usuarios_completaron_count = (int) ($usuariosCompletaron[$cap->id] ?? 0);
            $cap->empleados_objetivo = $cap->areas->sum(fn ($a) => (int) ($empleadosPorArea[$a->id] ?? 0));

            $modulosActivos = $cap->modulos_activos_count;
            $completadosTotal = (int) ($modulosCompletados[$cap->id] ?? 0);

            // Porcentaje completado del programa
            $posibles = $cap->empleados_objetivo * $modulosActivos;
            if ($posibles > 0) {
                $porcentaje = (int) round(($completadosTotal / $posibles) * 100);
            } elseif ($cap->participantes_count > 0 && $modulosActivos > 0) {
                $porcentaje = (int) round(($completadosTotal / ($cap->participantes_count * $modulosActivos)) * 100);
            } else {
                $porcentaje = 0;
            }
            $cap->porcentaje_completado = min(100, max(0, $porcentaje));
        }

        $totalPublicadas = Capacitacion::where('estado', true)->count();
        $totalBorradores = Capacitacion::where('estado', false)->count();
        $totalVencidas = Capacitacion::where('estado', true)
            ->whereNotNull('fecha_limite')
            ->where('fecha_limite', '<', now())
            ->count();
        $totalProximas = Capacitacion::where('estado', true)
            ->whereNotNull('fecha_limite')
            ->where('fecha_limite', '>=', now())
            ->where('fecha_limite', '<=', now()->addDays(30))
            ->count();

        $estadisticas = [
            'total' => $totalPublicadas + $totalBorradores,
            'activas' => $totalPublicadas,
            'vencidas' => $totalVencidas,
            'proximas' => $totalProximas,
            'borradores' => $totalBorradores,
            'inactivas' => $totalBorradores,
            'sin_area' => Capacitacion::where('estado', true)->doesntHave('areas')->count(),
        ];

        $areas = Area::orderBy('nombre')->get(['id', 'nombre']);

        $creadorIds = Capacitacion::select('creado_por')->whereNotNull('creado_por')->distinct()->pluck('creado_por');
        $creadores = Usuario::whereIn('id', $creadorIds)
            ->orderBy('nombre_completo')
            ->get(['id', 'nombre_completo', 'rol']);

        $sugerencias = Capacitacion::with('areas:id,nombre')
            ->select('id', 'titulo', 'estado', 'creado_por', 'fecha_limite')
            ->orderBy('titulo')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'estado' => $c->estaVencida() ? 'Vencida' : ($c->proximaAVencer() ? 'Por vencer' : ($c->estado ? 'Activa' : 'Borrador')),
                'areas' => $c->areas->pluck('nombre')->join(', '),
            ]);

        $filtros = [
            'q' => $q,
            'area_id' => $areaId,
            'estado' => $estado,
            'creado_por' => $creadoPor,
        ];

        return view('admin.capacitaciones.index', compact(
            'capacitaciones',
            'estadisticas',
            'areas',
            'creadores',
            'sugerencias',
            'filtros'
        ));
    }

    public function borradores(?Request $request = null)
    {
        $request = $request ?? request();
        $q = trim((string) $request->query('q', ''));
        $areaId = $request->query('area_id');
        $creadoPor = $request->query('creado_por');

        $query = Capacitacion::query()
            ->where('estado', false)
            ->with(['areas:id,nombre', 'creador:id,nombre_completo,rol'])
            ->withCount([
                'modulos as modulos_activos_count' => fn ($q) => $q->where('estado', true),
                'evaluaciones',
            ]);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('titulo', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if (!empty($areaId)) {
            if ($areaId === 'sin_area') {
                $query->whereDoesntHave('areas');
            } else {
                $query->whereHas('areas', fn ($a) => $a->where('areas.id', $areaId));
            }
        }

        if (!empty($creadoPor)) {
            $query->where('creado_por', $creadoPor);
        }

        $capacitaciones = $query->orderByDesc('id')->get();

        $empleadosPorArea = Usuario::where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->whereNotNull('area_id')
            ->selectRaw('area_id, count(*) as total')
            ->groupBy('area_id')
            ->pluck('total', 'area_id');

        foreach ($capacitaciones as $cap) {
            $cap->participantes_count = 0;
            $cap->usuarios_completaron_count = 0;
            $cap->porcentaje_completado = 0;
            $cap->empleados_objetivo = $cap->areas->sum(fn ($a) => (int) ($empleadosPorArea[$a->id] ?? 0));
        }

        $totalPublicadas = Capacitacion::where('estado', true)->count();
        $totalBorradores = Capacitacion::where('estado', false)->count();
        $totalVencidas = Capacitacion::where('estado', true)
            ->whereNotNull('fecha_limite')
            ->where('fecha_limite', '<', now())
            ->count();
        $totalProximas = Capacitacion::where('estado', true)
            ->whereNotNull('fecha_limite')
            ->where('fecha_limite', '>=', now())
            ->where('fecha_limite', '<=', now()->addDays(30))
            ->count();

        $estadisticas = [
            'total' => $totalPublicadas + $totalBorradores,
            'activas' => $totalPublicadas,
            'vencidas' => $totalVencidas,
            'proximas' => $totalProximas,
            'borradores' => $totalBorradores,
            'inactivas' => $totalBorradores,
            'sin_area' => Capacitacion::where('estado', true)->doesntHave('areas')->count(),
        ];

        $areas = Area::orderBy('nombre')->get(['id', 'nombre']);

        $creadorIds = Capacitacion::select('creado_por')->whereNotNull('creado_por')->distinct()->pluck('creado_por');
        $creadores = Usuario::whereIn('id', $creadorIds)
            ->orderBy('nombre_completo')
            ->get(['id', 'nombre_completo', 'rol']);

        $sugerencias = Capacitacion::where('estado', false)
            ->with('areas:id,nombre')
            ->select('id', 'titulo', 'estado', 'creado_por')
            ->orderBy('titulo')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'estado' => 'Borrador',
                'areas' => $c->areas->pluck('nombre')->join(', '),
            ]);

        $filtros = [
            'q' => $q,
            'area_id' => $areaId,
            'estado' => '0',
            'creado_por' => $creadoPor,
        ];

        return view('admin.capacitaciones.borradores', compact(
            'capacitaciones',
            'estadisticas',
            'areas',
            'creadores',
            'sugerencias',
            'filtros'
        ));
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
            'areas' => Area::with('sede:id,nombre,ciudad')
                ->where('estado', true)
                ->when($capacitacion, fn ($q) => $q->orWhereIn('id', $capacitacion->areas()->pluck('areas.id')))
                ->orderBy('nombre')
                ->get(),
            'sedes' => Sede::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'ciudad']),
            'areasSeleccionadas' => $capacitacion ? $capacitacion->areas()->pluck('areas.id')->all() : [],
        ];
    }

    private function validarAreas(Request $request): array
    {
        $areas = $request->input('areas', []);
        if (!is_array($areas)) {
            $areas = [];
        }

        // Si se envió la creación de una nueva área junto con la capacitación
        if ($request->filled('nueva_area_nombre')) {
            $nuevaSedeId = $request->input('nueva_area_sede_id');

            // Si además se indicó crear una nueva sede al vuelo
            if ($request->filled('nueva_sede_nombre')) {
                $sedeCreada = Sede::firstOrCreate(
                    ['nombre' => trim($request->input('nueva_sede_nombre'))],
                    [
                        'ciudad' => trim($request->input('nueva_sede_ciudad', 'Rivera')),
                        'estado' => true,
                    ]
                );
                $nuevaSedeId = $sedeCreada->id;
            }

            if (!$nuevaSedeId) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'nueva_area_sede_id' => 'Debes seleccionar la sede a la que pertenece la nueva área.',
                ]);
            }

            $nombreArea = Area::formatearNombre($request->input('nueva_area_nombre'));
            $nuevaArea = Area::firstOrCreate(
                ['nombre' => $nombreArea],
                [
                    'sede_id' => $nuevaSedeId,
                    'descripcion' => $request->input('nueva_area_descripcion'),
                    'estado' => true,
                ]
            );

            if (!in_array($nuevaArea->id, $areas)) {
                $areas[] = $nuevaArea->id;
            }
        }

        if (empty($areas)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'areas' => 'Selecciona al menos un área o registra una nueva área con su sede.',
            ]);
        }

        return array_values(array_unique(array_map('intval', $areas)));
    }
}
