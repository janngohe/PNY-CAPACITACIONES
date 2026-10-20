<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaInduccion;
use App\Models\Configuracion;
use App\Models\Induccion;
use App\Models\SesionInduccion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InduccionAdminController extends Controller
{
    /**
     * Panel de administración de Inducciones y control de PIN de Facilitadores.
     */
    public function index(): View
    {
        $pinActual = Configuracion::getPinInduccion();

        $inducciones = Induccion::with(['sedes', 'areas', 'creador'])
            ->withCount('sesiones')
            ->latest()
            ->get();

        $sesiones = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
            ->withCount('asistencias')
            ->latest()
            ->paginate(15);

        $totalInducciones = Induccion::count();
        $totalSesiones = SesionInduccion::count();
        $totalAsistencias = AsistenciaInduccion::count();
        $asistenciasExternos = AsistenciaInduccion::where('es_externo', true)->count();
        $asistenciasInternos = $totalAsistencias - $asistenciasExternos;

        $estadisticas = [
            'total_inducciones' => $totalInducciones,
            'total_sesiones' => $totalSesiones,
            'total_asistencias' => $totalAsistencias,
            'asistencias_internos' => $asistenciasInternos,
            'asistencias_externos' => $asistenciasExternos,
        ];

        return view('admin.inducciones.index', compact('pinActual', 'inducciones', 'sesiones', 'estadisticas'));
    }

    /**
     * Regenera el PIN de 4 dígitos previa validación de la contraseña del Administrador.
     */
    public function regenerarPin(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'password_confirmacion' => ['required', 'string'],
        ], [
            'password_confirmacion.required' => 'Debes ingresar tu contraseña de administrador para confirmar.',
        ]);

        $admin = auth()->user();

        if (!Hash::check($request->password_confirmacion, $admin->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña de administrador incorrecta. No se ha modificado el PIN.',
                ], 422);
            }

            return back()->with('error', 'Contraseña de administrador incorrecta. Por seguridad, el PIN no fue modificado.');
        }

        $nuevoPin = Configuracion::regenerarPinInduccion();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "El PIN de inducción ha sido regenerado exitosamente a: {$nuevoPin}",
                'nuevo_pin' => $nuevoPin,
            ]);
        }

        return back()->with('success', "¡PIN regenerado exitosamente! El nuevo código de 4 dígitos es: {$nuevoPin}");
    }

    /**
     * Descarga el acta oficial en PDF de una sesión de inducción.
     */
    public function descargarActaPdf(SesionInduccion $sesion)
    {
        $sesion->loadMissing(['induccion', 'facilitador.area', 'sede', 'asistencias.usuario']);

        $pdf = Pdf::loadView('induccion.acta_pdf', compact('sesion'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $nombreArchivo = 'Acta_Induccion_' . $sesion->codigo_sesion . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }
}
