<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EvaluacionCandidato extends Model
{
    protected $table = 'evaluaciones_candidatos';

    protected $fillable = [
        'codigo_sesion',
        'nombre_completo',
        'identificacion',
        'lugar_expedicion',
        'fecha_nacimiento',
        'edad',
        'cargo_aspira',
        'firma_digital',
        'acepta_terminos',
        'fecha_hora_aceptacion',
        'paso_actual',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_hora_aceptacion' => 'datetime',
            'acepta_terminos' => 'boolean',
            'edad' => 'integer',
            'paso_actual' => 'integer',
        ];
    }

    public function respuestasPaca(): HasMany
    {
        return $this->hasMany(RespuestaPaca::class, 'evaluacion_candidato_id')->orderBy('numero_pregunta');
    }

    public function resultadoPaca(): HasOne
    {
        return $this->hasOne(ResultadoPaca::class, 'evaluacion_candidato_id');
    }

    public function respuestasValanti(): HasMany
    {
        return $this->hasMany(RespuestaValanti::class, 'evaluacion_candidato_id')->orderBy('numero_pregunta');
    }

    public function resultadoValanti(): HasOne
    {
        return $this->hasOne(ResultadoValanti::class, 'evaluacion_candidato_id');
    }

    /**
     * La Prueba 1 (PAC-A) está finalizada cuando ya existe su resultado calificado.
     */
    public function pacaFinalizada(): bool
    {
        return $this->resultadoPaca !== null;
    }

    /**
     * La Prueba 2 (VALANTI) está finalizada cuando ya existe su resultado calificado.
     */
    public function valantiFinalizada(): bool
    {
        return $this->resultadoValanti !== null;
    }

    /**
     * Cantidad de preguntas PAC-A respondidas (usa withCount si fue cargado).
     */
    public function totalRespondidasPaca(): int
    {
        return (int) ($this->respuestas_paca_count ?? $this->respuestasPaca()->count());
    }

    public function progresoPaca(): float
    {
        return round(($this->totalRespondidasPaca() / 40) * 100, 2);
    }

    /**
     * Cantidad de preguntas VALANTI respondidas (usa withCount si fue cargado).
     */
    public function totalRespondidasValanti(): int
    {
        return (int) ($this->respuestas_valanti_count ?? $this->respuestasValanti()->count());
    }

    public function progresoValanti(): float
    {
        return round(($this->totalRespondidasValanti() / 30) * 100, 2);
    }

    /**
     * Estado derivado de las pruebas psicotécnicas (PAC-A + VALANTI).
     */
    public function estadoPaca(): string
    {
        if ($this->pacaFinalizada() && $this->valantiFinalizada()) {
            return 'FINALIZADA';
        }

        if ($this->pacaFinalizada() && !$this->valantiFinalizada()) {
            return 'EN_VALANTI';
        }

        return $this->totalRespondidasPaca() > 0 ? 'EN_CURSO' : 'PENDIENTE';
    }

    public function badgeEstadoPaca(): array
    {
        return match ($this->estadoPaca()) {
            'FINALIZADA' => [
                'texto' => '2/2 Pruebas Finalizadas',
                'clase' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'icono' => 'fa-circle-check',
            ],
            'EN_VALANTI' => [
                'texto' => 'PAC-A Lista · VALANTI (' . round($this->progresoValanti()) . '%)',
                'clase' => 'bg-sky-50 text-sky-700 border-sky-200',
                'icono' => 'fa-spinner fa-spin',
            ],
            'EN_CURSO' => [
                'texto' => 'En PAC-A (' . round($this->progresoPaca()) . '%)',
                'clase' => 'bg-amber-50 text-amber-700 border-amber-200',
                'icono' => 'fa-spinner fa-spin',
            ],
            default => [
                'texto' => 'Autorizado · Sin iniciar',
                'clase' => 'bg-slate-100 text-slate-700 border-slate-200',
                'icono' => 'fa-hourglass-half',
            ],
        };
    }
}
