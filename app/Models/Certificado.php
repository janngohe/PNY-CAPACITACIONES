<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificado extends Model
{
    protected $table = 'certificados';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'capacitacion_id',
        'plantilla_certificado_id',
        'codigo',
        'nombre_empleado',
        'identificacion',
        'nombre_capacitacion',
        'area_nombre',
        'porcentaje',
        'fecha_emision',
        'ruta_archivo',
        'emitido_por',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'fecha_emision' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class, 'capacitacion_id');
    }

    public function plantillaCertificado(): BelongsTo
    {
        return $this->belongsTo(PlantillaCertificado::class, 'plantilla_certificado_id');
    }
}
