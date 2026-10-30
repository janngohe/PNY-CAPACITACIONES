<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\AplicacionPrueba;
use App\Models\Candidato;
use App\Models\ConsentimientoInformado;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Muestra el resumen del panel de Psicología y Gestión Humana.
     */
    public function index(Request $request)
    {
        $totalEvaluadosQr = Candidato::where('origen_registro', 'QR_AUTOREGISTRO')->count();
        $totalConsentimientos = ConsentimientoInformado::count();
        $consentimientosFirmados = ConsentimientoInformado::whereIn('estado', ['FIRMADO_FISICO', 'FIRMADO_DIGITAL'])->count();
        $consentimientosPendientes = ConsentimientoInformado::whereIn('estado', ['GENERADO', 'DESCARGADO'])->count();

        $aplicacionesPaca = AplicacionPrueba::where('prueba_codigo', 'PAC-A')->count();
        $pacaFinalizadas = AplicacionPrueba::where('prueba_codigo', 'PAC-A')->where('estado', 'FINALIZADA')->count();
        $pacaBorradores = AplicacionPrueba::where('prueba_codigo', 'PAC-A')->whereIn('estado', ['BORRADOR', 'EN_CURSO'])->count();

        // Evaluaciones recientes
        $aplicacionesRecientes = AplicacionPrueba::with(['candidato', 'resultadoPaca', 'responsable'])
            ->latest()
            ->take(6)
            ->get();

        // Evaluados recientes vía QR
        $evaluadosRecientes = Candidato::where('origen_registro', 'QR_AUTOREGISTRO')
            ->with(['consentimientos', 'aplicaciones.resultadoPaca'])
            ->latest()
            ->take(5)
            ->get();

        return view('psicologia.dashboard', compact(
            'totalEvaluadosQr',
            'totalConsentimientos',
            'consentimientosFirmados',
            'consentimientosPendientes',
            'aplicacionesPaca',
            'pacaFinalizadas',
            'pacaBorradores',
            'aplicacionesRecientes',
            'evaluadosRecientes'
        ));
    }
}
