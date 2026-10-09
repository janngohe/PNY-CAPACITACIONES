<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\IntentoEvaluacion;
use App\Models\PlantillaCertificado;
use App\Models\UsuarioCapacitacion;
use App\Support\Seguimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Consulta de certificados emitidos y emisión manual de los que correspondan
 * (capacitaciones COMPLETADAS cuyo certificado aún no existe).
 */
class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $certificados = Certificado::query()
            ->with(['capacitacion:id,titulo', 'usuario:id,area_id'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('nombre_empleado', 'like', "%$q%")
                ->orWhere('identificacion', 'like', "%$q%")
                ->orWhere('codigo', 'like', "%$q%")
                ->orWhere('nombre_capacitacion', 'like', "%$q%")))
            ->orderByDesc('fecha_emision')
            ->paginate(12)
            ->withQueryString();

        $pendientes = $this->pendientes()->load(['usuario.area', 'capacitacion']);

        return view('admin.certificados.index', [
            'certificados' => $certificados,
            'pendientes' => $pendientes,
            'q' => $q,
            'totalEmitidos' => Certificado::count(),
        ]);
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
