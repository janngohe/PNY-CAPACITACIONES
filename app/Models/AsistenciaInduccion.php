<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaInduccion extends Model
{
    protected $table = 'asistencias_induccion';

    protected $fillable = [
        'sesion_id',
        'usuario_id',
        'identificacion',
        'nombre_completo',
        'cargo',
        'area',
        'sede',
        'es_externo',
        'empresa_externa',
        'telefono',
        'firma_digital',
        'fecha_registro',
        'ip_origen',
    ];

    protected function casts(): array
    {
        return [
            'es_externo' => 'boolean',
            'fecha_registro' => 'datetime',
        ];
    }

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(SesionInduccion::class, 'sesion_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function getFirmaUrlAttribute(): ?string
    {
        if (empty($this->firma_digital)) {
            return null;
        }

        // Si ya es base64 data URI
        if (str_starts_with($this->firma_digital, 'data:image')) {
            return $this->firma_digital;
        }

        return asset('storage/' . $this->firma_digital);
    }
}
