<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficio extends Model
{
    protected $table = 'beneficios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'puntos_requeridos',
        'estado',
        'icono',
    ];

    protected $casts = [
        'puntos_requeridos' => 'integer',
        'estado' => 'boolean',
    ];

    public function canjes(): HasMany
    {
        return $this->hasMany(CanjeBeneficio::class, 'beneficio_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoPuntos::class, 'beneficio_id');
    }

    /**
     * Determina el estado del beneficio para un usuario específico.
     * Retorna: 'CANJEADO', 'SOLICITADO', 'DISPONIBLE' o 'PENDIENTE'.
     */
    public function estadoParaUsuario(Usuario $usuario): string
    {
        $canje = $this->canjes()
            ->where('usuario_id', $usuario->id)
            ->first();

        if ($canje) {
            return $canje->estado; // 'CANJEADO' o 'SOLICITADO'
        }

        if (($usuario->puntos_saldo ?? 0) >= $this->puntos_requeridos) {
            return 'DISPONIBLE';
        }

        return 'PENDIENTE';
    }

    /**
     * Porcentaje de progreso de un usuario hacia este beneficio (0 a 100).
     */
    public function porcentajeProgreso(Usuario $usuario): int
    {
        $estado = $this->estadoParaUsuario($usuario);
        if ($estado === 'CANJEADO' || $estado === 'SOLICITADO') {
            return 100;
        }

        if ($this->puntos_requeridos <= 0) {
            return 100;
        }

        $saldo = max(0, (int) ($usuario->puntos_saldo ?? 0));
        return (int) min(100, round(($saldo / $this->puntos_requeridos) * 100));
    }
}
