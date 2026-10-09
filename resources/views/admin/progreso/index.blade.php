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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
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

    <!-- FILTROS DE BÚSQUEDA -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs">
        <form method="GET" action="{{ route('admin.progreso.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label for="filtro-q" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Buscar empleado</label>
                <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                       placeholder="Nombre o Cédula..."
                       class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
            </div>

            <div>
                <label for="filtro-area" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Área</label>
                <select id="filtro-area" name="area_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ (string)$filtros['area_id'] === (string)$area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-cap" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Capacitación</label>
                <select id="filtro-cap" name="capacitacion_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <option value="">Todas las capacitaciones</option>
                    @foreach ($capacitaciones as $cap)
                        <option value="{{ $cap->id }}" {{ (string)$filtros['capacitacion_id'] === (string)$cap->id ? 'selected' : '' }}>{{ $cap->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-estado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Estado</label>
                <select id="filtro-estado" name="estado"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $clave => $nombre)
                        <option value="{{ $clave }}" {{ $filtros['estado'] === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter mr-1"></i> Filtrar
                </button>
                @if ($filtros['q'] || $filtros['area_id'] || $filtros['capacitacion_id'] || $filtros['estado'])
                    <a href="{{ route('admin.progreso.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors" title="Limpiar">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE SEGUIMIENTO -->
    <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                    <tr>
                        <th class="px-5 sm:px-6 py-3.5 font-bold">Colaborador</th>
                        <th class="px-4 py-3.5 font-bold">Área</th>
                        <th class="px-4 py-3.5 font-bold">Capacitación</th>
                        <th class="px-4 py-3.5 font-bold">Módulos</th>
                        <th class="px-4 py-3.5 font-bold">Avance</th>
                        <th class="px-4 py-3.5 font-bold text-center">Mejor Nota</th>
                        <th class="px-4 py-3.5 font-bold text-center">Estado</th>
                        <th class="px-4 py-3.5 font-bold text-center">Certificado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/50">
                    @forelse ($filas as $fila)
                        <tr class="hover:bg-brand-light/30 transition-colors">
                            <td class="px-5 sm:px-6 py-3.5">
                                <p class="font-bold text-brand-dark leading-tight">{{ $fila->usuario->nombre_completo }}</p>
                                <p class="text-[11px] text-muted font-mono mt-0.5">C.C. {{ $fila->usuario->identificacion }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $fila->usuario->area->nombre ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-brand-dark block line-clamp-1 max-w-xs">{{ $fila->capacitacion->titulo }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $fila->completados }} de {{ $fila->total_modulos }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="w-28 space-y-1">
                                    <div class="flex items-center justify-between text-[10px] font-bold">
                                        <span class="text-brand-blue">{{ $fila->porcentaje }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-brand-blue h-1.5 rounded-full" style="width: {{ $fila->porcentaje }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold">
                                @if ($fila->mejor_nota !== null)
                                    <span class="{{ $fila->mejor_nota >= (float)$fila->capacitacion->porcentaje_aprobacion ? 'text-emerald-700' : 'text-red-600' }}">
                                        {{ rtrim(rtrim(number_format($fila->mejor_nota, 1), '0'), '.') }}%
                                    </span>
                                @else
                                    <span class="text-muted font-normal">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $badge = match($fila->estado) {
                                        'COMPLETADA' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'MODULOS_COMPLETOS' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'EN_PROGRESO' => 'bg-blue-50 text-brand-blue border-blue-200',
                                        'NO_APROBADA' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge }}">
                                    {{ $estados[$fila->estado] ?? $fila->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($fila->certificado)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                        <i class="fa-solid fa-award"></i> Emitido
                                    </span>
                                @else
                                    <span class="text-muted text-[11px]">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-muted">
                                <i class="fa-solid fa-chart-line text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron registros de progreso con los filtros indicados.
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
@endsection
