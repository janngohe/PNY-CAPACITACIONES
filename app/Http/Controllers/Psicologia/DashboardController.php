<?php

namespace App\Http\Controllers\Psicologia;

use App\Constants\PacaPreguntas;
use App\Constants\ValantiPreguntas;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\EvaluacionCandidato;
use App\Models\RespuestaPaca;
use App\Models\RespuestaValanti;
use App\Models\Usuario;
use App\Services\Psicologia\CalificadorPacaService;
use App\Services\Psicologia\CalificadorValantiService;
use App\Services\Psicologia\ReporteExcelPsicologiaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Pantalla de selección de entorno:
     * - Panel de Psicología
     * - Portal de Capacitaciones (Modo Colaborador)
     */
    public function portalSeleccion()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.portal_seleccion', compact('usuario'));
    }

    /**
     * Panel Principal de Psicología (Visión general de los 4 módulos).
     */
    public function index()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.dashboard', compact('usuario'));
    }

    /**
     * Módulo 1: Realizar Prueba (Generación del QR para el evaluado).
     */
    public function realizarPrueba(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        // Código institucional único y fijo para el QR de evaluación psicológica
        $codigoSesion = 'PSI-PNY';

        // 1. Detección automática de túnel Ngrok si está activo en desarrollo (puerto 4040)
        $urlNgrokBase = null;
        try {
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT_MS => 300,
                CURLOPT_CONNECTTIMEOUT_MS => 200,
            ]);
            $ngrokRes = curl_exec($ch);
            curl_close($ch);
            if ($ngrokRes) {
                $ngrokData = json_decode($ngrokRes, true);
                foreach ($ngrokData['tunnels'] ?? [] as $tun) {
                    if (!empty($tun['public_url']) && str_starts_with($tun['public_url'], 'https://')) {
                        $urlNgrokBase = rtrim($tun['public_url'], '/');
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ngrok no se está ejecutando localmente
        }

        if (!$urlNgrokBase && str_contains($request->getHost(), 'ngrok')) {
            $urlNgrokBase = $request->getSchemeAndHttpHost();
        }

        // 2. Detección de IP Local WiFi
        $localIp = gethostbyname(gethostname());
        $puerto = $request->getPort();
        $puertoStr = ($puerto && !in_array($puerto, [80, 443])) ? ':' . $puerto : '';

        // 3. URLs completas del formulario que escaneará el usuario en su móvil
        $rutaEvaluacion = '/evaluacion-psicologica/' . $codigoSesion;
        $urlEvaluacionActual = url($rutaEvaluacion);
        $urlEvaluacionLocal = 'http://' . $localIp . $puertoStr . $rutaEvaluacion;
        $urlEvaluacionNgrok = $urlNgrokBase ? ($urlNgrokBase . $rutaEvaluacion) : null;

        return view('psicologia.realizar_prueba', compact(
            'usuario',
            'codigoSesion',
            'urlEvaluacionActual',
            'urlEvaluacionLocal',
            'urlEvaluacionNgrok',
            'urlNgrokBase',
            'localIp'
        ));
    }

    /**
     * Paso 1: Vista pública que abre la persona evaluada al escanear el QR desde su celular.
     */
    public function evaluacionPublica(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? 'PSI-PNY';
        $fechaHoraActual = now();
        $areas = Area::where('estado', true)->orderBy('nombre')->get()->unique('nombre');

        return view('psicologia.evaluacion_candidato', compact('codigo', 'fechaHoraActual', 'areas'));
    }

    /**
     * Permite que un candidato/empleado que cerró su navegador o escaneó de nuevo el QR
     * continúe exactamente en la prueba donde quedó ingresando su número de cédula.
     */
    public function retomarEvaluacionPorCedula(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? 'PSI-PNY';
        $identificacion = trim((string) $request->input('identificacion_retomar', ''));

        if ($identificacion === '') {
            return back()->with('error_retomar', 'Ingresa tu número de cédula para buscar tu prueba en curso.');
        }

        $evaluacion = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->where('identificacion', $identificacion)
            ->latest('id')
            ->first();

        if (!$evaluacion) {
            return back()->with('error_retomar', "No se encontró ninguna evaluación iniciada con la cédula {$identificacion}. Completa el formulario de autorización abajo para iniciar.");
        }

        session(['evaluacion_candidato_id' => $evaluacion->id]);

        if (!$evaluacion->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso2', [
                'codigo' => $evaluacion->codigo_sesion ?: $codigo,
                'evaluacion' => $evaluacion->id,
            ]);
        }

        if (!$evaluacion->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso3', [
                'codigo' => $evaluacion->codigo_sesion ?: $codigo,
                'evaluacion' => $evaluacion->id,
            ]);
        }

        return redirect()->route('psicologia.evaluacion.completada', [
            'codigo' => $evaluacion->codigo_sesion ?: $codigo,
            'evaluacion' => $evaluacion->id,
        ]);
    }

    /**
     * Procesa y almacena la Autorización firmada por el candidato (Paso 1) y redirige a la Prueba 1 (VALANTI).
     * Si el candidato ya tenía una prueba en progreso sin finalizar con esa misma cédula, la retoma automáticamente.
     */
    public function guardarConsentimiento(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? 'PSI-PNY';

        $validados = $request->validate([
            'nombre_completo' => ['required', 'string', 'min:3', 'max:150'],
            'identificacion' => ['required', 'string', 'min:4', 'max:30'],
            'lugar_expedicion' => ['required', 'string', 'min:2', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'edad' => ['required', 'integer', 'min:16', 'max:99'],
            'cargo_aspira' => ['required', 'string', 'min:2', 'max:120'],
            'firma_digital' => ['required', 'string'],
            'acepta_terminos' => ['required', 'accepted'],
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'identificacion.required' => 'El número de cédula de ciudadanía es obligatorio.',
            'lugar_expedicion.required' => 'El lugar de expedición de la cédula es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'edad.required' => 'La edad es obligatoria.',
            'edad.min' => 'La edad mínima permitida es 16 años.',
            'cargo_aspira.required' => 'Debes seleccionar el área a la que aspiras.',
            'firma_digital.required' => 'Debes dibujar tu firma digital en el recuadro.',
            'acepta_terminos.accepted' => 'Debes marcar la casilla de aceptación para continuar.',
        ]);

        // Si ya tiene una evaluación en progreso (incompleta) con esta cédula, retomarla sin duplicar
        $enCurso = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->where('identificacion', trim($validados['identificacion']))
            ->where(function ($q) {
                $q->whereDoesntHave('resultadoValanti')
                  ->orWhereDoesntHave('resultadoPaca');
            })
            ->latest('id')
            ->first();

        if ($enCurso) {
            session(['evaluacion_candidato_id' => $enCurso->id]);
            if (!$enCurso->valantiFinalizada()) {
                return redirect()->route('psicologia.evaluacion.paso2', [
                    'codigo' => $enCurso->codigo_sesion ?: $codigo,
                    'evaluacion' => $enCurso->id,
                ]);
            }
            if (!$enCurso->pacaFinalizada()) {
                return redirect()->route('psicologia.evaluacion.paso3', [
                    'codigo' => $enCurso->codigo_sesion ?: $codigo,
                    'evaluacion' => $enCurso->id,
                ]);
            }
            return redirect()->route('psicologia.evaluacion.completada', [
                'codigo' => $enCurso->codigo_sesion ?: $codigo,
                'evaluacion' => $enCurso->id,
            ]);
        }

        $firmaData = $validados['firma_digital'];
        $rutaFirma = $firmaData;

        if (preg_match('/^data:image\/(\w+);base64,/', $firmaData, $type)) {
            $data = substr($firmaData, strpos($firmaData, ',') + 1);
            $decoded = base64_decode($data);
            if ($decoded !== false) {
                $nombreArchivo = 'firma_autorizacion_' . preg_replace('/[^A-Za-z0-9_-]/', '', $codigo) . '_' . preg_replace('/[^A-Za-z0-9_-]/', '', $validados['identificacion']) . '_' . time() . '.png';
                $rutaRelativa = 'firmas_psicologia/' . $nombreArchivo;
                Storage::disk('public')->put($rutaRelativa, $decoded);
                $rutaFirma = $rutaRelativa;
            }
        }

        $edadCalculada = \Carbon\Carbon::parse($validados['fecha_nacimiento'])->age;

        $evaluacion = EvaluacionCandidato::create([
            'codigo_sesion' => $codigo,
            'nombre_completo' => mb_strtoupper(trim($validados['nombre_completo']), 'UTF-8'),
            'identificacion' => trim($validados['identificacion']),
            'lugar_expedicion' => mb_convert_case(trim($validados['lugar_expedicion']), MB_CASE_TITLE, 'UTF-8'),
            'fecha_nacimiento' => $validados['fecha_nacimiento'],
            'edad' => $edadCalculada > 0 ? $edadCalculada : (int) $validados['edad'],
            'cargo_aspira' => mb_convert_case(trim($validados['cargo_aspira']), MB_CASE_TITLE, 'UTF-8'),
            'firma_digital' => $rutaFirma,
            'acepta_terminos' => true,
            'fecha_hora_aceptacion' => now(),
            'paso_actual' => 2,
            'estado' => 'EN_PROGRESO',
        ]);

        session(['evaluacion_candidato_id' => $evaluacion->id]);

        return redirect()->route('psicologia.evaluacion.paso2', [
            'codigo' => $codigo,
            'evaluacion' => $evaluacion->id,
        ]);
    }

    /**
     * Paso 2: Primera Prueba Psicotécnica (Cuestionario VALANTI de 30 pares).
     */
    public function evaluacionPaso2(Request $request, string $codigo, ?EvaluacionCandidato $evaluacion = null)
    {
        $candidato = $evaluacion;

        if (!$candidato && session('evaluacion_candidato_id')) {
            $candidato = EvaluacionCandidato::find(session('evaluacion_candidato_id'));
        }

        if (!$candidato) {
            $candidato = EvaluacionCandidato::where('codigo_sesion', $codigo)->latest('id')->first();
        }

        if (!$candidato) {
            return redirect()->route('psicologia.evaluacion.publica', ['codigo' => $codigo]);
        }

        if ($candidato->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso3', [
                'codigo' => $codigo,
                'evaluacion' => $candidato->id,
            ]);
        }

        $preguntas = ValantiPreguntas::PREGUNTAS;
        $combinaciones = ValantiPreguntas::COMBINACIONES_VALIDAS;

        $respuestasGuardadas = [];
        foreach ($candidato->respuestasValanti()->get() as $resp) {
            $respuestasGuardadas[$resp->numero_pregunta] = "{$resp->puntos_a}-{$resp->puntos_b}";
        }

        return view('psicologia.evaluacion_paso2', compact(
            'codigo',
            'candidato',
            'preguntas',
            'combinaciones',
            'respuestasGuardadas'
        ));
    }

    /**
     * Auto-guarda el borrador de respuestas de VALANTI en tiempo real vía AJAX.
     */
    public function guardarBorradorValanti(Request $request, string $codigo, EvaluacionCandidato $evaluacion)
    {
        if ($evaluacion->valantiFinalizada()) {
            return response()->json(['error' => 'La prueba VALANTI ya ha sido finalizada.'], 403);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        DB::transaction(function () use ($evaluacion, $respuestasInput) {
            foreach ($respuestasInput as $numero => $parStr) {
                $numero = (int) $numero;
                $parStr = trim((string) $parStr);

                if ($numero >= 1 && $numero <= 30 && isset(ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr])) {
                    $par = ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr];
                    RespuestaValanti::updateOrCreate(
                        [
                            'evaluacion_candidato_id' => $evaluacion->id,
                            'numero_pregunta' => $numero,
                        ],
                        [
                            'puntos_a' => $par['a'],
                            'puntos_b' => $par['b'],
                        ]
                    );
                }
            }
        });

        $totalRespondidas = $evaluacion->respuestasValanti()->count();

        return response()->json([
            'ok' => true,
            'respondidas' => $totalRespondidas,
            'porcentaje' => round(($totalRespondidas / 30) * 100),
        ]);
    }

    /**
     * Valida los 30 pares de VALANTI (suma de cada fila = 3, suma total = 90),
     * califica mediante CalificadorValantiService y avanza al Paso 3 (PAC-A).
     */
    public function finalizarValanti(
        Request $request,
        string $codigo,
        EvaluacionCandidato $evaluacion,
        CalificadorValantiService $calificador
    ) {
        if ($evaluacion->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso3', [
                'codigo' => $codigo,
                'evaluacion' => $evaluacion->id,
            ]);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        foreach ($respuestasInput as $num => $parStr) {
            $num = (int) $num;
            $parStr = trim((string) $parStr);
            if ($num >= 1 && $num <= 30 && isset(ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr])) {
                $par = ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr];
                RespuestaValanti::updateOrCreate(
                    [
                        'evaluacion_candidato_id' => $evaluacion->id,
                        'numero_pregunta' => $num,
                    ],
                    [
                        'puntos_a' => $par['a'],
                        'puntos_b' => $par['b'],
                    ]
                );
            }
        }

        $respuestasDB = $evaluacion->respuestasValanti()->get()->keyBy('numero_pregunta');

        $faltantes = [];
        for ($i = 1; $i <= 30; $i++) {
            $r = $respuestasDB->get($i);
            if (!$r || !ValantiPreguntas::esCombinacionValida((int) $r->puntos_a, (int) $r->puntos_b)) {
                $faltantes[] = $i;
            }
        }

        if (!empty($faltantes)) {
            return back()
                ->withInput()
                ->with('error_validacion', 'Faltan por distribuir los 3 puntos en las siguientes preguntas: ' . implode(', ', $faltantes))
                ->with('preguntas_faltantes', $faltantes);
        }

        DB::transaction(function () use ($evaluacion, $calificador) {
            $calificador->calificarYAplicar($evaluacion);
            $evaluacion->update([
                'paso_actual' => 3,
            ]);
        });

        return redirect()->route('psicologia.evaluacion.paso3', [
            'codigo' => $codigo,
            'evaluacion' => $evaluacion->id,
        ]);
    }

    /**
     * Paso 3: Segunda Prueba Psicotécnica (Cuestionario PAC-A de 40 afirmaciones).
     */
    public function evaluacionPaso3(Request $request, string $codigo, ?EvaluacionCandidato $evaluacion = null)
    {
        $candidato = $evaluacion ?? EvaluacionCandidato::where('codigo_sesion', $codigo)->latest('id')->first();

        if (!$candidato) {
            return redirect()->route('psicologia.evaluacion.publica', ['codigo' => $codigo]);
        }

        if (!$candidato->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso2', [
                'codigo' => $codigo,
                'evaluacion' => $candidato->id,
            ]);
        }

        if ($candidato->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.completada', [
                'codigo' => $codigo,
                'evaluacion' => $candidato->id,
            ]);
        }

        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $opciones = PacaPreguntas::OPCIONES;
        $respuestasGuardadas = $candidato->respuestasPaca()
            ->pluck('opcion_seleccionada', 'numero_pregunta')
            ->toArray();

        return view('psicologia.evaluacion_paso3', compact(
            'codigo',
            'candidato',
            'afirmaciones',
            'opciones',
            'respuestasGuardadas'
        ));
    }

    /**
     * Auto-guarda el borrador de respuestas de PAC-A en tiempo real vía AJAX.
     */
    public function guardarBorradorPaca(Request $request, string $codigo, EvaluacionCandidato $evaluacion)
    {
        if ($evaluacion->pacaFinalizada()) {
            return response()->json(['error' => 'La prueba PAC-A ya ha sido finalizada.'], 403);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        DB::transaction(function () use ($evaluacion, $respuestasInput) {
            foreach ($respuestasInput as $numero => $opcion) {
                $numero = (int) $numero;
                $opcion = strtoupper(trim((string) $opcion));

                if ($numero >= 1 && $numero <= 40 && in_array($opcion, ['S', 'F', 'R', 'N'], true)) {
                    RespuestaPaca::updateOrCreate(
                        [
                            'evaluacion_candidato_id' => $evaluacion->id,
                            'numero_pregunta' => $numero,
                        ],
                        [
                            'opcion_seleccionada' => $opcion,
                            'puntaje' => PacaPreguntas::puntajeDeOpcion($opcion),
                        ]
                    );
                }
            }
        });

        $totalRespondidas = $evaluacion->respuestasPaca()->count();

        return response()->json([
            'ok' => true,
            'respondidas' => $totalRespondidas,
            'porcentaje' => round(($totalRespondidas / 40) * 100),
        ]);
    }

    /**
     * Valida las 40 respuestas de PAC-A, califica mediante CalificadorPacaService y finaliza todo el proceso.
     */
    public function finalizarPaca(
        Request $request,
        string $codigo,
        EvaluacionCandidato $evaluacion,
        CalificadorPacaService $calificador
    ) {
        if ($evaluacion->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.completada', [
                'codigo' => $codigo,
                'evaluacion' => $evaluacion->id,
            ]);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        foreach ($respuestasInput as $num => $op) {
            $num = (int) $num;
            $op = strtoupper(trim((string) $op));
            if ($num >= 1 && $num <= 40 && in_array($op, ['S', 'F', 'R', 'N'], true)) {
                RespuestaPaca::updateOrCreate(
                    [
                        'evaluacion_candidato_id' => $evaluacion->id,
                        'numero_pregunta' => $num,
                    ],
                    [
                        'opcion_seleccionada' => $op,
                        'puntaje' => PacaPreguntas::puntajeDeOpcion($op),
                    ]
                );
            }
        }

        $respuestasDB = $evaluacion->respuestasPaca()
            ->pluck('opcion_seleccionada', 'numero_pregunta')
            ->toArray();

        $faltantes = [];
        for ($i = 1; $i <= 40; $i++) {
            if (!isset($respuestasDB[$i]) || !in_array($respuestasDB[$i], ['S', 'F', 'R', 'N'], true)) {
                $faltantes[] = $i;
            }
        }

        if (!empty($faltantes)) {
            return back()
                ->withInput()
                ->with('error_validacion', 'Faltan por responder las siguientes afirmaciones: ' . implode(', ', $faltantes))
                ->with('preguntas_faltantes', $faltantes);
        }

        DB::transaction(function () use ($evaluacion, $calificador) {
            $calificador->calificarYAplicar($evaluacion);
            $evaluacion->update([
                'paso_actual' => 4,
                'estado' => 'COMPLETADA',
            ]);
        });

        return redirect()->route('psicologia.evaluacion.completada', [
            'codigo' => $codigo,
            'evaluacion' => $evaluacion->id,
        ]);
    }

    /**
     * Pantalla final de confirmación cuando el candidato completa las 2 pruebas (PAC-A + VALANTI).
     */
    public function evaluacionCompletada(Request $request, string $codigo, ?EvaluacionCandidato $evaluacion = null)
    {
        $candidato = $evaluacion ?? EvaluacionCandidato::where('codigo_sesion', $codigo)->latest('id')->first();

        return view('psicologia.evaluacion_completada', compact('codigo', 'candidato'));
    }

    /**
     * Módulo 2: Reportes (Filtro por año y vista previa del listado de aplicación de pruebas psicotécnicas).
     */
    public function reportes(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $anioActual = (int) now()->format('Y');

        $aniosEnBd = EvaluacionCandidato::selectRaw('YEAR(COALESCE(fecha_hora_aceptacion, created_at)) as anio')
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('anio')
            ->map(fn ($y) => (int) $y)
            ->filter(fn ($y) => $y >= 2020 && $y <= 2100)
            ->toArray();

        $aniosDisponibles = array_values(array_unique(array_merge([$anioActual], $aniosEnBd)));
        rsort($aniosDisponibles);

        $anioSeleccionado = (int) $request->input('anio', $anioActual);
        if (!in_array($anioSeleccionado, $aniosDisponibles, true)) {
            $aniosDisponibles[] = $anioSeleccionado;
            rsort($aniosDisponibles);
        }

        $evaluaciones = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->whereRaw('YEAR(COALESCE(fecha_hora_aceptacion, created_at)) = ?', [$anioSeleccionado])
            ->orderBy('id', 'asc')
            ->get();

        $totalAnio = $evaluaciones->count();
        $totalCumple = $evaluaciones->where('certificado_concepto', 'CUMPLE_SIN_INDICE')->count();
        $totalRecomendaciones = $evaluaciones->where('certificado_concepto', 'CUMPLE_CON_RECOMENDACIONES')->count();
        $totalNoCumple = $evaluaciones->where('certificado_concepto', 'NO_CUMPLE')->count();
        $totalPendientesCert = $totalAnio - ($totalCumple + $totalRecomendaciones + $totalNoCumple);

        return view('psicologia.reportes', compact(
            'usuario',
            'anioSeleccionado',
            'aniosDisponibles',
            'evaluaciones',
            'totalAnio',
            'totalCumple',
            'totalRecomendaciones',
            'totalNoCumple',
            'totalPendientesCert'
        ));
    }

    /**
     * Descarga el archivo Excel (.xlsx) filtrado únicamente por año con la estructura exacta
     * de PRUEBAS APLICADAS {AÑO}.xlsx.
     */
    public function descargarReporteExcel(Request $request, ReporteExcelPsicologiaService $excelService)
    {
        $anioActual = (int) now()->format('Y');
        $anioSeleccionado = (int) $request->input('anio', $anioActual);
        if ($anioSeleccionado < 2000 || $anioSeleccionado > 2100) {
            $anioSeleccionado = $anioActual;
        }

        $evaluaciones = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->whereRaw('YEAR(COALESCE(fecha_hora_aceptacion, created_at)) = ?', [$anioSeleccionado])
            ->orderBy('id', 'asc')
            ->get();

        $rutaTemporal = $excelService->generarArchivoXlsx($evaluaciones, $anioSeleccionado);
        $nombreDescarga = "PRUEBAS APLICADAS {$anioSeleccionado}.xlsx";

        return response()->download($rutaTemporal, $nombreDescarga, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Módulo 3: Historial de Pruebas (Vista completa con filtros, consentimiento PDF y resultados PAC-A / VALANTI).
     */
    public function historialPruebas(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $query = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->withCount(['respuestasPaca', 'respuestasValanti']);

        if ($request->filled('buscar')) {
            $buscar = trim((string) $request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('identificacion', 'like', "%{$buscar}%")
                  ->orWhere('cargo_aspira', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $estado = $request->input('estado');
            if ($estado === 'FINALIZADA') {
                $query->whereHas('resultadoPaca')->whereHas('resultadoValanti');
            } elseif ($estado === 'EN_CURSO') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('resultadoValanti')
                      ->whereHas('respuestasPaca');
                });
            } elseif ($estado === 'BORRADOR') {
                $query->whereDoesntHave('resultadoPaca')
                      ->whereDoesntHave('respuestasPaca');
            }
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->input('fecha'));
        }

        $aplicaciones = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $filtrosActivos = $request->anyFilled(['buscar', 'estado', 'fecha']);

        // Detectar túnel Ngrok o IP local para construir el QR móvil de continuidad
        $urlNgrokBase = null;
        try {
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT_MS => 250,
                CURLOPT_CONNECTTIMEOUT_MS => 180,
            ]);
            $ngrokRes = curl_exec($ch);
            curl_close($ch);
            if ($ngrokRes) {
                $ngrokData = json_decode($ngrokRes, true);
                foreach ($ngrokData['tunnels'] ?? [] as $tun) {
                    if (!empty($tun['public_url']) && str_starts_with($tun['public_url'], 'https://')) {
                        $urlNgrokBase = rtrim($tun['public_url'], '/');
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ngrok no activo
        }

        if (!$urlNgrokBase && str_contains($request->getHost(), 'ngrok')) {
            $urlNgrokBase = $request->getSchemeAndHttpHost();
        }

        $localIp = gethostbyname(gethostname());
        $puerto = $request->getPort();
        $puertoStr = ($puerto && !in_array($puerto, [80, 443])) ? ':' . $puerto : '';
        $urlLocalBase = 'http://' . $localIp . $puertoStr;

        return view('psicologia.pruebas.historial', compact(
            'usuario',
            'aplicaciones',
            'filtrosActivos',
            'urlNgrokBase',
            'urlLocalBase'
        ));
    }

    /**
     * Detalle de Resultados de la Prueba 1 (PAC-A) de un candidato.
     */
    public function verResultadoPaca(EvaluacionCandidato $evaluacion, CalificadorPacaService $calificador)
    {
        $evaluacion->load(['respuestasPaca', 'resultadoPaca', 'resultadoValanti']);

        if (!$evaluacion->resultadoPaca && $evaluacion->respuestasPaca->count() === 40) {
            $calificador->calificarYAplicar($evaluacion);
            $evaluacion->load('resultadoPaca');
        }

        if (!$evaluacion->resultadoPaca) {
            return redirect()->route('psicologia.pruebas.historial')
                ->with('error', 'Esta evaluación aún no cuenta con las 40 respuestas de PAC-A finalizadas.');
        }

        $aplicacion = $evaluacion;
        $resultado = $evaluacion->resultadoPaca;
        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $respuestasPorId = $evaluacion->respuestasPaca->keyBy('numero_pregunta');
        $preguntasAccidentogena = PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA;
        $preguntasTipoA = PacaPreguntas::PREGUNTAS_PATRON_TIPO_A;
        $preguntasControl = PacaPreguntas::PREGUNTAS_CONTROL;
        $opciones = PacaPreguntas::OPCIONES;

        return view('psicologia.paca.resultado', compact(
            'aplicacion',
            'resultado',
            'afirmaciones',
            'respuestasPorId',
            'preguntasAccidentogena',
            'preguntasTipoA',
            'preguntasControl',
            'opciones'
        ));
    }

    /**
     * Detalle de Resultados de la Prueba 2 (VALANTI) de un candidato.
     */
    public function verResultadoValanti(EvaluacionCandidato $evaluacion, CalificadorValantiService $calificador)
    {
        $evaluacion->load(['respuestasValanti', 'resultadoValanti', 'resultadoPaca']);

        if (!$evaluacion->resultadoValanti && $evaluacion->respuestasValanti->count() === 30) {
            $calificador->calificarYAplicar($evaluacion);
            $evaluacion->load('resultadoValanti');
        }

        if (!$evaluacion->resultadoValanti) {
            return redirect()->route('psicologia.pruebas.historial')
                ->with('error', 'Esta evaluación aún no cuenta con las 30 respuestas de VALANTI finalizadas.');
        }

        // Asegurar que el Perfil Ideal fijo (50, 65, 50, 65, 65) y el PDF automático estén sincronizados
        $res = $evaluacion->resultadoValanti;
        if (
            $res->perfil_empresa_verdad !== 50 ||
            $res->perfil_empresa_rectitud !== 65 ||
            $res->perfil_empresa_paz !== 50 ||
            $res->perfil_empresa_amor !== 65 ||
            $res->perfil_empresa_no_violencia !== 65 ||
            !Storage::disk('public')->exists($calificador->rutaArchivoPdf($evaluacion))
        ) {
            $res = $calificador->calificarYAplicar($evaluacion);
        }

        $aplicacion = $evaluacion;
        $resultado = $res;
        $preguntas = ValantiPreguntas::PREGUNTAS;
        $baremos = ValantiPreguntas::BAREMOS;
        $combinaciones = ValantiPreguntas::COMBINACIONES_VALIDAS;
        $respuestasPorNumero = $evaluacion->respuestasValanti->keyBy('numero_pregunta');

        return view('psicologia.valanti.resultado', compact(
            'aplicacion',
            'resultado',
            'preguntas',
            'baremos',
            'respuestasPorNumero',
            'combinaciones'
        ));
    }

    /**
     * Permite al profesional de Psicología o Administrador ajustar las respuestas de PAC-A
     * y recalcular de inmediato los puntajes, niveles de riesgo e interpretación institucional.
     */
    public function actualizarRespuestasPaca(
        Request $request,
        EvaluacionCandidato $evaluacion,
        CalificadorPacaService $calificador
    ) {
        $respuestasInput = (array) $request->input('respuestas', []);

        if (empty($respuestasInput)) {
            return back()->with('error', 'No se recibieron respuestas para actualizar.');
        }

        DB::transaction(function () use ($evaluacion, $respuestasInput) {
            foreach ($respuestasInput as $num => $op) {
                $num = (int) $num;
                $op = strtoupper(trim((string) $op));
                if ($num >= 1 && $num <= 40 && in_array($op, ['S', 'F', 'R', 'N'], true)) {
                    RespuestaPaca::updateOrCreate(
                        [
                            'evaluacion_candidato_id' => $evaluacion->id,
                            'numero_pregunta' => $num,
                        ],
                        [
                            'opcion_seleccionada' => $op,
                            'puntaje' => PacaPreguntas::puntajeDeOpcion($op),
                        ]
                    );
                }
            }
        });

        // Recalcular resultado cuantitativo y cualitativo
        $resultado = $calificador->calificarYAplicar($evaluacion);

        // Si ya cuenta con ambas pruebas y tiene certificado, sincronizar concepto aptitudinal
        if ($evaluacion->pacaFinalizada() && $evaluacion->valantiFinalizada()) {
            $nuevoConcepto = $evaluacion->calcularConceptoAptitudinal();
            $evaluacion->update([
                'certificado_concepto' => $nuevoConcepto,
            ]);
        }

        $msg = "Respuestas de PAC-A actualizadas y prueba recalificada exitosamente. Tendencia Accidentógena: {$resultado->nivel_riesgo_accidentogena} ({$resultado->puntaje_tendencia_accidentogena} pts) · Patrón Tipo A: {$resultado->nivel_riesgo_patron_tipo_a} ({$resultado->puntaje_patron_tipo_a} pts).";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'mensaje' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Permite al profesional de Psicología o Administrador ajustar las respuestas de VALANTI
     * y recalcular de inmediato los puntajes directos, baremos estándar (PE), gráfico radial y PDF.
     */
    public function actualizarRespuestasValanti(
        Request $request,
        EvaluacionCandidato $evaluacion,
        CalificadorValantiService $calificador
    ) {
        $respuestasInput = (array) $request->input('respuestas', []);

        if (empty($respuestasInput)) {
            return back()->with('error', 'No se recibieron respuestas para actualizar.');
        }

        DB::transaction(function () use ($evaluacion, $respuestasInput) {
            foreach ($respuestasInput as $num => $parStr) {
                $num = (int) $num;
                $parStr = trim((string) $parStr);
                if ($num >= 1 && $num <= 30 && isset(ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr])) {
                    $par = ValantiPreguntas::COMBINACIONES_VALIDAS[$parStr];
                    RespuestaValanti::updateOrCreate(
                        [
                            'evaluacion_candidato_id' => $evaluacion->id,
                            'numero_pregunta' => $num,
                        ],
                        [
                            'puntos_a' => $par['a'],
                            'puntos_b' => $par['b'],
                        ]
                    );
                }
            }
        });

        // Recalcular resultado, gráfico radial en base64 y PDF
        $resultado = $calificador->calificarYAplicar($evaluacion);

        // Si ya cuenta con ambas pruebas y tiene certificado, sincronizar concepto aptitudinal
        if ($evaluacion->pacaFinalizada() && $evaluacion->valantiFinalizada()) {
            $nuevoConcepto = $evaluacion->calcularConceptoAptitudinal();
            $evaluacion->update([
                'certificado_concepto' => $nuevoConcepto,
            ]);
        }

        $msg = "Respuestas de VALANTI actualizadas y perfil radial recalificado exitosamente. (Verdad: {$resultado->pe_verdad}, Rectitud: {$resultado->pe_rectitud}, Paz: {$resultado->pe_paz}, Amor: {$resultado->pe_amor}, No Violencia: {$resultado->pe_no_violencia}).";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'mensaje' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Genera y muestra/descarga el PDF oficial de resultados de la prueba PAC-A.
     */
    public function verPacaPdf(
        Request $request,
        EvaluacionCandidato $evaluacion,
        CalificadorPacaService $calificador
    ) {
        $evaluacion->load(['respuestasPaca', 'resultadoPaca']);

        if (!$evaluacion->resultadoPaca && $evaluacion->respuestasPaca->count() === 40) {
            $calificador->calificarYAplicar($evaluacion);
            $evaluacion->load('resultadoPaca');
        }

        if (!$evaluacion->resultadoPaca) {
            abort(404, 'Resultados PAC-A no disponibles.');
        }

        $resultado = $evaluacion->resultadoPaca;
        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $respuestasPorId = $evaluacion->respuestasPaca->keyBy('numero_pregunta');
        $preguntasAccidentogena = PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA;
        $preguntasTipoA = PacaPreguntas::PREGUNTAS_PATRON_TIPO_A;
        $preguntasControl = PacaPreguntas::PREGUNTAS_CONTROL;
        $opciones = PacaPreguntas::OPCIONES;

        // Logo institucional en Base64
        $logoBase64 = null;
        $rutaLogo = public_path('images/Logo.png');
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
        }

        // Psicóloga oficial y firma digital
        $psicologo = null;
        if (Auth::user() && !empty(Auth::user()->firma_digital)) {
            $psicologo = Auth::user();
        } else {
            $psicologo = Usuario::where('rol', 'PSICOLOGIA')->whereNotNull('firma_digital')->first();
        }

        $firmaPsicologaBase64 = null;
        if ($psicologo && !empty($psicologo->firma_digital)) {
            if (str_starts_with($psicologo->firma_digital, 'data:image')) {
                $firmaPsicologaBase64 = $this->convertirTintaFirmaANegro($psicologo->firma_digital);
            } elseif (Storage::disk('public')->exists($psicologo->firma_digital)) {
                $firmaPsicologaBase64 = $this->convertirTintaFirmaANegro(Storage::disk('public')->get($psicologo->firma_digital));
            }
        }

        $pdf = Pdf::loadView('psicologia.paca.pdf', compact(
            'evaluacion',
            'resultado',
            'afirmaciones',
            'respuestasPorId',
            'preguntasAccidentogena',
            'preguntasTipoA',
            'preguntasControl',
            'opciones',
            'logoBase64',
            'firmaPsicologaBase64',
            'psicologo'
        ))->setPaper('letter', 'portrait');

        $nombreDescarga = "Reporte_PACA_{$evaluacion->identificacion}.pdf";

        if ($request->boolean('inline')) {
            return $pdf->stream($nombreDescarga);
        }

        return $pdf->download($nombreDescarga);
    }

    /**
     * Sirve o descarga el archivo PDF ya generado automáticamente con el Gráfico Radial de VALANTI.
     */
    public function verValantiPdf(
        Request $request,
        EvaluacionCandidato $evaluacion,
        CalificadorValantiService $calificador
    ) {
        $evaluacion->load('resultadoValanti');
        if (!$evaluacion->resultadoValanti) {
            abort(404, 'Resultados VALANTI no disponibles.');
        }

        $rutaRelativa = $calificador->rutaArchivoPdf($evaluacion);
        $calificador->generarYGuardarPdfReporte($evaluacion, $evaluacion->resultadoValanti);

        $rutaAbsoluta = Storage::disk('public')->path($rutaRelativa);
        $nombreDescarga = "Cuestionario_VALANTI_{$evaluacion->identificacion}.pdf";

        if ($request->boolean('inline')) {
            return response()->file($rutaAbsoluta, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $nombreDescarga . '"',
            ]);
        }

        return response()->download($rutaAbsoluta, $nombreDescarga);
    }

    /**
     * Genera y muestra/descarga el PDF oficial del Consentimiento Informado GH-FO-025 firmado.
     */
    public function verConsentimientoPdf(Request $request, EvaluacionCandidato $evaluacion)
    {
        $logoBase64 = null;
        $rutaLogo = public_path('images/Logo.png');
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
        }

        $firmaBase64 = null;
        if (!empty($evaluacion->firma_digital)) {
            if (str_starts_with($evaluacion->firma_digital, 'data:image')) {
                $firmaBase64 = $this->convertirTintaFirmaANegro($evaluacion->firma_digital);
            } elseif (Storage::disk('public')->exists($evaluacion->firma_digital)) {
                $firmaBase64 = $this->convertirTintaFirmaANegro(Storage::disk('public')->get($evaluacion->firma_digital));
            }
        }

        $pdf = Pdf::loadView('psicologia.consentimiento.pdf', compact(
            'evaluacion',
            'logoBase64',
            'firmaBase64'
        ))->setPaper('letter', 'portrait');

        $nombreArchivo = "Consentimiento_GH-FO-025_{$evaluacion->identificacion}.pdf";

        if ($request->boolean('inline')) {
            return $pdf->stream($nombreArchivo);
        }

        return $pdf->download($nombreArchivo);
    }

    /**
     * Módulo 4: Certificado Aptitudinal (GH-FO-028).
     * Lista únicamente los candidatos que ya presentaron las 2 pruebas completas (PAC-A y VALANTI).
     */
    public function certificadoAptitudinal(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $evaluacionesCompletas = EvaluacionCandidato::with(['resultadoPaca', 'resultadoValanti'])
            ->whereHas('resultadoPaca')
            ->whereHas('resultadoValanti')
            ->latest('id')
            ->get();

        $totalCompletas = $evaluacionesCompletas->count();
        $totalEmitidos = $evaluacionesCompletas->filter(fn (EvaluacionCandidato $e) => $e->certificadoEmitido())->count();
        $totalPendientes = $totalCompletas - $totalEmitidos;

        return view('psicologia.certificado_aptitudinal', compact(
            'usuario',
            'evaluacionesCompletas',
            'totalCompletas',
            'totalEmitidos',
            'totalPendientes'
        ));
    }

    /**
     * Guarda el diligenciamiento del Certificado de Aptitud Psicológica (GH-FO-028).
     * El concepto aptitudinal está predeterminado con la flechita según los resultados
     * de las pruebas PAC-A y VALANTI, permitiendo a la psicóloga registrar sus observaciones.
     */
    public function guardarCertificadoAptitudinal(Request $request, EvaluacionCandidato $evaluacion)
    {
        $evaluacion->load(['resultadoPaca', 'resultadoValanti']);

        if (!$evaluacion->pacaFinalizada() || !$evaluacion->valantiFinalizada()) {
            $redirectRoute = $request->routeIs('admin.*') ? 'admin.psicologia.certificados' : 'psicologia.certificado_aptitudinal';
            return redirect()->route($redirectRoute)
                ->with('error', 'El candidato aún no ha completado las dos pruebas psicotécnicas (PAC-A y VALANTI).');
        }

        $validados = $request->validate([
            'certificado_ciudad_fecha' => ['nullable', 'string', 'max:180'],
            'certificado_concepto' => ['nullable', 'string', 'in:CUMPLE_SIN_INDICE,CUMPLE_CON_RECOMENDACIONES,NO_CUMPLE'],
            'certificado_observaciones' => ['nullable', 'string', 'max:2000'],
        ]);

        $conceptoCalculado = $evaluacion->calcularConceptoAptitudinal();
        $conceptoInput = $validados['certificado_concepto'] ?? null;
        $concepto = in_array($conceptoInput, ['CUMPLE_SIN_INDICE', 'CUMPLE_CON_RECOMENDACIONES', 'NO_CUMPLE'], true)
            ? $conceptoInput
            : $conceptoCalculado;

        $ciudadFecha = trim((string) ($validados['certificado_ciudad_fecha'] ?? ''));
        if ($ciudadFecha === '') {
            $ciudadFecha = 'Rivera-Huila, ' . now()->format('d/m/Y');
        }

        $observaciones = trim((string) ($validados['certificado_observaciones'] ?? ''));
        $observacionesMayus = $observaciones !== '' 
            ? mb_strtoupper($observaciones, 'UTF-8') 
            : ($evaluacion->certificado_observaciones ?? $evaluacion->generarObservacionSugerida());

        $evaluacion->update([
            'certificado_ciudad_fecha' => $ciudadFecha,
            'certificado_concepto' => $concepto,
            'certificado_observaciones' => $observacionesMayus,
            'certificado_emitido_at' => now(),
            'certificado_emitido_por' => Auth::id(),
        ]);

        if ($request->boolean('descargar_pdf')) {
            return $this->verCertificadoAptitudinalPdf($request, $evaluacion);
        }

        $redirectRoute = $request->routeIs('admin.*') ? 'admin.psicologia.certificados' : 'psicologia.certificado_aptitudinal';
        return redirect()->route($redirectRoute)
            ->with('success', "Certificado de Aptitud Psicológica (GH-FO-028) guardado para {$evaluacion->nombre_completo}.");
    }

    /**
     * Genera y muestra o descarga el PDF oficial del Certificado de Aptitud Psicológica (GH-FO-028)
     * incorporando automáticamente la firma digital de la psicóloga registrada en el sistema.
     */
    public function verCertificadoAptitudinalPdf(Request $request, EvaluacionCandidato $evaluacion)
    {
        $evaluacion->load(['resultadoPaca', 'resultadoValanti']);

        if (!$evaluacion->certificadoEmitido()) {
            if ($evaluacion->pacaFinalizada() && $evaluacion->valantiFinalizada()) {
                $evaluacion->update([
                    'certificado_concepto' => $evaluacion->calcularConceptoAptitudinal(),
                    'certificado_observaciones' => $evaluacion->generarObservacionSugerida(),
                    'certificado_ciudad_fecha' => 'Rivera-Huila, ' . now()->format('d/m/Y'),
                    'certificado_emitido_at' => now(),
                    'certificado_emitido_por' => Auth::id(),
                ]);
            } else {
                $redirectRoute = $request->routeIs('admin.*') ? 'admin.psicologia.certificados' : 'psicologia.certificado_aptitudinal';
                return redirect()->route($redirectRoute)
                    ->with('error', 'El candidato aún no ha completado las dos pruebas psicotécnicas (PAC-A y VALANTI).');
            }
        }

        $logoBase64 = null;
        $rutaLogo = public_path('images/Logo.png');
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
        }

        // Obtener la firma digital de la psicóloga oficial
        $firmaPsicologaBase64 = null;
        $psicologo = null;
        if ($evaluacion->certificado_emitido_por) {
            $emisor = Usuario::find($evaluacion->certificado_emitido_por);
            if ($emisor && !empty($emisor->firma_digital)) {
                $psicologo = $emisor;
            }
        }
        if (!$psicologo && Auth::user() && !empty(Auth::user()->firma_digital)) {
            $psicologo = Auth::user();
        }
        if (!$psicologo) {
            $psicologo = Usuario::where('rol', 'PSICOLOGIA')->whereNotNull('firma_digital')->first();
        }

        if ($psicologo && !empty($psicologo->firma_digital)) {
            if (str_starts_with($psicologo->firma_digital, 'data:image')) {
                $firmaPsicologaBase64 = $this->convertirTintaFirmaANegro($psicologo->firma_digital);
            } elseif (Storage::disk('public')->exists($psicologo->firma_digital)) {
                $firmaPsicologaBase64 = $this->convertirTintaFirmaANegro(Storage::disk('public')->get($psicologo->firma_digital));
            }
        }

        $pdf = Pdf::loadView('psicologia.certificado.pdf', compact(
            'evaluacion',
            'logoBase64',
            'firmaPsicologaBase64'
        ))->setPaper('letter', 'portrait');

        $nombreArchivo = "Certificado_Aptitud_GH-FO-028_{$evaluacion->identificacion}.pdf";

        if ($request->boolean('inline')) {
            return $pdf->stream($nombreArchivo);
        }

        return $pdf->download($nombreArchivo);
    }

    /**
     * Guarda o actualiza la firma digital oficial de la profesional de Psicología
     * para estamparla automáticamente en todos los Certificados Aptitudinales (GH-FO-028).
     */
    public function guardarFirmaPsicologia(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $request->validate([
            'modo_firma' => ['required', 'string', 'in:dibujar,archivo'],
            'firma_canvas' => ['nullable', 'string'],
            'firma_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ], [
            'firma_archivo.image' => 'El archivo debe ser una imagen válida (PNG, JPG, WEBP).',
            'firma_archivo.max' => 'El peso de la imagen no puede exceder 4MB.',
        ]);

        $modo = $request->input('modo_firma');
        $rutaFirma = null;

        if ($modo === 'dibujar') {
            $canvasData = (string) $request->input('firma_canvas', '');
            if (empty($canvasData) || !str_starts_with($canvasData, 'data:image')) {
                return back()->with('error', 'Debes dibujar tu firma en el recuadro digital antes de guardar.');
            }

            if ($usuario->firma_digital && Storage::disk('public')->exists($usuario->firma_digital)) {
                Storage::disk('public')->delete($usuario->firma_digital);
            }

            $rutaFirma = $this->guardarFirmaBase64($canvasData);
        } else {
            if (!$request->hasFile('firma_archivo')) {
                return back()->with('error', 'Debes seleccionar o arrastrar un archivo de imagen con tu firma.');
            }

            if ($usuario->firma_digital && Storage::disk('public')->exists($usuario->firma_digital)) {
                Storage::disk('public')->delete($usuario->firma_digital);
            }

            $rutaFirma = $request->file('firma_archivo')->store('firmas_usuarios', 'public');
        }

        if (!$rutaFirma) {
            return back()->with('error', 'Ocurrió un error al procesar la firma digital. Intenta nuevamente.');
        }

        $usuario->update([
            'firma_digital' => $rutaFirma,
            'firma_actualizada_el' => now(),
        ]);

        return back()->with('success', '¡Firma digital guardada exitosamente! Se estampará de forma automática en los Certificados de Aptitud Psicológica (GH-FO-028).');
    }

    /**
     * Elimina la firma digital registrada del usuario de psicología.
     */
    public function eliminarFirmaPsicologia(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        if ($usuario->firma_digital && Storage::disk('public')->exists($usuario->firma_digital)) {
            Storage::disk('public')->delete($usuario->firma_digital);
        }

        $usuario->update([
            'firma_digital' => null,
            'firma_actualizada_el' => now(),
        ]);

        return back()->with('success', 'Tu firma digital ha sido eliminada. Podrás registrar una nueva cuando lo desees.');
    }

    /**
     * Auxiliar para guardar firma generada desde canvas HTML5 (base64 data URL).
     */
    private function guardarFirmaBase64(string $dataUrl): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
            return null;
        }

        $data = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $data = base64_decode($data);

        if ($data === false) {
            return null;
        }

        $nombreArchivo = 'firmas_usuarios/firma_psicologa_' . Str::random(24) . '.png';
        Storage::disk('public')->put($nombreArchivo, $data);

        return $nombreArchivo;
    }

    /**
     * Convierte el trazo de tinta de la firma a un color negro nítido (#000000),
     * preservando 100% la transparencia del fondo para evitar fondos negros o artefactos en DomPDF.
     */
    private function convertirTintaFirmaANegro(?string $rawImage): ?string
    {
        if (empty($rawImage)) {
            return null;
        }

        if (str_starts_with($rawImage, 'data:image')) {
            $bin = substr($rawImage, strpos($rawImage, ',') + 1);
            $bin = base64_decode($bin);
        } else {
            $bin = $rawImage;
        }

        if ($bin === false || empty($bin)) {
            return null;
        }

        if (!extension_loaded('gd')) {
            return 'data:image/png;base64,' . base64_encode($bin);
        }

        $source = @imagecreatefromstring($bin);
        if (!$source) {
            return 'data:image/png;base64,' . base64_encode($bin);
        }

        $w = imagesx($source);
        $h = imagesy($source);

        $dest = imagecreatetruecolor($w, $h);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparente = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefill($dest, 0, 0, $transparente);

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgba = imagecolorat($source, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;

                // Si el píxel es transparente o casi transparente, lo dejamos transparente intacto
                if ($alpha >= 118) {
                    continue;
                }

                // Convertir trazo a negro intenso (#000000) ajustando la opacidad para que resalte nítido
                $nuevoAlpha = (int) max(0, round($alpha * 0.7));
                $colorNegro = imagecolorallocatealpha($dest, 0, 0, 0, $nuevoAlpha);
                imagesetpixel($dest, $x, $y, $colorNegro);
            }
        }

        imagedestroy($source);

        ob_start();
        imagepng($dest);
        $pngData = ob_get_clean();
        imagedestroy($dest);

        return 'data:image/png;base64,' . base64_encode($pngData ?: $bin);
    }
}
