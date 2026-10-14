@extends('layouts.jefe')

@section('title', 'Borradores de Capacitaciones')
@section('page_title', 'Borradores de Capacitaciones')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    <!-- ========================================================
         BANNER DE BIENVENIDA CON NOMBRE COMPLETO Y OLAS
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-slate-800 to-[#0e4474] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-white/10">
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-cyan-400/10 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-sky/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-cyan-300 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Bandeja de Borradores · Área: {{ $usuario->area->nombre ?? 'Sin área asignada' }}</span>
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl tracking-tight leading-tight text-white">
                    Borradores de Capacitaciones
                </h1>

                <p class="mt-2 text-sm sm:text-base text-slate-200/90 leading-relaxed">
                    Estas capacitaciones aún <strong class="text-white font-semibold">no son visibles</strong> para los empleados de tu área. Puedes prepararlas, agregar sus módulos, configurar su evaluación y publicarlas cuando estén listas.
                </p>

                <div class="mt-5 flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('jefe.capacitaciones.create') }}" id="btn-hero-crear-borrador"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-brand-blue hover:bg-brand-light font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-plus"></i>
                        <span>Nueva Capacitación</span>
                    </a>
                    <a href="{{ route('jefe.capacitaciones.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/25 text-white font-heading font-bold text-xs sm:text-sm transition-colors">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Ver Publicadas ({{ $estadisticas['activas'] }})</span>
                    </a>
                </div>
            </div>

            <div class="lg:w-72 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/15 shrink-0">
                <div class="flex items-center justify-between text-xs text-white/90 mb-2">
                    <span class="font-semibold uppercase tracking-wider text-[11px] text-cyan-300">Resumen Formación</span>
                    <span class="font-bold text-sm text-white">{{ $estadisticas['empleados'] }} empleados</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center text-xs mt-2">
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-amber-300">{{ $estadisticas['borradores'] }}</span>
                        <span class="text-[10px] text-slate-300">Borradores</span>
                    </div>
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-emerald-300">{{ $estadisticas['activas'] }}</span>
                        <span class="text-[10px] text-slate-300">Publicadas</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
            </svg>
        </div>
    </div>

    <!-- ========================================================
         PESTAÑAS DE NAVEGACIÓN RÁPIDA: PUBLICADAS / BORRADORES
         ======================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-4">
        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-line shadow-2xs">
            <a href="{{ route('jefe.capacitaciones.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold text-slate-600 hover:text-brand-blue hover:bg-white/60 transition-all">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                <span>Publicadas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-bold">
                    {{ $estadisticas['activas'] }}
                </span>
            </a>
            <a href="{{ route('jefe.capacitaciones.borradores') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold bg-white text-brand-blue shadow-xs border border-line/60 transition-all">
                <i class="fa-solid fa-file-pen text-amber-500 text-xs"></i>
                <span>Borradores</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">
                    {{ $estadisticas['borradores'] }}
                </span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('jefe.capacitaciones.create') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-blue text-white text-xs font-bold shadow-xs hover:bg-brand-deep transition-colors">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Capacitación</span>
            </a>
        </div>
    </div>

    <!-- ========================================================
         LISTADO DE BORRADORES (DISEÑO IDÉNTICO A LAS PUBLICADAS)
         ======================================================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-bold text-lg text-brand-dark">Capacitaciones en Borrador</h2>
                <p class="text-xs text-muted">Contenido en edición. Cuando todo esté listo, presiona "Publicar Ahora" para habilitarla a tu área.</p>
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
                         class="bg-white rounded-3xl border-2 border-dashed border-amber-200/90 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col sm:flex-row bg-gradient-to-br from-white via-white to-amber-50/20">

                    <!-- Portada con ola -->
                    <div class="relative sm:w-44 h-40 sm:h-auto shrink-0 overflow-hidden bg-slate-200">
                        <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/20 to-transparent sm:bg-gradient-to-r"></div>
                        <svg class="absolute -bottom-px left-0 w-full h-4 text-white sm:hidden" viewBox="0 0 600 20" preserveAspectRatio="none">
                            <path d="M0,5 C100,18 200,3 300,14 C400,22 500,6 600,15 L600,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                        <svg class="absolute top-0 -right-px h-full w-4 text-white hidden sm:block" viewBox="0 0 20 300" preserveAspectRatio="none">
                            <path d="M20,0 C5,40 18,80 8,120 C0,160 16,200 6,240 C2,270 14,285 20,300 L20,0 Z" fill="currentColor"/>
                        </svg>

                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-amber-500/90 text-white shadow-xs">
                            <i class="fa-solid fa-file-pen text-[10px]"></i>
                            Borrador
                        </span>
                    </div>

                    <!-- Contenido -->
                    <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between gap-4 min-w-0">
                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 text-[10px] font-extrabold uppercase tracking-wide">
                                        Pendiente de Publicar
                                    </span>
                                    <span class="text-[11px] text-muted">
                                        {{ $cap->evaluaciones->isNotEmpty() ? 'Con evaluación asignada' : 'Sin evaluación aún' }}
                                    </span>
                                </div>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight text-brand-dark line-clamp-2">
                                    {{ $cap->titulo }}
                                </h3>
                                <p class="text-[11px] text-muted mt-1">
                                    {{ $cap->modulos_activos_count }} {{ \Illuminate\Support\Str::plural('módulo', $cap->modulos_activos_count) }}
                                    @if ($cap->duracion_estimada) · {{ $cap->duracion_estimada }} h @endif
                                    @if ($cap->fecha_limite) · Límite {{ $cap->fecha_limite->format('d/m/Y') }} @endif
                                </p>
                            </div>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $cap->descripcion }}
                            </p>

                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($cap->areas as $area)
                                    <span class="px-2 py-0.5 rounded-full bg-brand-light text-brand-blue text-[10px] font-bold">
                                        {{ $area->nombre }}
                                    </span>
                                @empty
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold">
                                        Área por defecto: {{ $usuario->area->nombre ?? 'Tu Área' }}
                                    </span>
                                @endforelse
                            </div>

                            <div class="p-2.5 rounded-2xl bg-amber-50/80 border border-amber-200/70 text-[11px] text-amber-900 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-amber-600 text-xs shrink-0"></i>
                                <span>No disponible para colaboradores hasta que pulses <strong>Publicar Ahora</strong>.</span>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                            <!-- Publicar Ahora -->
                            <form method="POST" action="{{ route('jefe.capacitaciones.publicar', $cap) }}" class="m-0"
                                  onsubmit="return confirm('¿Deseas publicar esta capacitación ahora? Estará visible de inmediato para los empleados de tu área.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white font-heading font-extrabold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                    <span>Publicar Ahora</span>
                                </button>
                            </form>

                            <!-- Editar Borrador -->
                            <a href="{{ route('jefe.capacitaciones.edit', $cap) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                                <span>Editar</span>
                            </a>

                            <!-- + Evaluación -->
                            <a href="{{ route('jefe.evaluaciones.create', ['capacitacion' => $cap->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                <i class="fa-solid fa-clipboard-question text-xs"></i>
                                <span>+ Evaluación</span>
                            </a>

                            <!-- Descartar Borrador -->
                            <form method="POST" action="{{ route('jefe.capacitaciones.destroy', $cap) }}" class="ml-auto m-0"
                                  onsubmit="return confirm('¿Estás seguro de descartar este borrador? Se eliminarán los datos no publicados.');">
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
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">No tienes capacitaciones en borrador</h3>
                    <p class="text-xs text-muted max-w-sm mx-auto">
                        Todas tus capacitaciones están publicadas o puedes crear una nueva guardándola como borrador mientras completas sus temas y recursos.
                    </p>
                    <div class="pt-2 flex justify-center gap-3">
                        <a href="{{ route('jefe.capacitaciones.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            <span>Crear Capacitación</span>
                        </a>
                        <a href="{{ route('jefe.capacitaciones.index') }}"
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
