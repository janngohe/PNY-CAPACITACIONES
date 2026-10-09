@extends('layouts.admin')

@section('title', 'Asignación de Capacitaciones a Áreas')
@section('page_title', 'Asignación a Áreas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Segmentación y Alcance',
        'titulo' => 'Asignar Capacitaciones a las Áreas',
        'descripcion' => 'Define qué áreas tienen acceso a cada curso formativo. Todos los colaboradores pertenecientes a las áreas marcadas verán la capacitación en su catálogo de estudio.',
        'icono' => 'fa-diagram-project',
    ])

    <div class="space-y-4">
        @forelse ($capacitaciones as $cap)
            @php
                $areasAsignadas = $cap->areas->pluck('id')->all();
            @endphp
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 transition-all">
                <form method="POST" action="{{ route('admin.asignaciones.update', $cap) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-line/60 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-sm shrink-0">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>
                            <div>
                                <h3 class="font-heading font-extrabold text-base text-brand-dark leading-tight">{{ $cap->titulo }}</h3>
                                <p class="text-xs text-muted mt-0.5">
                                    {{ $cap->modulos_activos_count }} módulos
                                    · Min. aprobación {{ rtrim(rtrim(number_format((float) $cap->porcentaje_aprobacion, 2), '0'), '.') }}%
                                    · Estado: <strong class="{{ $cap->estado ? 'text-emerald-700' : 'text-slate-500' }}">{{ $cap->estado ? 'Activa' : 'Desactivada' }}</strong>
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors self-start sm:self-auto cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Guardar Asignación</span>
                        </button>
                    </div>

                    <!-- Cuadrícula de Áreas con checkboxes -->
                    <div>
                        <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Selecciona las áreas autorizadas:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                            @foreach ($areas as $area)
                                @php
                                    $asignada = in_array($area->id, $areasAsignadas, true);
                                    $colaboradores = $empleadosPorArea[$area->id] ?? 0;
                                @endphp
                                <label class="flex items-center justify-between p-3 rounded-2xl border border-line bg-slate-50/70 hover:bg-brand-light/50 transition-colors cursor-pointer {{ $asignada ? 'ring-2 ring-brand-blue/30 border-brand-blue/40 bg-brand-light/30' : '' }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <input type="checkbox" name="areas[]" value="{{ $area->id }}" {{ $asignada ? 'checked' : '' }}
                                               class="rounded text-brand-blue focus:ring-brand-blue">
                                        <div class="truncate">
                                            <span class="block text-xs font-bold text-brand-dark truncate leading-tight">{{ $area->nombre }}</span>
                                            <span class="text-[10px] text-muted">{{ $colaboradores }} colaboradores</span>
                                        </div>
                                    </div>
                                    @if ($asignada)
                                        <span class="text-[10px] font-extrabold text-brand-blue bg-white px-1.5 py-0.5 rounded shadow-2xs">Activa</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-line/80 p-12 text-center text-muted">
                No hay capacitaciones creadas para asignar.
            </div>
        @endforelse
    </div>
</div>
@endsection
