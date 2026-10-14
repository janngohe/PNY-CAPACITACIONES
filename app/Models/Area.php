<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Mutador para formatear el nombre automáticamente con primera letra mayúscula por palabra.
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::formatearNombre($value),
        );
    }

    /**
     * Normaliza y aplica formato Title Case (primera letra mayúscula y resto minúscula en cada palabra).
     */
    public static function formatearNombre(?string $nombre): ?string
    {
        if ($nombre === null) {
            return null;
        }

        $limpio = preg_replace('/\s+/', ' ', trim($nombre));
        if ($limpio === '') {
            return '';
        }

        return mb_convert_case($limpio, MB_CASE_TITLE, 'UTF-8');
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
