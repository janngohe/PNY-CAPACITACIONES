<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use Illuminate\Http\Request;

class QrController extends Controller
{
    /**
     * Muestra la pantalla de generación, proyección y enlace QR para auto-registro de evaluados.
     */
    public function index(Request $request)
    {
        // Detectar IP local del servidor para permitir pruebas en la misma red Wi-Fi
        $localIp = gethostbyname(gethostname());
        if ($localIp === '127.0.0.1' || !filter_var($localIp, FILTER_VALIDATE_IP)) {
            $localIp = request()->server('SERVER_ADDR', '127.0.0.1');
        }

        $puerto = request()->server('SERVER_PORT', '80');
        $esPuertoEstandar = in_array((string)$puerto, ['80', '443'], true);

        $urlBaseActual = url('/evaluacion');
        $urlRedLocal = 'http://' . $localIp . ($esPuertoEstandar ? '' : (':' . $puerto)) . '/evaluacion';

        // Detección automática de túnel Ngrok si está activo
        $urlNgrokBase = null;
        try {
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT_MS => 400,
                CURLOPT_CONNECTTIMEOUT_MS => 250,
            ]);
            $ngrokRes = curl_exec($ch);
            curl_close($ch);
            if ($ngrokRes) {
                $ngrokData = json_decode($ngrokRes, true);
                $tunnels = $ngrokData['tunnels'] ?? [];
                foreach ($tunnels as $tun) {
                    if (!empty($tun['public_url']) && str_starts_with($tun['public_url'], 'https://')) {
                        $urlNgrokBase = rtrim($tun['public_url'], '/');
                        break;
                    }
                }
                if (!$urlNgrokBase && !empty($tunnels[0]['public_url'])) {
                    $urlNgrokBase = rtrim($tunnels[0]['public_url'], '/');
                }
            }
        } catch (\Throwable $e) {
            // Silencioso si no hay túnel
        }

        $urlNgrok = $urlNgrokBase ? ($urlNgrokBase . '/evaluacion') : null;

        // Estadísticas rápidas de evaluados vía QR
        $totalEvaluadosQr = Candidato::where('origen_registro', 'QR_AUTOREGISTRO')->count();
        $ultimosEvaluados = Candidato::where('origen_registro', 'QR_AUTOREGISTRO')
            ->with(['consentimientos', 'aplicaciones.resultadoPaca'])
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('psicologia.qr.index', compact(
            'urlBaseActual',
            'urlRedLocal',
            'localIp',
            'urlNgrokBase',
            'urlNgrok',
            'totalEvaluadosQr',
            'ultimosEvaluados'
        ));
    }

    /**
     * Vista especial lista para imprimir en hoja tamaño carta con diseño institucional oficial.
     */
    public function imprimirCartel(Request $request)
    {
        $urlEvaluacion = $request->input('url') ?: url('/evaluacion');

        return view('psicologia.qr.imprimir', compact('urlEvaluacion'));
    }
}
