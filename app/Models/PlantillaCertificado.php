<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantillaCertificado extends Model
{
    protected $table = 'plantillas_certificado';

    protected $fillable = [
        'nombre',
        'descripcion',
        'area_id',
        'ruta_plantilla',
        'nombre_organizacion',
        'texto_certificado',
        'firma_1_nombre',
        'firma_1_cargo',
        'firma_1_ruta',
        'firma_2_nombre',
        'firma_2_cargo',
        'firma_2_ruta',
        'estado',
    ];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function capacitaciones(): HasMany
    {
        return $this->hasMany(Capacitacion::class, 'plantilla_certificado_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'plantilla_certificado_id');
    }
}
