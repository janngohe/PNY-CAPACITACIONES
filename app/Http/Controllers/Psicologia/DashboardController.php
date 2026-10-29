<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Pantalla de selección de entorno:
     * - Panel de Psicología
     * - Portal de Capacitaciones (Modo Colaborador)
     */
    public function portalSeleccion()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.portal_seleccion', compact('usuario'));
    }

    /**
     * Panel Principal de Psicología (Visión general de los 4 módulos).
     */
    public function index()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.dashboard', compact('usuario'));
    }

    /**
     * Módulo 1: Realizar Prueba.
     */
    public function realizarPrueba()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.realizar_prueba', compact('usuario'));
    }

    /**
     * Módulo 2: Reportes.
     */
    public function reportes()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.reportes', compact('usuario'));
    }

    /**
     * Módulo 3: Historial de Pruebas.
     */
    public function historialPruebas()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.historial_pruebas', compact('usuario'));
    }

    /**
     * Módulo 4: Certificado Aptitudinal.
     */
    public function certificadoAptitudinal()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.certificado_aptitudinal', compact('usuario'));
    }
}
