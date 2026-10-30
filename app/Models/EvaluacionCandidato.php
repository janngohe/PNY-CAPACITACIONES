<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}

