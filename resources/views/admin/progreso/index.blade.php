@extends('layouts.admin')

@section('title', 'Progreso de Participantes')
@section('page_title', 'Seguimiento y Progreso')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Monitoreo de Aprendizaje',
        'titulo' => 'Progreso de los Participantes',
        'descripcion' => 'Consulta en tiempo real el avance de cada colaborador en sus capacitaciones asignadas: módulos completados, notas de evaluaciones y emisión de certificados.',
        'icono' => 'fa-chart-line',
    ])

    <!-- TARJETAS DE INDICADORES -->
    <div id="contenedor-resumen" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-4 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-list-check"></i>
            </span>
            <div>
                <span class="block text-xl font-heading font-extrabold text-brand-dark leading-tight">{{ $resumen['registros'] }}</span>
                <span class="text-[10px] text-muted uppercase font-bold tracking-wider">Asignaciones</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-4 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-cyan-50 text-brand-cyan flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-users"></i>
            </span>
            <div>
                <span class="block text-xl font-heading font-extrabold text-brand-dark leading-tight">{{ $resumen['participantes'] }}</span>
                <span class="text-[10px] text-muted uppercase font-bold tracking-wider">Colaboradores</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-4 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-check-double"></i>
            </span>
            <div>
                <span class="block text-xl font-heading font-extrabold text-emerald-700 leading-tight">{{ $resumen['completadas'] }}</span>
                <span class="text-[10px] text-muted uppercase font-bold tracking-wider">Cursos Finalizados</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-4 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-percent"></i>
            </span>
            <div>
                <span class="block text-xl font-heading font-extrabold text-purple-700 leading-tight">{{ $resumen['promedio'] }}%</span>
                <span class="text-[10px] text-muted uppercase font-bold tracking-wider">Avance Promedio</span>
            </div>
        </div>
    </div>

    <!-- FILTROS DE BÚSQUEDA EN TIEMPO REAL -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs">
        <form id="form-filtro-progreso" method="GET" action="{{ route('admin.progreso.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div>
                <label for="filtro-q" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                    Buscar empleado <span class="text-brand-blue font-normal lowercase">(en vivo)</span>
                </label>
                <div class="relative">
                    <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                           placeholder="Nombre o Cédula..."
                           autocomplete="off"
                           class="w-full pl-9 pr-8 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs pointer-events-none"></i>
                    <span id="spinner-busqueda" class="hidden absolute right-2.5 top-2.5 text-brand-blue text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </span>
                </div>
            </div>

            <div>
                <label for="filtro-sede" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Sede</label>
                <select id="filtro-sede" name="sede_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todas las sedes</option>
                    @foreach ($sedes as $s)
                        <option value="{{ $s->id }}" {{ (string)($filtros['sede_id'] ?? '') === (string)$s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-area" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Área</label>
                <select id="filtro-area" name="area_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ (string)$filtros['area_id'] === (string)$area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-cap" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Capacitación</label>
                <select id="filtro-cap" name="capacitacion_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todas las capacitaciones</option>
                    @foreach ($capacitaciones as $cap)
                        <option value="{{ $cap->id }}" {{ (string)$filtros['capacitacion_id'] === (string)$cap->id ? 'selected' : '' }}>{{ $cap->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-estado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Estado</label>
                <select id="filtro-estado" name="estado"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $clave => $nombre)
                        <option value="{{ $clave }}" {{ $filtros['estado'] === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- ESTADO DEL FILTRADOR EN TIEMPO REAL Y BOTÓN LIMPIAR -->
            <div class="flex items-center gap-2">
                <div class="flex-1 flex items-center justify-between h-9 px-3 rounded-xl bg-slate-50/80 border border-line/70 text-[11px] text-slate-600 font-medium select-none">
                    <span class="inline-flex items-center gap-1.5 min-w-0 truncate">
                        <span id="indicador-en-vivo-dot" class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        <span id="indicador-en-vivo-text" class="truncate text-[10.5px] font-semibold text-slate-500">Filtrado en vivo</span>
                    </span>
                    <button type="button" id="btn-limpiar-filtros"
                            class="{{ ($filtros['q'] || $filtros['area_id'] || $filtros['capacitacion_id'] || $filtros['estado']) ? '' : 'hidden' }} ml-1 px-1.5 py-0.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 text-xs font-bold transition-all cursor-pointer"
                            title="Limpiar todos los filtros">
                        <i class="fa-solid fa-xmark mr-0.5"></i> Limpiar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TABLA DE SEGUIMIENTO (RESPONSIVE: TARJETAS EN MÓVIL, TABLA EN ESCRITORIO) -->
    <div id="contenedor-tabla-seguimiento" class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-opacity duration-200">
        
        <!-- VISTA MÓVIL: TARJETAS (block md:hidden) -->
        <div class="block md:hidden divide-y divide-line/60">
            @forelse ($filas as $fila)
                @php
                    $asignacionesPayload = $fila->asignaciones->map(function ($asig) use ($estados) {
                        return [
                            'id' => $asig->capacitacion->id,
                            'titulo' => $asig->capacitacion->titulo,
                            'descripcion' => $asig->capacitacion->descripcion,
                            'total_modulos' => $asig->total_modulos,
                            'completados' => $asig->completados,
                            'porcentaje' => $asig->porcentaje,
                            'estado' => $asig->estado,
                            'estado_nombre' => $estados[$asig->estado] ?? $asig->estado,
                            'minimo_aprobacion' => (float) $asig->capacitacion->porcentaje_aprobacion,
                            'mejor_nota' => $asig->mejor_nota,
                            'certificado' => (bool) $asig->certificado,
                        ];
                    });
                @endphp
                <div class="p-4 space-y-3.5 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-2xl bg-brand-light text-brand-blue font-heading font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                {{ strtoupper(substr($fila->usuario->nombre_completo, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <h4 class="font-heading font-bold text-sm text-brand-dark leading-tight truncate">{{ $fila->usuario->nombre_completo }}</h4>
                                <p class="text-[11px] text-muted font-mono mt-0.5">C.C. {{ $fila->usuario->identificacion }}</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-bold shrink-0">
                            <i class="fa-solid fa-briefcase text-[9px] text-slate-400"></i>
                            {{ $fila->usuario->area->nombre ?? 'Sin área' }}
                        </span>
                    </div>

                    <!-- Avance Global Barra -->
                    <div class="bg-slate-50 p-3 rounded-2xl border border-line/60 space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-600">Avance Global</span>
                            <span class="text-brand-blue font-extrabold text-sm">{{ $fila->porcentaje_global }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-brand-blue h-2 rounded-full transition-all duration-300" style="width: {{ $fila->porcentaje_global }}%"></div>
                        </div>
                    </div>

                    <!-- Métricas de formación -->
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-slate-50 p-2 rounded-xl border border-line/60">
                            <span class="block text-[10px] font-bold uppercase text-muted">Asignadas</span>
                            <span class="text-xs font-extrabold text-brand-dark">{{ $fila->total_asignadas }}</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-line/60">
                            <span class="block text-[10px] font-bold uppercase text-muted">Completas</span>
                            <span class="text-xs font-extrabold {{ $fila->completadas > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $fila->completadas }}
                            </span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-line/60">
                            <span class="block text-[10px] font-bold uppercase text-muted">Certificados</span>
                            <span class="text-xs font-extrabold text-amber-700">
                                {{ $fila->certificados_total }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="button"
                                class="btn-ver-asignaciones w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-brand-blue/10 hover:bg-brand-blue text-brand-blue hover:text-white font-heading font-bold text-xs transition-all cursor-pointer shadow-2xs"
                                data-nombre="{{ $fila->usuario->nombre_completo }}"
                                data-identificacion="{{ $fila->usuario->identificacion }}"
                                data-area="{{ $fila->usuario->area->nombre ?? 'Sin área' }}"
                                data-asignadas="{{ $fila->total_asignadas }}"
                                data-promedio="{{ $fila->porcentaje_global }}"
                                data-lista='@json($asignacionesPayload)'>
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>Ver detalle de cursos</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-muted">
                    <i class="fa-solid fa-chart-line text-3xl mb-2 text-slate-300 block"></i>
                    No se encontraron colaboradores con los filtros indicados.
                </div>
            @endforelse
        </div>

        <!-- VISTA ESCRITORIO: TABLA (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                    <tr>
                        <th class="px-5 sm:px-6 py-3.5 font-bold">Colaborador</th>
                        <th class="px-4 py-3.5 font-bold">Área</th>
                        <th class="px-4 py-3.5 font-bold text-center">Capacitaciones</th>
                        <th class="px-4 py-3.5 font-bold">Avance Global</th>
                        <th class="px-4 py-3.5 font-bold text-center">Finalizadas</th>
                        <th class="px-4 py-3.5 font-bold text-center">Certificados</th>
                        <th class="px-5 sm:px-6 py-3.5 font-bold text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/50">
                    @forelse ($filas as $fila)
                        @php
                            $asignacionesPayload = $fila->asignaciones->map(function ($asig) use ($estados) {
                                return [
                                    'id' => $asig->capacitacion->id,
                                    'titulo' => $asig->capacitacion->titulo,
                                    'descripcion' => $asig->capacitacion->descripcion,
                                    'total_modulos' => $asig->total_modulos,
                                    'completados' => $asig->completados,
                                    'porcentaje' => $asig->porcentaje,
                                    'estado' => $asig->estado,
                                    'estado_nombre' => $estados[$asig->estado] ?? $asig->estado,
                                    'minimo_aprobacion' => (float) $asig->capacitacion->porcentaje_aprobacion,
                                    'mejor_nota' => $asig->mejor_nota,
                                    'certificado' => (bool) $asig->certificado,
                                ];
                            });
                        @endphp
                        <tr class="hover:bg-brand-light/30 transition-colors">
                            <td class="px-5 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-brand-light text-brand-blue font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($fila->usuario->nombre_completo, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-brand-dark leading-tight">{{ $fila->usuario->nombre_completo }}</p>
                                        <p class="text-[11px] text-muted font-mono mt-0.5">C.C. {{ $fila->usuario->identificacion }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-medium">
                                    <i class="fa-solid fa-briefcase text-[10px] text-slate-400"></i>
                                    {{ $fila->usuario->area->nombre ?? 'Sin área' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-heading font-extrabold bg-blue-50 text-brand-blue border border-blue-200">
                                    <i class="fa-solid fa-graduation-cap text-[11px]"></i>
                                    {{ $fila->total_asignadas }} {{ $fila->total_asignadas === 1 ? 'capacitación' : 'capacitaciones' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="w-32 space-y-1">
                                    <div class="flex items-center justify-between text-[10px] font-bold">
                                        <span class="text-slate-600">Promedio</span>
                                        <span class="text-brand-blue">{{ $fila->porcentaje_global }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-brand-blue h-1.5 rounded-full transition-all duration-300" style="width: {{ $fila->porcentaje_global }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-xs font-bold {{ $fila->completadas > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                    {{ $fila->completadas }} / {{ $fila->total_asignadas }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($fila->certificados_total > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fa-solid fa-award"></i> {{ $fila->certificados_total }}
                                    </span>
                                @else
                                    <span class="text-muted text-[11px]">—</span>
                                @endif
                            </td>
                            <td class="px-5 sm:px-6 py-3.5 text-right">
                                <button type="button"
                                        class="btn-ver-asignaciones inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-brand-blue/10 hover:bg-brand-blue text-brand-blue hover:text-white font-heading font-bold text-xs transition-all cursor-pointer shadow-2xs hover:shadow"
                                        data-nombre="{{ $fila->usuario->nombre_completo }}"
                                        data-identificacion="{{ $fila->usuario->identificacion }}"
                                        data-area="{{ $fila->usuario->area->nombre ?? 'Sin área' }}"
                                        data-asignadas="{{ $fila->total_asignadas }}"
                                        data-promedio="{{ $fila->porcentaje_global }}"
                                        data-lista='@json($asignacionesPayload)'>
                                    <i class="fa-solid fa-eye text-[11px]"></i>
                                    <span>Ver asignaciones</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-muted">
                                <i class="fa-solid fa-chart-line text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron colaboradores con los filtros indicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($filas->hasPages())
            <div class="px-6 py-4 border-t border-line/60 bg-slate-50/50">
                {{ $filas->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL DE ASIGNACIONES DEL COLABORADOR -->
<div id="modal-asignaciones" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-titulo" role="dialog" aria-modal="true">
    <!-- Backdrop oscuro -->
    <div id="modal-backdrop" class="fixed inset-0 bg-brand-dark/60 backdrop-blur-xs transition-opacity opacity-0"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-panel" class="relative bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <!-- Encabezado del modal con fondo corporativo -->
            <div class="bg-brand-dark text-white p-6 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
                <div class="absolute right-12 -bottom-10 w-24 h-24 bg-brand-blue/20 rounded-full pointer-events-none"></div>

                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <span class="w-12 h-12 rounded-2xl bg-white/10 text-brand-cyan flex items-center justify-center text-xl shrink-0 border border-white/10">
                            <i class="fa-solid fa-user-graduate"></i>
                        </span>
                        <div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-cyan tracking-wider uppercase">
                                <i class="fa-solid fa-list-check"></i> Asignaciones del Participante
                            </span>
                            <h3 id="modal-colaborador-nombre" class="text-lg font-heading font-extrabold text-white leading-tight"></h3>
                            <p class="text-xs text-white/70 mt-0.5">
                                <span id="modal-colaborador-doc" class="font-mono"></span> &bull; 
                                <span id="modal-colaborador-area"></span>
                            </p>
                        </div>
                    </div>

                    <button type="button" id="btn-cerrar-modal" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar ventana">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Resumen rápido en cabecera -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-white/10 text-xs">
                    <div>
                        <span class="text-white/60 text-[10px] uppercase font-bold tracking-wider block">Total Asignadas</span>
                        <span id="modal-total-asignadas" class="text-base font-extrabold text-white font-heading">0</span>
                    </div>
                    <div>
                        <span class="text-white/60 text-[10px] uppercase font-bold tracking-wider block">Avance Promedio</span>
                        <span id="modal-promedio-global" class="text-base font-extrabold text-brand-cyan font-heading">0%</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <span class="text-white/60 text-[10px] uppercase font-bold tracking-wider block">Estado de Cursos</span>
                        <span id="modal-conteo-estados" class="text-xs font-semibold text-white/90"></span>
                    </div>
                </div>
            </div>

            <!-- Cuerpo del modal: lista de capacitaciones -->
            <div class="p-6 max-h-[60vh] overflow-y-auto space-y-3 bg-slate-50/60" id="modal-lista-asignaciones">
                <!-- Se inyecta dinámicamente con JS -->
            </div>

            <!-- Pie del modal -->
            <div class="px-6 py-4 bg-white border-t border-line/60 flex items-center justify-between">
                <span class="text-[11px] text-muted">
                    <i class="fa-solid fa-circle-info text-brand-blue mr-1"></i> Información actualizada según la plataforma.
                </span>
                <button type="button" id="btn-cerrar-modal-pie" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-asignaciones');
    const backdrop = document.getElementById('modal-backdrop');
    const panel = document.getElementById('modal-panel');
    const btnCerrar = document.getElementById('btn-cerrar-modal');
    const btnCerrarPie = document.getElementById('btn-cerrar-modal-pie');

    const nombreEl = document.getElementById('modal-colaborador-nombre');
    const docEl = document.getElementById('modal-colaborador-doc');
    const areaEl = document.getElementById('modal-colaborador-area');
    const totalEl = document.getElementById('modal-total-asignadas');
    const promEl = document.getElementById('modal-promedio-global');
    const estadosEl = document.getElementById('modal-conteo-estados');
    const listaEl = document.getElementById('modal-lista-asignaciones');

    function abrirModal(datos) {
        nombreEl.textContent = datos.nombre;
        docEl.textContent = 'C.C. ' + datos.identificacion;
        areaEl.textContent = datos.area;
        totalEl.textContent = datos.asignadas + (datos.asignadas === 1 ? ' curso' : ' cursos');
        promEl.textContent = datos.promedio + '%';

        const lista = Array.isArray(datos.lista) ? datos.lista : [];
        const completadas = lista.filter(x => x.estado === 'COMPLETADA').length;
        estadosEl.textContent = `${completadas} completada${completadas === 1 ? '' : 's'} de ${lista.length}`;

        listaEl.innerHTML = '';

        if (lista.length === 0) {
            listaEl.innerHTML = `
                <div class="p-8 text-center text-muted bg-white rounded-2xl border border-line/60">
                    <i class="fa-solid fa-folder-open text-2xl text-slate-300 mb-2 block"></i>
                    Este colaborador no tiene capacitaciones asignadas actualmente.
                </div>
            `;
        } else {
            lista.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-2xl border border-line/80 p-4 shadow-2xs hover:border-brand-blue/40 transition-all';

                let badgeEstado = 'bg-slate-100 text-slate-600 border-slate-200';
                if (item.estado === 'COMPLETADA') badgeEstado = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                else if (item.estado === 'MODULOS_COMPLETOS') badgeEstado = 'bg-cyan-50 text-cyan-700 border-cyan-200';
                else if (item.estado === 'EN_PROGRESO') badgeEstado = 'bg-blue-50 text-brand-blue border-blue-200';
                else if (item.estado === 'NO_APROBADA') badgeEstado = 'bg-red-50 text-red-700 border-red-200';

                let notaHtml = '<span class="text-muted">—</span>';
                if (item.mejor_nota !== null && item.mejor_nota !== undefined) {
                    const aprobada = Number(item.mejor_nota) >= Number(item.minimo_aprobacion);
                    notaHtml = `<span class="font-bold ${aprobada ? 'text-emerald-700' : 'text-red-600'}">${item.mejor_nota}%</span> (Mín: ${item.minimo_aprobacion}%)`;
                }

                let certificadoHtml = '';
                if (item.certificado) {
                    certificadoHtml = `
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                            <i class="fa-solid fa-award"></i> Certificado Emitido
                        </span>
                    `;
                }

                card.innerHTML = `
                    <div class="flex items-start justify-between gap-3 mb-2.5">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold flex items-center justify-center shrink-0">
                                    ${index + 1}
                                </span>
                                <h4 class="font-heading font-bold text-brand-dark text-sm truncate" title="${item.titulo}">
                                    ${item.titulo}
                                </h4>
                            </div>
                            ${item.descripcion ? `<p class="text-[11px] text-muted line-clamp-1 mt-1 pl-7">${item.descripcion}</p>` : ''}
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border shrink-0 ${badgeEstado}">
                            ${item.estado_nombre}
                        </span>
                    </div>

                    <div class="space-y-1.5 pl-7">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">
                                <i class="fa-regular fa-circle-check text-brand-blue mr-1"></i>
                                Módulos: <strong class="text-brand-dark">${item.completados} de ${item.total_modulos}</strong>
                            </span>
                            <span class="font-bold text-brand-blue">${item.porcentaje}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-brand-blue h-2 rounded-full transition-all duration-300" style="width: ${item.porcentaje}%"></div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 text-[11px] border-t border-slate-100">
                            <div class="text-slate-500">
                                <i class="fa-solid fa-clipboard-check text-slate-400 mr-1"></i>
                                Evaluación: ${notaHtml}
                            </div>
                            <div>
                                ${certificadoHtml}
                            </div>
                        </div>
                    </div>
                `;

                listaEl.appendChild(card);
            });
        }

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModal() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }

    // Función para inicializar botones del modal de asignaciones
    function inicializarBotonesModal() {
        document.querySelectorAll('.btn-ver-asignaciones').forEach(btn => {
            btn.onclick = () => {
                try {
                    const rawLista = btn.getAttribute('data-lista');
                    const lista = rawLista ? JSON.parse(rawLista) : [];
                    abrirModal({
                        nombre: btn.getAttribute('data-nombre'),
                        identificacion: btn.getAttribute('data-identificacion'),
                        area: btn.getAttribute('data-area'),
                        asignadas: parseInt(btn.getAttribute('data-asignadas'), 10) || 0,
                        promedio: parseInt(btn.getAttribute('data-promedio'), 10) || 0,
                        lista: lista,
                    });
                } catch (e) {
                    console.error('Error parseando asignaciones:', e);
                }
            };
        });
    }

    // Inicialización inicial de modales
    inicializarBotonesModal();

    btnCerrar?.addEventListener('click', cerrarModal);
    btnCerrarPie?.addEventListener('click', cerrarModal);
    backdrop?.addEventListener('click', cerrarModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarModal();
        }
    });

    /* ==========================================================================
       SISTEMA DE FILTRADO EN TIEMPO REAL (SIN RECARGA DE PÁGINA)
       ========================================================================== */
    const formFiltro = document.getElementById('form-filtro-progreso');
    const inputQ = document.getElementById('filtro-q');
    const selectSede = document.getElementById('filtro-sede');
    const selectArea = document.getElementById('filtro-area');
    const selectCap = document.getElementById('filtro-cap');
    const selectEstado = document.getElementById('filtro-estado');
    const spinner = document.getElementById('spinner-busqueda');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    const contenedorTabla = document.getElementById('contenedor-tabla-seguimiento');
    const contenedorResumen = document.getElementById('contenedor-resumen');

    let debounceTimer = null;
    let abortController = null;

    function ejecutarFiltroEnVivo() {
        if (!formFiltro) return;

        // Cancelar petición anterior si aún estaba pendiente
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        const formData = new FormData(formFiltro);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value && value.toString().trim()) {
                params.append(key, value.toString().trim());
            }
        }

        const url = `${formFiltro.action}${params.toString() ? '?' + params.toString() : ''}`;

        // Mostrar indicador visual de carga
        if (spinner) spinner.classList.remove('hidden');
        if (contenedorTabla) contenedorTabla.classList.add('opacity-50');

        // Visibilidad del botón de limpiar filtros
        const hayFiltros = Boolean(
            (inputQ && inputQ.value.trim()) ||
            (selectSede && selectSede.value) ||
            (selectArea && selectArea.value) ||
            (selectCap && selectCap.value) ||
            (selectEstado && selectEstado.value)
        );
        if (btnLimpiar) {
            if (hayFiltros) btnLimpiar.classList.remove('hidden');
            else btnLimpiar.classList.add('hidden');
        }

        fetch(url, {
            signal: abortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Actualizar tarjetas de indicadores superiores
            const nuevoResumen = doc.getElementById('contenedor-resumen');
            if (nuevoResumen && contenedorResumen) {
                contenedorResumen.innerHTML = nuevoResumen.innerHTML;
            }

            // Actualizar tabla/tarjetas de resultados
            const nuevaTabla = doc.getElementById('contenedor-tabla-seguimiento');
            if (nuevaTabla && contenedorTabla) {
                contenedorTabla.innerHTML = nuevaTabla.innerHTML;
            }

            // Actualizar la URL en la barra de navegación sin recargar
            window.history.replaceState({}, '', url);

            // Re-vincular eventos del modal y de paginación AJAX
            inicializarBotonesModal();
            inicializarPaginacionAjax();
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error al filtrar progreso en tiempo real:', err);
            }
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
            if (contenedorTabla) contenedorTabla.classList.remove('opacity-50');
        });
    }

    // Paginación suave vía AJAX sin perder filtros
    function inicializarPaginacionAjax() {
        if (!contenedorTabla) return;
        contenedorTabla.querySelectorAll('nav[role="navigation"] a, .pagination a').forEach(enlace => {
            enlace.onclick = (e) => {
                e.preventDefault();
                const href = enlace.getAttribute('href');
                if (!href) return;

                if (contenedorTabla) contenedorTabla.classList.add('opacity-50');

                fetch(href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const nuevaTabla = doc.getElementById('contenedor-tabla-seguimiento');
                    if (nuevaTabla) {
                        contenedorTabla.innerHTML = nuevaTabla.innerHTML;
                    }

                    const nuevoResumen = doc.getElementById('contenedor-resumen');
                    if (nuevoResumen && contenedorResumen) {
                        contenedorResumen.innerHTML = nuevoResumen.innerHTML;
                    }

                    window.history.replaceState({}, '', href);
                    inicializarBotonesModal();
                    inicializarPaginacionAjax();
                })
                .catch(err => console.error('Error en paginación AJAX:', err))
                .finally(() => {
                    if (contenedorTabla) contenedorTabla.classList.remove('opacity-50');
                });
            };
        });
    }

    // Eventos en tiempo real:
    // 1. Entrada de texto (nombre o cédula) con debounce de 260ms
    inputQ?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(ejecutarFiltroEnVivo, 260);
    });

    // 2. Selección inmediata de sede, área, capacitación o estado
    selectSede?.addEventListener('change', ejecutarFiltroEnVivo);
    selectArea?.addEventListener('change', ejecutarFiltroEnVivo);
    selectCap?.addEventListener('change', ejecutarFiltroEnVivo);
    selectEstado?.addEventListener('change', ejecutarFiltroEnVivo);

    // 3. Prevenir recarga completa de formulario al presionar Enter
    formFiltro?.addEventListener('submit', (e) => {
        e.preventDefault();
        clearTimeout(debounceTimer);
        ejecutarFiltroEnVivo();
    });

    // 4. Botón limpiar filtros
    btnLimpiar?.addEventListener('click', () => {
        if (inputQ) inputQ.value = '';
        if (selectSede) selectSede.value = '';
        if (selectArea) selectArea.value = '';
        if (selectCap) selectCap.value = '';
        if (selectEstado) selectEstado.value = '';
        ejecutarFiltroEnVivo();
    });

    // Inicializar paginación AJAX inicial
    inicializarPaginacionAjax();
});
</script>
@endsection
