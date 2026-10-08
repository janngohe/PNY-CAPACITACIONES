<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresoModulo extends Model
{
    protected $table = 'progreso_modulos';

    protected $fillable = [
        'usuario_id',
        'modulo_id',
        'porcentaje',
        'completado',
        'fecha_inicio',
        'fecha_finalizacion',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'completado' => 'boolean',
            'fecha_inicio' => 'datetime',
            'fecha_finalizacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }
}
