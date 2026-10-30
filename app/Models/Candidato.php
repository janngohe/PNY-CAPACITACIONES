<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    protected $table = 'candidatos';

    protected $fillable = [
        'nombre_completo',
        'numero_identificacion',
        'lugar_expedicion',
        'ciudad_municipio',
        'telefono',
        'email',
        'cargo_postulado',
        'token_acceso',
        'origen_registro',
        'creado_por',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function consentimientos(): HasMany
    {
        return $this->hasMany(ConsentimientoInformado::class, 'candidato_id')->orderByDesc('id');
    }

    public function aplicaciones(): HasMany
    {
        return $this->hasMany(AplicacionPrueba::class, 'candidato_id')->orderByDesc('id');
    }

    /**
     * Retorna el último consentimiento informado válido (firmado físicamente o digitalmente).
     */
    public function consentimientoActivo(): ?ConsentimientoInformado
    {
        return $this->consentimientos()
            ->whereIn('estado', ['FIRMADO_FISICO', 'FIRMADO_DIGITAL'])
            ->first();
    }

    /**
     * Retorna el último consentimiento generado o en trámite.
     */
    public function ultimoConsentimiento(): ?ConsentimientoInformado
    {
        return $this->consentimientos()->first();
    }

    /**
     * Obtiene o genera un token seguro único para el acceso del evaluado vía QR.
     */
    public function asegurarTokenAcceso(): string
    {
        if (empty($this->token_acceso)) {
            $this->token_acceso = bin2hex(random_bytes(24));
            $this->save();
        }
        return $this->token_acceso;
    }
}
