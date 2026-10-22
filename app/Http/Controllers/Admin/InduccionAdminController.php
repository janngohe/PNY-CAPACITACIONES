<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaInduccion;
use App\Models\Configuracion;
use App\Models\Induccion;
use App\Models\SesionInduccion;
use Barryvdh\DomPDF\Facade\Pdf;
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

        $query = Induccion::with(['sedes', 'areas', 'creador'])
            ->withCount(['sesiones', 'asistencias'])
            ->latest();

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $inducciones = $query->paginate(12)->withQueryString();

        $totalInducciones = Induccion::count();
        $induccionesActivas = Induccion::where('estado', true)->count();
        $totalSesiones = SesionInduccion::count();
        $totalAsistencias = AsistenciaInduccion::count();

        $estadisticas = [
            'total_inducciones' => $totalInducciones,
            'inducciones_activas' => $induccionesActivas,
            'total_sesiones' => $totalSesiones,
            'total_asistencias' => $totalAsistencias,
        ];

        return view('admin.inducciones.index', compact('pinActual', 'inducciones', 'estadisticas'));
    }

    /**
     * Historial de sesiones de inducción con buscador en tiempo real.
     */
    public function sesiones(Request $request): View
    {
        $query = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
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

        $metricas = [
            'total_sesiones' => $totalSesiones,
            'en_curso' => $sesionesEnCurso,
            'finalizadas' => $sesionesFinalizadas,
            'total_asistencias' => $totalAsistencias,
            'asistencias_externos' => $asistenciasExternos,
        ];

        return view('admin.inducciones.sesiones', compact('sesiones', 'metricas'));
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
                if ($tipo !== 'video' && empty($rutaArchivo) && empty($url)) {
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
                if ($tipo !== 'video' && empty($rutaArchivo) && empty($url)) {
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
}
