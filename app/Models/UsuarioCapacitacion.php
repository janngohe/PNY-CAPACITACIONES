<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioCapacitacion extends Model
{
    protected $table = 'usuario_capacitacion';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'capacitacion_id',
        'fecha_asignacion',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
            'fecha_inicio' => 'datetime',
            'fecha_finalizacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }
}
