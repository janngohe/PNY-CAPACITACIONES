@extends('layouts.admin')

@section('title', 'Borradores de Capacitaciones')
@section('page_title', 'Borradores de Capacitaciones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Bandeja de Borradores',
        'titulo' => 'Borradores de Capacitaciones',
        'descripcion' => 'Capacitaciones institucionales en proceso de creación o edición. No son visibles para los empleados hasta que sean publicadas formalmente.',
        'icono' => 'fa-file-lines',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y PESTAÑAS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-4">
        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-line shadow-2xs">
            <a href="{{ route('admin.capacitaciones.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold text-slate-600 hover:text-brand-blue hover:bg-white/60 transition-all">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                <span>Publicadas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-bold">
                    {{ $estadisticas['activas'] }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.borradores') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold bg-white text-brand-blue shadow-xs border border-line/60 transition-all">
                <i class="fa-solid fa-file-pen text-amber-500 text-xs"></i>
                <span>Borradores</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">
                    {{ $estadisticas['borradores'] }}
                </span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.capacitaciones.create') }}" id="btn-crear-capacitacion"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Capacitación</span>
            </a>
        </div>
    </div>

    <!-- LISTA DE BORRADORES EN GRID -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-heading font-bold text-base text-brand-dark">Catálogo en Borrador</h3>
                <p class="text-xs text-muted">Revisa contenidos, asignaciones a áreas y publica cuando esté completado.</p>
            </div>
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                {{ $capacitaciones->count() }} {{ \Illuminate\Support\Str::plural('borrador', $capacitaciones->count()) }}
            </span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 sm:gap-6">
            @forelse ($capacitaciones as $cap)
                @php
                    $imagen = $cap->url_imagen;
                @endphp
                <article id="borrador-{{ $cap->id }}"
                         class="bg-white rounded-3xl border-2 border-dashed border-amber-200/90 shadow-2xs hover:shadow-md transition-all overflow-hidden flex flex-col sm:flex-row bg-gradient-to-br from-white via-white to-amber-50/20">

                    <!-- Portada -->
                    <div class="relative sm:w-48 h-44 sm:h-auto shrink-0 overflow-hidden bg-slate-200">
                        <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/20 to-transparent sm:bg-gradient-to-r"></div>

                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-amber-500/90 text-white shadow-xs">
                            <i class="fa-solid fa-file-pen text-[10px]"></i>
                            Borrador
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
                                    · {{ $cap->evaluaciones_count }} {{ \Illuminate\Support\Str::plural('evaluación', $cap->evaluaciones_count) }}
                                    @if ($cap->duracion_estimada) · {{ $cap->duracion_estimada }} h @endif
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
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                            <!-- Publicar Ahora -->
                            <form method="POST" action="{{ route('admin.capacitaciones.publicar', $cap) }}" class="m-0"
                                  onsubmit="return confirm('¿Deseas publicar esta capacitación institucional? Estará activa de inmediato para las áreas asignadas.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white font-heading font-extrabold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                    <span>Publicar Ahora</span>
                                </button>
                            </form>

                            <a href="{{ route('admin.capacitaciones.edit', $cap) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                                <span>Editar</span>
                            </a>

                            <!-- Descartar Borrador -->
                            <form method="POST" action="{{ route('admin.capacitaciones.destroy', $cap) }}" class="ml-auto m-0"
                                  onsubmit="return confirm('¿Estás seguro de descartar este borrador institucional definitivamente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-heading font-bold text-red-600 hover:bg-red-50 border border-red-200 transition-colors cursor-pointer"
                                        title="Descartar borrador">
                                    <i class="fa-regular fa-trash-can text-xs"></i>
                                    <span class="hidden sm:inline">Descartar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">No hay capacitaciones en borrador</h3>
                    <p class="text-xs text-muted max-w-md mx-auto">
                        Todas las capacitaciones institucionales se encuentran publicadas. Al crear una nueva, puedes guardarla como borrador para completarla con calma.
                    </p>
                    <div class="pt-2 flex justify-center gap-3">
                        <a href="{{ route('admin.capacitaciones.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            <span>Crear Capacitación</span>
                        </a>
                        <a href="{{ route('admin.capacitaciones.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs sm:text-sm transition-colors">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Ver Publicadas</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
