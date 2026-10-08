<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'identificacion',
        'nombre_completo',
        'password',
        'rol',
        'area_id',
        'usuario_nuevo',
        'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'usuario_nuevo' => 'boolean',
            'estado' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function capacitaciones(): BelongsToMany
    {
        return $this->belongsToMany(Capacitacion::class, 'usuario_capacitacion', 'usuario_id', 'capacitacion_id')
            ->withPivot(['fecha_asignacion', 'fecha_inicio', 'fecha_finalizacion', 'estado']);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(UsuarioCapacitacion::class, 'usuario_id');
    }

    public function progresoModulos(): HasMany
    {
        return $this->hasMany(ProgresoModulo::class, 'usuario_id');
    }

    public function intentosEvaluacion(): HasMany
    {
        return $this->hasMany(IntentoEvaluacion::class, 'usuario_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'usuario_id');
    }

    public function certificadosExternos(): HasMany
    {
        return $this->hasMany(CertificadoExterno::class, 'usuario_id');
    }
}
