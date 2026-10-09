@extends('layouts.admin')

@section('title', 'Gestión de Capacitaciones')
@section('page_title', 'Capacitaciones e Inducciones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Gestión General de Formación',
        'titulo' => 'Capacitaciones e Inducciones',
        'descripcion' => 'Administra todo el catálogo formativo de la empresa: crea cursos con módulos interactivos, asigna áreas destinatarias, establece condiciones de aprobación y gestiona su disponibilidad.',
        'icono' => 'fa-book-open',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y ESTADÍSTICAS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-line text-xs font-semibold text-slate-700 shadow-2xs">
                Total: <strong class="text-brand-dark">{{ $estadisticas['total'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                Activas: <strong>{{ $estadisticas['activas'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 border border-line text-xs font-semibold text-slate-600">
                Desactivadas: <strong>{{ $estadisticas['inactivas'] }}</strong>
            </span>
            @if ($estadisticas['sin_area'] > 0)
                <span class="px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-xs font-bold text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $estadisticas['sin_area'] }} sin área asignada
                </span>
            @endif
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.asignaciones.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Asignar a Áreas</span>
            </a>
            <a href="{{ route('admin.capacitaciones.create') }}" id="btn-crear-capacitacion"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Capacitación</span>
            </a>
        </div>
    </div>

    <!-- LISTA DE CAPACITACIONES EN GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 sm:gap-6">
        @forelse ($capacitaciones as $cap)
            @php
                $imagen = $cap->url_imagen;
                $activa = (bool) $cap->estado;
            @endphp
            <article id="capacitacion-{{ $cap->id }}"
                     class="bg-white rounded-3xl border border-line/80 shadow-2xs hover:shadow-md transition-all overflow-hidden flex flex-col sm:flex-row {{ $activa ? '' : 'opacity-85' }}">

                <!-- Portada -->
                <div class="relative sm:w-48 h-44 sm:h-auto shrink-0 overflow-hidden bg-brand-light">
                    <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}"
                         class="w-full h-full object-cover {{ $activa ? '' : 'grayscale' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/10 to-transparent sm:bg-gradient-to-r"></div>

                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md {{ $activa ? 'bg-emerald-600/90 text-white' : 'bg-slate-700/85 text-white' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-white {{ $activa ? 'animate-pulse' : '' }}"></span>
                        {{ $activa ? 'Activa' : 'Desactivada' }}
                    </span>
                </div>

                <!-- Detalle -->
                <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between gap-4 min-w-0">
                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-muted">
                                    Creado por: <strong class="text-brand-dark">{{ $cap->creador->nombre_completo ?? 'Administración' }}</strong>
                                </span>
                            </div>
                            <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight text-brand-dark line-clamp-2">{{ $cap->titulo }}</h3>
                            <p class="text-[11px] text-muted mt-1">
                                {{ $cap->modulos_activos_count }} {{ \Illuminate\Support\Str::plural('módulo', $cap->modulos_activos_count) }}
                                · Min. Aprobación {{ rtrim(rtrim(number_format((float) $cap->porcentaje_aprobacion, 2), '0'), '.') }}%
                                · {{ $cap->evaluaciones_count }} {{ \Illuminate\Support\Str::plural('evaluación', $cap->evaluaciones_count) }}
                            </p>
                        </div>

                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $cap->descripcion }}</p>

                        <!-- Áreas Asignadas -->
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Áreas con acceso:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($cap->areas as $area)
                                    <span class="px-2 py-0.5 rounded-full bg-brand-light text-brand-blue text-[10px] font-bold">{{ $area->nombre }}</span>
                                @empty
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                        Sin áreas asignadas
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Participación -->
                        <div class="text-[11px] text-slate-500 flex items-center justify-between pt-1">
                            <span><strong class="text-brand-dark">{{ $cap->participantes_count }}</strong> colaboradores han participado</span>
                            <span class="text-muted">Alcance: ~{{ $cap->empleados_objetivo }} empleados</span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                        <a href="{{ route('admin.capacitaciones.edit', $cap) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Editar</span>
                        </a>

                        <a href="{{ route('admin.evaluaciones.create', ['capacitacion' => $cap->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            <span>Evaluación</span>
                        </a>

                        <a href="{{ route('admin.asignaciones.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-diagram-project"></i>
                            <span>Áreas</span>
                        </a>

                        <form method="POST" action="{{ route('admin.capacitaciones.estado', $cap) }}" class="ml-auto m-0"
                              onsubmit="return confirm('¿{{ $activa ? 'Desactivar esta capacitación? Dejará de mostrarse a los empleados pero conservará todo su historial.' : '¿Activar nuevamente esta capacitación?' }}');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-heading font-bold border transition-colors cursor-pointer {{ $activa ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                {{ $activa ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-line/80 p-12 text-center space-y-3">
                <i class="fa-solid fa-book-open text-4xl text-slate-300"></i>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">Aún no hay capacitaciones creadas</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">Crea tu primer curso institucional y asígnalo a las áreas correspondientes.</p>
                <a href="{{ route('admin.capacitaciones.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
                    Crear Capacitación Ahora
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
