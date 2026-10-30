<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoValanti extends Model
{
    protected $table = 'valanti_resultados';

    protected $fillable = [
        'evaluacion_candidato_id',
        'pd_verdad',
        'pd_rectitud',
        'pd_paz',
        'pd_amor',
        'pd_no_violencia',
        'pd_total',
        'pe_verdad',
        'pe_rectitud',
        'pe_paz',
        'pe_amor',
        'pe_no_violencia',
        'nivel_verdad',
        'nivel_rectitud',
        'nivel_paz',
        'nivel_amor',
        'nivel_no_violencia',
        'perfil_empresa_verdad',
        'perfil_empresa_rectitud',
        'perfil_empresa_paz',
        'perfil_empresa_amor',
        'perfil_empresa_no_violencia',
        'distancia_verdad',
        'distancia_rectitud',
        'distancia_paz',
        'distancia_amor',
        'distancia_no_violencia',
        'finalizada_at',
    ];

    protected function casts(): array
    {
        return [
            'pd_verdad' => 'integer',
            'pd_rectitud' => 'integer',
            'pd_paz' => 'integer',
            'pd_amor' => 'integer',
            'pd_no_violencia' => 'integer',
            'pd_total' => 'integer',
            'pe_verdad' => 'integer',
            'pe_rectitud' => 'integer',
            'pe_paz' => 'integer',
            'pe_amor' => 'integer',
            'pe_no_violencia' => 'integer',
            'perfil_empresa_verdad' => 'integer',
            'perfil_empresa_rectitud' => 'integer',
            'perfil_empresa_paz' => 'integer',
            'perfil_empresa_amor' => 'integer',
            'perfil_empresa_no_violencia' => 'integer',
            'distancia_verdad' => 'integer',
            'distancia_rectitud' => 'integer',
            'distancia_paz' => 'integer',
            'distancia_amor' => 'integer',
            'distancia_no_violencia' => 'integer',
            'finalizada_at' => 'datetime',
        ];
    }

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionCandidato::class, 'evaluacion_candidato_id');
    }

    /**
     * Devuelve la dimensión con mayor Puntaje Estándar (PE).
     */
    public function dimensionPredominante(): array
    {
        $dimensiones = [
            'Verdad' => $this->pe_verdad,
            'Rectitud' => $this->pe_rectitud,
            'Paz' => $this->pe_paz,
            'Amor' => $this->pe_amor,
            'No Violencia' => $this->pe_no_violencia,
        ];
        arsort($dimensiones);
        $nombre = array_key_first($dimensiones);

        return [
            'nombre' => $nombre,
            'pe' => $dimensiones[$nombre],
        ];
    }
}

