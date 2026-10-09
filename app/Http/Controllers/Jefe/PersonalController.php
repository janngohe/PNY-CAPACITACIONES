<?php

namespace App\Http\Controllers\Jefe;

use App\Http\Controllers\Controller;
use App\Models\ProgresoModulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    /**
     * Listado del personal (empleados) perteneciente al área del Jefe de Área.
     */
    public function index(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $area = $usuario->area;

        if (! $usuario->area_id) {
            return view('jefe.personal.index', [
                'usuario' => $usuario,
                'area' => null,
                'empleados' => collect(),
                'estadisticas' => [
                    'total' => 0,
                    'activos' => 0,
                    'inactivos' => 0,
                    'con_certificados' => 0,
                ],
                'buscar' => '',
                'filtroEstado' => 'todos',
            ]);
        }

        $buscar = trim($request->query('buscar', ''));
        $filtroEstado = $request->query('estado', 'todos');

        $query = Usuario::query()
            ->where('area_id', $usuario->area_id)
            ->where('rol', 'EMPLEADO')
            ->with(['asignaciones.capacitacion:id,titulo,estado', 'certificados:id,usuario_id,capacitacion_id'])
            ->withCount([
                'asignaciones',
                'asignaciones as completadas_count' => fn ($q) => $q->where('estado', 'COMPLETADA'),
                'asignaciones as en_progreso_count' => fn ($q) => $q->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS']),
                'certificados',
            ]);

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_completo', 'like', "%{$buscar}%")
                  ->orWhere('identificacion', 'like', "%{$buscar}%");
            });
        }

        if ($filtroEstado === 'activos') {
            $query->where('estado', true);
        } elseif ($filtroEstado === 'inactivos') {
            $query->where('estado', false);
        }

        $empleados = $query->orderBy('nombre_completo')->get();

        // Obtener última actividad registrada para cada empleado
        if ($empleados->isNotEmpty()) {
            $ultimasActividades = ProgresoModulo::query()
                ->whereIn('usuario_id', $empleados->pluck('id'))
                ->selectRaw('usuario_id, max(coalesce(fecha_finalizacion, fecha_inicio)) as ultima_fecha')
                ->groupBy('usuario_id')
                ->pluck('ultima_fecha', 'usuario_id');

            foreach ($empleados as $emp) {
                $emp->ultima_actividad = $ultimasActividades->get($emp->id);
            }
        }

        // Estadísticas globales del área
        $todosEmpleados = Usuario::query()
            ->where('area_id', $usuario->area_id)
            ->where('rol', 'EMPLEADO')
            ->withCount('certificados')
            ->get();

        $estadisticas = [
            'total' => $todosEmpleados->count(),
            'activos' => $todosEmpleados->where('estado', true)->count(),
            'inactivos' => $todosEmpleados->where('estado', false)->count(),
            'con_certificados' => $todosEmpleados->filter(fn ($e) => $e->certificados_count > 0)->count(),
        ];

        return view('jefe.personal.index', compact(
            'usuario',
            'area',
            'empleados',
            'estadisticas',
            'buscar',
            'filtroEstado'
        ));
    }
}
