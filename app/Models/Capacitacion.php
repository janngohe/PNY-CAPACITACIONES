<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capacitacion extends Model
{
    protected $table = 'capacitaciones';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'ruta_imagen',
        'porcentaje_aprobacion',
        'intentos_permitidos',
        'duracion_estimada',
        'incentivo',
        'fecha_disponibilidad',
        'fecha_limite',
        'plantilla_certificado_id',
        'creado_por',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje_aprobacion' => 'decimal:2',
            'fecha_disponibilidad' => 'datetime',
            'fecha_limite' => 'datetime',
            'estado' => 'boolean',
        ];
    }

    public function getUrlImagenAttribute(): string
    {
        $ruta = $this->ruta_imagen;

        if (empty($ruta)) {
            return asset('images/Img-login.jpg');
        }

        if (str_starts_with($ruta, 'http://') || str_starts_with($ruta, 'https://')) {
            return $ruta;
        }

        $rutaLimpia = ltrim($ruta, '/');

        return asset($rutaLimpia);
    }

    public function plantillaCertificado(): BelongsTo
    {
        return $this->belongsTo(PlantillaCertificado::class, 'plantilla_certificado_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function modulos(): HasMany
    {
        return $this->hasMany(Modulo::class, 'capacitacion_id')->orderBy('numero_seccion');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'capacitacion_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'area_capacitacion', 'capacitacion_id', 'area_id');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_capacitacion', 'capacitacion_id', 'usuario_id')
            ->withPivot(['fecha_asignacion', 'fecha_inicio', 'fecha_finalizacion', 'estado']);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(UsuarioCapacitacion::class, 'capacitacion_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'capacitacion_id');
    }
}
