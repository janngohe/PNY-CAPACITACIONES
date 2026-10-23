<?php

namespace App\Http\Controllers\Induccion;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AsistenciaInduccion;
use App\Models\Configuracion;
use App\Models\Induccion;
use App\Models\Sede;
use App\Models\SesionInduccion;
use App\Models\Usuario;
use App\Services\PptxRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacilitadorController extends Controller
{
    /**
     * Redirige al login principal en modo Inducción.
     */
    public function showLogin(): RedirectResponse
    {
        if (session()->has('facilitador_usuario_id')) {
            return redirect()->route('induccion.index');
        }

        return redirect()->route('login', ['modo' => 'induccion']);
    }

    /**
     * Valida la Cédula y el PIN de 4 dígitos desde el formulario unificado.
     */
    public function login(Request $request): RedirectResponse
    {
        $identificacion = $request->input('identification') ?? $request->input('identificacion');
        $request->merge(['identification' => $identificacion]);

        $request->validate([
            'identification' => ['required', 'string'],
            'pin' => ['required', 'string', 'size:4'],
        ], [
            'identification.required' => 'Ingresa tu número de documento.',
            'pin.required' => 'Ingresa el código PIN de 4 dígitos.',
            'pin.size' => 'El PIN debe ser exactamente de 4 dígitos.',
        ]);

        $pinCorrecto = Configuracion::getPinInduccion();

        if (trim($request->pin) !== trim($pinCorrecto)) {
            return redirect()->route('login', ['modo' => 'induccion'])
                ->withInput()
                ->withErrors(['password' => 'El código PIN de 4 dígitos es incorrecto. Solicítalo al Administrador o a tu Jefe de Área.']);
        }

        $usuario = Usuario::where('identificacion', trim($identificacion))
            ->where('estado', true)
            ->first();

        if (!$usuario) {
            return redirect()->route('login', ['modo' => 'induccion'])
                ->withInput()
                ->withErrors(['identification' => 'El documento ingresado no corresponde a un colaborador activo en la plataforma.']);
        }

        // Iniciar sesión del facilitador
        session(['facilitador_usuario_id' => $usuario->id]);

        return redirect()->route('induccion.index')
            ->with('success', "¡Bienvenido(a), {$usuario->nombre_completo}! Has ingresado al portal de Inducciones.");
    }

    /**
     * Cierra la sesión del facilitador y vuelve al login principal.
     */
    public function logout(): RedirectResponse
    {
        session()->forget('facilitador_usuario_id');
        return redirect()->route('login')->with('status', 'Has salido del módulo de inducciones.');
    }

    /**
     * Panel principal del Facilitador: inducciones disponibles y sesiones recientes.
     */
    public function index(Request $request): View
    {
        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();
        $facilitador = Usuario::with('area', 'sede')->find($facilitadorId);

        $inducciones = Induccion::with(['creador'])
            ->withCount('sesiones')
            ->where('estado', true)
            ->latest()
            ->get();

        $detalleInducciones = $inducciones->mapWithKeys(function ($ind) {
            return [$ind->id => [
                'id' => $ind->id,
                'titulo' => $ind->titulo,
                'descripcion' => $ind->descripcion,
                'imagen_portada_url' => $ind->imagen_portada_url,
                'creador' => $ind->creador?->nombre_completo ?? 'Administración',
                'fecha_creacion' => $ind->created_at ? $ind->created_at->format('d/m/Y') : '—',
                'sesiones_count' => $ind->sesiones_count ?? 0,
                'recursos' => $ind->lista_recursos,
                'contenido_diapositivas' => $ind->contenido_diapositivas,
                'tiene_pptx' => collect($ind->lista_recursos)->contains('tipo', 'pptx'),
                'tiene_video' => collect($ind->lista_recursos)->contains('tipo', 'video'),
                'tiene_pdf' => collect($ind->lista_recursos)->contains('tipo', 'pdf'),
                'tiene_imagen' => collect($ind->lista_recursos)->contains('tipo', 'imagen'),
                'tiene_texto' => collect($ind->lista_recursos)->contains('tipo', 'texto'),
            ]];
        });

        $sesionesRecientes = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
            ->withCount('asistencias')
            ->when($facilitadorId, fn($q) => $q->where('facilitador_id', $facilitadorId))
            ->latest()
            ->take(20)
            ->get();

        $sedes = Sede::where('estado', true)->orderBy('nombre')->get();
        $areas = Area::where('estado', true)->orderBy('nombre')->get();

        return view('induccion.index', compact('facilitador', 'inducciones', 'detalleInducciones', 'sesionesRecientes', 'sedes', 'areas'));
    }

    /**
     * Formulario para crear una nueva inducción.
     */
    public function create(): View
    {
        return view('induccion.create');
    }

    /**
     * Almacena una nueva inducción con imagen de portada y múltiples recursos (Videos, PPTX, PDFs, Imágenes).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'], // Máximo 10 MB
            'recursos' => ['nullable', 'array'],
        ], [
            'titulo.required' => 'El título de la inducción es obligatorio.',
            'imagen_portada.image' => 'El archivo de portada debe ser una imagen válida.',
            'imagen_portada.max' => 'La imagen de portada no debe superar los 10 MB.',
        ]);

        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();

        // 1. Guardar Imagen de Portada
        $rutaPortada = null;
        if ($request->hasFile('imagen_portada')) {
            $archivoImg = $request->file('imagen_portada');
            $nombreImg = time() . '_portada_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivoImg->getClientOriginalName());
            $rutaPortada = $archivoImg->storeAs('inducciones/portadas', $nombreImg, 'public');
        }

        // 2. Procesar Múltiples Recursos (Videos, PowerPoint PPTX, PDFs, Imágenes)
        $recursosProcesados = [];
        $primerVideo = null;
        $primerArchivo = null;

        $recursosInput = $request->input('recursos', []);
        $archivosInput = $request->file('recursos', []);

        if (is_array($recursosInput)) {
            foreach ($recursosInput as $idx => $rec) {
                $tipo = $rec['tipo'] ?? 'video';
                $titulo = trim($rec['titulo'] ?? '') ?: ('Recurso ' . ($idx + 1));
                $url = trim($rec['url'] ?? '');
                $texto = trim($rec['texto'] ?? $rec['contenido'] ?? '');
                $rutaArchivo = null;

                // Revisar si se subió archivo para este recurso
                if (isset($archivosInput[$idx]['archivo']) && $archivosInput[$idx]['archivo']->isValid()) {
                    $archivo = $archivosInput[$idx]['archivo'];
                    $nombreLimpio = time() . '_' . $idx . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo->getClientOriginalName());

                    if ($tipo === 'pptx') {
                        $rutaArchivo = $archivo->storeAs('inducciones/presentaciones', $nombreLimpio, 'public');
                    } elseif ($tipo === 'pdf') {
                        $rutaArchivo = $archivo->storeAs('inducciones/documentos', $nombreLimpio, 'public');
                    } elseif ($tipo === 'imagen') {
                        $rutaArchivo = $archivo->storeAs('inducciones/imagenes', $nombreLimpio, 'public');
                    } else {
                        $rutaArchivo = $archivo->storeAs('inducciones/archivos', $nombreLimpio, 'public');
                    }
                }

                // Validar omisión si está vacío
                if ($tipo === 'video' && empty($url)) {
                    continue;
                }
                if ($tipo === 'texto' && empty($texto)) {
                    continue;
                }
                if (!in_array($tipo, ['video', 'texto']) && empty($rutaArchivo) && empty($url)) {
                    continue;
                }

                if ($tipo === 'video' && !$primerVideo) {
                    $primerVideo = $url;
                }
                if (in_array($tipo, ['pptx', 'pdf']) && !$primerArchivo && $rutaArchivo) {
                    $primerArchivo = $rutaArchivo;
                }

                $recursosProcesados[] = [
                    'tipo' => $tipo,
                    'titulo' => $titulo,
                    'url' => $url ?: null,
                    'archivo' => $rutaArchivo,
                    'texto' => $texto ?: null,
                ];
            }
        }

        $induccion = Induccion::create([
            'titulo' => trim($request->titulo),
            'descripcion' => $request->descripcion ? trim($request->descripcion) : null,
            'imagen_portada' => $rutaPortada,
            'tipo' => 'GENERAL',
            'archivo_presentacion' => $primerArchivo,
            'video_url' => $primerVideo,
            'recursos' => $recursosProcesados,
            'estado' => true,
            'creado_por' => $facilitadorId,
        ]);

        return redirect()->route('induccion.index')
            ->with('success', "La inducción '{$induccion->titulo}' fue creada exitosamente con sus recursos multimedia.");
    }

    /**
     * Inicia una sesión en vivo para una inducción específica en una sede y áreas seleccionadas.
     */
    public function iniciarSesion(Request $request, Induccion $induccion): RedirectResponse
    {
        $request->validate([
            'sede_id' => ['required', 'exists:sedes,id'],
            'areas_convocadas' => ['nullable', 'array'],
            'permitir_externos' => ['nullable'],
        ]);

        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();
        $codigo = SesionInduccion::generarCodigoUnico();
        $permitirExternos = $request->boolean('permitir_externos', false);

        $sesion = SesionInduccion::create([
            'induccion_id' => $induccion->id,
            'facilitador_id' => $facilitadorId,
            'sede_id' => $request->sede_id,
            'codigo_sesion' => $codigo,
            'estado' => 'EN_CURSO',
            'fecha_inicio' => now(),
            'areas_convocadas' => $request->areas_convocadas ?? [],
            'permitir_externos' => $permitirExternos,
        ]);

        return redirect()->route('induccion.presentacion', ['codigo' => $sesion->codigo_sesion])
            ->with('success', "Inducción iniciada exitosamente con código {$codigo}. Proyecta esta pantalla a los asistentes.");
    }

    /**
     * Pantalla de presentación / proyector en vivo con visor de diapositivas/video,
     * código QR gigante y monitor de asistentes en tiempo real.
     */
    public function presentacion(string $codigo): View|RedirectResponse
    {
        $sesion = SesionInduccion::with(['induccion', 'facilitador', 'sede', 'asistencias.usuario'])
            ->where('codigo_sesion', $codigo)
            ->firstOrFail();

        // Si la sesión ya fue finalizada, redirigir al Home de Inducción para ver sus detalles y no mostrar la presentación
        if (!$sesion->estaActiva()) {
            return redirect()->route('induccion.index', ['ver_sesion' => $sesion->codigo_sesion])
                ->with('info', "La sesión {$sesion->codigo_sesion} ya ha concluido. Consulta aquí sus detalles y descarga el acta oficial.");
        }

        $urlAsistencia = route('induccion.asistencia.form', ['codigo' => $sesion->codigo_sesion]);

        // Detección de IP Local de red (para que celulares en la misma WiFi puedan acceder en modo local)
        $localIp = gethostbyname(gethostname());
        $puerto = request()->getPort();
        $puertoStr = ($puerto && !in_array($puerto, [80, 443])) ? ':' . $puerto : '';
        $urlAsistenciaLocal = 'http://' . $localIp . $puertoStr . '/asistencia/' . $sesion->codigo_sesion;
        $urlManualLocal = 'http://' . $localIp . $puertoStr . '/asistencia';
        $urlManualActual = url('/asistencia');

        // Detección automática de túnel Ngrok si está activo en desarrollo
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
                $tunnels = $ngrokData['tunnels'] ?? [];
                foreach ($tunnels as $tun) {
                    if (!empty($tun['public_url']) && str_starts_with($tun['public_url'], 'https://')) {
                        $urlNgrokBase = rtrim($tun['public_url'], '/');
                        break;
                    }
                }
                if (!$urlNgrokBase && !empty($tunnels[0]['public_url'])) {
                    $urlNgrokBase = rtrim($tunnels[0]['public_url'], '/');
                }
            }
        } catch (\Throwable $e) {
            // Silencioso si no hay túnel
        }

        $urlAsistenciaNgrok = $urlNgrokBase ? $urlNgrokBase . '/asistencia/' . $sesion->codigo_sesion : null;
        $urlManualNgrok = $urlNgrokBase ? $urlNgrokBase . '/asistencia' : null;

        // Solo construir las diapositivas y procesar recursos si la sesión sigue activa.
        // Si ya finalizó, la presentación de la inducción queda oculta y solo se muestran los participantes.
        $recursos = [];
        $slides = [];

        if ($sesion->estaActiva()) {
            // Los PowerPoint se convierten en el servidor a diapositivas HTML/SVG (sin servicios externos)
            $recursos = collect($sesion->induccion->lista_recursos)->map(function ($rec) {
                $rec['pptx'] = $rec['tipo'] === 'pptx' ? PptxRenderer::render($rec['archivo'] ?? null) : null;
                return $rec;
            })->all();

            // Si archivo_presentacion no estaba en recursos, procesarlo
            $archivosEnRecursos = collect($recursos)->pluck('archivo')->filter()->toArray();
            if (!empty($sesion->induccion->archivo_presentacion) && !in_array($sesion->induccion->archivo_presentacion, $archivosEnRecursos)) {
                $ext = strtolower(pathinfo($sesion->induccion->archivo_presentacion, PATHINFO_EXTENSION));
                $esPptx = in_array($ext, ['ppt', 'pptx', 'ppsx', 'pps']);
                $tipo = $esPptx ? 'pptx' : 'pdf';
                $recursos[] = [
                    'id' => count($recursos) + 1,
                    'tipo' => $tipo,
                    'titulo' => $esPptx ? 'Diapositivas PowerPoint' : 'Documento de Presentación',
                    'url' => null,
                    'archivo' => $sesion->induccion->archivo_presentacion,
                    'archivo_url' => asset('storage/' . $sesion->induccion->archivo_presentacion),
                    'embed_url' => null,
                    'pptx' => $esPptx ? PptxRenderer::render($sesion->induccion->archivo_presentacion) : null,
                ];
            }

            // Construir la baraja de diapositivas unificadas
            // 1. Diapositiva de Portada Institucional y Bienvenida
            $slides[] = [
                'id' => 'slide-portada',
                'tipo' => 'portada',
                'titulo' => $sesion->induccion->titulo,
                'subtitulo' => 'Inducción · C.I. Piscícola New York',
                'tipo_induccion' => $sesion->induccion->tipo ?? 'GENERAL',
            ];

            // 2. Diapositivas de Recursos Multimedia (PPTX, PDF, Video, Imagen)
            foreach ($recursos as $rIdx => $rec) {
                if ($rec['tipo'] === 'pptx' && !empty($rec['pptx']['slides'])) {
                    foreach ($rec['pptx']['slides'] as $sIdx => $slideHtml) {
                        $slides[] = [
                            'id' => 'slide-pptx-' . $rIdx . '-' . $sIdx,
                            'tipo' => 'pptx',
                            'recurso_titulo' => $rec['titulo'] ?? 'Presentación',
                            'titulo' => ($rec['titulo'] ?? 'Diapositiva') . ' (' . ($sIdx + 1) . '/' . count($rec['pptx']['slides']) . ')',
                            'html' => $slideHtml,
                            'width' => $rec['pptx']['width'] ?? 1920,
                            'height' => $rec['pptx']['height'] ?? 1080,
                        ];
                    }
                } elseif ($rec['tipo'] === 'pdf' && !empty($rec['archivo'])) {
                    $slides[] = [
                        'id' => 'slide-pdf-' . $rIdx,
                        'tipo' => 'pdf',
                        'recurso_titulo' => $rec['titulo'] ?? 'Documento PDF',
                        'titulo' => $rec['titulo'] ?? 'Documento de Inducción',
                        'archivo_url' => asset('storage/' . $rec['archivo']),
                        'archivo' => $rec['archivo'],
                    ];
                } elseif ($rec['tipo'] === 'video' && (!empty($rec['embed_url']) || !empty($rec['url']))) {
                    $slides[] = [
                        'id' => 'slide-video-' . $rIdx,
                        'tipo' => 'video',
                        'recurso_titulo' => $rec['titulo'] ?? 'Video Explicativo',
                        'titulo' => $rec['titulo'] ?? 'Video de Inducción',
                        'embed_url' => $rec['embed_url'] ?? Induccion::convertirUrlVideoEmbed($rec['url']),
                    ];
                } elseif ($rec['tipo'] === 'imagen' && !empty($rec['archivo'])) {
                    $slides[] = [
                        'id' => 'slide-img-' . $rIdx,
                        'tipo' => 'imagen',
                        'recurso_titulo' => $rec['titulo'] ?? 'Material Visual',
                        'titulo' => $rec['titulo'] ?? 'Material Visual',
                        'url' => asset('storage/' . $rec['archivo']),
                    ];
                } elseif ($rec['tipo'] === 'texto' && !empty($rec['texto'])) {
                    $slides[] = [
                        'id' => 'slide-texto-' . $rIdx,
                        'tipo' => 'texto',
                        'recurso_titulo' => $rec['titulo'] ?? 'Contenido Informativo',
                        'titulo' => $rec['titulo'] ?? 'Contenido Informativo',
                        'subtitulo' => 'Información de la Inducción',
                        'contenido' => $rec['texto'],
                    ];
                }
            }

            // 3. Si no tiene diapositivas visuales ni de texto, agregar diapositivas estructuradas por defecto
            $tieneContenidoVisual = collect($slides)->contains(fn($s) => in_array($s['tipo'], ['pptx', 'pdf', 'imagen', 'video', 'texto']));
            if (!$tieneContenidoVisual) {
                $slides[] = [
                    'id' => 'slide-objetivos',
                    'tipo' => 'temario',
                    'titulo' => 'Objetivos y Alcance de la Inducción',
                    'subtitulo' => 'C.I. Piscícola New York',
                    'contenido' => $sesion->induccion->descripcion ?: 'Inducción y lineamientos para el personal convocado.',
                ];
                $slides[] = [
                    'id' => 'slide-normas',
                    'tipo' => 'normas',
                    'titulo' => 'Políticas Corporativas y Seguridad',
                    'subtitulo' => 'Seguridad y Salud en el Trabajo / Bioseguridad',
                    'contenido' => 'Buenas prácticas operativas, uso estricto de elementos de protección personal y compromiso de calidad.',
                ];
            }

            // 4. Diapositiva de Cierre y Verificación de Asistencias
            $slides[] = [
                'id' => 'slide-cierre',
                'tipo' => 'cierre',
                'titulo' => 'Confirmación Final de Asistencia',
                'subtitulo' => 'Todos los participantes convocados deben escanear y firmar digitalmente.',
            ];
        }

        return view('induccion.presentacion', compact(
            'sesion',
            'urlAsistencia',
            'urlAsistenciaLocal',
            'urlAsistenciaNgrok',
            'localIp',
            'urlManualLocal',
            'urlManualActual',
            'urlManualNgrok',
            'urlNgrokBase',
            'recursos',
            'slides'
        ));
    }

    /**
     * Endpoint para consultar la lista actualizada de asistentes en tiempo real (Polling).
     */
    public function asistenciasStream(string $codigo): JsonResponse
    {
        $sesion = SesionInduccion::where('codigo_sesion', $codigo)->firstOrFail();

        $asistencias = AsistenciaInduccion::where('sesion_id', $sesion->id)
            ->latest('id')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'identificacion' => $a->identificacion,
                'nombre' => $a->nombre_completo,
                'cargo' => $a->cargo ?? ($a->es_externo ? 'Externo / Contratista' : 'Colaborador'),
                'area' => $a->area ?? ($a->es_externo ? ($a->empresa_externa ?? 'Externa') : 'General'),
                'sede' => $a->sede ?? 'Sede Principal',
                'es_externo' => (bool) $a->es_externo,
                'empresa' => $a->empresa_externa,
                'hora' => $a->fecha_registro ? $a->fecha_registro->format('h:i A') : '',
                'firma_url' => $a->firma_url,
            ]);

        return response()->json([
            'estado' => $sesion->estado,
            'total' => $asistencias->count(),
            'asistencias' => $asistencias,
        ]);
    }

    /**
     * Retorna los detalles completos de una sesión (Sede, Áreas, Facilitador, Asistentes y Firmas).
     */
    public function detallesSesion(string $codigo): JsonResponse
    {
        $sesion = SesionInduccion::with(['induccion', 'facilitador.area', 'sede', 'asistencias.usuario'])
            ->where('codigo_sesion', $codigo)
            ->firstOrFail();

        $areasNombres = [];
        if (!empty($sesion->areas_convocadas) && is_array($sesion->areas_convocadas)) {
            $areasNombres = Area::whereIn('id', $sesion->areas_convocadas)->pluck('nombre')->toArray();
        }

        return response()->json([
            'codigo' => $sesion->codigo_sesion,
            'estado' => $sesion->estado,
            'esta_activa' => $sesion->estaActiva(),
            'induccion' => [
                'titulo' => $sesion->induccion->titulo,
                'tipo' => $sesion->induccion->tipo ?? 'GENERAL',
                'descripcion' => $sesion->induccion->descripcion,
            ],
            'facilitador' => $sesion->facilitador->nombre_completo ?? 'Facilitador',
            'sede' => [
                'nombre' => $sesion->sede->nombre ?? 'Sede Principal',
                'ciudad' => $sesion->sede->ciudad ?? '',
            ],
            'areas_convocadas' => $areasNombres,
            'todas_las_areas' => empty($areasNombres),
            'areas_texto' => !empty($areasNombres) ? implode(', ', $areasNombres) : 'Todas las áreas convocadas',
            'permitir_externos' => (bool) $sesion->permitir_externos,
            'fecha_inicio' => $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y h:i A') : '',
            'fecha_cierre' => $sesion->fecha_cierre ? $sesion->fecha_cierre->format('d/m/Y h:i A') : 'En curso',
            'total_asistentes' => $sesion->asistencias->count(),
            'total_internos' => $sesion->asistencias->where('es_externo', false)->count(),
            'total_externos' => $sesion->asistencias->where('es_externo', true)->count(),
            'acta_url' => route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]),
            'presentacion_url' => route('induccion.presentacion', ['codigo' => $sesion->codigo_sesion]),
            'asistencias' => $sesion->asistencias->map(fn($a, $idx) => [
                'numero' => $idx + 1,
                'nombre' => $a->nombre_completo,
                'identificacion' => $a->identificacion,
                'cargo' => $a->cargo ?? ($a->es_externo ? 'Externo / Contratista' : 'Colaborador'),
                'area' => $a->area ?? ($a->es_externo ? ($a->empresa_externa ?? 'Externa') : 'General'),
                'es_externo' => (bool) $a->es_externo,
                'empresa' => $a->empresa_externa,
                'hora' => $a->fecha_registro ? $a->fecha_registro->format('h:i A') : '',
                'firma_url' => $a->firma_url,
            ]),
        ]);
    }

    /**
     * Cierra la sesión en vivo de la inducción.
     */
    public function finalizarSesion(string $codigo): JsonResponse|RedirectResponse
    {
        $sesion = SesionInduccion::where('codigo_sesion', $codigo)->firstOrFail();
        $sesion->update([
            'estado' => 'FINALIZADA',
            'fecha_cierre' => now(),
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'La sesión ha sido finalizada exitosamente.',
            ]);
        }

        return redirect()->route('induccion.index', ['ver_sesion' => $codigo])
            ->with('success', 'La sesión ha sido finalizada. Ya puedes consultar los detalles y descargar el acta.');
    }

    /**
     * Descarga el Acta Oficial de Asistencia en PDF con membrete y firmas digitales capturadas.
     */
    public function descargarActaPdf(string $codigo)
    {
        $sesion = SesionInduccion::with(['induccion', 'facilitador.area', 'sede', 'asistencias.usuario'])
            ->where('codigo_sesion', $codigo)
            ->firstOrFail();

        $pdf = Pdf::loadView('induccion.acta_pdf', compact('sesion'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $nombreArchivo = 'Acta_Induccion_' . $sesion->codigo_sesion . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Retorna las diapositivas convertidas a HTML/SVG en formato JSON para el visor interactivo.
     */
    public function slidesJson(Request $request, Induccion $induccion): JsonResponse
    {
        $archivo = $request->query('archivo');

        if (empty($archivo)) {
            $pptxRec = collect($induccion->lista_recursos)->firstWhere('tipo', 'pptx');
            $archivo = $pptxRec['archivo'] ?? $induccion->archivo_presentacion;
        }

        if (empty($archivo)) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró archivo de diapositivas PowerPoint para esta inducción.',
                'slides' => [],
            ], 404);
        }

        $archivoRelativo = preg_replace('#^storage/#', '', $archivo);
        $pptxData = PptxRenderer::render($archivoRelativo);

        if (!$pptxData || empty($pptxData['slides'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudieron renderizar las diapositivas de la presentación.',
                'slides' => [],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'titulo' => $induccion->titulo,
            'archivo' => $archivoRelativo,
            'archivo_url' => asset('storage/' . $archivoRelativo),
            'width' => $pptxData['width'] ?? 1920,
            'height' => $pptxData['height'] ?? 1080,
            'total' => count($pptxData['slides']),
            'slides' => $pptxData['slides'],
        ]);
    }

    /**
     * Vista de pantalla completa dedicada para visualizar diapositivas de la inducción.
     */
    public function visorDiapositivas(Request $request, Induccion $induccion): View|RedirectResponse
    {
        $archivo = $request->query('archivo');

        if (empty($archivo)) {
            $pptxRec = collect($induccion->lista_recursos)->firstWhere('tipo', 'pptx');
            $archivo = $pptxRec['archivo'] ?? $induccion->archivo_presentacion;
        }

        if (empty($archivo)) {
            return redirect()->back()->with('error', 'Esta inducción no contiene diapositivas PowerPoint para visualizar.');
        }

        $archivoRelativo = preg_replace('#^storage/#', '', $archivo);
        $pptxData = PptxRenderer::render($archivoRelativo);

        return view('induccion.visor_diapositivas', compact('induccion', 'archivoRelativo', 'pptxData'));
    }
}

