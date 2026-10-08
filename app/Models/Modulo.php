<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = [
        'capacitacion_id',
        'titulo',
        'descripcion',
        'numero_seccion',
        'ruta_imagen',
        'estado',
    ];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }

    public function contenidos(): HasMany
    {
        return $this->hasMany(Contenido::class, 'modulo_id')->orderBy('orden');
    }

    public function progresos(): HasMany
    {
        return $this->hasMany(ProgresoModulo::class, 'modulo_id');
    }
}
