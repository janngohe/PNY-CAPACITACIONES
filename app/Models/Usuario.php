<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;

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
        'sede_id',
        'firma_digital',
        'cargo',
        'firma_actualizada_el',
        'puntos_saldo',
        'usuario_nuevo',
        'estado',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'puntos_saldo' => 'integer',
            'usuario_nuevo' => 'boolean',
            'estado' => 'boolean',
            'firma_actualizada_el' => 'datetime',
        ];
    }

    /**
     * Determina si el usuario tiene una firma digital registrada en su perfil.
     */
    public function tieneFirmaRegistrada(): bool
    {
        return !empty($this->firma_digital);
    }

    /**
     * Retorna la URL pública de la firma digital almacenada.
     */
    public function firmaUrl(): ?string
    {
        return $this->firma_digital ? asset('storage/' . $this->firma_digital) : null;
    }

    /**
     * Retorna el cargo oficial del usuario (personalizado o deducido de su área).
     */
    public function cargoOficial(): string
    {
        if (!empty($this->cargo)) {
            return $this->cargo;
        }

        if ($this->rol === 'PSICOLOGIA') {
            return 'Especialista en Psicología';
        }

        if ($this->rol === 'JEFE_AREA') {
            return 'Jefe de Área ' . ($this->area?->nombre ?? '');
        }

        if ($this->rol === 'ADMINISTRADOR') {
            return 'Administrador del Sistema';
        }

        return 'Colaborador ' . ($this->area?->nombre ?? '');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Retorna la sede asignada directamente o deducida a través de su área institucional.
     */
    public function sedeActual(): ?Sede
    {
        return $this->sede ?? $this->area?->sede;
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

    /**
     * Capacitaciones publicadas por este usuario (Jefe de Área).
     */
    public function capacitacionesCreadas(): HasMany
    {
        return $this->hasMany(Capacitacion::class, 'creado_por');
    }

    /**
     * Nombre de la ruta del panel principal según el rol.
     * Si el panel del rol aún no existe, cae en el panel del empleado.
     */
    public function rutaPanel(): string
    {
        $ruta = match ($this->rol) {
            'ADMINISTRADOR' => 'admin.dashboard',
            'JEFE_AREA' => 'jefe.dashboard',
            'PSICOLOGIA' => 'psicologia.portal_seleccion',
            default => 'empleado.capacitaciones',
        };

        return Route::has($ruta) ? $ruta : 'empleado.capacitaciones';
    }

    public function esPsicologia(): bool
    {
        return $this->rol === 'PSICOLOGIA';
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

    public function movimientosPuntos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class, 'usuario_id')->orderByDesc('id');
    }

    public function canjesBeneficios(): HasMany
    {
        return $this->hasMany(CanjeBeneficio::class, 'usuario_id')->orderByDesc('id');
    }
}
