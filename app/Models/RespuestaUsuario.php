<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaUsuario extends Model
{
    protected $table = 'respuestas_usuario';

    public $timestamps = false;

    protected $fillable = [
        'intento_id',
        'pregunta_id',
        'opcion_id',
        'respuesta_texto',
        'es_correcta',
    ];

    protected function casts(): array
    {
        return ['es_correcta' => 'boolean'];
    }

    public function intento(): BelongsTo
    {
        return $this->belongsTo(IntentoEvaluacion::class, 'intento_id');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'pregunta_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(OpcionRespuesta::class, 'opcion_id');
    }
}
