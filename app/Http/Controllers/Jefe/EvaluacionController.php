<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\OpcionRespuesta;
use App\Models\Pregunta;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EvaluacionController extends Controller
{
    protected const TIPOS_PREGUNTA = ['UNICA', 'MULTIPLE', 'VERDADERO_FALSO'];

    /** Prefijo de rutas / layout del panel que usa este controlador (jefe | admin). */
    protected string $panel = 'jefe';

    public function index()
    {
        $usuario = $this->jefe();

        $evaluaciones = Evaluacion::query()
            ->whereHas('capacitacion', fn ($q) => $q->where('creado_por', $usuario->id))
            ->with('capacitacion')
            ->withCount(['preguntas', 'intentos'])
            ->orderByDesc('id')
            ->get();

        return view('jefe.evaluaciones.index', compact('usuario', 'evaluaciones'));
    }

    public function create(Request $request)
    {
        $usuario = $this->jefe();

        return view('jefe.evaluaciones.form', [
            'usuario' => $usuario,
            'panel' => $this->panel,
            'evaluacion' => null,
            'bloqueada' => false,
            'capacitaciones' => $this->capacitacionesDelJefe($usuario),
            'capacitacionSeleccionada' => $request->integer('capacitacion') ?: null,
            'preguntasForm' => $this->preguntasParaFormulario(null),
            'tiposPregunta' => self::TIPOS_PREGUNTA,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $usuario = $this->jefe();
        $datos = $this->validar($request, $usuario, false);

        DB::transaction(function () use ($datos) {
            $evaluacion = Evaluacion::create([
                'capacitacion_id' => $datos['capacitacion_id'],
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'] ?? null,
                'porcentaje_aprobacion' => $datos['porcentaje_aprobacion'],
                'intentos_permitidos' => $datos['intentos_permitidos'],
                'estado' => true,
            ]);

            $this->sincronizarPreguntas($evaluacion, $datos['preguntas']);
        });

        return redirect()->route($this->panel . '.evaluaciones.index')
            ->with('success', 'Evaluación creada correctamente.');
    }

    public function edit(Evaluacion $evaluacion)
    {
        $usuario = $this->jefe();
        $this->autorizar($evaluacion, $usuario);

        return view('jefe.evaluaciones.form', [
            'usuario' => $usuario,
            'panel' => $this->panel,
            'evaluacion' => $evaluacion,
            'bloqueada' => $evaluacion->intentos()->exists(),
            'capacitaciones' => $this->capacitacionesDelJefe($usuario),
            'capacitacionSeleccionada' => $evaluacion->capacitacion_id,
            'preguntasForm' => $this->preguntasParaFormulario($evaluacion),
            'tiposPregunta' => self::TIPOS_PREGUNTA,
        ]);
    }

    public function update(Request $request, Evaluacion $evaluacion): RedirectResponse
    {
        $usuario = $this->jefe();
        $this->autorizar($evaluacion, $usuario);

        // Si ya existen intentos, las preguntas quedan congeladas para no alterar los resultados históricos
        $bloqueada = $evaluacion->intentos()->exists();
        $datos = $this->validar($request, $usuario, $bloqueada);

        DB::transaction(function () use ($datos, $evaluacion, $bloqueada) {
            $evaluacion->update([
                'capacitacion_id' => $datos['capacitacion_id'],
                'titulo' => $datos['titulo'],
                'descripcion' => $datos['descripcion'] ?? null,
                'porcentaje_aprobacion' => $datos['porcentaje_aprobacion'],
                'intentos_permitidos' => $datos['intentos_permitidos'],
            ]);

            if (! $bloqueada) {
                $this->sincronizarPreguntas($evaluacion, $datos['preguntas']);
            }
        });

        return redirect()->route($this->panel . '.evaluaciones.index')
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    /**
     * Las evaluaciones no se eliminan: solo se activan o desactivan.
     */
    public function toggleEstado(Evaluacion $evaluacion): RedirectResponse
    {
        $this->autorizar($evaluacion, $this->jefe());

        $evaluacion->update(['estado' => ! $evaluacion->estado]);

        return back()->with('success', $evaluacion->estado
            ? 'Evaluación activada.'
            : 'Evaluación desactivada: los empleados ya no podrán presentarla (se conservan los resultados).');
    }

    /* ------------------------------------------------------------------ */

    protected function jefe(): Usuario
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return $usuario;
    }

    protected function autorizar(Evaluacion $evaluacion, Usuario $usuario): void
    {
        $evaluacion->loadMissing('capacitacion');

        abort_unless(
            $evaluacion->capacitacion && (int) $evaluacion->capacitacion->creado_por === (int) $usuario->id,
            403,
            'Esta evaluación pertenece a una capacitación que no publicaste.'
        );
    }

    protected function capacitacionesDelJefe(Usuario $usuario)
    {
        return $usuario->capacitacionesCreadas()->orderBy('titulo')->get(['id', 'titulo', 'estado']);
    }

    protected function reglaCapacitacion(Usuario $usuario)
    {
        return Rule::exists('capacitaciones', 'id')->where('creado_por', $usuario->id);
    }

    protected function validar(Request $request, Usuario $usuario, bool $bloqueada): array
    {
        $reglas = [
            'capacitacion_id' => ['required', 'integer', $this->reglaCapacitacion($usuario)],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'porcentaje_aprobacion' => ['required', 'numeric', 'between:1,100'],
            'intentos_permitidos' => ['required', 'integer', 'between:1,20'],
        ];

        if (! $bloqueada) {
            $reglas += [
                'preguntas' => ['required', 'array', 'min:1'],
                'preguntas.*.id' => ['nullable', 'integer'],
                'preguntas.*.pregunta' => ['required', 'string', 'max:3000'],
                'preguntas.*.tipo' => ['required', 'in:' . implode(',', self::TIPOS_PREGUNTA)],
                'preguntas.*.opciones' => ['required', 'array', 'min:2'],
                'preguntas.*.opciones.*.id' => ['nullable', 'integer'],
                'preguntas.*.opciones.*.texto' => ['required', 'string', 'max:2000'],
                'preguntas.*.opciones.*.es_correcta' => ['nullable', 'boolean'],
            ];
        }

        $datos = $request->validate($reglas, [
            'capacitacion_id.required' => 'Selecciona la capacitación a la que pertenece la evaluación.',
            'capacitacion_id.exists' => 'Solo puedes crear evaluaciones para capacitaciones que tú publicaste.',
            'titulo.required' => 'El título de la evaluación es obligatorio.',
            'porcentaje_aprobacion.between' => 'El porcentaje de aprobación debe estar entre 1 y 100.',
            'intentos_permitidos.between' => 'Los intentos permitidos deben estar entre 1 y 20.',
            'preguntas.required' => 'Agrega al menos una pregunta.',
            'preguntas.min' => 'Agrega al menos una pregunta.',
            'preguntas.*.pregunta.required' => 'Cada pregunta debe tener su enunciado.',
            'preguntas.*.opciones.required' => 'Cada pregunta necesita opciones de respuesta.',
            'preguntas.*.opciones.min' => 'Cada pregunta debe tener al menos 2 opciones.',
            'preguntas.*.opciones.*.texto.required' => 'Ninguna opción puede quedar vacía.',
        ]);

        if (! $bloqueada) {
            $errores = [];
            foreach ($datos['preguntas'] as $key => $pregunta) {
                $correctas = collect($pregunta['opciones'])->filter(fn ($o) => ! empty($o['es_correcta']))->count();
                $numero = $this->posicion($datos['preguntas'], $key);

                if ($pregunta['tipo'] === 'VERDADERO_FALSO' && count($pregunta['opciones']) !== 2) {
                    $errores["preguntas.$key.opciones"] = "La pregunta $numero (Verdadero/Falso) debe tener exactamente 2 opciones.";
                }

                if (in_array($pregunta['tipo'], ['UNICA', 'VERDADERO_FALSO'], true) && $correctas !== 1) {
                    $errores["preguntas.$key.opciones"] = "La pregunta $numero debe tener exactamente 1 respuesta correcta.";
                }

                if ($pregunta['tipo'] === 'MULTIPLE' && $correctas < 1) {
                    $errores["preguntas.$key.opciones"] = "La pregunta $numero debe tener al menos 1 respuesta correcta.";
                }
            }

            if ($errores) {
                throw ValidationException::withMessages($errores);
            }
        }

        return $datos;
    }

    protected function posicion(array $items, int|string $key): int
    {
        return array_search($key, array_keys($items), true) + 1;
    }

    protected function sincronizarPreguntas(Evaluacion $evaluacion, array $preguntasForm): void
    {
        $existentes = $evaluacion->preguntas()->get()->keyBy('id');
        $mantenidas = [];

        $orden = 0;
        foreach ($preguntasForm as $datos) {
            $orden++;
            $pregunta = isset($datos['id']) ? $existentes->get((int) $datos['id']) : null;

            $atributos = [
                'pregunta' => $datos['pregunta'],
                'tipo' => $datos['tipo'],
                'orden' => $orden,
            ];

            if ($pregunta) {
                $pregunta->update($atributos);
            } else {
                $pregunta = $evaluacion->preguntas()->create($atributos);
            }

            $mantenidas[] = $pregunta->id;
            $this->sincronizarOpciones($pregunta, $datos['opciones']);
        }

        // Sin intentos registrados es seguro retirar preguntas (la edición se bloquea si hay intentos)
        $evaluacion->preguntas()->whereNotIn('id', $mantenidas)->delete();
    }

    protected function sincronizarOpciones(Pregunta $pregunta, array $opcionesForm): void
    {
        $existentes = $pregunta->opciones()->get()->keyBy('id');
        $mantenidas = [];

        foreach ($opcionesForm as $datos) {
            $opcion = isset($datos['id']) ? $existentes->get((int) $datos['id']) : null;

            $atributos = [
                'texto' => $datos['texto'],
                'es_correcta' => ! empty($datos['es_correcta']),
            ];

            if ($opcion) {
                $opcion->update($atributos);
            } else {
                $opcion = $pregunta->opciones()->create($atributos);
            }

            $mantenidas[] = $opcion->id;
        }

        OpcionRespuesta::where('pregunta_id', $pregunta->id)->whereNotIn('id', $mantenidas)->delete();
    }

    protected function preguntasParaFormulario(?Evaluacion $evaluacion): array
    {
        $old = old('preguntas');
        if (is_array($old)) {
            return $old;
        }

        if (! $evaluacion) {
            return [[
                'id' => null,
                'pregunta' => '',
                'tipo' => 'UNICA',
                'opciones' => [
                    ['id' => null, 'texto' => '', 'es_correcta' => true],
                    ['id' => null, 'texto' => '', 'es_correcta' => false],
                ],
            ]];
        }

        return $evaluacion->preguntas()->with('opciones')->get()
            ->map(fn (Pregunta $p) => [
                'id' => $p->id,
                'pregunta' => $p->pregunta,
                'tipo' => $p->tipo,
                'opciones' => $p->opciones->map(fn (OpcionRespuesta $o) => [
                    'id' => $o->id,
                    'texto' => $o->texto,
                    'es_correcta' => (bool) $o->es_correcta,
                ])->all(),
            ])->all();
    }
}
