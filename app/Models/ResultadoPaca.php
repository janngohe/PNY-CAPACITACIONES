<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoPaca extends Model
{
    protected $table = 'paca_resultados';

    protected $fillable = [
        'evaluacion_candidato_id',
        'puntaje_tendencia_accidentogena',
        'puntaje_patron_tipo_a',
        'interpretacion_accidentogena',
        'estado_interpretacion_accidentogena',
        'interpretacion_tipo_a',
        'estado_interpretacion_tipo_a',
        'observaciones_profesional',
        'calculado_automaticamente',
        'finalizada_at',
    ];

    protected function casts(): array
    {
        return [
            'puntaje_tendencia_accidentogena' => 'float',
            'puntaje_patron_tipo_a' => 'float',
            'calculado_automaticamente' => 'boolean',
            'finalizada_at' => 'datetime',
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionCandidato::class, 'evaluacion_candidato_id');
    }

    public function tieneAmbiguedad(): bool
    {
        return $this->estado_interpretacion_accidentogena === 'AMBIGUA_VALIDACION_PENDIENTE'
            || $this->estado_interpretacion_tipo_a === 'AMBIGUA_VALIDACION_PENDIENTE';
    }
}

