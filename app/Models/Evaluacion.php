<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';

    protected $fillable = [
        'capacitacion_id',
        'titulo',
        'descripcion',
        'porcentaje_aprobacion',
        'intentos_permitidos',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje_aprobacion' => 'decimal:2',
            'estado' => 'boolean',
        ];
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'evaluacion_id')->orderBy('orden');
    }

    public function intentos(): HasMany
    {
        return $this->hasMany(IntentoEvaluacion::class, 'evaluacion_id');
    }
}
