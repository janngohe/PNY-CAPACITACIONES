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
     * Muestra la vista de inducciones para el Jefe de Área con el PIN de 4 dígitos visible
     * y el historial de "Inducciones de Área" (sesiones dictadas o facilitadas por colaboradores
     * de su área o por el mismo jefe hacia otros).
     */
    public function index(): View
    {
        $jefe = auth()->user();
        $pinActual = Configuracion::getPinInduccion();

        // Inducciones activas institucionales disponibles para proyectar
        $inducciones = Induccion::where('estado', true)
            ->latest()
            ->get();

        $areaId = $jefe->area_id;
        $areaNombre = $jefe->area?->nombre;

        // Historial de Inducciones de Área:
        // Sesiones dictadas/facilitadas por un empleado de esa área o por el mismo jefe a otros
        $sesionesArea = SesionInduccion::with(['induccion', 'facilitador.area', 'sede'])
            ->withCount('asistencias as total_asistencias_count')
            ->where(function ($query) use ($areaId, $areaNombre, $jefe) {
                // Facilitadas por el mismo jefe
                $query->where('facilitador_id', $jefe->id);

                // O facilitadas por colaboradores de su misma área
                if ($areaId) {
                    $query->orWhereHas('facilitador', fn ($f) => $f->where('area_id', $areaId));
                }
                if ($areaNombre) {
                    $query->orWhereHas('facilitador.area', fn ($a) => $a->where('nombre', $areaNombre));
                }
            })
            ->latest()
            ->get();

        return view('jefe.inducciones.index', compact('pinActual', 'inducciones', 'sesionesArea', 'areaNombre'));
    }

    /**
     * Retorna el listado de participantes y firmas digitales de una sesión en JSON para visualización interactiva.
     */
    public function participantesJson(SesionInduccion $sesion): JsonResponse
    {
        $jefe = auth()->user();
        $areaId = $jefe->area_id;
        $areaNombre = $jefe->area?->nombre;

        $sesion->load([
            'induccion',
            'facilitador',
            'sede',
            'asistencias.usuario.area',
            'asistencias.usuario.sede',
        ]);

        $participantes = $sesion->asistencias->map(function ($asist) use ($areaId, $areaNombre) {
            $u = $asist->usuario;
            $asistAreaId = $u?->area_id;
            $asistAreaNombre = $asist->area ?: ($u?->area?->nombre ?? '');

            $esDeMiArea = ($areaId && $asistAreaId == $areaId) ||
                          ($areaNombre && strcasecmp(trim($asistAreaNombre), trim($areaNombre)) === 0);

            return [
                'id' => $asist->id,
                'nombre_completo' => $asist->nombre_completo ?: ($u?->nombre_completo ?? 'Sin nombre'),
                'identificacion' => $asist->identificacion ?: ($u?->identificacion ?? '-'),
                'cargo' => $asist->cargo ?: ($u?->cargoOficial() ?? 'Colaborador'),
                'area' => $asist->area ?: ($u?->area?->nombre ?? 'Sin área'),
                'sede' => $asist->sede ?: ($u?->sedeActual()?->nombre ?? 'Sede Principal'),
                'es_externo' => (bool) $asist->es_externo,
                'es_mi_area' => $esDeMiArea,
                'empresa_externa' => $asist->empresa_externa,
                'telefono' => $asist->telefono,
                'tiene_firma' => !empty($asist->firma_digital),
                'firma_url' => $asist->firma_url,
                'fecha_registro' => $asist->fecha_registro ? $asist->fecha_registro->format('d/m/Y h:i A') : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'area_jefe' => $areaNombre,
            'sesion' => [
                'id' => $sesion->id,
                'codigo' => $sesion->codigo_sesion,
                'induccion_titulo' => $sesion->induccion?->titulo ?? 'Inducción sin título',
                'facilitador' => $sesion->facilitador?->nombre_completo ?? 'Facilitador no asignado',
                'sede' => $sesion->sede?->nombre ?? 'Sede Principal',
                'areas_convocadas' => $sesion->areas_convocadas_nombres,
                'areas_convocadas_array' => $sesion->areas_convocadas_nombres_array,
                'fecha_inicio' => $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y h:i A') : 'N/A',
                'fecha_cierre' => $sesion->fecha_cierre ? $sesion->fecha_cierre->format('d/m/Y h:i A') : null,
                'estado' => $sesion->estado,
                'es_activa' => $sesion->estaActiva(),
                'total_participantes' => $participantes->count(),
                'total_mi_area' => $participantes->where('es_mi_area', true)->count(),
                'total_externos' => $participantes->where('es_externo', true)->count(),
                'acta_pdf_url' => route('induccion.acta.pdf', $sesion->codigo_sesion),
            ],
            'participantes' => $participantes->values(),
        ]);
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
