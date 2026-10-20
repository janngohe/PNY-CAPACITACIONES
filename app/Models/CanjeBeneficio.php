<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanjeBeneficio extends Model
{
    protected $table = 'canjes_beneficios';

    protected $fillable = [
        'usuario_id',
        'beneficio_id',
        'puntos_utilizados',
        'estado',
        'codigo_canje',
        'fecha_canje',
        'notas',
    ];

    protected $casts = [
        'puntos_utilizados' => 'integer',
        'fecha_canje' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function beneficio(): BelongsTo
    {
        return $this->belongsTo(Beneficio::class, 'beneficio_id');
    }
}
