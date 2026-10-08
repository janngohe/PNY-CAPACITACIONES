<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificadoExterno extends Model
{
    protected $table = 'certificados_externos';

    protected $fillable = [
        'usuario_id',
        'entidad_emisora',
        'fecha_emision',
        'ruta_archivo',
        'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_emision' => 'date'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
