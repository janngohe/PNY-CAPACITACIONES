<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoPuntos extends Model
{
    protected $table = 'movimientos_puntos';

    protected $fillable = [
        'usuario_id',
        'capacitacion_id',
        'beneficio_id',
        'tipo', // 'ACREDITACION', 'CANJE', 'AJUSTE'
        'puntos',
        'saldo_anterior',
        'saldo_nuevo',
        'motivo',
    ];

    protected $casts = [
        'puntos' => 'integer',
        'saldo_anterior' => 'integer',
        'saldo_nuevo' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }

    public function beneficio(): BelongsTo
    {
        return $this->belongsTo(Beneficio::class, 'beneficio_id');
    }
}
