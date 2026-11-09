<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EvaluacionCandidato extends Model
{
    protected $table = 'evaluaciones_candidatos';

    protected $fillable = [
        'codigo_sesion',
        'nombre_completo',
        'identificacion',
        'lugar_expedicion',
        'fecha_nacimiento',
        'edad',
        'cargo_aspira',
        'firma_digital',
        'acepta_terminos',
        'fecha_hora_aceptacion',
        'paso_actual',
        'estado',
        'certificado_ciudad_fecha',
        'certificado_concepto',
        'certificado_observaciones',
        'certificado_emitido_at',
        'certificado_emitido_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_hora_aceptacion' => 'datetime',
            'certificado_emitido_at' => 'datetime',
            'acepta_terminos' => 'boolean',
            'edad' => 'integer',
            'paso_actual' => 'integer',
        ];
    }

    public function certificadoEmitido(): bool
    {
        return !empty($this->certificado_concepto);
    }

    /**
     * Determina automáticamente el concepto aptitudinal oficial (GH-FO-028)
     * a partir de los resultados cuantitativos y cualitativos de las pruebas PAC-A y VALANTI:
     * - CUMPLE_SIN_INDICE: Sin rasgos accidentógenos (<= 8.5), sin patrón tipo A (< 18) y valores promedio/altos.
     * - CUMPLE_CON_RECOMENDACIONES: Presencia o sospecha de tendencia accidentógena (>= 9.0),
     *   patrón tipo A (>= 18.0) o valores en segundo plano / bajo.
     * - NO_CUMPLE: Valores extremos negados (PE < 30) o concurrencia de alto riesgo accidentógeno y valores bajos.
     */
    public function calcularConceptoAptitudinal(): string
    {
        $paca = $this->resultadoPaca;
        $valanti = $this->resultadoValanti;

        if (!$paca || !$valanti) {
            return 'CUMPLE_CON_RECOMENDACIONES';
        }

        $ptsAcc = (float) $paca->puntaje_tendencia_accidentogena;
        $ptsTipoA = (float) $paca->puntaje_patron_tipo_a;

        $valoresPE = [
            'VERDAD'       => (int) $valanti->pe_verdad,
            'RECTITUD'     => (int) $valanti->pe_rectitud,
            'PAZ'          => (int) $valanti->pe_paz,
            'AMOR'         => (int) $valanti->pe_amor,
            'NO_VIOLENCIA' => (int) $valanti->pe_no_violencia,
        ];

        $bajos = 0;           // PE < 40 (Negado / Muy Bajo)
        $promedioBajo = 0;    // PE 40..44 (Segundo Plano)
        $muyBajos = 0;        // PE < 30 (Extremadamente bajo)
        foreach ($valoresPE as $pe) {
            if ($pe < 30) {
                $muyBajos++;
            }
            if ($pe < 40) {
                $bajos++;
            } elseif ($pe <= 44) {
                $promedioBajo++;
            }
        }

        // 1. Criterio NO CUMPLE CON EL PERFIL:
        if ($muyBajos > 0 || ($ptsAcc >= 11.0 && $ptsTipoA >= 20.0 && $bajos > 0) || ($bajos >= 2 && ($ptsAcc >= 11.0 || $ptsTipoA >= 20.0))) {
            return 'NO_CUMPLE';
        }

        // 2. Criterio CUMPLE CON RECOMENDACIONES (A nivel accidentógeno, Tipo A o valores en segundo plano/bajos):
        if ($ptsAcc >= 9.0 || $ptsTipoA >= 18.0 || $promedioBajo > 0 || $bajos > 0) {
            return 'CUMPLE_CON_RECOMENDACIONES';
        }

        // 3. Criterio CUMPLE SIN ÍNDICE DE ACCIDENTALIDAD:
        return 'CUMPLE_SIN_INDICE';
    }

    /**
     * Genera la observación psicológica sugerida a partir de los hallazgos de PAC-A y VALANTI.
     */
    public function generarObservacionSugerida(): string
    {
        $paca = $this->resultadoPaca;
        $valanti = $this->resultadoValanti;

        if (!$paca || !$valanti) {
            return 'CUMPLE';
        }

        $partes = [];

        // 1. Dimensiones en segundo plano o negadas en VALANTI
        $nombresValanti = [
            'VERDAD'       => 'VERDAD',
            'RECTITUD'     => 'RECTITUD',
            'PAZ'          => 'PAZ',
            'AMOR'         => 'AMOR',
            'NO_VIOLENCIA' => 'NO VIOLENCIA',
        ];
        $valoresPE = [
            'VERDAD'       => (int) $valanti->pe_verdad,
            'RECTITUD'     => (int) $valanti->pe_rectitud,
            'PAZ'          => (int) $valanti->pe_paz,
            'AMOR'         => (int) $valanti->pe_amor,
            'NO_VIOLENCIA' => (int) $valanti->pe_no_violencia,
        ];

        $negados = [];
        $segundoPlano = [];
        foreach ($valoresPE as $dim => $pe) {
            if ($pe < 40) {
                $negados[] = $nombresValanti[$dim];
            } elseif ($pe <= 44) {
                $segundoPlano[] = $nombresValanti[$dim];
            }
        }

        if (!empty($negados)) {
            $partes[] = 'NEGADO VALOR ' . implode(' Y ', $negados);
        }
        if (!empty($segundoPlano)) {
            $partes[] = 'SEGUNDO PLANO VALOR ' . implode(' Y ', $segundoPlano);
        }

        // 2. PAC-A Tendencia Accidentógena
        $ptsAcc = (float) $paca->puntaje_tendencia_accidentogena;
        if ($ptsAcc >= 11.0) {
            $partes[] = 'Presencia de rasgos que sugieren tendencia accidentógena en el evaluado.';
        } elseif ($ptsAcc >= 9.0) {
            $partes[] = 'Se sugiere indagar y/o descartar tendencia accidentógena.';
        }

        // 3. PAC-A Patrón Tipo A
        $ptsTipoA = (float) $paca->puntaje_patron_tipo_a;
        if ($ptsTipoA >= 20.0) {
            $partes[] = 'Presencia de rasgos que indican la presencia del Patrón de comportamiento tipo A, en el evaluado.';
        } elseif ($ptsTipoA >= 18.0) {
            $partes[] = 'Se sugiere indagar y /o descatar la tendencia al Patrón de comportamiento tipo A.';
        }

        if (empty($partes)) {
            return 'CUMPLE';
        }

        return mb_strtoupper(implode(', ', $partes), 'UTF-8');
    }

    /**
     * Retorna el concepto actual (el emitido o el calculado automáticamente según resultados).
     */
    public function conceptoAptitudinalActual(): string
    {
        return $this->certificado_concepto ?: $this->calcularConceptoAptitudinal();
    }

    /**
     * Devuelve el texto para la columna OBSERVACION del reporte Excel anual.
     */
    public function observacionReporteExcel(): string
    {
        $obs = trim((string) ($this->certificado_observaciones ?? ''));
        if ($obs !== '') {
            return $obs;
        }

        $concepto = $this->conceptoAptitudinalActual();
        if ($concepto === 'CUMPLE_SIN_INDICE') {
            return 'CUMPLE';
        }

        return $this->generarObservacionSugerida();
    }

    public function badgeCertificadoAptitudinal(): array
    {
        $concepto = $this->conceptoAptitudinalActual();
        $emitido = $this->certificadoEmitido();

        $info = match ($concepto) {
            'CUMPLE_SIN_INDICE' => [
                'texto' => 'Cumple (Sin Accidentalidad)',
                'clase' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icono' => 'fa-circle-check',
            ],
            'CUMPLE_CON_RECOMENDACIONES' => [
                'texto' => 'Cumple (Con Recomendaciones)',
                'clase' => 'bg-sky-50 text-sky-700 border-sky-200',
                'icono' => 'fa-circle-info',
            ],
            'NO_CUMPLE' => [
                'texto' => 'No Cumple Perfil',
                'clase' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icono' => 'fa-circle-xmark',
            ],
            default => [
                'texto' => 'Pendiente',
                'clase' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icono' => 'fa-pen-to-square',
            ],
        };

        if (!$emitido) {
            $info['subtexto'] = 'Auto: ' . $info['texto'];
            $info['texto'] = 'Por emitir (' . $info['texto'] . ')';
        }

        return $info;
    }

    public function respuestasPaca(): HasMany
    {
        return $this->hasMany(RespuestaPaca::class, 'evaluacion_candidato_id')->orderBy('numero_pregunta');
    }

    public function resultadoPaca(): HasOne
    {
        return $this->hasOne(ResultadoPaca::class, 'evaluacion_candidato_id');
    }

    public function respuestasValanti(): HasMany
    {
        return $this->hasMany(RespuestaValanti::class, 'evaluacion_candidato_id')->orderBy('numero_pregunta');
    }

    public function resultadoValanti(): HasOne
    {
        return $this->hasOne(ResultadoValanti::class, 'evaluacion_candidato_id');
    }

    /**
     * La Prueba 1 (PAC-A) está finalizada cuando ya existe su resultado calificado.
     */
    public function pacaFinalizada(): bool
    {
        return $this->resultadoPaca !== null;
    }

    /**
     * La Prueba 2 (VALANTI) está finalizada cuando ya existe su resultado calificado.
     */
    public function valantiFinalizada(): bool
    {
        return $this->resultadoValanti !== null;
    }

    /**
     * Cantidad de preguntas PAC-A respondidas (usa withCount si fue cargado).
     */
    public function totalRespondidasPaca(): int
    {
        return (int) ($this->respuestas_paca_count ?? $this->respuestasPaca()->count());
    }

    public function progresoPaca(): float
    {
        return round(($this->totalRespondidasPaca() / 40) * 100, 2);
    }

    /**
     * Cantidad de preguntas VALANTI respondidas (usa withCount si fue cargado).
     */
    public function totalRespondidasValanti(): int
    {
        return (int) ($this->respuestas_valanti_count ?? $this->respuestasValanti()->count());
    }

    public function progresoValanti(): float
    {
        return round(($this->totalRespondidasValanti() / 30) * 100, 2);
    }

    /**
     * Estado derivado de las pruebas psicotécnicas (VALANTI + PAC-A).
     */
    public function estadoPaca(): string
    {
        if ($this->valantiFinalizada() && $this->pacaFinalizada()) {
            return 'FINALIZADA';
        }

        if ($this->valantiFinalizada() && !$this->pacaFinalizada()) {
            return 'EN_PACA';
        }

        return $this->totalRespondidasValanti() > 0 ? 'EN_CURSO' : 'PENDIENTE';
    }

    public function badgeEstadoPaca(): array
    {
        return match ($this->estadoPaca()) {
            'FINALIZADA' => [
                'texto' => '2/2 Pruebas Finalizadas',
                'clase' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icono' => 'fa-circle-check',
            ],
            'EN_PACA' => [
                'texto' => 'VALANTI Lista · PAC-A (' . round($this->progresoPaca()) . '%)',
                'clase' => 'bg-sky-50 text-sky-700 border-sky-200',
                'icono' => 'fa-spinner fa-spin',
            ],
            'EN_CURSO' => [
                'texto' => 'En VALANTI (' . round($this->progresoValanti()) . '%)',
                'clase' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icono' => 'fa-spinner fa-spin',
            ],
            default => [
                'texto' => 'Autorizado · Sin iniciar',
                'clase' => 'bg-slate-100 text-slate-700 border-slate-200',
                'icono' => 'fa-hourglass-half',
            ],
        };
    }
}
