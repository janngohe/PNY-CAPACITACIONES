<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Certificado;
use App\Models\CertificadoExterno;
use App\Models\IntentoEvaluacion;
use App\Models\PlantillaCertificado;
use App\Models\UsuarioCapacitacion;
use App\Support\Seguimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Consulta de certificados emitidos, emisión manual de los pendientes,
 * gestión de plantillas de certificados y aprobación/rechazo de certificados externos.
 */
class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $certificados = Certificado::query()
            ->with(['capacitacion:id,titulo,duracion_estimada', 'usuario:id,area_id', 'plantillaCertificado'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nombre_empleado', 'like', "%$q%")
                ->orWhere('identificacion', 'like', "%$q%")
                ->orWhere('codigo', 'like', "%$q%")
                ->orWhere('nombre_capacitacion', 'like', "%$q%")))
            ->orderByDesc('fecha_emision')
            ->paginate(12)
            ->withQueryString();

        $pendientes = $this->pendientes()->load(['usuario.area', 'capacitacion']);

        $certificadosExternos = CertificadoExterno::query()
            ->with('usuario.area')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('entidad_emisora', 'like', "%$q%")
                    ->orWhereHas('usuario', fn ($u) => $u->where('nombre_completo', 'like', "%$q%")
                        ->orWhere('identificacion', 'like', "%$q%"));
            })
            ->orderByRaw("CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 2 END")
            ->orderByDesc('id')
            ->get();

        $plantillas = PlantillaCertificado::query()
            ->with('area')
            ->orderByDesc('id')
            ->get();

        $areas = Area::where('estado', true)->orderBy('nombre')->get();

        $jefes = \App\Models\Usuario::where('rol', 'JEFE_AREA')
            ->where('estado', true)
            ->with('area')
            ->orderBy('nombre_completo')
            ->get();

        return view('admin.certificados.index', [
            'certificados' => $certificados,
            'pendientes' => $pendientes,
            'certificadosExternos' => $certificadosExternos,
            'plantillas' => $plantillas,
            'areas' => $areas,
            'jefes' => $jefes,
            'q' => $q,
            'totalEmitidos' => Certificado::count(),
            'totalExternos' => CertificadoExterno::count(),
            'externosPendientes' => CertificadoExterno::where('estado', 'PENDIENTE')->count(),
            'totalPlantillas' => $plantillas->count(),
        ]);
    }

    public function guardarPlantilla(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'archivo_plantilla' => ['required', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'firma_1_nombre' => ['nullable', 'string', 'max:150'],
            'firma_1_cargo' => ['nullable', 'string', 'max:150'],
            'firma_1_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'firma_1_ruta_existente' => ['nullable', 'string', 'max:255'],
            'firma_2_nombre' => ['nullable', 'string', 'max:150'],
            'firma_2_cargo' => ['nullable', 'string', 'max:150'],
            'firma_2_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'nombre_organizacion' => ['nullable', 'string', 'max:200'],
            'texto_certificado' => ['nullable', 'string'],
            'estado' => ['nullable'],
        ]);

        $rutaPlantilla = $request->file('archivo_plantilla')->store('plantillas', 'public');

        // Procesar Firma 1: archivo nuevo, ruta existente de firma de perfil, o búsqueda por nombre de jefe
        $rutaFirma1 = null;
        if ($request->hasFile('firma_1_archivo')) {
            $rutaFirma1 = $request->file('firma_1_archivo')->store('firmas', 'public');
        } elseif ($request->filled('firma_1_ruta_existente')) {
            $rutaFirma1 = $request->input('firma_1_ruta_existente');
        } elseif (!empty($datos['firma_1_nombre'])) {
            $jefeUsuario = \App\Models\Usuario::where('nombre_completo', $datos['firma_1_nombre'])->first();
            if ($jefeUsuario && $jefeUsuario->tieneFirmaRegistrada()) {
                $rutaFirma1 = $jefeUsuario->firma_digital;
            }
        }

        $rutaFirma2 = $request->hasFile('firma_2_archivo')
            ? $request->file('firma_2_archivo')->store('firmas', 'public')
            : null;

        PlantillaCertificado::create([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'area_id' => !empty($datos['area_id']) ? $datos['area_id'] : null,
            'creado_por' => auth()->id(),
            'ruta_plantilla' => $rutaPlantilla,
            'nombre_organizacion' => $datos['nombre_organizacion'] ?? 'C.I. Piscícola New York S.A.S.',
            'texto_certificado' => $datos['texto_certificado'] ?? null,
            'firma_1_nombre' => $datos['firma_1_nombre'] ?? 'Jefe Inmediato',
            'firma_1_cargo' => $datos['firma_1_cargo'] ?? 'Jefe de Área',
            'firma_1_ruta' => $rutaFirma1,
            'firma_1_actualizada_el' => $rutaFirma1 ? now() : null,
            'firma_2_nombre' => $datos['firma_2_nombre'] ?? 'Gerente General',
            'firma_2_cargo' => $datos['firma_2_cargo'] ?? 'Gerencia General',
            'firma_2_ruta' => $rutaFirma2,
            'estado' => $request->has('estado') ? (bool) $request->input('estado') : true,
        ]);

        return redirect()->route('admin.certificados.index', ['tab' => 'plantillas'])
            ->with('success', '¡La plantilla de certificado fue creada y configurada exitosamente!');
    }

    public function actualizarPlantilla(Request $request, PlantillaCertificado $plantilla): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'archivo_plantilla' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'firma_1_nombre' => ['nullable', 'string', 'max:150'],
            'firma_1_cargo' => ['nullable', 'string', 'max:150'],
            'firma_1_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'firma_1_ruta_existente' => ['nullable', 'string', 'max:255'],
            'firma_2_nombre' => ['nullable', 'string', 'max:150'],
            'firma_2_cargo' => ['nullable', 'string', 'max:150'],
            'firma_2_archivo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'nombre_organizacion' => ['nullable', 'string', 'max:200'],
            'texto_certificado' => ['nullable', 'string'],
            'estado' => ['nullable'],
        ]);

        $camposActualizar = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'area_id' => !empty($datos['area_id']) ? $datos['area_id'] : null,
            'nombre_organizacion' => $datos['nombre_organizacion'] ?? 'C.I. Piscícola New York S.A.S.',
            'texto_certificado' => $datos['texto_certificado'] ?? null,
            'firma_1_nombre' => $datos['firma_1_nombre'] ?? null,
            'firma_1_cargo' => $datos['firma_1_cargo'] ?? null,
            'firma_2_nombre' => $datos['firma_2_nombre'] ?? null,
            'firma_2_cargo' => $datos['firma_2_cargo'] ?? null,
            'estado' => $request->has('estado') ? (bool) $request->input('estado') : $plantilla->estado,
        ];

        if ($request->hasFile('archivo_plantilla')) {
            if ($plantilla->ruta_plantilla && Storage::disk('public')->exists($plantilla->ruta_plantilla)) {
                Storage::disk('public')->delete($plantilla->ruta_plantilla);
            }
            $camposActualizar['ruta_plantilla'] = $request->file('archivo_plantilla')->store('plantillas', 'public');
        }

        if ($request->hasFile('firma_1_archivo')) {
            if ($plantilla->firma_1_ruta && Storage::disk('public')->exists($plantilla->firma_1_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_1_ruta);
            }
            $camposActualizar['firma_1_ruta'] = $request->file('firma_1_archivo')->store('firmas', 'public');
            $camposActualizar['firma_1_actualizada_el'] = now();
        } elseif ($request->filled('firma_1_ruta_existente')) {
            $camposActualizar['firma_1_ruta'] = $request->input('firma_1_ruta_existente');
            $camposActualizar['firma_1_actualizada_el'] = now();
        } elseif (!empty($datos['firma_1_nombre']) && empty($plantilla->firma_1_ruta)) {
            $jefeUsuario = \App\Models\Usuario::where('nombre_completo', $datos['firma_1_nombre'])->first();
            if ($jefeUsuario && $jefeUsuario->tieneFirmaRegistrada()) {
                $camposActualizar['firma_1_ruta'] = $jefeUsuario->firma_digital;
                $camposActualizar['firma_1_actualizada_el'] = now();
            }
        }

        if ($request->hasFile('firma_2_archivo')) {
            if ($plantilla->firma_2_ruta && Storage::disk('public')->exists($plantilla->firma_2_ruta)) {
                Storage::disk('public')->delete($plantilla->firma_2_ruta);
            }
            $camposActualizar['firma_2_ruta'] = $request->file('firma_2_archivo')->store('firmas', 'public');
        }

        $plantilla->update($camposActualizar);

        return redirect()->route('admin.certificados.index', ['tab' => 'plantillas'])
            ->with('success', 'La plantilla de certificado fue actualizada exitosamente.');
    }

    public function togglePlantillaEstado(PlantillaCertificado $plantilla): RedirectResponse
    {
        $plantilla->update(['estado' => !$plantilla->estado]);

        $mensaje = $plantilla->estado ? 'Plantilla activada correctamente.' : 'Plantilla desactivada correctamente.';

        return redirect()->route('admin.certificados.index', ['tab' => 'plantillas'])->with('success', $mensaje);
    }

    public function eliminarPlantilla(PlantillaCertificado $plantilla): RedirectResponse
    {
        if ($plantilla->certificados()->exists()) {
            return redirect()->route('admin.certificados.index', ['tab' => 'plantillas'])
                ->with('error', 'No se puede eliminar la plantilla porque ya tiene certificados emitidos asociados. Puedes desactivarla para que no se use en nuevas emisiones.');
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

        return redirect()->route('admin.certificados.index', ['tab' => 'plantillas'])
            ->with('success', 'Plantilla eliminada exitosamente.');
    }

    public function aprobarExterno(Request $request, CertificadoExterno $certificadoExterno): RedirectResponse
    {
        $certificadoExterno->update(['estado' => 'APROBADO']);

        return back()->with('success', 'El certificado externo ha sido APROBADO exitosamente.');
    }

    public function rechazarExterno(Request $request, CertificadoExterno $certificadoExterno): RedirectResponse
    {
        $certificadoExterno->update(['estado' => 'RECHAZADO']);

        return back()->with('success', 'El certificado externo ha sido marcado como RECHAZADO.');
    }

    public function generar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'usuario_id' => ['nullable', 'integer', 'exists:usuarios,id'],
            'capacitacion_id' => ['nullable', 'integer', 'exists:capacitaciones,id'],
        ]);

        $pendientes = $this->pendientes()->load(['usuario.area', 'capacitacion']);

        if (! empty($datos['usuario_id']) && ! empty($datos['capacitacion_id'])) {
            $pendientes = $pendientes->filter(fn ($a) => (int) $a->usuario_id === (int) $datos['usuario_id']
                && (int) $a->capacitacion_id === (int) $datos['capacitacion_id']);
        }

        if ($pendientes->isEmpty()) {
            return back()->with('error', 'No hay certificados pendientes por generar.');
        }

        $emitidos = 0;
        foreach ($pendientes as $asignacion) {
            $usuario = $asignacion->usuario;
            $capacitacion = $asignacion->capacitacion;

            $mejorNota = IntentoEvaluacion::query()
                ->join('evaluaciones', 'evaluaciones.id', '=', 'intentos_evaluacion.evaluacion_id')
                ->where('intentos_evaluacion.usuario_id', $usuario->id)
                ->where('evaluaciones.capacitacion_id', $capacitacion->id)
                ->where('intentos_evaluacion.estado', 'APROBADO')
                ->max('intentos_evaluacion.porcentaje');

            $certificado = Certificado::firstOrCreate(
                ['usuario_id' => $usuario->id, 'capacitacion_id' => $capacitacion->id],
                [
                    'plantilla_certificado_id' => $this->plantillaPara($usuario->area_id)?->id,
                    'codigo' => Seguimiento::codigoCertificado(),
                    'nombre_empleado' => $usuario->nombre_completo,
                    'identificacion' => $usuario->identificacion,
                    'nombre_capacitacion' => $capacitacion->titulo,
                    'area_nombre' => $usuario->area->nombre ?? null,
                    'porcentaje' => $mejorNota ?? 100,
                    'fecha_emision' => now(),
                    'emitido_por' => $request->user()->id,
                ]
            );

            $emitidos += $certificado->wasRecentlyCreated ? 1 : 0;
        }

        return redirect()->route('admin.certificados.index')
            ->with('success', $emitidos === 1 ? 'Se generó 1 certificado.' : "Se generaron $emitidos certificados.");
    }

    /**
     * Asignaciones COMPLETADAS que todavía no tienen certificado.
     */
    private function pendientes()
    {
        return UsuarioCapacitacion::query()
            ->where('estado', 'COMPLETADA')
            ->whereNotExists(function ($q) {
                $q->selectRaw('1')->from('certificados')
                    ->whereColumn('certificados.usuario_id', 'usuario_capacitacion.usuario_id')
                    ->whereColumn('certificados.capacitacion_id', 'usuario_capacitacion.capacitacion_id');
            })
            ->get();
    }

    /**
     * Plantilla activa del área del empleado; si no hay, la general o la primera activa.
     */
    private function plantillaPara(?int $areaId): ?PlantillaCertificado
    {
        $activas = PlantillaCertificado::where('estado', true)->get();

        return $activas->firstWhere('area_id', $areaId)
            ?? $activas->firstWhere('area_id', null)
            ?? $activas->first();
    }
}
