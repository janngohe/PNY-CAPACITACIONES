@extends('layouts.jefe')

@section('title', 'Capacitaciones Publicadas')
@section('page_title', 'Capacitaciones')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    <!-- ========================================================
         BANNER DE BIENVENIDA CON NOMBRE COMPLETO Y OLAS
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-brand-blue/20">
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky mb-3">
                    <span class="w-2 h-2 rounded-full bg-brand-sky animate-ping"></span>
                    <span>Jefe de Área: {{ $usuario->area->nombre ?? 'Sin área asignada' }}</span>
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl tracking-tight leading-tight text-white">
                    ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-brand-sky">{{ $usuario->nombre_completo }}</span>!
                </h1>

                <p class="mt-2 text-sm sm:text-base text-slate-200/90 leading-relaxed">
                    Estas son las <strong class="text-white font-semibold">capacitaciones e inducciones</strong> que has publicado. Solo los empleados de tu área pueden verlas y realizarlas; aquí puedes seguir su progreso.
                </p>

                <div class="mt-5 flex flex-wrap gap-2.5">
                    <a href="{{ route('jefe.capacitaciones.create') }}" id="btn-hero-crear"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-brand-blue hover:bg-brand-light font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-plus"></i>
                        <span>Nueva Capacitación</span>
                    </a>
                    <a href="{{ route('jefe.resultados.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/25 text-white font-heading font-bold text-xs sm:text-sm transition-colors">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Consultar Resultados</span>
                    </a>
                </div>
            </div>

            <div class="lg:w-72 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/15 shrink-0">
                <div class="flex items-center justify-between text-xs text-white/90 mb-2">
                    <span class="font-semibold uppercase tracking-wider text-[11px] text-brand-sky">Mi Área</span>
                    <span class="font-bold text-sm text-white">{{ $estadisticas['empleados'] }} empleados</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center text-xs mt-2">
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-white">{{ $estadisticas['activas'] }}</span>
                        <span class="text-[10px] text-slate-300">Activas</span>
                    </div>
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-amber-300">{{ $estadisticas['inactivas'] }}</span>
                        <span class="text-[10px] text-slate-300">Desactivadas</span>
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

    @unless ($usuario->area_id)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3" role="alert">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            <p><strong>Tu usuario no tiene un área asignada.</strong> Para publicar capacitaciones debe existir un área en tu perfil; solicítalo a administración.</p>
        </div>
    @endunless

    <!-- ========================================================
         LISTADO DE CAPACITACIONES PUBLICADAS
         ======================================================== -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-heading font-bold text-lg sm:text-xl text-brand-dark">Capacitaciones e Inducciones Publicadas</h2>
                <p class="text-xs text-muted">Edita el contenido, consulta a los participantes o desactiva una capacitación (nunca se elimina).</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('jefe.capacitaciones.create') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-blue text-white text-xs font-bold shadow-xs hover:bg-brand-deep transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span>Nueva Capacitación</span>
                </a>
                <span class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold border border-line">
                    Total ({{ $estadisticas['total'] }})
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 sm:gap-6">
            @forelse ($capacitaciones as $cap)
                @php
                    $imagen = $cap->url_imagen;
                    $activa = (bool) $cap->estado;
                @endphp
                <article id="capacitacion-{{ $cap->id }}"
                         class="bg-white rounded-3xl border border-line/75 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col sm:flex-row {{ $activa ? '' : 'opacity-90' }}">

                    <!-- Portada con ola -->
                    <div class="relative sm:w-44 h-40 sm:h-auto shrink-0 overflow-hidden bg-brand-light">
                        <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}"
                             class="w-full h-full object-cover {{ $activa ? '' : 'grayscale' }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/10 to-transparent sm:bg-gradient-to-r"></div>
                        <svg class="absolute -bottom-px left-0 w-full h-4 text-white sm:hidden" viewBox="0 0 600 20" preserveAspectRatio="none">
                            <path d="M0,5 C100,18 200,3 300,14 C400,22 500,6 600,15 L600,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                        <svg class="absolute top-0 -right-px h-full w-4 text-white hidden sm:block" viewBox="0 0 20 300" preserveAspectRatio="none">
                            <path d="M20,0 C5,40 18,80 8,120 C0,160 16,200 6,240 C2,270 14,285 20,300 L20,0 Z" fill="currentColor"/>
                        </svg>

                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md {{ $activa ? 'bg-emerald-600/90 text-white' : 'bg-slate-700/85 text-white' }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-white {{ $activa ? 'animate-pulse' : '' }}"></span>
                            {{ $activa ? 'Activa' : 'Desactivada' }}
                        </span>
                    </div>

                    <!-- Contenido -->
                    <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between gap-4 min-w-0">
                        <div class="space-y-3">
                            <div>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight text-brand-dark line-clamp-2">{{ $cap->titulo }}</h3>
                                <p class="text-[11px] text-muted mt-1">
                                    {{ $cap->modulos_activos_count }} {{ \Illuminate\Support\Str::plural('módulo', $cap->modulos_activos_count) }}
                                    @if ($cap->duracion_estimada) · {{ $cap->duracion_estimada }} h @endif
                                    · Aprobación {{ rtrim(rtrim(number_format((float) $cap->porcentaje_aprobacion, 2), '0'), '.') }}%
                                    @if ($cap->fecha_limite) · Límite {{ $cap->fecha_limite->format('d/m/Y') }} @endif
                                </p>
                            </div>

                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $cap->descripcion }}</p>

                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($cap->areas as $area)
                                    <span class="px-2 py-0.5 rounded-full bg-brand-light text-brand-blue text-[10px] font-bold">{{ $area->nombre }}</span>
                                @endforeach
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700">
                                        {{ $cap->participantes_count }} de {{ $estadisticas['empleados'] }} empleados han participado
                                    </span>
                                    <span class="font-bold text-brand-blue">{{ $cap->progreso_promedio }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand-blue h-2 rounded-full transition-all duration-300" style="width: {{ $cap->progreso_promedio }}%"></div>
                                </div>
                                <p class="text-[10px] text-muted">Progreso promedio del área</p>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                            <a href="{{ route('jefe.capacitaciones.show', $cap) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                Participantes
                            </a>
                            <a href="{{ route('jefe.capacitaciones.edit', $cap) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Editar
                            </a>
                            <a href="{{ route('jefe.evaluaciones.create', ['capacitacion' => $cap->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                + Evaluación
                            </a>

                            <form method="POST" action="{{ route('jefe.capacitaciones.estado', $cap) }}" class="ml-auto m-0"
                                  onsubmit="return confirm('{{ $activa ? '¿Desactivar esta capacitación? Los empleados dejarán de verla, pero se conservará todo el historial.' : '¿Activar nuevamente esta capacitación para los empleados de tu área?' }}');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" id="btn-estado-{{ $cap->id }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-heading font-bold border transition-colors cursor-pointer {{ $activa ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                    {{ $activa ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">Aún no has publicado capacitaciones</h3>
                    <p class="text-xs text-muted max-w-sm mx-auto">Crea tu primera capacitación o inducción: se habilitará automáticamente para los empleados de tu área.</p>
                    <a href="{{ route('jefe.capacitaciones.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors">
                        <i class="fa-solid fa-plus"></i>
                        <span>Crear Capacitación</span>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
