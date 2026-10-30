<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\AplicacionPrueba;
use Illuminate\Http\Request;

class PruebaController extends Controller
{
    /**
     * Historial general de pruebas aplicadas con filtros avanzados:
     * - Búsqueda por texto (nombre, identificación, cargo)
     * - Estado de la sesión (Finalizada, En curso, Borrador)
     * - Rango de fechas de aplicación (desde / hasta)
     * - Indicador de Tendencia Accidentógena (Presencia de rasgos, Por indagar, Ausencia)
     * - Modalidad de Consentimiento (Firma digital, Firma física)
     * - Criterio de ordenamiento
     */
    public function historial(Request $request)
    {
        $query = AplicacionPrueba::with(['candidato', 'consentimiento', 'resultadoPaca', 'responsable'])
            ->orderByDesc('id');

        // 1. Filtro por Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 2. Filtro de búsqueda por texto
        if ($request->filled('buscar')) {
            $busqueda = trim((string) $request->buscar);
            $query->whereHas('candidato', function ($q) use ($busqueda) {
                $q->where('nombre_completo', 'like', "%{$busqueda}%")
                  ->orWhere('numero_identificacion', 'like', "%{$busqueda}%")
                  ->orWhere('cargo_postulado', 'like', "%{$busqueda}%");
            });
        }

        // 3. Filtro por Fecha o Rango
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_aplicacion', $request->fecha);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_aplicacion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_aplicacion', '<=', $request->fecha_hasta);
        }

        // 4. Filtro por Nivel de Tendencia Accidentógena
        if ($request->filled('tendencia')) {
            $tendencia = $request->tendencia;
            $query->whereHas('resultadoPaca', function ($q) use ($tendencia) {
                if ($tendencia === 'PRESENCIA') {
                    $q->where('puntaje_tendencia_accidentogena', '>=', 11.0);
                } elseif ($tendencia === 'INDAGAR') {
                    $q->whereBetween('puntaje_tendencia_accidentogena', [9.0, 10.5]);
                } elseif ($tendencia === 'AUSENCIA') {
                    $q->where('puntaje_tendencia_accidentogena', '<=', 8.5);
                }
            });
        }

        // 5. Filtro por Modalidad o Estado de Consentimiento
        if ($request->filled('consentimiento')) {
            $tipoCons = $request->consentimiento;
            $query->whereHas('consentimiento', function ($q) use ($tipoCons) {
                if ($tipoCons === 'DIGITAL') {
                    $q->where(function ($sub) {
                        $sub->where('tipo_firma', 'DIGITAL')
                            ->orWhere('estado', 'FIRMADO_DIGITAL');
                    });
                } elseif ($tipoCons === 'FISICO') {
                    $q->where(function ($sub) {
                        $sub->where('tipo_firma', 'FISICA')
                            ->orWhere('estado', 'FIRMADO_FISICO');
                    });
                } elseif ($tipoCons === 'PENDIENTE') {
                    $q->whereIn('estado', ['GENERADO', 'DESCARGADO']);
                }
            });
        }

        // 6. Ordenamiento configurable
        if ($request->filled('orden')) {
            $orden = $request->orden;
            if ($orden === 'antiguos') {
                $query->reorder()->orderBy('fecha_aplicacion', 'asc')->orderBy('id', 'asc');
            } elseif ($orden === 'mayor_riesgo') {
                $query->leftJoin('resultados_paca', 'aplicaciones_pruebas.id', '=', 'resultados_paca.aplicacion_id')
                      ->select('aplicaciones_pruebas.*')
                      ->reorder()
                      ->orderByDesc('resultados_paca.puntaje_tendencia_accidentogena');
            } elseif ($orden === 'nombre_asc') {
                $query->join('candidatos', 'aplicaciones_pruebas.candidato_id', '=', 'candidatos.id')
                      ->select('aplicaciones_pruebas.*')
                      ->reorder()
                      ->orderBy('candidatos.nombre_completo', 'asc');
            }
        }

        $aplicaciones = $query->paginate(15)->withQueryString();

        // Contadores para métricas rápidas de los filtros actuales
        $filtrosActivos = $request->hasAny(['buscar', 'estado', 'fecha_desde', 'fecha_hasta', 'tendencia', 'consentimiento', 'orden']);

        return view('psicologia.pruebas.historial', compact('aplicaciones', 'filtrosActivos'));
    }
}
