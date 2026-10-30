<?php

namespace App\Http\Controllers;

use App\Constants\PacaPreguntas;
use App\Models\AplicacionPrueba;
use App\Models\Candidato;
use App\Models\ConsentimientoInformado;
use App\Models\RespuestaPaca;
use App\Services\Psicologia\CalificadorPacaService;
use App\Services\Psicologia\ConsentimientoDocumentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvaluacionPublicaController extends Controller
{
    /**
     * Pantalla de inicio de auto-registro para personas evaluadas vía QR.
     */
    public function inicio(Request $request)
    {
        $token = $request->query('token');
        $candidato = null;

        if ($token) {
            $candidato = Candidato::where('token_acceso', $token)->first();
        }

        if (!$candidato && $request->session()->has('evaluado_candidato_id')) {
            $candidato = Candidato::find($request->session()->get('evaluado_candidato_id'));
        }

        return view('evaluacion.inicio', compact('candidato'));
    }

    /**
     * Procesa el registro o identificación de la persona evaluada.
     */
    public function registrar(Request $request, ConsentimientoDocumentoService $documentoService)
    {
        // Limpieza de identificación
        $identificacion = preg_replace('/[^0-9A-Za-z\-_]/', '', (string)$request->input('numero_identificacion'));
        $request->merge(['numero_identificacion' => $identificacion]);

        $request->validate([
            'nombre_completo' => ['required', 'string', 'max:150'],
            'numero_identificacion' => ['required', 'string', 'max:30'],
            'lugar_expedicion' => ['required', 'string', 'max:150'],
            'ciudad_municipio' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'cargo_postulado' => ['nullable', 'string', 'max:150'],
        ], [
            'nombre_completo.required' => 'Por favor escribe tu nombre completo.',
            'numero_identificacion.required' => 'El número de cédula de ciudadanía es obligatorio.',
            'lugar_expedicion.required' => 'Indica el lugar donde fue expedida tu cédula.',
            'ciudad_municipio.required' => 'Indica tu ciudad o municipio actual.',
        ]);

        $candidato = DB::transaction(function () use ($request, $documentoService) {
            $candidato = Candidato::where('numero_identificacion', $request->numero_identificacion)->first();

            if ($candidato) {
                // Actualizar datos si cambiaron
                $candidato->update([
                    'nombre_completo' => mb_strtoupper(trim($request->nombre_completo)),
                    'lugar_expedicion' => trim($request->lugar_expedicion),
                    'ciudad_municipio' => trim($request->ciudad_municipio),
                    'telefono' => trim($request->telefono),
                    'email' => trim($request->email),
                    'cargo_postulado' => trim($request->cargo_postulado) ?: $candidato->cargo_postulado,
                ]);
            } else {
                $candidato = Candidato::create([
                    'nombre_completo' => mb_strtoupper(trim($request->nombre_completo)),
                    'numero_identificacion' => trim($request->numero_identificacion),
                    'lugar_expedicion' => trim($request->lugar_expedicion),
                    'ciudad_municipio' => trim($request->ciudad_municipio),
                    'telefono' => trim($request->telefono),
                    'email' => trim($request->email),
                    'cargo_postulado' => trim($request->cargo_postulado),
                    'origen_registro' => 'QR_AUTOREGISTRO',
                    'token_acceso' => bin2hex(random_bytes(24)),
                ]);
            }

            $candidato->asegurarTokenAcceso();

            // Verificar si ya tiene consentimiento firmado hoy o en proceso
            $consentimiento = $candidato->consentimientoActivo();
            if (!$consentimiento) {
                $consentimiento = $candidato->ultimoConsentimiento();
            }

            if (!$consentimiento || $consentimiento->estado === 'REVOCADO') {
                $consentimiento = $candidato->consentimientos()->create([
                    'ciudad_municipio' => $candidato->ciudad_municipio,
                    'fecha_consentimiento' => now()->toDateString(),
                    'codigo_documental' => 'GH-FO-025',
                    'version_documental' => '01',
                    'estado' => 'GENERADO',
                    'observaciones' => 'Generado automáticamente mediante auto-registro móvil QR por el aspirante.',
                ]);

                try {
                    $documentoService->generarDocumentosCompletos($consentimiento);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            return $candidato;
        });

        $request->session()->put('evaluado_candidato_id', $candidato->id);

        return redirect()->route('evaluacion.consentimiento', ['token' => $candidato->token_acceso]);
    }

    /**
     * Muestra el Consentimiento Informado GH-FO-025 para lectura y firma digital del aspirante.
     */
    public function verConsentimiento(Request $request)
    {
        $candidato = $this->resolverCandidato($request);
        if (!$candidato) {
            return redirect()->route('evaluacion.inicio');
        }

        $consentimiento = $candidato->ultimoConsentimiento();
        if (!$consentimiento) {
            return redirect()->route('evaluacion.inicio');
        }

        return view('evaluacion.consentimiento', compact('candidato', 'consentimiento'));
    }

    /**
     * Recibe la firma digital en pantalla y formaliza el consentimiento informado.
     */
    public function firmarConsentimiento(Request $request, ConsentimientoDocumentoService $documentoService)
    {
        $candidato = $this->resolverCandidato($request);
        if (!$candidato) {
            return redirect()->route('evaluacion.inicio');
        }

        $consentimiento = $candidato->ultimoConsentimiento();
        if (!$consentimiento) {
            return redirect()->route('evaluacion.inicio');
        }

        $request->validate([
            'firma_canvas' => ['required', 'string'],
            'acepto_terminos' => ['accepted'],
        ], [
            'firma_canvas.required' => 'Por favor realiza tu firma en el recuadro digital antes de continuar.',
            'acepto_terminos.accepted' => 'Debes marcar la casilla aceptando los términos del consentimiento informado.',
        ]);

        $base64Data = (string)$request->input('firma_canvas');
        if (!str_contains($base64Data, 'base64,')) {
            return back()->withErrors(['firma_canvas' => 'El formato de la firma digital es inválido.']);
        }

        $datosImagen = explode('base64,', $base64Data)[1] ?? null;
        if (!$datosImagen) {
            return back()->withErrors(['firma_canvas' => 'No se pudo decodificar la imagen de la firma.']);
        }

        $contenidoBinario = base64_decode($datosImagen);
        if ($contenidoBinario === false || strlen($contenidoBinario) < 100) {
            return back()->withErrors(['firma_canvas' => 'Por favor dibuja una firma visible en el recuadro.']);
        }

        // Guardar firma digital en storage
        $directorioRelativo = 'consentimientos_firmas';
        $directorioAbsoluto = storage_path('app/public/' . $directorioRelativo);
        File::ensureDirectoryExists($directorioAbsoluto);

        $nombreArchivo = "firma_candidato_{$candidato->id}_" . time() . ".png";
        $rutaCompleta = "{$directorioAbsoluto}/{$nombreArchivo}";
        file_put_contents($rutaCompleta, $contenidoBinario);

        $rutaRelativa = "{$directorioRelativo}/{$nombreArchivo}";

        // Actualizar consentimiento
        $consentimiento->update([
            'estado' => 'FIRMADO_DIGITAL',
            'tipo_firma' => 'DIGITAL',
            'firma_digital_ruta' => $rutaRelativa,
            'ip_firma' => $request->ip(),
            'fecha_firma_digital' => now(),
            'fecha_firma_fisica' => now()->toDateString(),
            'observaciones' => 'Consentimiento informado firmado digitalmente por el aspirante vía auto-registro QR.'
        ]);

        // Regenerar PDF oficial con la firma estampada
        try {
            $documentoService->generarPdf($consentimiento);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('evaluacion.pruebas', ['token' => $candidato->token_acceso])
            ->with('exito', 'Consentimiento informado registrado exitosamente.');
    }

    /**
     * Muestra las pruebas disponibles (PAC-A activa, VALANTI en desarrollo).
     */
    public function seleccionarPrueba(Request $request)
    {
        $candidato = $this->resolverCandidato($request);
        if (!$candidato) {
            return redirect()->route('evaluacion.inicio');
        }

        $consentimiento = $candidato->consentimientoActivo();
        if (!$consentimiento) {
            return redirect()->route('evaluacion.consentimiento', ['token' => $candidato->token_acceso])
                ->with('advertencia', 'Es indispensable leer y firmar el consentimiento informado antes de continuar.');
        }

        $aplicacionPaca = $candidato->aplicaciones()
            ->where('prueba_codigo', 'PAC-A')
            ->first();

        return view('evaluacion.pruebas', compact('candidato', 'consentimiento', 'aplicacionPaca'));
    }

    /**
     * Inicia o retoma la aplicación de PAC-A para el evaluado.
     */
    public function iniciarPaca(Request $request)
    {
        $candidato = $this->resolverCandidato($request);
        if (!$candidato) {
            return redirect()->route('evaluacion.inicio');
        }

        $consentimiento = $candidato->consentimientoActivo();
        if (!$consentimiento) {
            return redirect()->route('evaluacion.consentimiento', ['token' => $candidato->token_acceso]);
        }

        $aplicacion = $candidato->aplicaciones()
            ->where('prueba_codigo', 'PAC-A')
            ->whereIn('estado', ['BORRADOR', 'EN_CURSO'])
            ->first();

        if (!$aplicacion) {
            $aplicacion = $candidato->aplicaciones()->create([
                'consentimiento_id' => $consentimiento->id,
                'prueba_codigo' => 'PAC-A',
                'fecha_aplicacion' => now()->toDateString(),
                'hora_inicio' => now(),
                'estado' => 'EN_CURSO',
                'progreso_porcentaje' => 0,
                'token_sesion' => bin2hex(random_bytes(24)),
                'observaciones' => 'Iniciada vía auto-registro QR por el candidato evaluado.',
            ]);
        } else {
            if (empty($aplicacion->token_sesion)) {
                $aplicacion->token_sesion = bin2hex(random_bytes(24));
                $aplicacion->save();
            }
        }

        return redirect()->route('evaluacion.paca.aplicar', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]);
    }

    /**
     * Formulario interactivo responsivo de las 40 preguntas de PAC-A.
     */
    public function aplicarPaca(Request $request, string $token, string $appToken)
    {
        $candidato = Candidato::where('token_acceso', $token)->firstOrFail();
        $aplicacion = AplicacionPrueba::where('candidato_id', $candidato->id)
            ->where('token_sesion', $appToken)
            ->with(['respuestasPaca', 'candidato'])
            ->firstOrFail();

        if ($aplicacion->estaFinalizada()) {
            return redirect()->route('evaluacion.paca.completada', [
                'token' => $candidato->token_acceso,
                'appToken' => $aplicacion->token_sesion,
            ]);
        }

        $afirmaciones = PacaPreguntas::AFIRMACIONES;
        $opciones = PacaPreguntas::OPCIONES;

        // Mapear respuestas existentes
        $respuestasGuardadas = $aplicacion->respuestasPaca->pluck('opcion_seleccionada', 'numero_pregunta')->toArray();

        return view('evaluacion.paca', compact('candidato', 'aplicacion', 'afirmaciones', 'opciones', 'respuestasGuardadas'));
    }

    /**
     * Guarda el borrador de respuestas de PAC-A vía AJAX o envío normal.
     */
    public function guardarBorradorPaca(Request $request, string $token, string $appToken)
    {
        $candidato = Candidato::where('token_acceso', $token)->firstOrFail();
        $aplicacion = AplicacionPrueba::where('candidato_id', $candidato->id)
            ->where('token_sesion', $appToken)
            ->firstOrFail();

        if ($aplicacion->estaFinalizada()) {
            return response()->json(['error' => 'La prueba ya ha sido finalizada y no puede modificarse.'], 403);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        DB::transaction(function () use ($aplicacion, $respuestasInput) {
            foreach ($respuestasInput as $numero => $opcion) {
                $numero = (int) $numero;
                $opcion = strtoupper(trim((string)$opcion));

                if ($numero >= 1 && $numero <= 40 && in_array($opcion, ['S', 'F', 'R', 'N'], true)) {
                    $puntaje = PacaPreguntas::puntajeDeOpcion($opcion);
                    RespuestaPaca::updateOrCreate(
                        [
                            'aplicacion_id' => $aplicacion->id,
                            'numero_pregunta' => $numero,
                        ],
                        [
                            'opcion_seleccionada' => $opcion,
                            'puntaje' => $puntaje,
                        ]
                    );
                }
            }

            $totalRespondidas = $aplicacion->respuestasPaca()->count();
            $progreso = round(($totalRespondidas / 40) * 100, 2);

            $aplicacion->update([
                'progreso_porcentaje' => $progreso,
                'estado' => $totalRespondidas > 0 ? 'EN_CURSO' : 'BORRADOR',
            ]);
        });

        $totalRespondidas = $aplicacion->respuestasPaca()->count();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'mensaje' => 'Respuestas guardadas automáticamente.',
                'respondidas' => $totalRespondidas,
                'porcentaje' => round(($totalRespondidas / 40) * 100),
            ]);
        }

        return back()->with('exito', 'Borrador guardado exitosamente. Progreso: ' . round(($totalRespondidas / 40) * 100) . '%.');
    }

    /**
     * Valida las 40 respuestas, califica mediante CalificadorPacaService y finaliza la prueba.
     */
    public function finalizarPaca(Request $request, string $token, string $appToken, CalificadorPacaService $calificador)
    {
        $candidato = Candidato::where('token_acceso', $token)->firstOrFail();
        $aplicacion = AplicacionPrueba::where('candidato_id', $candidato->id)
            ->where('token_sesion', $appToken)
            ->firstOrFail();

        if ($aplicacion->estaFinalizada()) {
            return redirect()->route('evaluacion.paca.completada', [
                'token' => $candidato->token_acceso,
                'appToken' => $aplicacion->token_sesion,
            ]);
        }

        $respuestasInput = (array) $request->input('respuestas', []);

        // Guardar cualquier respuesta adicional enviada en el formulario final
        foreach ($respuestasInput as $num => $op) {
            $num = (int)$num;
            $op = strtoupper(trim((string)$op));
            if ($num >= 1 && $num <= 40 && in_array($op, ['S', 'F', 'R', 'N'], true)) {
                RespuestaPaca::updateOrCreate(
                    ['aplicacion_id' => $aplicacion->id, 'numero_pregunta' => $num],
                    [
                        'opcion_seleccionada' => $op,
                        'puntaje' => PacaPreguntas::puntajeDeOpcion($op),
                    ]
                );
            }
        }

        $respuestasDB = $aplicacion->respuestasPaca()->pluck('opcion_seleccionada', 'numero_pregunta')->toArray();

        // Verificar que las 40 preguntas estén contestadas
        $faltantes = [];
        for ($i = 1; $i <= 40; $i++) {
            if (!isset($respuestasDB[$i]) || !in_array($respuestasDB[$i], ['S', 'F', 'R', 'N'], true)) {
                $faltantes[] = $i;
            }
        }

        if (!empty($faltantes)) {
            return back()
                ->withInput()
                ->with('error_validacion', 'No es posible finalizar la prueba. Faltan por responder las siguientes afirmaciones: ' . implode(', ', $faltantes))
                ->with('preguntas_faltantes', $faltantes);
        }

        DB::transaction(function () use ($aplicacion, $calificador) {
            $aplicacion->update([
                'estado' => 'FINALIZADA',
                'progreso_porcentaje' => 100,
                'hora_finalizacion' => now(),
            ]);

            $calificador->calificarYAplicar($aplicacion);
        });

        return redirect()->route('evaluacion.paca.completada', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]);
    }

    /**
     * Pantalla de agradecimiento y confirmación de recepción confidencial.
     */
    public function pacaCompletada(Request $request, string $token, string $appToken)
    {
        $candidato = Candidato::where('token_acceso', $token)->firstOrFail();
        $aplicacion = AplicacionPrueba::where('candidato_id', $candidato->id)
            ->where('token_sesion', $appToken)
            ->firstOrFail();

        // Limpiar sesión para evitar accesos cruzados
        $request->session()->forget('evaluado_candidato_id');

        return view('evaluacion.completada', compact('candidato', 'aplicacion'));
    }

    /**
     * Descarga del consentimiento informado por parte del evaluado.
     */
    public function descargarConsentimientoPdf(Request $request, string $token)
    {
        $candidato = Candidato::where('token_acceso', $token)->firstOrFail();
        $consentimiento = $candidato->ultimoConsentimiento();

        if (!$consentimiento || !$consentimiento->ruta_archivo_pdf) {
            abort(404, 'Documento no disponible.');
        }

        $rutaAbsoluta = storage_path('app/' . $consentimiento->ruta_archivo_pdf);
        if (!file_exists($rutaAbsoluta)) {
            abort(404, 'El archivo PDF no se encuentra en el servidor.');
        }

        return response()->download($rutaAbsoluta, "Consentimiento_GH-FO-025_{$candidato->numero_identificacion}.pdf");
    }

    /**
     * Resuelve el candidato activo por token en URL o sesión.
     */
    protected function resolverCandidato(Request $request): ?Candidato
    {
        $token = $request->query('token') ?: $request->input('token');

        if ($token) {
            $candidato = Candidato::where('token_acceso', $token)->first();
            if ($candidato) {
                $request->session()->put('evaluado_candidato_id', $candidato->id);
                return $candidato;
            }
        }

        if ($request->session()->has('evaluado_candidato_id')) {
            return Candidato::find($request->session()->get('evaluado_candidato_id'));
        }

        return null;
    }
}
