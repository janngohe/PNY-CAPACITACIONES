<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionCandidato;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Código único de sesión de evaluación (persiste en la sesión hasta renovarlo)
        $codigoSesion = $request->query('codigo')
            ?? session('psicologia_codigo_sesion')
            ?? 'PSI-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        session(['psicologia_codigo_sesion' => $codigoSesion]);

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

        // Si se está navegando directamente mediante ngrok en el navegador
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
     * Genera un nuevo código de prueba y QR fresco.
     */
    public function generarNuevoCodigo()
    {
        $nuevoCodigo = 'PSI-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        session(['psicologia_codigo_sesion' => $nuevoCodigo]);

        return redirect()->route('psicologia.pruebas.realizar')
            ->with('success', "Se ha generado un nuevo código de evaluación: {$nuevoCodigo}");
    }

    /**
     * Paso 1: Vista pública que abre la persona evaluada al escanear el QR desde su celular.
     * Muestra el documento de Autorización para la realización de pruebas psicotécnicas y su formulario.
     */
    public function evaluacionPublica(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? session('psicologia_codigo_sesion') ?? 'PSI-DEMO';

        // Fecha y hora del sistema para plasmar en el documento oficial
        $fechaHoraActual = now();

        return view('psicologia.evaluacion_candidato', compact('codigo', 'fechaHoraActual'));
    }

    /**
     * Procesa y almacena la Autorización firmada por el candidato (Paso 1).
     */
    public function guardarConsentimiento(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? session('psicologia_codigo_sesion') ?? 'PSI-DEMO';

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

        // Procesar y guardar la firma digital
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

        // Registrar la evaluación del candidato
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

        return redirect()->route('psicologia.evaluacion.paso2', ['codigo' => $codigo]);
    }

    /**
     * Paso 2: Transición a la Primera Prueba (esperando lineamientos del usuario).
     */
    public function evaluacionPaso2(Request $request, ?string $codigo = null)
    {
        $codigo = $codigo ?? session('psicologia_codigo_sesion') ?? 'PSI-DEMO';
        $candidato = null;

        if (session('evaluacion_candidato_id')) {
            $candidato = EvaluacionCandidato::find(session('evaluacion_candidato_id'));
        }

        if (!$candidato) {
            $candidato = EvaluacionCandidato::where('codigo_sesion', $codigo)->latest('id')->first();
        }

        return view('psicologia.evaluacion_paso2', compact('codigo', 'candidato'));
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
     * Módulo 3: Historial de Pruebas.
     */
    public function historialPruebas()
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('psicologia.historial_pruebas', compact('usuario'));
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
