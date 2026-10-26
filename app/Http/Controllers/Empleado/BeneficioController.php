<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Beneficio;
use App\Models\CanjeBeneficio;
use App\Models\Usuario;
use App\Models\UsuarioCapacitacion;
use App\Services\PuntosService;
use Illuminate\Http\JsonResponse;
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

        // URL pública resuelta (incluyendo Ngrok si está activo en 4040)
        $urlNgrokBase = CanjeBeneficio::resolverUrlBasePublica();

        return view('empleado.beneficios', compact(
            'usuario',
            'beneficios',
            'movimientos',
            'canjes',
            'capacitacionesCompletadas',
            'puntosGanadosHistoricos',
            'puntosCanjeadosHistoricos',
            'urlNgrokBase'
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
                ->with('canje_exitoso', [
                    'id' => $canje->id,
                    'codigo' => $canje->codigo_canje,
                    'beneficio' => $beneficio->nombre,
                    'qr_data' => $canje->qr_contenido,
                    'puntos' => $canje->puntos_utilizados,
                    'nombre' => $usuario->nombre_completo,
                    'area' => $usuario->area?->nombre ?? ($usuario->sedeActual()?->nombre ?? 'Sin área asignada'),
                    'fecha' => $canje->fecha_canje ? $canje->fecha_canje->format('d/m/Y H:i') : now()->format('d/m/Y H:i'),
                ])
                ->with('success', "¡Felicidades! Has canjeado con éxito '{$beneficio->nombre}'. Se ha generado tu código QR único para presentarlo.");
        } catch (\Exception $e) {
            return redirect()->route('empleado.beneficios')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Vista pública de verificación del canje de un beneficio escaneado por código QR.
     */
    public function verificarCanjePublico(string $codigo)
    {
        $canje = CanjeBeneficio::where('codigo_canje', $codigo)
            ->with([
                'usuario.area.sede',
                'usuario.sede',
                'beneficio',
                'responsable.area.sede',
                'responsable.sede',
            ])
            ->first();

        return view('canjes.verificar', compact('canje', 'codigo'));
    }

    /**
     * Busca a un colaborador en tiempo real por cédula para asignarlo como responsable de entrega.
     */
    public function buscarResponsable(Request $request): JsonResponse
    {
        $identificacion = trim((string) $request->input('identificacion', ''));

        if ($identificacion === '') {
            return response()->json([
                'encontrado' => false,
                'mensaje' => 'Por favor ingresa un número de cédula.',
            ], 422);
        }

        $usuario = Usuario::with(['area.sede', 'sede'])
            ->where('identificacion', $identificacion)
            ->where('estado', true)
            ->first();

        if (!$usuario) {
            return response()->json([
                'encontrado' => false,
                'mensaje' => 'No se encontró ningún colaborador activo con el documento ingresado. Verifica el número de cédula.',
            ], 404);
        }

        $sede = $usuario->sedeActual()?->nombre ?? 'Sede Principal';
        $area = $usuario->area?->nombre ?? 'Sin área asignada';
        $cargo = $usuario->cargoOficial();

        return response()->json([
            'encontrado' => true,
            'usuario' => [
                'id' => $usuario->id,
                'identificacion' => $usuario->identificacion,
                'nombre_completo' => $usuario->nombre_completo,
                'cargo' => $cargo,
                'area' => $area,
                'sede' => $sede,
                'rol' => $usuario->rol,
                'resumen' => "{$usuario->nombre_completo} (CC {$usuario->identificacion} · {$area} - {$sede})",
            ],
        ]);
    }

    /**
     * Confirma la entrega y canje efectivo de un beneficio escaneado.
     */
    public function confirmarCanjePublico(Request $request, string $codigo)
    {
        $canje = CanjeBeneficio::where('codigo_canje', $codigo)
            ->with(['usuario', 'beneficio'])
            ->first();

        if (!$canje) {
            return redirect()->route('canje.verificar', $codigo)
                ->with('error', 'El código de canje no fue encontrado o es inválido.');
        }

        if ($canje->estado === 'CANJEADO') {
            return redirect()->route('canje.verificar', $codigo)
                ->with('info', 'Este canje ya había sido confirmado y recibido anteriormente.');
        }

        // Marcar como CANJEADO
        $canje->estado = 'CANJEADO';
        $canje->fecha_entrega = now();

        // Buscar responsable por cédula ingresada o ID
        $responsableCc = trim((string) $request->input('responsable_cc', ''));
        $usuarioResponsable = null;

        if ($responsableCc !== '') {
            $usuarioResponsable = Usuario::with(['area.sede', 'sede'])
                ->where('identificacion', $responsableCc)
                ->where('estado', true)
                ->first();
        } elseif ($request->filled('responsable_usuario_id')) {
            $usuarioResponsable = Usuario::with(['area.sede', 'sede'])
                ->where('id', (int) $request->input('responsable_usuario_id'))
                ->where('estado', true)
                ->first();
        } elseif (Auth::check()) {
            $usuarioResponsable = Auth::user();
        }

        if ($usuarioResponsable) {
            $canje->responsable_usuario_id = $usuarioResponsable->id;
            $canje->responsable_cc = $usuarioResponsable->identificacion;
            $areaNom = $usuarioResponsable->area?->nombre ?? 'Sin área asignada';
            $sedeNom = $usuarioResponsable->sedeActual()?->nombre ?? 'Sede Principal';
            $canje->entregado_por = substr("{$usuarioResponsable->nombre_completo} (CC {$usuarioResponsable->identificacion} · {$areaNom} - {$sedeNom})", 0, 255);
        } else {
            $entregadoPor = trim((string) $request->input('entregado_por', ''));
            $canje->entregado_por = $entregadoPor ?: ($responsableCc ? "CC {$responsableCc}" : 'Gestión Humana / En Sede');
            $canje->responsable_cc = $responsableCc ?: null;
        }

        if ($request->filled('notas')) {
            $notaAdicional = 'Entrega confirmada: ' . trim($request->input('notas'));
            $canje->notas = $canje->notas ? ($canje->notas . ' | ' . $notaAdicional) : $notaAdicional;
        }

        $canje->save();

        return redirect()->route('canje.verificar', $codigo)
            ->with('canje_confirmado_ahora', true)
            ->with('success', "¡Canje confirmado exitosamente! El incentivo '{$canje->beneficio?->nombre}' ha sido entregado a {$canje->usuario?->nombre_completo}.");
    }
}
