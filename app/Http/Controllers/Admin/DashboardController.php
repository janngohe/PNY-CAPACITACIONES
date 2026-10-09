<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\Evaluacion;
use App\Models\IntentoEvaluacion;
use App\Models\Usuario;
use App\Support\Seguimiento;

class DashboardController extends Controller
{
    public function index()
    {
        $filas = Seguimiento::progreso();

        $usuarios = [
            'total' => Usuario::count(),
            'activos' => Usuario::where('estado', true)->count(),
            'empleados' => Usuario::where('rol', 'EMPLEADO')->where('estado', true)->count(),
            'jefes' => Usuario::where('rol', 'JEFE_AREA')->where('estado', true)->count(),
            'admins' => Usuario::where('rol', 'ADMINISTRADOR')->where('estado', true)->count(),
        ];

        $intentos = IntentoEvaluacion::count();
        $aprobados = IntentoEvaluacion::where('estado', 'APROBADO')->count();

        $kpis = [
            'capacitaciones' => Capacitacion::where('estado', true)->count(),
            'capacitaciones_total' => Capacitacion::count(),
            'evaluaciones' => Evaluacion::where('estado', true)->count(),
            'areas' => Area::where('estado', true)->count(),
            'certificados' => Certificado::count(),
            'intentos' => $intentos,
            'tasa_aprobacion' => $intentos > 0 ? (int) round(($aprobados / $intentos) * 100) : 0,
            'avance_global' => $filas->isNotEmpty() ? (int) round($filas->avg('porcentaje')) : 0,
            'completadas' => $filas->where('estado', 'COMPLETADA')->count(),
            'en_progreso' => $filas->whereIn('estado', ['EN_PROGRESO', 'MODULOS_COMPLETOS'])->count(),
            'pendientes' => $filas->where('estado', 'PENDIENTE')->count(),
        ];

        $porArea = $filas->groupBy(fn ($f) => $f->usuario->area?->nombre ?? 'Sin área')
            ->map(fn ($grupo, $nombre) => (object) [
                'nombre' => $nombre,
                'asignaciones' => $grupo->count(),
                'promedio' => (int) round($grupo->avg('porcentaje')),
                'completadas' => $grupo->where('estado', 'COMPLETADA')->count(),
            ])
            ->sortByDesc('promedio')
            ->values();

        $porCapacitacion = $filas->groupBy(fn ($f) => $f->capacitacion->id)
            ->map(fn ($grupo) => (object) [
                'capacitacion' => $grupo->first()->capacitacion,
                'participantes' => $grupo->count(),
                'promedio' => (int) round($grupo->avg('porcentaje')),
                'completadas' => $grupo->where('estado', 'COMPLETADA')->count(),
            ])
            ->sortByDesc('participantes')
            ->take(6)
            ->values();

        $ultimosCertificados = Certificado::with('capacitacion:id,titulo')
            ->orderByDesc('fecha_emision')
            ->limit(5)
            ->get();

        $ultimosIntentos = IntentoEvaluacion::with(['usuario:id,nombre_completo', 'evaluacion:id,titulo'])
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $usuario = auth()->user();

        return view('admin.dashboard', compact('usuario', 'usuarios', 'kpis', 'porArea', 'porCapacitacion', 'ultimosCertificados', 'ultimosIntentos'));
    }
}
