<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Capacitacion;
use App\Models\Certificado;
use App\Models\IntentoEvaluacion;
use App\Models\Sede;
use App\Models\Usuario;
use App\Support\Seguimiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Generación y descarga de reportes (CSV para Excel y PDF).
 */
class ReporteController extends Controller
{
    private const TIPOS = [
        'progreso' => ['titulo' => 'Progreso de participantes', 'icono' => 'fa-chart-line', 'desc' => 'Avance por módulos, estado y mejor nota de cada empleado en cada capacitación asignada.'],
        'resultados' => ['titulo' => 'Resultados de evaluaciones', 'icono' => 'fa-clipboard-check', 'desc' => 'Todos los intentos presentados con su porcentaje y resultado (aprobado / no aprobado).'],
        'certificados' => ['titulo' => 'Certificados emitidos', 'icono' => 'fa-award', 'desc' => 'Listado de certificados con código de verificación, empleado, capacitación y fecha de emisión.'],
        'usuarios' => ['titulo' => 'Usuarios de la plataforma', 'icono' => 'fa-users', 'desc' => 'Usuarios registrados con su rol, área y estado (activo / inactivo).'],
    ];

    public function index()
    {
        return view('admin.reportes.index', [
            'tipos' => self::TIPOS,
            'sedes' => Sede::where('estado', true)->orderBy('nombre')->get(['id', 'nombre', 'ciudad']),
            'areas' => Area::orderBy('nombre')->get(['id', 'nombre', 'sede_id']),
            'capacitaciones' => Capacitacion::orderBy('titulo')->get(['id', 'titulo']),
        ]);
    }

    public function descargar(Request $request, string $tipo, string $formato)
    {
        $request->validate([
            'sede_id' => ['nullable', 'integer'],
            'area_id' => ['nullable', 'integer'],
            'capacitacion_id' => ['nullable', 'integer'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        [$columnas, $filas] = match ($tipo) {
            'progreso' => $this->progreso($request),
            'resultados' => $this->resultados($request),
            'certificados' => $this->certificados($request),
            'usuarios' => $this->usuarios($request),
        };

        $titulo = self::TIPOS[$tipo]['titulo'];
        $nombre = 'reporte_' . $tipo . '_' . now()->format('Ymd_His');

        if ($formato === 'pdf') {
            $sedeFiltro = null;
            if ($request->filled('sede_id')) {
                $sedeFiltro = Sede::find($request->query('sede_id'))?->nombre;
            }

            return Pdf::loadView('admin.reportes.pdf', [
                'titulo' => $titulo,
                'columnas' => $columnas,
                'filas' => $filas,
                'generadoPor' => $request->user()->nombre_completo,
                'sedeFiltro' => $sedeFiltro,
                'fecha' => now(),
            ])->setPaper('letter', 'landscape')->download($nombre . '.pdf');
        }

        return new StreamedResponse(function () use ($columnas, $filas) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para que Excel respete las tildes
            fputcsv($out, $columnas, ';');
            foreach ($filas as $fila) {
                fputcsv($out, $fila, ';');
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '.csv"',
        ]);
    }

    private function progreso(Request $request): array
    {
        $filas = Seguimiento::progreso([
            'sede_id' => $request->query('sede_id'),
            'area_id' => $request->query('area_id'),
            'capacitacion_id' => $request->query('capacitacion_id'),
        ]);

        return [
            ['Empleado', 'Identificación', 'Sede', 'Área', 'Capacitación', 'Módulos completados', 'Total módulos', '% Avance', 'Estado', 'Mejor nota (%)', 'Certificado'],
            $filas->map(fn ($f) => [
                $f->usuario->nombre_completo,
                $f->usuario->identificacion,
                $f->usuario->sedeActual()?->nombre ?? '—',
                $f->usuario->area?->nombre ?? '—',
                $f->capacitacion->titulo,
                $f->completados,
                $f->total_modulos,
                $f->porcentaje,
                Seguimiento::ESTADOS[$f->estado] ?? $f->estado,
                $f->mejor_nota !== null ? number_format($f->mejor_nota, 1, ',', '') : '—',
                $f->certificado ? 'Sí' : 'No',
            ])->all(),
        ];
    }

    private function resultados(Request $request): array
    {
        $intentos = IntentoEvaluacion::query()
            ->with(['usuario.area.sede', 'usuario.sede', 'evaluacion.capacitacion'])
            ->when($request->query('sede_id'), fn ($q, $s) => $q->whereHas('usuario', fn ($u) => $u->where(fn ($w) => $w
                ->where('sede_id', $s)
                ->orWhereHas('area', fn ($a) => $a->where('sede_id', $s))
            )))
            ->when($request->query('area_id'), fn ($q, $a) => $q->whereHas('usuario', fn ($u) => $u->where('area_id', $a)))
            ->when($request->query('capacitacion_id'), fn ($q, $c) => $q->whereHas('evaluacion', fn ($e) => $e->where('capacitacion_id', $c)))
            ->when($request->query('desde'), fn ($q, $d) => $q->where('fecha_inicio', '>=', Carbon::parse($d)->startOfDay()))
            ->when($request->query('hasta'), fn ($q, $h) => $q->where('fecha_inicio', '<=', Carbon::parse($h)->endOfDay()))
            ->orderByDesc('id')
            ->get();

        return [
            ['Empleado', 'Identificación', 'Sede', 'Área', 'Capacitación', 'Evaluación', 'Intento', 'Correctas', 'Total preguntas', '% Obtenido', 'Resultado', 'Fecha'],
            $intentos->map(fn ($i) => [
                $i->usuario?->nombre_completo,
                $i->usuario?->identificacion,
                $i->usuario?->sedeActual()?->nombre ?? '—',
                $i->usuario?->area?->nombre ?? '—',
                $i->evaluacion?->capacitacion?->titulo,
                $i->evaluacion?->titulo,
                $i->numero_intento,
                $i->respuestas_correctas,
                $i->total_preguntas,
                number_format((float) $i->porcentaje, 1, ',', ''),
                $i->estado === 'APROBADO' ? 'Aprobado' : 'No aprobado',
                ($i->fecha_finalizacion ?? $i->fecha_inicio)?->format('d/m/Y H:i'),
            ])->all(),
        ];
    }

    private function certificados(Request $request): array
    {
        $certificados = Certificado::query()
            ->with(['usuario.area.sede', 'usuario.sede'])
            ->when($request->query('sede_id'), fn ($q, $s) => $q->whereHas('usuario', fn ($u) => $u->where(fn ($w) => $w
                ->where('sede_id', $s)
                ->orWhereHas('area', fn ($a) => $a->where('sede_id', $s))
            )))
            ->when($request->query('capacitacion_id'), fn ($q, $c) => $q->where('capacitacion_id', $c))
            ->when($request->query('area_id'), fn ($q, $a) => $q->whereHas('usuario', fn ($u) => $u->where('area_id', $a)))
            ->when($request->query('desde'), fn ($q, $d) => $q->where('fecha_emision', '>=', Carbon::parse($d)->startOfDay()))
            ->when($request->query('hasta'), fn ($q, $h) => $q->where('fecha_emision', '<=', Carbon::parse($h)->endOfDay()))
            ->orderByDesc('fecha_emision')
            ->get();

        return [
            ['Código', 'Empleado', 'Identificación', 'Sede', 'Área', 'Capacitación', '% Obtenido', 'Fecha de emisión'],
            $certificados->map(fn ($c) => [
                $c->codigo,
                $c->nombre_empleado,
                $c->identificacion,
                $c->usuario?->sedeActual()?->nombre ?? '—',
                $c->area_nombre ?? '—',
                $c->nombre_capacitacion,
                number_format((float) $c->porcentaje, 1, ',', ''),
                $c->fecha_emision?->format('d/m/Y'),
            ])->all(),
        ];
    }

    private function usuarios(Request $request): array
    {
        $usuarios = Usuario::query()
            ->with(['area:id,nombre,sede_id', 'sede:id,nombre,ciudad', 'area.sede:id,nombre,ciudad'])
            ->when($request->query('sede_id'), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('sede_id', $s)
                ->orWhereHas('area', fn ($a) => $a->where('sede_id', $s))))
            ->when($request->query('area_id'), fn ($q, $a) => $q->where('area_id', $a))
            ->orderBy('nombre_completo')
            ->get();

        $roles = ['EMPLEADO' => 'Empleado', 'JEFE_AREA' => 'Jefe de Área', 'ADMINISTRADOR' => 'Administrador'];

        return [
            ['Identificación', 'Nombre completo', 'Rol', 'Sede', 'Área', 'Estado'],
            $usuarios->map(fn ($u) => [
                $u->identificacion,
                $u->nombre_completo,
                $roles[$u->rol] ?? $u->rol,
                $u->sedeActual()?->nombre ?? '—',
                $u->area?->nombre ?? '—',
                $u->estado ? 'Activo' : 'Inactivo',
            ])->all(),
        ];
    }
}
