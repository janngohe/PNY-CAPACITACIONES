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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacilitadorController extends Controller
{
    /**
     * Muestra la pantalla de acceso rápido para Facilitadores (Cédula + PIN).
     */
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('facilitador_usuario_id')) {
            return redirect()->route('induccion.index');
        }

        return view('induccion.acceso');
    }

    /**
     * Valida la Cédula y el PIN de 4 dígitos.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identificacion' => ['required', 'string'],
            'pin' => ['required', 'string', 'size:4'],
        ], [
            'identificacion.required' => 'Ingresa tu número de documento.',
            'pin.required' => 'Ingresa el código PIN de 4 dígitos.',
            'pin.size' => 'El PIN debe ser exactamente de 4 dígitos.',
        ]);

        $pinCorrecto = Configuracion::getPinInduccion();

        if (trim($request->pin) !== trim($pinCorrecto)) {
            return back()->withInput()->with('error', 'El código PIN de 4 dígitos es incorrecto. Solicítalo al Administrador o a tu Jefe de Área.');
        }

        $usuario = Usuario::where('identificacion', trim($request->identificacion))
            ->where('estado', true)
            ->first();

        if (!$usuario) {
            return back()->withInput()->with('error', 'El documento ingresado no corresponde a un colaborador activo en la plataforma.');
        }

        // Iniciar sesión del facilitador
        session(['facilitador_usuario_id' => $usuario->id]);

        return redirect()->route('induccion.index')
            ->with('success', "¡Bienvenido(a), {$usuario->nombre_completo}! Has ingresado al portal de Inducciones.");
    }

    /**
     * Cierra la sesión del facilitador.
     */
    public function logout(): RedirectResponse
    {
        session()->forget('facilitador_usuario_id');
        return redirect()->route('induccion.acceso')->with('info', 'Has salido del módulo de inducciones.');
    }

    /**
     * Panel principal del Facilitador: inducciones disponibles y sesiones recientes.
     */
    public function index(Request $request): View
    {
        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();
        $facilitador = Usuario::with('area', 'sede')->find($facilitadorId);

        $inducciones = Induccion::with(['sedes', 'areas', 'creador'])
            ->withCount('sesiones')
            ->where('estado', true)
            ->latest()
            ->get();

        $sesionesRecientes = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
            ->withCount('asistencias')
            ->latest()
            ->take(8)
            ->get();

        $sedes = Sede::where('estado', true)->orderBy('nombre')->get();
        $areas = Area::where('estado', true)->orderBy('nombre')->get();

        return view('induccion.index', compact('facilitador', 'inducciones', 'sesionesRecientes', 'sedes', 'areas'));
    }

    /**
     * Formulario para crear una nueva inducción.
     */
    public function create(): View
    {
        $sedes = Sede::where('estado', true)->orderBy('nombre')->get();
        $areas = Area::where('estado', true)->orderBy('nombre')->get();

        return view('induccion.create', compact('sedes', 'areas'));
    }

    /**
     * Almacena una nueva inducción con diapositivas/PDF y video en iframe opcional.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:3000'],
            'tipo' => ['required', 'string', 'max:50'],
            'archivo_presentacion' => ['nullable', 'file', 'mimes:pdf', 'max:51200'], // Máximo 50 MB
            'video_url' => ['nullable', 'url', 'max:500'],
            'sedes' => ['nullable', 'array'],
            'sedes.*' => ['exists:sedes,id'],
            'areas' => ['nullable', 'array'],
            'areas.*' => ['exists:areas,id'],
        ], [
            'titulo.required' => 'El título de la inducción es obligatorio.',
            'archivo_presentacion.mimes' => 'El archivo de presentación debe ser un documento PDF.',
            'archivo_presentacion.max' => 'El PDF no puede superar los 50 MB.',
            'video_url.url' => 'Ingresa una URL de video válida (ej: YouTube o Vimeo).',
        ]);

        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();

        $rutaArchivo = null;
        if ($request->hasFile('archivo_presentacion')) {
            $archivo = $request->file('archivo_presentacion');
            $nombreLimpio = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo->getClientOriginalName());
            $rutaArchivo = $archivo->storeAs('inducciones/presentaciones', $nombreLimpio, 'public');
        }

        $induccion = Induccion::create([
            'titulo' => trim($request->titulo),
            'descripcion' => $request->descripcion ? trim($request->descripcion) : null,
            'tipo' => $request->tipo,
            'archivo_presentacion' => $rutaArchivo,
            'video_url' => $request->video_url ? trim($request->video_url) : null,
            'estado' => true,
            'creado_por' => $facilitadorId,
        ]);

        if (!empty($request->sedes)) {
            $induccion->sedes()->sync($request->sedes);
        }

        if (!empty($request->areas)) {
            $induccion->areas()->sync($request->areas);
        }

        return redirect()->route('induccion.index')
            ->with('success', "La inducción '{$induccion->titulo}' fue creada exitosamente.");
    }

    /**
     * Inicia una sesión en vivo para una inducción específica en una sede y áreas seleccionadas.
     */
    public function iniciarSesion(Request $request, Induccion $induccion): RedirectResponse
    {
        $request->validate([
            'sede_id' => ['required', 'exists:sedes,id'],
            'areas_convocadas' => ['nullable', 'array'],
        ]);

        $facilitadorId = session('facilitador_usuario_id') ?? auth()->id();
        $codigo = SesionInduccion::generarCodigoUnico();

        $sesion = SesionInduccion::create([
            'induccion_id' => $induccion->id,
            'facilitador_id' => $facilitadorId,
            'sede_id' => $request->sede_id,
            'codigo_sesion' => $codigo,
            'estado' => 'EN_CURSO',
            'fecha_inicio' => now(),
            'areas_convocadas' => $request->areas_convocadas ?? [],
        ]);

        return redirect()->route('induccion.presentacion', ['codigo' => $sesion->codigo_sesion])
            ->with('success', "Sesión en vivo iniciada con código {$codigo}. Proyecta esta pantalla a los asistentes.");
    }

    /**
     * Pantalla de presentación / proyector en vivo con visor de diapositivas/video,
     * código QR gigante y monitor de asistentes en tiempo real.
     */
    public function presentacion(string $codigo): View
    {
        $sesion = SesionInduccion::with(['induccion.sedes', 'induccion.areas', 'facilitador', 'sede', 'asistencias'])
            ->where('codigo_sesion', $codigo)
            ->firstOrFail();

        $urlAsistencia = route('induccion.asistencia.form', ['codigo' => $sesion->codigo_sesion]);

        return view('induccion.presentacion', compact('sesion', 'urlAsistencia'));
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
            ]);

        return response()->json([
            'estado' => $sesion->estado,
            'total' => $asistencias->count(),
            'asistencias' => $asistencias,
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

        return redirect()->route('induccion.presentacion', ['codigo' => $codigo])
            ->with('success', 'La sesión ha sido finalizada. Ya puedes descargar el acta oficial de asistencia.');
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
}
