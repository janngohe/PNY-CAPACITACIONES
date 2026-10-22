<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SesionInduccion extends Model
{
    protected $table = 'sesiones_induccion';

    protected $fillable = [
        'induccion_id',
        'facilitador_id',
        'sede_id',
        'codigo_sesion',
        'estado',
        'fecha_inicio',
        'fecha_cierre',
        'areas_convocadas',
        'permitir_externos',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_cierre' => 'datetime',
            'areas_convocadas' => 'array',
            'permitir_externos' => 'boolean',
        ];
    }

    public function induccion(): BelongsTo
    {
        return $this->belongsTo(Induccion::class, 'induccion_id');
    }

    public function facilitador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'facilitador_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(AsistenciaInduccion::class, 'sesion_id');
    }

    public function estaActiva(): bool
    {
        return $this->estado === 'EN_CURSO';
    }

    public function getUrlAsistenciaAttribute(): string
    {
        return route('induccion.asistencia.form', ['codigo' => $this->codigo_sesion]);
    }

    /**
     * Retorna los nombres de las áreas convocadas como cadena separada por comas.
     */
    public function getAreasConvocadasNombresAttribute(): string
    {
        if (empty($this->areas_convocadas) || !is_array($this->areas_convocadas)) {
            return 'Todas las áreas convocadas';
        }

        $nombres = Area::whereIn('id', $this->areas_convocadas)->pluck('nombre')->toArray();
        return !empty($nombres) ? implode(', ', $nombres) : 'Todas las áreas convocadas';
    }

    /**
     * Retorna los nombres de las áreas convocadas como arreglo.
     */
    public function getAreasConvocadasNombresArrayAttribute(): array
    {
        if (empty($this->areas_convocadas) || !is_array($this->areas_convocadas)) {
            return [];
        }

        return Area::whereIn('id', $this->areas_convocadas)->pluck('nombre')->toArray();
    }

    /**
     * Genera un código de sesión único, legible y corto (ej. IND-7429).
     */
    public static function generarCodigoUnico(): string
    {
        do {
            $codigo = 'IND-' . strtoupper(Str::random(4));
        } while (static::where('codigo_sesion', $codigo)->exists());

        return $codigo;
    }
}
