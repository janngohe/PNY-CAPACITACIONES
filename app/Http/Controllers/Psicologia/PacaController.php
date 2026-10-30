<?php

namespace App\Http\Controllers\Psicologia;

use App\Constants\PacaPreguntas;
use App\Http\Controllers\Controller;
use App\Models\AplicacionPrueba;
use App\Models\RespuestaPaca;
use App\Services\Psicologia\CalificadorPacaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacaController extends Controller
{
    /**
     * Muestra la interfaz de aplicación del instrumento PAC-A (40 afirmaciones).
     */
    public function aplicar(AplicacionPrueba $aplicacion)
    {
        if ($aplicacion->estaFinalizada()) {
            return redirect()->route('psicologia.paca.resultado', $aplicacion)
                ->with('info', 'Esta prueba ya fue finalizada y calificada. Se muestran los resultados registrados.');
        }

        $aplicacion->load(['candidato', 'consentimiento', 'responsable']);
        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $opciones = PacaPreguntas::OPCIONES;

        // Respuestas guardadas previamente (borrador)
        $respuestasGuardadas = $aplicacion->respuestasPaca()
            ->get()
            ->keyBy('numero_pregunta')
            ->map(fn($r) => $r->opcion_seleccionada)
            ->toArray();

        return view('psicologia.paca.aplicar', compact(
            'aplicacion',
            'afirmaciones',
            'opciones',
            'respuestasGuardadas'
        ));
    }

    /**
     * Guarda el borrador de las respuestas ingresadas sin finalizar la prueba.
     * Permite continuar la aplicación en cualquier momento.
     */
    public function guardarBorrador(Request $request, AplicacionPrueba $aplicacion)
    {
        if ($aplicacion->estaFinalizada()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'La prueba ya está finalizada.'], 403);
            }
            return redirect()->route('psicologia.paca.resultado', $aplicacion);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        DB::transaction(function () use ($aplicacion, $respuestasInput) {
            foreach ($respuestasInput as $num => $opcion) {
                $num = (int)$num;
                $opcion = strtoupper(trim((string)$opcion));

                if ($num >= 1 && $num <= 40 && array_key_exists($opcion, PacaPreguntas::OPCIONES)) {
                    RespuestaPaca::updateOrCreate(
                        ['aplicacion_id' => $aplicacion->id, 'numero_pregunta' => $num],
                        [
                            'opcion_seleccionada' => $opcion,
                            'puntaje' => PacaPreguntas::puntajeDeOpcion($opcion),
                        ]
                    );
                }
            }

            $conteo = $aplicacion->respuestasPaca()->count();
            $porcentaje = ($conteo / 40) * 100;

            $aplicacion->update([
                'progreso_porcentaje' => round($porcentaje, 2),
                'estado' => $porcentaje > 0 ? 'EN_CURSO' : 'BORRADOR',
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            $conteo = $aplicacion->respuestasPaca()->count();
            return response()->json([
                'success' => true,
                'mensaje' => "Borrador guardado: {$conteo} de 40 preguntas respondidas.",
                'progreso' => round(($conteo / 40) * 100, 1),
                'conteo' => $conteo,
            ]);
        }

        return back()->with('success', 'Borrador guardado correctamente. Puede continuar en cualquier momento.');
    }

    /**
     * Valida de forma estricta las 40 respuestas y califica definitivamente la prueba.
     */
    public function finalizar(Request $request, AplicacionPrueba $aplicacion, CalificadorPacaService $calificador)
    {
        if ($aplicacion->estaFinalizada()) {
            return redirect()->route('psicologia.paca.resultado', $aplicacion)
                ->with('warning', 'La prueba ya había sido finalizada previamente.');
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        // 1. Validar que las 40 preguntas estén respondidas
        $faltantes = [];
        for ($i = 1; $i <= 40; $i++) {
            if (!isset($respuestasInput[$i]) || !array_key_exists(strtoupper((string)$respuestasInput[$i]), PacaPreguntas::OPCIONES)) {
                $faltantes[] = $i;
            }
        }

        if (count($faltantes) > 0) {
            $listaFaltantes = implode(', ', array_slice($faltantes, 0, 10));
            if (count($faltantes) > 10) {
                $listaFaltantes .= '... (y ' . (count($faltantes) - 10) . ' más)';
            }

            return back()->withInput()->with('error', "No es posible finalizar la prueba. Faltan por responder " . count($faltantes) . " afirmaciones: [Preguntas: {$listaFaltantes}]. Debe responder las 40 preguntas.");
        }

        // 2. Guardar las 40 respuestas y calificar dentro de una transacción
        DB::transaction(function () use ($aplicacion, $respuestasInput, $calificador) {
            foreach ($respuestasInput as $num => $opcion) {
                $num = (int)$num;
                $opcion = strtoupper(trim((string)$opcion));

                RespuestaPaca::updateOrCreate(
                    ['aplicacion_id' => $aplicacion->id, 'numero_pregunta' => $num],
                    [
                        'opcion_seleccionada' => $opcion,
                        'puntaje' => PacaPreguntas::puntajeDeOpcion($opcion),
                    ]
                );
            }

            $aplicacion->update([
                'estado' => 'FINALIZADA',
                'hora_finalizacion' => now(),
                'progreso_porcentaje' => 100.0,
            ]);

            // Invocar el servicio de calificación independiente
            $calificador->calificarYAplicar($aplicacion);
        });

        return redirect()->route('psicologia.paca.resultado', $aplicacion)
            ->with('success', '¡Prueba PAC-A finalizada y calificada exitosamente! Se presentan los indicadores calculados.');
    }

    /**
     * Muestra la vista de resultados de la prueba PAC-A.
     */
    public function resultado(AplicacionPrueba $aplicacion)
    {
        $aplicacion->load([
            'candidato',
            'consentimiento',
            'responsable',
            'resultadoPaca',
            'respuestasPaca',
        ]);

        $resultado = $aplicacion->resultadoPaca;

        if (!$resultado) {
            return redirect()->route('psicologia.paca.aplicar', $aplicacion)
                ->with('error', 'La prueba aún no cuenta con resultados calculados.');
        }

        // Desglose de preguntas por indicador
        $respuestasPorId = $aplicacion->respuestasPaca->keyBy('numero_pregunta');

        $preguntasAccidentogena = PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA;
        $preguntasTipoA = PacaPreguntas::PREGUNTAS_PATRON_TIPO_A;
        $afirmaciones = PacaPreguntas::AFIRMACIONES;

        return view('psicologia.paca.resultado', compact(
            'aplicacion',
            'resultado',
            'respuestasPorId',
            'preguntasAccidentogena',
            'preguntasTipoA',
            'afirmaciones'
        ));
    }
}
