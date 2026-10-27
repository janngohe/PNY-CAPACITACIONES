<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Beneficio;
use App\Models\CanjeBeneficio;
use App\Models\MovimientoPuntos;
use App\Models\Sede;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeneficioController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $estado = $request->query('estado', 'todos');

        $query = Beneficio::withCount('canjes')
            ->with([
                'canjes.usuario.area.sede',
                'canjes.usuario.sede',
                'canjes.responsable.area.sede',
                'canjes.responsable.sede',
            ]);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if ($estado === 'activos') {
            $query->where('estado', true);
        } elseif ($estado === 'inactivos') {
            $query->where('estado', false);
        }

        $beneficios = $query->orderBy('puntos_requeridos')->get();

        $todosCanjes = CanjeBeneficio::with([
            'usuario.area.sede',
            'usuario.sede',
            'beneficio',
            'responsable.area.sede',
            'responsable.sede',
        ])
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->get();

        $sedes = Sede::orderBy('nombre')->get();
        $areas = Area::with('sede')->orderBy('nombre')->get();

        $formatearCanje = function ($c, $b = null) {
            $u = $c->usuario;
            $r = $c->responsable;
            $beneficio = $b ?: $c->beneficio;

            return [
                'id' => $c->id,
                'codigo' => $c->codigo_canje,
                'qr_data' => $c->qr_contenido,
                'url_verificacion' => $c->url_verificacion,
                'beneficio_id' => $beneficio ? $beneficio->id : null,
                'beneficio_nombre' => $beneficio ? $beneficio->nombre : 'Incentivo',
                'beneficio_icono' => $beneficio ? ($beneficio->icono ?: 'fa-gift') : 'fa-gift',
                'empleado' => $u ? $u->nombre_completo : 'Colaborador no encontrado',
                'documento' => $u ? $u->identificacion : '—',
                'cargo' => $u ? $u->cargoOficial() : '—',
                'sede' => $u && $u->sedeActual() ? $u->sedeActual()->nombre : 'Sin sede asignada',
                'area' => $u && $u->area ? $u->area->nombre : 'Sin área asignada',
                'puntos' => $c->puntos_utilizados ?? ($beneficio?->puntos_requeridos ?? 0),
                'fecha' => $c->fecha_canje ? $c->fecha_canje->format('d/m/Y H:i') : ($c->created_at ? $c->created_at->format('d/m/Y H:i') : '—'),
                'fecha_raw' => $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : null,
                'estado' => $c->estado ?? 'CANJEADO',
                'entregado_por' => $c->entregado_por,
                'fecha_entrega' => $c->fecha_entrega ? $c->fecha_entrega->format('d/m/Y H:i') : null,
                'responsable_nombre' => $r ? $r->nombre_completo : ($c->entregado_por ? trim(explode('(', $c->entregado_por)[0]) : null),
                'responsable_cc' => $c->responsable_cc ?: ($r ? $r->identificacion : null),
                'responsable_cargo' => $r ? $r->cargoOficial() : null,
                'responsable_sede' => $r && $r->sedeActual() ? $r->sedeActual()->nombre : null,
                'responsable_area' => $r && $r->area ? $r->area->nombre : null,
            ];
        };

        $canjesPorBeneficio = $beneficios->mapWithKeys(function ($b) use ($formatearCanje) {
            return [$b->id => [
                'id' => $b->id,
                'nombre' => $b->nombre,
                'puntos' => $b->puntos_requeridos,
                'icono' => $b->icono ?: 'fa-gift',
                'canjes' => $b->canjes->map(function ($c) use ($b, $formatearCanje) {
                    return $formatearCanje($c, $b);
                })->values(),
            ]];
        });

        $todosCanjesArray = $todosCanjes->map(function ($c) use ($formatearCanje) {
            return $formatearCanje($c);
        })->values();

        $totalCanjes = CanjeBeneficio::count();
        $puntosCanjeados = abs((int) MovimientoPuntos::where('tipo', 'CANJE')->sum('puntos'));
        if ($puntosCanjeados === 0 && $totalCanjes > 0) {
            $puntosCanjeados = (int) CanjeBeneficio::sum('puntos_utilizados');
        }

        $estadisticas = [
            'total' => Beneficio::count(),
            'activos' => Beneficio::where('estado', true)->count(),
            'inactivos' => Beneficio::where('estado', false)->count(),
            'total_canjes' => $totalCanjes,
            'pendientes' => CanjeBeneficio::where('estado', 'SOLICITADO')->count(),
            'canjeados' => CanjeBeneficio::where('estado', 'CANJEADO')->count(),
            'puntos_canjeados' => $puntosCanjeados,
            'colaboradores_canje' => CanjeBeneficio::distinct('usuario_id')->count('usuario_id'),
        ];

        return view('admin.beneficios.index', compact(
            'beneficios',
            'todosCanjes',
            'todosCanjesArray',
            'canjesPorBeneficio',
            'sedes',
            'areas',
            'estadisticas',
            'totalCanjes',
            'puntosCanjeados',
            'q',
            'estado'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'puntos_requeridos' => ['required', 'integer', 'min:1', 'max:100000'],
            'icono' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable'],
        ], [
            'nombre.required' => 'El nombre del beneficio es obligatorio.',
            'puntos_requeridos.required' => 'Debes especificar la cantidad de puntos necesarios.',
            'puntos_requeridos.min' => 'La cantidad de puntos debe ser al menos 1.',
        ]);

        Beneficio::create([
            'nombre' => trim($datos['nombre']),
            'descripcion' => !empty($datos['descripcion']) ? trim($datos['descripcion']) : null,
            'puntos_requeridos' => (int) $datos['puntos_requeridos'],
            'icono' => !empty($datos['icono']) ? trim($datos['icono']) : 'fa-gift',
            'estado' => $request->boolean('estado', true),
        ]);

        return redirect()->route('admin.beneficios.index')
            ->with('success', '¡Beneficio "' . $datos['nombre'] . '" registrado exitosamente en el catálogo!');
    }

    public function update(Request $request, Beneficio $beneficio): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'puntos_requeridos' => ['required', 'integer', 'min:1', 'max:100000'],
            'icono' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable'],
        ], [
            'nombre.required' => 'El nombre del beneficio es obligatorio.',
            'puntos_requeridos.required' => 'Debes especificar la cantidad de puntos necesarios.',
            'puntos_requeridos.min' => 'La cantidad de puntos debe ser al menos 1.',
        ]);

        $beneficio->update([
            'nombre' => trim($datos['nombre']),
            'descripcion' => !empty($datos['descripcion']) ? trim($datos['descripcion']) : null,
            'puntos_requeridos' => (int) $datos['puntos_requeridos'],
            'icono' => !empty($datos['icono']) ? trim($datos['icono']) : 'fa-gift',
            'estado' => $request->boolean('estado', true),
        ]);

        return redirect()->route('admin.beneficios.index')
            ->with('success', 'Beneficio "' . $beneficio->nombre . '" actualizado correctamente.');
    }

    public function toggleEstado(Beneficio $beneficio): RedirectResponse
    {
        $beneficio->update(['estado' => !$beneficio->estado]);

        return redirect()->route('admin.beneficios.index')
            ->with('success', $beneficio->estado
                ? 'El beneficio "' . $beneficio->nombre . '" ha sido activado y está disponible para canje.'
                : 'El beneficio "' . $beneficio->nombre . '" ha sido desactivado temporalmente.');
    }

    public function destroy(Beneficio $beneficio): RedirectResponse
    {
        $canjesCount = $beneficio->canjes()->count();
        if ($canjesCount > 0) {
            $beneficio->update(['estado' => false]);
            return redirect()->route('admin.beneficios.index')
                ->with('warning', 'El beneficio "' . $beneficio->nombre . '" cuenta con ' . $canjesCount . ' canjes registrados. Ha sido desactivado para preservar la integridad histórica.');
        }

        $nombre = $beneficio->nombre;
        $beneficio->delete();

        return redirect()->route('admin.beneficios.index')
            ->with('success', 'El beneficio "' . $nombre . '" ha sido eliminado del catálogo.');
    }
}
