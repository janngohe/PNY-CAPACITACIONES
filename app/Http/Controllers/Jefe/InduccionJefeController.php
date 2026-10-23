<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Induccion;
use App\Models\SesionInduccion;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class InduccionJefeController extends Controller
{
    /**
     * Muestra la vista de inducciones para el Jefe de Área con el PIN de 4 dígitos visible.
     */
    public function index(): View
    {
        $jefe = auth()->user();
        $pinActual = Configuracion::getPinInduccion();

        // Inducciones activas institucionales disponibles para proyectar
        $inducciones = Induccion::where('estado', true)
            ->latest()
            ->get();

        $sesionesRecientes = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
            ->withCount('asistencias')
            ->latest()
            ->take(6)
            ->get();

        return view('jefe.inducciones.index', compact('pinActual', 'inducciones', 'sesionesRecientes'));
    }

    /**
     * Consulta el PIN vía AJAX para mostrar en modales o notificaciones.
     */
    public function consultarPin(): JsonResponse
    {
        return response()->json([
            'pin' => Configuracion::getPinInduccion(),
        ]);
    }
}
