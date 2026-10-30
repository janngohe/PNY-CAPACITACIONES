<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaValanti extends Model
{
    protected $table = 'valanti_respuestas';

    protected $fillable = [
        'evaluacion_candidato_id',
        'numero_pregunta',
        'puntos_a',
        'puntos_b',
    ];

    protected function casts(): array
    {
        return [
            'numero_pregunta' => 'integer',
            'puntos_a' => 'integer',
            'puntos_b' => 'integer',
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionCandidato::class, 'evaluacion_candidato_id');
    }

    public function clavePar(): string
    {
        return "{$this->puntos_a}-{$this->puntos_b}";
    }
}

