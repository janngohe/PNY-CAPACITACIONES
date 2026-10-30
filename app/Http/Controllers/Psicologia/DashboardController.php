<?php

namespace App\Http\Controllers\Psicologia;

use App\Constants\PacaPreguntas;
use App\Constants\ValantiPreguntas;
use App\Http\Controllers\Controller;
use App\Models\EvaluacionCandidato;
use App\Models\RespuestaPaca;
use App\Models\RespuestaValanti;
use App\Models\Usuario;
use App\Services\Psicologia\CalificadorPacaService;
use App\Services\Psicologia\CalificadorValantiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        return view('psicologia.evaluacion_candidato', compact('codigo', 'fechaHoraActual'));
    }

    /**
     * Procesa y almacena la Autorización firmada por el candidato (Paso 1) y redirige a la Prueba 1 (PAC-A).
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
            'cargo_aspira.required' => 'El cargo al que aspiras es obligatorio.',
            'firma_digital.required' => 'Debes dibujar tu firma digital en el recuadro.',
            'acepta_terminos.accepted' => 'Debes marcar la casilla de aceptación para continuar.',
        ]);

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

        $evaluacion = EvaluacionCandidato::create([
            'codigo_sesion' => $codigo,
            'nombre_completo' => mb_strtoupper(trim($validados['nombre_completo']), 'UTF-8'),
            'identificacion' => trim($validados['identificacion']),
            'lugar_expedicion' => mb_convert_case(trim($validados['lugar_expedicion']), MB_CASE_TITLE, 'UTF-8'),
            'fecha_nacimiento' => $validados['fecha_nacimiento'],
            'edad' => (int) $validados['edad'],
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
     * Paso 2: Primera Prueba Psicotécnica (Cuestionario PAC-A de 40 preguntas).
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

        if ($candidato->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso3', [
                'codigo' => $codigo,
                'evaluacion' => $candidato->id,
            ]);
        }

        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $opciones = PacaPreguntas::OPCIONES;
        $respuestasGuardadas = $candidato->respuestasPaca()
            ->pluck('opcion_seleccionada', 'numero_pregunta')
            ->toArray();

        return view('psicologia.evaluacion_paso2', compact(
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
            return response()->json(['error' => 'La prueba ya ha sido finalizada.'], 403);
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
     * Valida las 40 respuestas de PAC-A, califica mediante CalificadorPacaService y avanza al Paso 3 (VALANTI).
     */
    public function finalizarPaca(
        Request $request,
        string $codigo,
        EvaluacionCandidato $evaluacion,
        CalificadorPacaService $calificador
    ) {
        if ($evaluacion->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso3', [
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
                'paso_actual' => 3,
            ]);
        });

        return redirect()->route('psicologia.evaluacion.paso3', [
            'codigo' => $codigo,
            'evaluacion' => $evaluacion->id,
        ]);
    }

    /**
     * Paso 3: Segunda Prueba Psicotécnica (Cuestionario VALANTI de 30 pares).
     */
    public function evaluacionPaso3(Request $request, string $codigo, ?EvaluacionCandidato $evaluacion = null)
    {
        $candidato = $evaluacion ?? EvaluacionCandidato::where('codigo_sesion', $codigo)->latest('id')->first();

        if (!$candidato) {
            return redirect()->route('psicologia.evaluacion.publica', ['codigo' => $codigo]);
        }

        if (!$candidato->pacaFinalizada()) {
            return redirect()->route('psicologia.evaluacion.paso2', [
                'codigo' => $codigo,
                'evaluacion' => $candidato->id,
            ]);
        }

        if ($candidato->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.completada', [
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

        return view('psicologia.evaluacion_paso3', compact(
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
     * califica mediante CalificadorValantiService y finaliza todo el proceso.
     */
    public function finalizarValanti(
        Request $request,
        string $codigo,
        EvaluacionCandidato $evaluacion,
        CalificadorValantiService $calificador
    ) {
        if ($evaluacion->valantiFinalizada()) {
            return redirect()->route('psicologia.evaluacion.completada', [
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
     * Módulo 2: Reportes.
     */
    public function reportes()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.reportes', compact('usuario'));
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

        return view('psicologia.pruebas.historial', compact(
            'usuario',
            'aplicaciones',
            'filtrosActivos'
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

        return view('psicologia.paca.resultado', compact(
            'aplicacion',
            'resultado',
            'afirmaciones',
            'respuestasPorId',
            'preguntasAccidentogena',
            'preguntasTipoA'
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
        $respuestasPorNumero = $evaluacion->respuestasValanti->keyBy('numero_pregunta');

        return view('psicologia.valanti.resultado', compact(
            'aplicacion',
            'resultado',
            'preguntas',
            'baremos',
            'respuestasPorNumero'
        ));
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
                $firmaBase64 = $evaluacion->firma_digital;
            } elseif (Storage::disk('public')->exists($evaluacion->firma_digital)) {
                $firmaBase64 = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($evaluacion->firma_digital));
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
     * Módulo 4: Certificado Aptitudinal.
     */
    public function certificadoAptitudinal()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.certificado_aptitudinal', compact('usuario'));
    }
}
