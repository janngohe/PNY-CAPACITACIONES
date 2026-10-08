<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contenido extends Model
{
    protected $table = 'contenidos';

    protected $fillable = [
        'modulo_id',
        'titulo',
        'tipo',
        'contenido',
        'ruta_archivo',
        'orden',
        'obligatorio',
    ];

    protected function casts(): array
    {
        return ['obligatorio' => 'boolean'];
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }
}
