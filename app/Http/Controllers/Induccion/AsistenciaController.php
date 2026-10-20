<?php

namespace App\Http\Controllers\Induccion;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaInduccion;
use App\Models\SesionInduccion;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    /**
     * Muestra el formulario móvil de registro de asistencia a la inducción.
     */
    public function formulario(?string $codigo = null): View
    {
        $codigo = $codigo ? strtoupper(trim($codigo)) : null;
        $sesion = null;
        $errorMensaje = null;

        if ($codigo) {
            $sesion = SesionInduccion::with(['induccion', 'facilitador', 'sede'])
                ->where('codigo_sesion', $codigo)
                ->first();

            if (!$sesion) {
                $errorMensaje = "No se encontró ninguna sesión de inducción con el código '{$codigo}'.";
            } elseif (!$sesion->estaActiva()) {
                $errorMensaje = "Esta sesión de inducción ya fue finalizada por el facilitador y no admite nuevos registros.";
            }
        }

        return view('induccion.asistencia', compact('sesion', 'codigo', 'errorMensaje'));
    }

    /**
     * Busca la cédula en tiempo real para autocompletar si es empleado o desplegar campos para externo.
     */
    public function buscarIdentificacion(Request $request): JsonResponse
    {
        $identificacion = trim((string) $request->input('identificacion', ''));
        $codigo = trim((string) $request->input('codigo_sesion', ''));

        if ($identificacion === '') {
            return response()->json(['error' => 'Ingresa un número de documento.'], 422);
        }

        $sesion = SesionInduccion::where('codigo_sesion', $codigo)->first();
        if (!$sesion) {
            return response()->json(['error' => 'Sesión no encontrada o no válida.'], 404);
        }

        // Verificar si ya registró asistencia en esta sesión
        $yaRegistrado = AsistenciaInduccion::where('sesion_id', $sesion->id)
            ->where('identificacion', $identificacion)
            ->exists();

        if ($yaRegistrado) {
            return response()->json([
                'ya_registrado' => true,
                'mensaje' => 'Ya registraste tu asistencia para esta sesión de inducción.',
            ]);
        }

        // Buscar usuario en la base de datos interna de PNY
        $usuario = Usuario::with(['area', 'sede'])
            ->where('identificacion', $identificacion)
            ->where('estado', true)
            ->first();

        if ($usuario) {
            return response()->json([
                'found' => true,
                'es_externo' => false,
                'usuario_id' => $usuario->id,
                'nombre_completo' => $usuario->nombre_completo,
                'cargo' => $usuario->cargoOficial(),
                'area' => $usuario->area?->nombre ?? 'Sin área asignada',
                'sede' => $usuario->sedeActual()?->nombre ?? 'Sede Principal',
            ]);
        }

        // Si no existe, es persona externa (contratista, pasante, visitante)
        return response()->json([
            'found' => false,
            'es_externo' => true,
            'identificacion' => $identificacion,
        ]);
    }

    /**
     * Guarda la asistencia del participante con su firma digital táctil.
     */
    public function registrar(Request $request, string $codigo): JsonResponse|RedirectResponse
    {
        $sesion = SesionInduccion::where('codigo_sesion', strtoupper(trim($codigo)))
            ->where('estado', 'EN_CURSO')
            ->first();

        if (!$sesion) {
            return response()->json([
                'success' => false,
                'message' => 'La sesión de inducción no existe o ya no se encuentra activa.',
            ], 422);
        }

        $request->validate([
            'identificacion' => ['required', 'string', 'max:30'],
            'nombre_completo' => ['required', 'string', 'max:150'],
            'firma_digital' => ['required', 'string'], // Base64 data URI del canvas
            'es_externo' => ['nullable'],
            'empresa_externa' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
        ], [
            'identificacion.required' => 'El número de identificación es obligatorio.',
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'firma_digital.required' => 'Debes dibujar tu firma en el recuadro para confirmar la asistencia.',
        ]);

        $identificacion = trim($request->identificacion);

        // Prevenir duplicados
        $yaExiste = AsistenciaInduccion::where('sesion_id', $sesion->id)
            ->where('identificacion', $identificacion)
            ->exists();

        if ($yaExiste) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro de asistencia con este número de documento en esta sesión.',
            ], 422);
        }

        // Procesar y guardar la firma digital en PNG
        $firmaData = $request->firma_digital;
        $rutaFirma = null;

        if (preg_match('/^data:image\/(\w+);base64,/', $firmaData, $type)) {
            $data = substr($firmaData, strpos($firmaData, ',') + 1);
            $type = strtolower($type[1]); // png, jpeg, etc.
            $data = base64_decode($data);

            if ($data !== false) {
                $nombreArchivo = 'firma_' . $sesion->id . '_' . $identificacion . '_' . time() . '.' . $type;
                $rutaFirma = 'firmas_asistencias/' . $nombreArchivo;
                Storage::disk('public')->put($rutaFirma, $data);
            }
        }

        // Si falló el guardado como archivo, almacenar el base64 directamente
        if (!$rutaFirma) {
            $rutaFirma = $firmaData;
        }

        $esExterno = filter_var($request->input('es_externo', false), FILTER_VALIDATE_BOOLEAN);

        // Si es interno, recuperar sus datos institucionales para mayor precisión
        $usuario = Usuario::with(['area', 'sede'])
            ->where('identificacion', $identificacion)
            ->where('estado', true)
            ->first();

        $asistencia = AsistenciaInduccion::create([
            'sesion_id' => $sesion->id,
            'usuario_id' => $usuario?->id,
            'identificacion' => $identificacion,
            'nombre_completo' => $usuario ? $usuario->nombre_completo : trim($request->nombre_completo),
            'cargo' => $usuario ? $usuario->cargoOficial() : 'Externo / Contratista',
            'area' => $usuario ? ($usuario->area?->nombre ?? 'General') : ($request->empresa_externa ? trim($request->empresa_externa) : 'Externo'),
            'sede' => $usuario ? ($usuario->sedeActual()?->nombre ?? 'Sede Principal') : $sesion->sede->nombre,
            'es_externo' => $usuario ? false : true,
            'empresa_externa' => $usuario ? 'C.I. Piscícola New York' : ($request->empresa_externa ? trim($request->empresa_externa) : 'Independiente / Visitante'),
            'telefono' => $request->telefono ? trim($request->telefono) : null,
            'firma_digital' => $rutaFirma,
            'fecha_registro' => now(),
            'ip_origen' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Asistencia registrada correctamente!',
            'nombre' => $asistencia->nombre_completo,
            'codigo' => $sesion->codigo_sesion,
            'hora' => $asistencia->fecha_registro->format('h:i A'),
        ]);
    }
}
