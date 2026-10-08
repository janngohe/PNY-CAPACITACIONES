<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    protected $table = 'preguntas';

    protected $fillable = ['evaluacion_id', 'pregunta', 'tipo', 'orden'];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(OpcionRespuesta::class, 'pregunta_id');
    }

    public function respuestasUsuario(): HasMany
    {
        return $this->hasMany(RespuestaUsuario::class, 'pregunta_id');
    }
}
