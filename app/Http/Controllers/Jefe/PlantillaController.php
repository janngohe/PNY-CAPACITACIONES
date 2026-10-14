<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\PlantillaCertificado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlantillaController extends Controller
{
    /**
     * Muestra las plantillas de certificados del área del Jefe:
     * - Plantillas institucionales asignadas por el Administrador (solo carga de firma).
     * - Plantillas propias creadas por el Jefe para su área (control y edición total).
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        $areaId = $usuario->area_id;

        // Plantillas vinculadas al área del Jefe o generales
        $todasPlantillas = PlantillaCertificado::query()
            ->with(['area', 'creador'])
            ->where(function ($query) use ($areaId, $usuario) {
                if ($areaId) {
                    $query->where('area_id', $areaId)
                          ->orWhereNull('area_id');
                }
                $query->orWhere('creado_por', $usuario->id);
            })
            ->orderByDesc('id')
            ->get();

        // Separar entre institucionales (asignadas por Admin) y creadas por el propio Jefe
        $plantillasAdmin = $todasPlantillas->filter(function ($pl) use ($usuario) {
            return $pl->creado_por !== $usuario->id;
        });

        $plantillasPropias = $todasPlantillas->filter(function ($pl) use ($usuario) {
            return $pl->creado_por === $usuario->id;
        });

        // Contar cuántas plantillas institucionales activas tienen pendiente la firma del Jefe
        $firmasPendientes = $plantillasAdmin->filter(function ($pl) {
            return $pl->estado && (empty($pl->firma_1_ruta));
        });

        // Obtener los datos oficiales de la otra firma (Firma 2: Gerencia / Aval institucional definida por el Administrador)
        $plantillaAdminReferencia = $plantillasAdmin->first() 
            ?? PlantillaCertificado::whereNull('creado_por')->latest('id')->first()
            ?? PlantillaCertificado::where('creado_por', '!=', $usuario->id)->latest('id')->first()
            ?? PlantillaCertificado::latest('id')->first();

        $firma2Oficial = [
            'nombre' => $plantillaAdminReferencia->firma_2_nombre ?? 'Dra. Elena Ramos',
            'cargo' => $plantillaAdminReferencia->firma_2_cargo ?? 'Gerente General',
            'ruta' => $plantillaAdminReferencia->firma_2_ruta ?? null,
            'url' => ($plantillaAdminReferencia && $plantillaAdminReferencia->firma_2_ruta)
                ? asset('storage/' . $plantillaAdminReferencia->firma_2_ruta)
                : null,
        ];

        return view('jefe.plantillas.index', [
            'usuario' => $usuario,
            'area' => $usuario->area,
            'todasPlantillas' => $todasPlantillas,
            'plantillasAdmin' => $plantillasAdmin,
            'plantillasPropias' => $plantillasPropias,
            'firmasPendientesCount' => $firmasPendientes->count(),
            'firmasPendientes' => $firmasPendientes,
            'firma2Oficial' => $firma2Oficial,
        ]);
    }

    /**
     * Permite al Jefe de Área crear una nueva plantilla para su área,
     * adaptando el formulario de creación del administrador.
     * La segunda firma (Aval Institucional) es inmutable para el Jefe y la define el Administrador.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'archivo_plantilla' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'firma_1_nombre' => ['nullable', 'string', 'max:150'],
            'firma_1_cargo' => ['nullable', 'string', 'max:150'],
            'firma_1_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'firma_1_canvas' => ['nullable', 'string'],
            'nombre_organizacion' => ['nullable', 'string', 'max:200'],
            'texto_certificado' => ['nullable', 'string'],
            'estado' => ['nullable'],
        ]);

        $rutaPlantilla = $request->hasFile('archivo_plantilla')
            ? $request->file('archivo_plantilla')->store('plantillas', 'public')
            : 'plantillas/oficial_pny.png';

        // Procesar Firma 1 (Jefe): archivo, pad digital o firma guardada en su perfil
        $rutaFirma1 = null;
        if ($request->filled('firma_1_canvas')) {
            $rutaFirma1 = $this->guardarFirmaBase64($request->input('firma_1_canvas'));
        } elseif ($request->hasFile('firma_1_archivo')) {
            $rutaFirma1 = $request->file('firma_1_archivo')->store('firmas', 'public');
        } elseif ($usuario->firma_digital) {
            // Auto-cargar automáticamente la firma oficial guardada en el perfil del Jefe
            $rutaFirma1 = $usuario->firma_digital;
        }

        // Obtener la firma 2 institucional oficial definida por el Administrador
        $plantillaAdminReferencia = PlantillaCertificado::whereNull('creado_por')
            ->orWhere('creado_por', '!=', $usuario->id)
            ->latest('id')
            ->first();

        $firma2Nombre = $plantillaAdminReferencia->firma_2_nombre ?? 'Dra. Elena Ramos';
        $firma2Cargo = $plantillaAdminReferencia->firma_2_cargo ?? 'Gerente General';
        $firma2Ruta = $plantillaAdminReferencia->firma_2_ruta ?? null;

        PlantillaCertificado::create([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'area_id' => $usuario->area_id,
            'creado_por' => $usuario->id,
            'ruta_plantilla' => $rutaPlantilla,
            'nombre_organizacion' => $datos['nombre_organizacion'] ?? 'C.I. Piscícola New York S.A.S.',
            'texto_certificado' => $datos['texto_certificado'] ?? null,
            'firma_1_nombre' => $datos['firma_1_nombre'] ?? $usuario->nombre_completo,
            'firma_1_cargo' => $datos['firma_1_cargo'] ?? $usuario->cargoOficial(),
            'firma_1_ruta' => $rutaFirma1,
            'firma_1_actualizada_el' => $rutaFirma1 ? now() : null,
            'firma_2_nombre' => $firma2Nombre,
            'firma_2_cargo' => $firma2Cargo,
            'firma_2_ruta' => $firma2Ruta,
            'estado' => $request->has('estado') ? (bool) $request->input('estado') : true,
        ]);

        return redirect()->route('jefe.plantillas.index')
            ->with('success', '¡La plantilla de certificado fue creada exitosamente para tu área!');
    }

    /**
     * Actualiza una plantilla creada por el propio Jefe.
     * No se permite si la plantilla fue creada por el Administrador.
     */
    public function update(Request $request, PlantillaCertificado $plantilla): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Validar propiedad
        if ($plantilla->creado_por !== $usuario->id) {
            return back()->with('error', 'No tienes permisos para modificar el diseño general de esta plantilla institucional asignada por el Administrador. Solo puedes cargar tu firma.');
        }

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'archivo_plantilla' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'firma_1_nombre' => ['nullable', 'string', 'max:150'],
            'firma_1_cargo' => ['nullable', 'string', 'max:150'],
            'firma_1_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'firma_1_canvas' => ['nullable', 'string'],
            'nombre_organizacion' => ['nullable', 'string', 'max:200'],
            'texto_certificado' => ['nullable', 'string'],
            'estado' => ['nullable'],
        ]);

        $camposActualizar = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'nombre_organizacion' => $datos['nombre_organizacion'] ?? 'C.I. Piscícola New York S.A.S.',
            'texto_certificado' => $datos['texto_certificado'] ?? null,
            'firma_1_nombre' => $datos['firma_1_nombre'] ?? $plantilla->firma_1_nombre,
            'firma_1_cargo' => $datos['firma_1_cargo'] ?? $plantilla->firma_1_cargo,
            'estado' => $request->has('estado') ? (bool) $request->input('estado') : $plantilla->estado,
        ];

        if ($request->hasFile('archivo_plantilla')) {
            if ($plantilla->ruta_plantilla && Storage::disk('public')->exists($plantilla->ruta_plantilla)) {
                Storage::disk('public')->delete($plantilla->ruta_plantilla);
            }
            $camposActualizar['ruta_plantilla'] = $request->file('archivo_plantilla')->store('plantillas', 'public');
        }

        if ($request->filled('firma_1_canvas')) {
            if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_1_ruta);
            }
            $camposActualizar['firma_1_ruta'] = $this->guardarFirmaBase64($request->input('firma_1_canvas'));
            $camposActualizar['firma_1_actualizada_el'] = now();
        } elseif ($request->hasFile('firma_1_archivo')) {
            if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_1_ruta);
            }
            $camposActualizar['firma_1_ruta'] = $request->file('firma_1_archivo')->store('firmas', 'public');
            $camposActualizar['firma_1_actualizada_el'] = now();
        }

        $plantilla->update($camposActualizar);

        return redirect()->route('jefe.plantillas.index')
            ->with('success', 'La plantilla de tu área fue actualizada correctamente.');
    }

    /**
     * Permite al Jefe de Área cargar o actualizar ÚNICAMENTE su firma en una plantilla
     * asignada a su área por el Administrador. No permite cambiar la maqueta ni textos.
     */
    public function actualizarFirma(Request $request, PlantillaCertificado $plantilla): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Validar que la plantilla pertenezca a su área o sea general
        if ($plantilla->area_id !== null && $plantilla->area_id !== $usuario->area_id) {
            return back()->with('error', 'No tienes autorización sobre esta plantilla de certificado.');
        }

        $request->validate([
            'firma_1_nombre' => ['required', 'string', 'max:150'],
            'firma_1_cargo' => ['required', 'string', 'max:150'],
            'firma_1_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'firma_1_canvas' => ['nullable', 'string'],
        ], [
            'firma_1_nombre.required' => 'Debes ingresar tu nombre oficial para la firma.',
            'firma_1_cargo.required' => 'Debes ingresar tu cargo oficial.',
        ]);

        $campos = [
            'firma_1_nombre' => $request->input('firma_1_nombre'),
            'firma_1_cargo' => $request->input('firma_1_cargo'),
            'firma_1_actualizada_el' => now(),
        ];

        // Procesar rúbrica: por pad digital o archivo cargado
        if ($request->filled('firma_1_canvas')) {
            if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_1_ruta);
            }
            $campos['firma_1_ruta'] = $this->guardarFirmaBase64($request->input('firma_1_canvas'));
        } elseif ($request->hasFile('firma_1_archivo')) {
            if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_1_ruta);
            }
            $campos['firma_1_ruta'] = $request->file('firma_1_archivo')->store('firmas', 'public');
        }

        $plantilla->update($campos);

        // Si el usuario aún no tenía firma guardada en su perfil, vincularla también
        if (!$usuario->tieneFirmaRegistrada() && !empty($campos['firma_1_ruta'])) {
            $usuario->update([
                'firma_digital' => $campos['firma_1_ruta'],
                'cargo' => $campos['firma_1_cargo'],
                'firma_actualizada_el' => now(),
            ]);
        }

        return redirect()->route('jefe.plantillas.index')
            ->with('success', '¡Tu firma oficial fue vinculada exitosamente a la plantilla "' . $plantilla->nombre . '"!');
    }

    /**
     * Activa / desactiva solo plantillas creadas por el Jefe.
     */
    public function toggleEstado(PlantillaCertificado $plantilla): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($plantilla->creado_por !== $usuario->id) {
            return back()->with('error', 'Solo puedes activar o desactivar plantillas creadas directamente por tu área.');
        }

        $plantilla->update(['estado' => !$plantilla->estado]);

        return redirect()->route('jefe.plantillas.index')
            ->with('success', 'El estado de la plantilla fue modificado.');
    }

    /**
     * Elimina solo plantillas creadas por el Jefe.
     */
    public function destroy(PlantillaCertificado $plantilla): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($plantilla->creado_por !== $usuario->id) {
            return back()->with('error', 'No puedes eliminar una plantilla asignada institucionalmente por el Administrador.');
        }

        if ($plantilla->ruta_plantilla && Storage::disk('public')->exists($plantilla->ruta_plantilla)) {
            Storage::disk('public')->delete($plantilla->ruta_plantilla);
        }
        if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
            Storage::disk('public')->delete($plantilla->firma_1_ruta);
        }
        if ($plantilla->firma_2_ruta && Storage::disk('public')->exists($plantilla->firma_2_ruta)) {
            Storage::disk('public')->delete($plantilla->firma_2_ruta);
        }

        $plantilla->delete();

        return redirect()->route('jefe.plantillas.index')
            ->with('success', 'La plantilla de certificado fue eliminada correctamente.');
    }

    /**
     * Guarda la firma oficial del perfil del Jefe de Área (primer ingreso o configuración general).
     * Aplica la firma automáticamente a todas las plantillas existentes de su área.
     */
    public function guardarFirmaPerfil(Request $request): RedirectResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($usuario->rol !== 'JEFE_AREA') {
            return back()->with('error', 'Solo los Jefes de Área pueden registrar su firma digital oficial.');
        }

        $request->validate([
            'cargo' => ['nullable', 'string', 'max:150'],
            'firma_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'firma_canvas' => ['nullable', 'string'],
        ]);

        $rutaFirma = null;

        if ($request->filled('firma_canvas')) {
            if ($usuario->firma_digital && Storage::disk('public')->exists($usuario->firma_digital)) {
                Storage::disk('public')->delete($usuario->firma_digital);
            }
            $rutaFirma = $this->guardarFirmaBase64($request->input('firma_canvas'));
        } elseif ($request->hasFile('firma_archivo')) {
            if ($usuario->firma_digital && Storage::disk('public')->exists($usuario->firma_digital)) {
                Storage::disk('public')->delete($usuario->firma_digital);
            }
            $rutaFirma = $request->file('firma_archivo')->store('firmas_usuarios', 'public');
        } else {
            return back()->with('error', 'Debes cargar una imagen de tu firma o dibujarla en el recuadro digital.');
        }

        $cargoOficial = $request->filled('cargo') ? $request->input('cargo') : $usuario->cargoOficial();

        $usuario->update([
            'firma_digital' => $rutaFirma,
            'cargo' => $cargoOficial,
            'firma_actualizada_el' => now(),
        ]);

        // Sincronizar automáticamente en todas las plantillas de su área
        if ($usuario->area_id) {
            PlantillaCertificado::where('area_id', $usuario->area_id)
                ->where(function ($q) use ($usuario) {
                    $q->whereNull('firma_1_ruta')
                      ->orWhere('firma_1_ruta', '')
                      ->orWhere('creado_por', $usuario->id)
                      ->orWhere('firma_1_nombre', $usuario->nombre_completo);
                })
                ->update([
                    'firma_1_nombre' => $usuario->nombre_completo,
                    'firma_1_cargo' => $cargoOficial,
                    'firma_1_ruta' => $rutaFirma,
                    'firma_1_actualizada_el' => now(),
                ]);
        }

        return redirect()->route('jefe.plantillas.index')
            ->with('success', '¡Tu firma oficial y cargo fueron guardados exitosamente y aplicados a las plantillas de tu área!');
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

        $nombreArchivo = 'firmas/firma_digital_' . Str::random(20) . '.png';
        Storage::disk('public')->put($nombreArchivo, $data);

        return $nombreArchivo;
    }
}
