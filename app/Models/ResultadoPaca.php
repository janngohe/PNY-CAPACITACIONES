<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoPaca extends Model
{
    protected $table = 'resultados_paca';

    protected $fillable = [
        'aplicacion_id',
        'puntaje_tendencia_accidentogena',
        'puntaje_patron_tipo_a',
        'interpretacion_accidentogena',
        'estado_interpretacion_accidentogena',
        'interpretacion_tipo_a',
        'estado_interpretacion_tipo_a',
        'observaciones_profesional',
        'calculado_automaticamente',
    ];

    protected function casts(): array
    {
        return [
            'puntaje_tendencia_accidentogena' => 'float',
            'puntaje_patron_tipo_a' => 'float',
            'calculado_automaticamente' => 'boolean',
        ];
    }

    public function aplicacion(): BelongsTo
    {
        return $this->belongsTo(AplicacionPrueba::class, 'aplicacion_id');
    }

    public function tieneAmbiguedad(): bool
    {
        return $this->estado_interpretacion_accidentogena === 'AMBIGUA_VALIDACION_PENDIENTE'
            || $this->estado_interpretacion_tipo_a === 'AMBIGUA_VALIDACION_PENDIENTE';
    }
}
