<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'area_id');
    }

    public function plantillasCertificado(): HasMany
    {
        return $this->hasMany(PlantillaCertificado::class, 'area_id');
    }

    public function capacitaciones(): BelongsToMany
    {
        return $this->belongsToMany(Capacitacion::class, 'area_capacitacion', 'area_id', 'capacitacion_id');
    }
}
