<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaPaca extends Model
{
    protected $table = 'respuestas_paca';

    protected $fillable = [
        'aplicacion_id',
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

    public function aplicacion(): BelongsTo
    {
        return $this->belongsTo(AplicacionPrueba::class, 'aplicacion_id');
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
