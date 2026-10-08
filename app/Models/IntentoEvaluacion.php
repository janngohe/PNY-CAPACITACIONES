<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntentoEvaluacion extends Model
{
    protected $table = 'intentos_evaluacion';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'evaluacion_id',
        'numero_intento',
        'porcentaje',
        'respuestas_correctas',
        'total_preguntas',
        'estado',
        'fecha_inicio',
        'fecha_finalizacion',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'fecha_inicio' => 'datetime',
            'fecha_finalizacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaUsuario::class, 'intento_id');
    }
}
