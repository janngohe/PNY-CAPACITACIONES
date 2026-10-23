<?php

namespace App\Http\Controllers\Induccion;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AsistenciaInduccion;
use App\Models\Sede;
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

        // Cargar sedes activas con sus áreas activas para el formulario de Personal Nuevo
        $sedes = Sede::with(['areas' => function ($query) {
            $query->where('estado', true)->orderBy('nombre');
        }])
        ->where('estado', true)
        ->orderBy('nombre')
        ->get();

        return view('induccion.asistencia', compact('sesion', 'codigo', 'errorMensaje', 'sedes'));
    }

    /**
     * Busca la cédula en tiempo real para autocompletar si es empleado o desplegar campos para externo / personal nuevo.
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

        // Buscar usuario en la base de datos interna de PNY
        $usuario = Usuario::with(['area', 'sede'])
            ->where('identificacion', $identificacion)
            ->where('estado', true)
            ->first();

        // 1. Validar que el facilitador que dicta la sesión no pueda marcar asistencia
        $sesion->loadMissing('facilitador');
        $esFacilitador = false;
        if ($sesion->facilitador_id && $usuario && (int)$usuario->id === (int)$sesion->facilitador_id) {
            $esFacilitador = true;
        } elseif ($sesion->facilitador && trim((string)$sesion->facilitador->identificacion) === $identificacion) {
            $esFacilitador = true;
        }

        if ($esFacilitador) {
            return response()->json([
                'bloqueado' => true,
                'motivo' => 'es_facilitador',
                'mensaje' => 'Eres el facilitador que está impartiendo esta sesión de inducción. El facilitador no debe registrar asistencia como participante.',
            ]);
        }

        // 2. Verificar si ya registró asistencia en esta sesión
        $yaRegistrado = AsistenciaInduccion::where('sesion_id', $sesion->id)
            ->where('identificacion', $identificacion)
            ->exists();

        if ($yaRegistrado) {
            return response()->json([
                'ya_registrado' => true,
                'mensaje' => 'Ya registraste tu asistencia para esta sesión de inducción.',
            ]);
        }

        if ($usuario) {
            // Verificar si la sesión tiene áreas convocadas específicas
            $areasConvocadas = $sesion->areas_convocadas ?? [];
            if (is_array($areasConvocadas) && count($areasConvocadas) > 0) {
                $areasConvocadasIds = array_map('intval', $areasConvocadas);
                $usuarioAreaId = (int) $usuario->area_id;

                if (!in_array($usuarioAreaId, $areasConvocadasIds, true)) {
                    $nombresConvocadas = Area::whereIn('id', $areasConvocadasIds)->pluck('nombre')->join(', ');
                    return response()->json([
                        'bloqueado' => true,
                        'motivo' => 'area_no_convocada',
                        'mensaje' => "Tu área ('" . ($usuario->area?->nombre ?? 'Sin área asignada') . "') no está convocada para esta inducción. Esta sesión está dirigida a: {$nombresConvocadas}.",
                    ]);
                }
            }

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

        // Si no está registrado en nómina:
        // Se permite registrar como Personal Nuevo o como Persona Externa (si la sesión lo permite)
        return response()->json([
            'found' => false,
            'permitir_externos' => (bool) $sesion->permitir_externos,
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

        $identificacion = trim((string) $request->identificacion);

        // Validar si es usuario interno registrado en nómina
        $usuario = Usuario::with(['area', 'sede'])
            ->where('identificacion', $identificacion)
            ->where('estado', true)
            ->first();

        // 1. Validar que el facilitador que dicta la sesión no pueda marcar asistencia
        $sesion->loadMissing('facilitador');
        $esFacilitador = false;
        if ($sesion->facilitador_id && $usuario && (int)$usuario->id === (int)$sesion->facilitador_id) {
            $esFacilitador = true;
        } elseif ($sesion->facilitador && trim((string)$sesion->facilitador->identificacion) === $identificacion) {
            $esFacilitador = true;
        }

        if ($esFacilitador) {
            return response()->json([
                'success' => false,
                'message' => 'Eres el facilitador responsable de impartir esta sesión de inducción. No puedes registrar asistencia como participante.',
            ], 422);
        }

        // 2. Prevenir duplicados
        $yaExiste = AsistenciaInduccion::where('sesion_id', $sesion->id)
            ->where('identificacion', $identificacion)
            ->exists();

        if ($yaExiste) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro de asistencia con este número de documento en esta sesión.',
            ], 422);
        }

        $tipoRegistro = $request->input('tipo_registro', 'nuevo');

        if ($usuario) {
            // Empleado reconocido en nómina
            $areasConvocadas = $sesion->areas_convocadas ?? [];
            if (is_array($areasConvocadas) && count($areasConvocadas) > 0) {
                $areasConvocadasIds = array_map('intval', $areasConvocadas);
                if (!in_array((int) $usuario->area_id, $areasConvocadasIds, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tu área de trabajo no está convocada para esta sesión de inducción.',
                    ], 422);
                }
            }

            $request->validate([
                'identificacion' => ['required', 'string', 'max:30'],
                'firma_digital' => ['required', 'string'],
            ], [
                'identificacion.required' => 'El número de identificación es obligatorio.',
                'firma_digital.required' => 'Debes dibujar tu firma en el recuadro.',
            ]);

            $nombreFinal = $usuario->nombre_completo;
            $cargoFinal = $usuario->cargoOficial();
            $areaFinal = $usuario->area?->nombre ?? 'General';
            $sedeFinal = $usuario->sedeActual()?->nombre ?? ($sesion->sede?->nombre ?? 'Sede Principal');
            $esExternoFinal = false;
            $empresaFinal = 'C.I. Piscícola New York';

        } elseif ($tipoRegistro === 'externo') {
            // Persona Externa (solo nombre completo, entidad y número de cédula)
            if (!$sesion->permitir_externos) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sesión no admite registro de personal externo o contratistas.',
                ], 422);
            }

            $request->validate([
                'identificacion' => ['required', 'string', 'max:30'],
                'nombre_completo' => ['required', 'string', 'max:150'],
                'entidad' => ['required', 'string', 'max:150'],
                'firma_digital' => ['required', 'string'],
            ], [
                'identificacion.required' => 'El número de identificación es obligatorio.',
                'nombre_completo.required' => 'El nombre completo es obligatorio.',
                'entidad.required' => 'Debes indicar la entidad o empresa a la que perteneces.',
                'firma_digital.required' => 'Debes dibujar tu firma en el recuadro para confirmar la asistencia.',
            ]);

            $entidad = trim((string) ($request->input('entidad') ?: $request->input('empresa_externa')));
            $nombreFinal = trim((string) $request->nombre_completo);
            $cargoFinal = 'Persona Externa / Contratista';
            $areaFinal = $entidad ?: 'Externa';
            $sedeFinal = $sesion->sede?->nombre ?? 'Sede Principal';
            $esExternoFinal = true;
            $empresaFinal = $entidad ?: 'Independiente / Visitante';

        } else {
            // Personal Externo (nombre completo, número de cédula, sede y área para la que va)
            $request->validate([
                'identificacion' => ['required', 'string', 'max:30'],
                'nombre_completo' => ['required', 'string', 'max:150'],
                'sede_id' => ['required', 'exists:sedes,id'],
                'area_id' => ['required', 'exists:areas,id'],
                'firma_digital' => ['required', 'string'],
            ], [
                'identificacion.required' => 'El número de identificación es obligatorio.',
                'nombre_completo.required' => 'El nombre completo es obligatorio.',
                'sede_id.required' => 'Debes seleccionar la sede a la que ingresas.',
                'sede_id.exists' => 'La sede seleccionada no es válida.',
                'area_id.required' => 'Debes seleccionar el área para la que vas.',
                'area_id.exists' => 'El área seleccionada no es válida.',
                'firma_digital.required' => 'Debes dibujar tu firma en el recuadro para confirmar la asistencia.',
            ]);

            $sedeModel = Sede::find($request->sede_id);
            $areaModel = Area::find($request->area_id);

            $nombreFinal = trim((string) $request->nombre_completo);
            $cargoFinal = 'Personal Externo';
            $areaFinal = $areaModel?->nombre ?? 'General';
            $sedeFinal = $sedeModel?->nombre ?? ($sesion->sede?->nombre ?? 'Sede Principal');
            $esExternoFinal = true;
            $empresaFinal = 'Personal Externo';
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

        $asistencia = AsistenciaInduccion::create([
            'sesion_id' => $sesion->id,
            'usuario_id' => $usuario?->id,
            'identificacion' => $identificacion,
            'nombre_completo' => $nombreFinal,
            'cargo' => $cargoFinal,
            'area' => $areaFinal,
            'sede' => $sedeFinal,
            'es_externo' => $esExternoFinal,
            'empresa_externa' => $empresaFinal,
            'telefono' => null, // Teléfono eliminado según requerimiento
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
