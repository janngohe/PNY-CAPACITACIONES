<?php

namespace App\Support;

use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\IntentoEvaluacion;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use App\Models\UsuarioCapacitacion;
use Illuminate\Support\Collection;

/**
 * Cálculos de seguimiento compartidos por el panel del administrador
 * (dashboard, progreso de participantes y reportes).
 */
class Seguimiento
{
    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'EN_PROGRESO' => 'En progreso',
        'MODULOS_COMPLETOS' => 'Módulos completos',
        'COMPLETADA' => 'Completada',
        'NO_APROBADA' => 'No aprobada',
    ];

    /**
     * Una fila por cada par (empleado activo, capacitación asignada a su área).
     *
     * @param  array{area_id?:int|string|null, capacitacion_id?:int|string|null, estado?:string|null, q?:string|null}  $filtros
     */
    public static function progreso(array $filtros = []): Collection
    {
        $capacitaciones = Capacitacion::query()
            ->with(['areas:id,nombre', 'modulos' => fn ($q) => $q->where('estado', true)])
            ->when(! empty($filtros['capacitacion_id']), fn ($q) => $q->where('id', $filtros['capacitacion_id']))
            ->orderBy('titulo')
            ->get();

        if ($capacitaciones->isEmpty()) {
            return collect();
        }

        $areaIds = $capacitaciones->flatMap(fn ($c) => $c->areas->pluck('id'))->unique()->values();

        $empleados = Usuario::query()
            ->with('area:id,nombre')
            ->where('rol', 'EMPLEADO')
            ->where('estado', true)
            ->whereIn('area_id', $areaIds)
            ->when(! empty($filtros['area_id']), fn ($q) => $q->where('area_id', $filtros['area_id']))
            ->when(! empty($filtros['q']), function ($q) use ($filtros) {
                $q->where(fn ($w) => $w
                    ->where('nombre_completo', 'like', '%' . $filtros['q'] . '%')
                    ->orWhere('identificacion', 'like', '%' . $filtros['q'] . '%'));
            })
            ->orderBy('nombre_completo')
            ->get();

        if ($empleados->isEmpty()) {
            return collect();
        }

        $moduloIds = $capacitaciones->flatMap(fn ($c) => $c->modulos->pluck('id'));

        $progresos = ProgresoModulo::query()
            ->whereIn('modulo_id', $moduloIds)
            ->whereIn('usuario_id', $empleados->pluck('id'))
            ->get()
            ->groupBy('usuario_id');

        $asignaciones = UsuarioCapacitacion::query()
            ->whereIn('capacitacion_id', $capacitaciones->pluck('id'))
            ->whereIn('usuario_id', $empleados->pluck('id'))
            ->get()
            ->keyBy(fn ($a) => $a->usuario_id . '-' . $a->capacitacion_id);

        $certificados = Certificado::query()
            ->whereIn('capacitacion_id', $capacitaciones->pluck('id'))
            ->get(['usuario_id', 'capacitacion_id'])
            ->keyBy(fn ($c) => $c->usuario_id . '-' . $c->capacitacion_id);

        $mejores = IntentoEvaluacion::query()
            ->join('evaluaciones', 'evaluaciones.id', '=', 'intentos_evaluacion.evaluacion_id')
            ->whereIn('evaluaciones.capacitacion_id', $capacitaciones->pluck('id'))
            ->selectRaw('intentos_evaluacion.usuario_id as usuario_id, evaluaciones.capacitacion_id as capacitacion_id, max(intentos_evaluacion.porcentaje) as mejor')
            ->groupBy('intentos_evaluacion.usuario_id', 'evaluaciones.capacitacion_id')
            ->get()
            ->keyBy(fn ($r) => $r->usuario_id . '-' . $r->capacitacion_id);

        $filas = collect();

        foreach ($capacitaciones as $cap) {
            $ids = $cap->modulos->pluck('id');
            $total = $ids->count();
            $areas = $cap->areas->pluck('id');

            foreach ($empleados->whereIn('area_id', $areas) as $empleado) {
                $clave = $empleado->id . '-' . $cap->id;
                $registros = $progresos->get($empleado->id, collect())->whereIn('modulo_id', $ids);
                $completados = $registros->where('completado', true)->count();
                $porcentaje = $total > 0 ? (int) round(($completados / $total) * 100) : 0;
                $asignacion = $asignaciones->get($clave);

                $estado = $asignacion?->estado
                    ?? ($porcentaje >= 100 ? 'MODULOS_COMPLETOS' : ($registros->isNotEmpty() ? 'EN_PROGRESO' : 'PENDIENTE'));

                $filas->push((object) [
                    'usuario' => $empleado,
                    'capacitacion' => $cap,
                    'total_modulos' => $total,
                    'completados' => $completados,
                    'porcentaje' => $porcentaje,
                    'estado' => $estado,
                    'mejor_nota' => isset($mejores[$clave]) ? (float) $mejores[$clave]->mejor : null,
                    'certificado' => $certificados->has($clave),
                ]);
            }
        }

        if (! empty($filtros['estado']) && isset(self::ESTADOS[$filtros['estado']])) {
            $filas = $filas->where('estado', $filtros['estado'])->values();
        }

        return $filas;
    }

    /**
     * Código único legible para certificados.
     */
    public static function codigoCertificado(): string
    {
        do {
            $codigo = 'PNY-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (Certificado::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
