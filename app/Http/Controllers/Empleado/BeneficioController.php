<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Beneficio;
use App\Models\UsuarioCapacitacion;
use App\Services\PuntosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficioController extends Controller
{
    protected PuntosService $puntosService;

    public function __construct(PuntosService $puntosService)
    {
        $this->puntosService = $puntosService;
    }

    /**
     * Muestra el catálogo de beneficios, saldo de puntos y movimientos para el empleado.
     */
    public function index()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Beneficios activos
        $beneficios = Beneficio::where('estado', true)
            ->orderBy('puntos_requeridos', 'asc')
            ->get();

        // Movimientos de puntos del empleado
        $movimientos = $usuario->movimientosPuntos()
            ->with(['capacitacion', 'beneficio'])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'mov_page');

        // Canjes realizados por el empleado
        $canjes = $usuario->canjesBeneficios()
            ->with('beneficio')
            ->orderBy('created_at', 'desc')
            ->get();

        // Capacitaciones completadas por el empleado
        $capacitacionesCompletadas = UsuarioCapacitacion::where('usuario_id', $usuario->id)
            ->whereIn('estado', ['COMPLETADA', 'MODULOS_COMPLETOS'])
            ->with('capacitacion')
            ->orderByDesc('fecha_finalizacion')
            ->get();

        // Puntos totales históricos acumulados (ganados)
        $puntosGanadosHistoricos = $usuario->movimientosPuntos()
            ->where('tipo', 'ACREDITACION')
            ->sum('puntos');

        // Puntos canjeados históricos
        $puntosCanjeadosHistoricos = $usuario->movimientosPuntos()
            ->where('tipo', 'CANJE')
            ->sum('puntos');

        return view('empleado.beneficios', compact(
            'usuario',
            'beneficios',
            'movimientos',
            'canjes',
            'capacitacionesCompletadas',
            'puntosGanadosHistoricos',
            'puntosCanjeadosHistoricos'
        ));
    }

    /**
     * Procesa la solicitud de canje de un beneficio.
     */
    public function canjear(Request $request, Beneficio $beneficio)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        try {
            $canje = $this->puntosService->canjearBeneficio($usuario, $beneficio);

            return redirect()->route('empleado.beneficios')
                ->with('success', "¡Felicidades! Has canjeado con éxito '{$beneficio->nombre}'. Tu código de canje es: {$canje->codigo_canje}.");
        } catch (\Exception $e) {
            return redirect()->route('empleado.beneficios')
                ->with('error', $e->getMessage());
        }
    }
}
