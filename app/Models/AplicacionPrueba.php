<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AplicacionPrueba extends Model
{
    protected $table = 'aplicaciones_pruebas';

    protected $fillable = [
        'candidato_id',
        'consentimiento_id',
        'prueba_codigo',
        'edad_evaluado',
        'fecha_aplicacion',
        'hora_inicio',
        'hora_finalizacion',
        'estado',
        'progreso_porcentaje',
        'usuario_responsable_id',
        'observaciones',
        'token_sesion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_aplicacion' => 'date',
            'hora_inicio' => 'datetime',
            'hora_finalizacion' => 'datetime',
            'progreso_porcentaje' => 'float',
            'edad_evaluado' => 'integer',
        ];
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }

    public function consentimiento(): BelongsTo
    {
        return $this->belongsTo(ConsentimientoInformado::class, 'consentimiento_id');
    }


    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsable_id');
    }

    public function respuestasPaca(): HasMany
    {
        return $this->hasMany(RespuestaPaca::class, 'aplicacion_id')->orderBy('numero_pregunta');
    }

    public function resultadoPaca(): HasOne
    {
        return $this->hasOne(ResultadoPaca::class, 'aplicacion_id');
    }

    public function estaFinalizada(): bool
    {
        return $this->estado === 'FINALIZADA';
    }

    public function badgeEstado(): array
    {
        return match ($this->estado) {
            'BORRADOR' => [
                'texto' => 'Borrador Guardado',
                'clase' => 'bg-slate-100 text-slate-700 border-slate-200',
                'icono' => 'fa-pencil',
            ],
            'EN_CURSO' => [
                'texto' => 'En Curso (' . round($this->progreso_porcentaje) . '%)',
                'clase' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icono' => 'fa-spinner fa-spin',
            ],
            'FINALIZADA' => [
                'texto' => 'Finalizada y Calificada',
                'clase' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icono' => 'fa-circle-check',
            ],
            'ANULADA' => [
                'texto' => 'Anulada',
                'clase' => 'bg-rose-50 text-rose-700 border-rose-200',
                'icono' => 'fa-ban',
            ],
            default => [
                'texto' => $this->estado,
                'clase' => 'bg-slate-50 text-slate-700 border-slate-200',
                'icono' => 'fa-circle-info',
            ],
        };
    }
}
