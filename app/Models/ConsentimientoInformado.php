<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentimientoInformado extends Model
{
    protected $table = 'consentimientos_informados';

    protected $fillable = [
        'candidato_id',
        'ciudad_municipio',
        'fecha_consentimiento',
        'codigo_documental',
        'version_documental',
        'estado',
        'tipo_firma',
        'firma_digital_ruta',
        'ip_firma',
        'fecha_firma_digital',
        'ruta_archivo_docx',
        'ruta_archivo_pdf',
        'fecha_firma_fisica',
        'observaciones',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha_consentimiento' => 'date',
            'fecha_firma_fisica' => 'date',
            'fecha_firma_digital' => 'datetime',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    public function aplicaciones(): HasMany
    {
        return $this->hasMany(AplicacionPrueba::class, 'consentimiento_id');
    }

    /**
     * Determina si el consentimiento fue otorgado y firmado (física o digitalmente).
     */
    public function estaFirmado(): bool
    {
        return in_array($this->estado, ['FIRMADO_FISICO', 'FIRMADO_DIGITAL'], true);
    }

    /**
     * Etiqueta amigable de estado con colores de badge.
     */
    public function badgeEstado(): array
    {
        return match ($this->estado) {
            'GENERADO' => [
                'texto' => 'Documento Generado',
                'clase' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icono' => 'fa-file-circle-exclamation',
            ],
            'DESCARGADO' => [
                'texto' => 'Descargado (Pendiente Firma)',
                'clase' => 'bg-blue-50 text-blue-700 border-blue-200',
                'icono' => 'fa-file-arrow-down',
            ],
            'FIRMADO_FISICO' => [
                'texto' => 'Firmado Físicamente',
                'clase' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icono' => 'fa-file-signature',
            ],
            'FIRMADO_DIGITAL' => [
                'texto' => 'Firmado Digitalmente (QR)',
                'clase' => 'bg-teal-50 text-teal-700 border-teal-200',
                'icono' => 'fa-signature',
            ],
            'REVOCADO' => [
                'texto' => 'Revocado / Anulado',
                'clase' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icono' => 'fa-ban',
            ],
            default => [
                'texto' => $this->estado,
                'clase' => 'bg-slate-50 text-slate-700 border-slate-200',
                'icono' => 'fa-file',
            ],
        };
    }
}
