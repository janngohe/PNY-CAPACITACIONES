<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AsistenciaInduccion;
use App\Models\Configuracion;
use App\Models\Induccion;
use App\Models\Sede;
use App\Models\SesionInduccion;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PptxRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InduccionAdminController extends Controller
{
    /**
     * Catálogo de Inducciones publicadas (CRUD).
     */
    public function index(Request $request): View
    {
        $pinActual = Configuracion::getPinInduccion();

        $query = Induccion::with(['creador'])
            ->withCount(['sesiones', 'asistencias'])
            ->latest();

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $inducciones = $query->paginate(6)->withQueryString();

        $totalInducciones = Induccion::count();
        $induccionesActivas = Induccion::where('estado', true)->count();
        $totalSesiones = SesionInduccion::count();
        $totalAsistencias = AsistenciaInduccion::count();

        $detalleInducciones = $inducciones->getCollection()->mapWithKeys(function ($ind) {
            return [$ind->id => [
                'id' => $ind->id,
                'titulo' => $ind->titulo,
                'descripcion' => $ind->descripcion,
                'imagen_portada_url' => $ind->imagen_portada_url,
                'creador' => $ind->creador?->nombre_completo ?? 'Administración',
                'fecha_creacion' => $ind->created_at ? $ind->created_at->format('d/m/Y') : '—',
                'sesiones_count' => $ind->sesiones_count ?? 0,
                'asistencias_count' => $ind->asistencias_count ?? 0,
                'recursos' => $ind->lista_recursos,
                'contenido_diapositivas' => $ind->contenido_diapositivas,
                'tiene_pptx' => collect($ind->lista_recursos)->contains('tipo', 'pptx'),
                'tiene_video' => collect($ind->lista_recursos)->contains('tipo', 'video'),
                'tiene_pdf' => collect($ind->lista_recursos)->contains('tipo', 'pdf'),
                'tiene_imagen' => collect($ind->lista_recursos)->contains('tipo', 'imagen'),
                'tiene_texto' => collect($ind->lista_recursos)->contains('tipo', 'texto'),
            ]];
        });

        $estadisticas = [
            'total_inducciones' => $totalInducciones,
            'inducciones_activas' => $induccionesActivas,
            'total_sesiones' => $totalSesiones,
            'total_asistencias' => $totalAsistencias,
        ];

        return view('admin.inducciones.index', compact('pinActual', 'inducciones', 'detalleInducciones', 'estadisticas'));
    }

    /**
     * Historial de sesiones de inducción con buscador en tiempo real.
     */
    public function sesiones(Request $request): View
    {
        $query = SesionInduccion::with([
            'induccion',
            'facilitador',
            'sede',
            'asistencias.usuario.area'
        ])
        ->withCount('asistencias')
        ->latest();

        if ($request->filled('estado')) {
            $est = strtoupper(trim($request->estado));
            if ($est === 'EN_CURSO') {
                $query->where('estado', 'EN_CURSO');
            } elseif ($est === 'FINALIZADA') {
                $query->where('estado', '!=', 'EN_CURSO');
            }
        }

        $sesiones = $query->paginate(40)->withQueryString();

        $totalSesiones = SesionInduccion::count();
        $sesionesEnCurso = SesionInduccion::where('estado', 'EN_CURSO')->count();
        $sesionesFinalizadas = SesionInduccion::where('estado', '!=', 'EN_CURSO')->count();
        $totalAsistencias = AsistenciaInduccion::count();
        $asistenciasExternos = AsistenciaInduccion::where('es_externo', true)->count();

        $sedes = Sede::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'ciudad']);
        $areas = Area::with('sede')->where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'sede_id']);

        $metricas = [
            'total_sesiones' => $totalSesiones,
            'en_curso' => $sesionesEnCurso,
            'finalizadas' => $sesionesFinalizadas,
            'total_asistencias' => $totalAsistencias,
            'asistencias_externos' => $asistenciasExternos,
        ];

        return view('admin.inducciones.sesiones', compact('sesiones', 'metricas', 'sedes', 'areas'));
    }

    /**
     * Muestra el formulario para crear una nueva inducción.
     */
    public function create(): View
    {
        return view('admin.inducciones.create');
    }

    /**
     * Guarda una nueva inducción en la base de datos con portada y recursos multimedia.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'recursos' => ['nullable', 'array'],
        ], [
            'titulo.required' => 'El título de la inducción es obligatorio.',
            'imagen_portada.image' => 'El archivo de portada debe ser una imagen válida (JPG, PNG o WEBP).',
            'imagen_portada.max' => 'La imagen de portada no debe superar los 10 MB.',
        ]);

        $adminId = auth()->id();

        // 1. Guardar Imagen de Portada
        $rutaPortada = null;
        if ($request->hasFile('imagen_portada')) {
            $archivoImg = $request->file('imagen_portada');
            $nombreImg = time() . '_portada_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivoImg->getClientOriginalName());
            $rutaPortada = $archivoImg->storeAs('inducciones/portadas', $nombreImg, 'public');
        }

        // 2. Procesar Recursos Multimedia
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
            'archivo_presentacion' => $primerArchivo,
            'video_url' => $primerVideo,
            'recursos' => $recursosProcesados,
            'estado' => true,
            'creado_por' => $adminId,
        ]);

        return redirect()->route('admin.inducciones.index')
            ->with('success', "La inducción '{$induccion->titulo}' fue creada exitosamente.");
    }

    /**
     * Muestra el formulario para editar una inducción existente.
     */
    public function edit(Induccion $induccion): View
    {
        $totalSesiones = $induccion->sesiones()->count();
        $totalAsistencias = AsistenciaInduccion::whereIn('sesion_id', $induccion->sesiones()->pluck('id'))->count();
        $tieneRegistros = ($totalSesiones > 0 || $totalAsistencias > 0);

        return view('admin.inducciones.edit', compact('induccion', 'tieneRegistros', 'totalSesiones', 'totalAsistencias'));
    }

    /**
     * Actualiza los datos y recursos de una inducción en la base de datos.
     */
    public function update(Request $request, Induccion $induccion): RedirectResponse
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'imagen_portada' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'recursos' => ['nullable', 'array'],
        ], [
            'titulo.required' => 'El título de la inducción es obligatorio.',
            'imagen_portada.image' => 'El archivo de portada debe ser una imagen válida (JPG, PNG o WEBP).',
            'imagen_portada.max' => 'La imagen de portada no debe superar los 10 MB.',
        ]);

        // 1. Imagen de Portada
        $rutaPortada = $induccion->imagen_portada;
        if ($request->hasFile('imagen_portada')) {
            if ($rutaPortada && Storage::disk('public')->exists($rutaPortada)) {
                Storage::disk('public')->delete($rutaPortada);
            }
            $archivoImg = $request->file('imagen_portada');
            $nombreImg = time() . '_portada_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivoImg->getClientOriginalName());
            $rutaPortada = $archivoImg->storeAs('inducciones/portadas', $nombreImg, 'public');
        } elseif ($request->boolean('eliminar_portada')) {
            if ($rutaPortada && Storage::disk('public')->exists($rutaPortada)) {
                Storage::disk('public')->delete($rutaPortada);
            }
            $rutaPortada = null;
        }

        // 2. Procesar Recursos Multimedia
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
                $rutaArchivo = $rec['archivo_actual'] ?? null;

                // Si se subió un nuevo archivo reemplazando el anterior
                if (isset($archivosInput[$idx]['archivo']) && $archivosInput[$idx]['archivo']->isValid()) {
                    if ($rutaArchivo && Storage::disk('public')->exists($rutaArchivo)) {
                        Storage::disk('public')->delete($rutaArchivo);
                    }
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

        $induccion->update([
            'titulo' => trim($request->titulo),
            'descripcion' => $request->descripcion ? trim($request->descripcion) : null,
            'imagen_portada' => $rutaPortada,
            'archivo_presentacion' => $primerArchivo,
            'video_url' => $primerVideo,
            'recursos' => $recursosProcesados,
            'estado' => $request->has('estado') ? $request->boolean('estado') : $induccion->estado,
        ]);

        return redirect()->route('admin.inducciones.index')
            ->with('success', "La inducción '{$induccion->titulo}' fue actualizada exitosamente.");
    }

    /**
     * Elimina permanentemente una inducción y sus archivos asociados (solo si no tiene registros).
     */
    public function destroy(Induccion $induccion): RedirectResponse
    {
        $totalSesiones = $induccion->sesiones()->count();
        $totalAsistencias = AsistenciaInduccion::whereIn('sesion_id', $induccion->sesiones()->pluck('id'))->count();

        if ($totalSesiones > 0 || $totalAsistencias > 0) {
            return redirect()->route('admin.inducciones.index')
                ->with('error', "No es posible eliminar la inducción '{$induccion->titulo}' porque ya cuenta con registros históricos ({$totalSesiones} sesiones, {$totalAsistencias} asistencias). Solo puedes desactivarla para que no se proyecte en la sala.");
        }

        $titulo = $induccion->titulo;

        // Eliminar archivos físicos
        if ($induccion->imagen_portada && Storage::disk('public')->exists($induccion->imagen_portada)) {
            Storage::disk('public')->delete($induccion->imagen_portada);
        }
        if (!empty($induccion->recursos) && is_array($induccion->recursos)) {
            foreach ($induccion->recursos as $rec) {
                if (!empty($rec['archivo']) && Storage::disk('public')->exists($rec['archivo'])) {
                    Storage::disk('public')->delete($rec['archivo']);
                }
            }
        }

        $induccion->delete();

        return redirect()->route('admin.inducciones.index')
            ->with('success', "La inducción '{$titulo}' fue eliminada exitosamente.");
    }

    /**
     * Alterna rápidamente el estado (Activa / Inactiva) de una inducción.
     */
    public function toggleEstado(Induccion $induccion): RedirectResponse|JsonResponse
    {
        $induccion->estado = !$induccion->estado;
        $induccion->save();

        $estadoTexto = $induccion->estado ? 'activada' : 'desactivada';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'estado' => $induccion->estado,
                'message' => "La inducción '{$induccion->titulo}' ha sido {$estadoTexto}.",
            ]);
        }

        return back()->with('success', "La inducción '{$induccion->titulo}' ha sido {$estadoTexto}.");
    }

    /**
     * Regenera el PIN de 4 dígitos previa validación de la contraseña del Administrador.
     */
    public function regenerarPin(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'password_confirmacion' => ['required', 'string'],
        ], [
            'password_confirmacion.required' => 'Debes ingresar tu contraseña de administrador para confirmar.',
        ]);

        $admin = auth()->user();

        if (!Hash::check($request->password_confirmacion, $admin->password)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña de administrador incorrecta. No se ha modificado el PIN.',
                ], 422);
            }

            return back()->with('error', 'Contraseña de administrador incorrecta. Por seguridad, el PIN no fue modificado.');
        }

        $nuevoPin = Configuracion::regenerarPinInduccion();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "El PIN de inducción ha sido regenerado exitosamente a: {$nuevoPin}",
                'nuevo_pin' => $nuevoPin,
            ]);
        }

        return back()->with('success', "¡PIN regenerado exitosamente! El nuevo código de 4 dígitos es: {$nuevoPin}");
    }

    /**
     * Descarga el acta oficial en PDF de una sesión de inducción.
     */
    public function descargarActaPdf(SesionInduccion $sesion)
    {
        $sesion->loadMissing(['induccion', 'facilitador.area', 'sede', 'asistencias.usuario']);

        $pdf = Pdf::loadView('induccion.acta_pdf', compact('sesion'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        $nombreArchivo = 'Acta_Induccion_' . $sesion->codigo_sesion . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Retorna las diapositivas convertidas a HTML/SVG en formato JSON para el visor interactivo (Admin).
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
     * Vista de pantalla completa dedicada para visualizar diapositivas de la inducción (Admin).
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

    /**
     * Retorna el listado de participantes y firmas digitales de una sesión en JSON para visualización interactiva.
     */
    public function participantesJson(SesionInduccion $sesion): JsonResponse
    {
        $sesion->load([
            'induccion',
            'facilitador',
            'sede',
            'asistencias.usuario.area',
            'asistencias.usuario.sede',
        ]);

        $participantes = $sesion->asistencias->map(function ($asist) {
            $u = $asist->usuario;
            return [
                'id' => $asist->id,
                'identificacion' => $asist->identificacion ?: ($u?->identificacion ?? '—'),
                'nombre_completo' => $asist->nombre_completo ?: ($u?->nombre_completo ?? 'Colaborador'),
                'cargo' => $asist->cargo ?: ($u?->cargoOficial() ?? '—'),
                'sede' => $asist->sede ?: ($u?->sedeActual()?->nombre ?? 'Sin sede'),
                'area' => $asist->area ?: ($u?->area?->nombre ?? 'Sin área'),
                'es_externo' => (bool)$asist->es_externo,
                'empresa_externa' => $asist->empresa_externa,
                'telefono' => $asist->telefono,
                'fecha_registro' => $asist->fecha_registro ? $asist->fecha_registro->format('d/m/Y h:i A') : ($asist->created_at ? $asist->created_at->format('d/m/Y h:i A') : '—'),
                'firma_url' => $asist->firma_url,
                'tiene_firma' => !empty($asist->firma_digital),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'sesion' => [
                'id' => $sesion->id,
                'codigo' => $sesion->codigo_sesion,
                'induccion_titulo' => $sesion->induccion?->titulo ?? 'Inducción sin título',
                'facilitador' => $sesion->facilitador?->nombre_completo ?? 'Facilitador no asignado',
                'sede' => $sesion->sede?->nombre ?? 'Sede Principal',
                'areas_convocadas' => $sesion->areas_convocadas_nombres,
                'areas_convocadas_array' => $sesion->areas_convocadas_nombres_array,
                'fecha_inicio' => $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y h:i A') : 'N/A',
                'fecha_cierre' => $sesion->fecha_cierre ? $sesion->fecha_cierre->format('d/m/Y h:i A') : null,
                'estado' => $sesion->estado,
                'es_activa' => $sesion->estaActiva(),
                'total_participantes' => $participantes->count(),
                'total_externos' => $participantes->where('es_externo', true)->count(),
                'acta_pdf_url' => route('admin.inducciones.acta_pdf', $sesion->id),
            ],
            'participantes' => $participantes,
        ]);
    }
}

