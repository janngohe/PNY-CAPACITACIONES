<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Sede extends Model
{
    protected $table = 'sedes';

    protected $fillable = [
        'nombre',
        'ciudad',
        'direccion',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /**
     * Mutador para formatear el nombre con Title Case automático.
     */
    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::formatearNombre($value),
        );
    }

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

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'sede_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'sede_id');
    }

    /**
     * Usuarios asignados directa o indirectamente (a través de sus áreas).
     */
    public function todosLosUsuarios()
    {
        return Usuario::where('sede_id', $this->id)
            ->orWhereIn('area_id', $this->areas()->pluck('id'));
    }
}
