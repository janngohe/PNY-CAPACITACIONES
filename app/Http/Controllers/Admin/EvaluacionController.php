<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Jefe\EvaluacionController as JefeEvaluacionController;
use App\Models\Capacitacion;
use App\Models\Evaluacion;
use App\Models\Usuario;
use Illuminate\Validation\Rule;

/**
 * Evaluaciones, preguntas y respuestas correctas de TODAS las capacitaciones.
 * Reutiliza el editor del Jefe de Área sin la restricción de autoría.
 */
class EvaluacionController extends JefeEvaluacionController
{
    protected string $panel = 'admin';

    public function index()
    {
        $evaluaciones = Evaluacion::query()
            ->with('capacitacion')
            ->withCount(['preguntas', 'intentos'])
            ->orderByDesc('id')
            ->get();

        return view('jefe.evaluaciones.index', [
            'usuario' => $this->jefe(),
            'panel' => $this->panel,
            'evaluaciones' => $evaluaciones,
        ]);
    }

    protected function autorizar(Evaluacion $evaluacion, Usuario $usuario): void
    {
        // El administrador puede gestionar cualquier evaluación
    }

    protected function capacitacionesDelJefe(Usuario $usuario)
    {
        return Capacitacion::orderBy('titulo')->get(['id', 'titulo', 'estado']);
    }

    protected function reglaCapacitacion(Usuario $usuario)
    {
        return Rule::exists('capacitaciones', 'id');
    }
}
