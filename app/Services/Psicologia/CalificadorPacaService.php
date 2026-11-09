<?php

namespace App\Services\Psicologia;

use App\Constants\PacaPreguntas;
use App\Models\EvaluacionCandidato;
use App\Models\ResultadoPaca;
use InvalidArgumentException;

class CalificadorPacaService
{
    // Textos institucionales oficiales extraídos de la hoja INFORMACION
    public const TXT_ACC_PRESENCIA = 'Presencia de rasgos que sugieren tendencia accidentógena en el evaluado.';
    public const TXT_ACC_INDAGAR   = 'Se sugiere indagar y/o descartar tendencia accidentógena.';
    public const TXT_ACC_AUSENCIA  = 'No se evidencian rasgos que indiquen tendencia accidentógena en el evaluado.';

    public const TXT_TPOA_PRESENCIA = 'Presencia de rasgos que indican la presencia del Patrón de comportamiento tipo A, en el evaluado.';
    public const TXT_TPOA_INDAGAR   = 'Se sugiere indagar y /o descatar la tendencia al Patrón de comportamiento tipo A';
    public const TXT_TPOA_AUSENCIA  = 'No se evidencian rasgos que indiquen la presencia del Patrón de comportamiento tipo A, en el evaluado.';

    /**
     * Califica una colección o array de respuestas [numero_pregunta => 'S'|'F'|'R'|'N']
     * o a partir de una EvaluacionCandidato con sus respuestas asociadas.
     *
     * @param array<int, string> $respuestas Mapa de [1..40 => 'S'|'F'|'R'|'N']
     * @return array Estructura con puntajes, interpretaciones y trazabilidad de ambigüedades
     */
    public function calificar(array $respuestas): array
    {
        // Validar que existan las 40 respuestas requeridas
        for ($i = 1; $i <= 40; $i++) {
            if (!isset($respuestas[$i]) || !array_key_exists(strtoupper((string)$respuestas[$i]), PacaPreguntas::OPCIONES)) {
                throw new InvalidArgumentException("La pregunta {$i} no tiene una opción de respuesta válida.");
            }
        }

        // 1. Cálculo del Indicador de Tendencia Accidentógena (12 preguntas)
        $puntajeAccidentogena = 0.0;
        $detalleAccidentogena = [];
        foreach (PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA as $numPregunta) {
            $opcion = strtoupper((string)$respuestas[$numPregunta]);
            $pts = PacaPreguntas::puntajeDeOpcion($opcion);
            $puntajeAccidentogena += $pts;
            $detalleAccidentogena[$numPregunta] = [
                'opcion' => $opcion,
                'puntaje' => $pts,
            ];
        }

        // 2. Cálculo del Indicador de Patrón Tipo A (20 preguntas)
        $puntajePatronTipoA = 0.0;
        $detalleTipoA = [];
        foreach (PacaPreguntas::PREGUNTAS_PATRON_TIPO_A as $numPregunta) {
            $opcion = strtoupper((string)$respuestas[$numPregunta]);
            $pts = PacaPreguntas::puntajeDeOpcion($opcion);
            $puntajePatronTipoA += $pts;
            $detalleTipoA[$numPregunta] = [
                'opcion' => $opcion,
                'puntaje' => $pts,
            ];
        }

        // 3. Interpretación y detección de ambigüedades documentadas
        $interpAccidentogena = $this->interpretarTendenciaAccidentogena($puntajeAccidentogena);
        $interpTipoA = $this->interpretarPatronTipoA($puntajePatronTipoA);

        return [
            'puntaje_tendencia_accidentogena' => round($puntajeAccidentogena, 1),
            'detalle_accidentogena' => $detalleAccidentogena,
            'interpretacion_accidentogena' => $interpAccidentogena['texto'],
            'estado_interpretacion_accidentogena' => $interpAccidentogena['estado'],
            'observacion_accidentogena' => $interpAccidentogena['observacion'],

            'puntaje_patron_tipo_a' => round($puntajePatronTipoA, 1),
            'detalle_tipo_a' => $detalleTipoA,
            'interpretacion_tipo_a' => $interpTipoA['texto'],
            'estado_interpretacion_tipo_a' => $interpTipoA['estado'],
            'observacion_tipo_a' => $interpTipoA['observacion'],

            'tiene_ambiguedad' => ($interpAccidentogena['estado'] === 'AMBIGUA_VALIDACION_PENDIENTE' || $interpTipoA['estado'] === 'AMBIGUA_VALIDACION_PENDIENTE'),
        ];
    }

    /**
     * Interpreta el puntaje de Tendencia Accidentógena.
     * Criterios oficiales:
     * - [>= 11.0]        => Presencia de rasgos
     * - [9.0 a 10.5]     => Se sugiere indagar
     * - [<= 8.5]         => No se evidencian rasgos
     */
    public function interpretarTendenciaAccidentogena(float $puntaje): array
    {
        if ($puntaje >= 11.0) {
            return [
                'texto' => self::TXT_ACC_PRESENCIA,
                'estado' => 'DEFINIDA',
                'observacion' => null,
            ];
        }

        if ($puntaje >= 9.0 && $puntaje <= 10.5) {
            return [
                'texto' => self::TXT_ACC_INDAGAR,
                'estado' => 'DEFINIDA',
                'observacion' => null,
            ];
        }

        return [
            'texto' => self::TXT_ACC_AUSENCIA,
            'estado' => 'DEFINIDA',
            'observacion' => null,
        ];
    }

    /**
     * Interpreta el puntaje de Patrón de Comportamiento Tipo A.
     * Criterios oficiales:
     * - [>= 20.0]       => Presencia de rasgos
     * - [18.0 a 19.5]   => Se sugiere indagar
     * - [< 18.0]        => No se evidencian rasgos
     */
    public function interpretarPatronTipoA(float $puntaje): array
    {
        if ($puntaje >= 20.0) {
            return [
                'texto' => self::TXT_TPOA_PRESENCIA,
                'estado' => 'DEFINIDA',
                'observacion' => null,
            ];
        }

        if ($puntaje >= 18.0 && $puntaje <= 19.5) {
            return [
                'texto' => self::TXT_TPOA_INDAGAR,
                'estado' => 'DEFINIDA',
                'observacion' => null,
            ];
        }

        return [
            'texto' => self::TXT_TPOA_AUSENCIA,
            'estado' => 'DEFINIDA',
            'observacion' => null,
        ];
    }

    /**
     * Califica y guarda los resultados en base de datos para una evaluación de candidato.
     */
    public function calificarYAplicar(EvaluacionCandidato $aplicacion): ResultadoPaca
    {
        $respuestasModel = $aplicacion->respuestasPaca()->get();
        if ($respuestasModel->count() < 40) {
            throw new InvalidArgumentException("La aplicación solo tiene {$respuestasModel->count()} respuestas de las 40 requeridas.");
        }

        $mapaRespuestas = [];
        foreach ($respuestasModel as $r) {
            $mapaRespuestas[$r->numero_pregunta] = $r->opcion_seleccionada;
        }

        $calculo = $this->calificar($mapaRespuestas);

        return ResultadoPaca::updateOrCreate(
            ['evaluacion_candidato_id' => $aplicacion->id],
            [
                'puntaje_tendencia_accidentogena' => $calculo['puntaje_tendencia_accidentogena'],
                'puntaje_patron_tipo_a' => $calculo['puntaje_patron_tipo_a'],
                'interpretacion_accidentogena' => $calculo['interpretacion_accidentogena'],
                'estado_interpretacion_accidentogena' => $calculo['estado_interpretacion_accidentogena'],
                'interpretacion_tipo_a' => $calculo['interpretacion_tipo_a'],
                'estado_interpretacion_tipo_a' => $calculo['estado_interpretacion_tipo_a'],
                'observaciones_profesional' => $calculo['tiene_ambiguedad'] 
                    ? trim(($calculo['observacion_accidentogena'] ?? '') . ' ' . ($calculo['observacion_tipo_a'] ?? ''))
                    : null,
                'calculado_automaticamente' => true,
                'finalizada_at' => now(),
            ]
        );
    }
}
