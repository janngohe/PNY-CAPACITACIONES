<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaPaca extends Model
{
    protected $table = 'paca_respuestas';

    protected $fillable = [
        'evaluacion_candidato_id',
        'numero_pregunta',
        'opcion_seleccionada',
        'puntaje',
    ];

    protected function casts(): array
    {
        return [
            'numero_pregunta' => 'integer',
            'puntaje' => 'float',
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionCandidato::class, 'evaluacion_candidato_id');
    }

    /**
     * Nombre textual de la opción seleccionada.
     */
    public function textoOpcion(): string
    {
        return match ($this->opcion_seleccionada) {
            'S' => 'Siempre',
            'F' => 'Frecuentemente',
            'R' => 'Rara vez',
            'N' => 'Nunca',
            default => $this->opcion_seleccionada,
        };
    }
}

