@extends('layouts.empleado')

@section('title', 'Capacitaciones e Inducciones')
@section('page_title', 'Mis Capacitaciones')

@section('content')
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    <!-- ========================================================
         BANNER DE BIENVENIDA CON NOMBRE COMPLETO Y EFECTO DE OLAS
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-brand-blue/20">
        
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky mb-3">
                    <span class="w-2 h-2 rounded-full bg-brand-sky animate-ping"></span>
                    <span>Área: {{ $usuario->area->nombre ?? ($usuario->area_nombre ?? 'Producción Piscícola') }}</span>
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl tracking-tight leading-tight text-white">
                    ¡Bienvenido, <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-100 to-brand-sky">{{ $usuario->nombre_completo ?? 'Colaborador' }}</span>!
                </h1>

                <p class="mt-2 text-sm sm:text-base text-slate-200/90 leading-relaxed font-normal">
                    Tienes asignadas las siguientes <strong class="text-white font-semibold">capacitaciones e inducciones</strong> para fortalecer los estándares de calidad y bioseguridad en <strong class="text-white font-semibold">C.I. Piscícola New York</strong>.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-300">
                    <span class="inline-flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Identificación: <strong class="text-white">{{ $usuario->identificacion ?? '---' }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-brand-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Rol: <strong class="text-white">{{ $usuario->rol ?? 'EMPLEADO' }}</strong>
                    </span>
                </div>
            </div>

            <div class="lg:w-72 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/15 flex flex-col justify-between shrink-0">
                <div class="flex items-center justify-between text-xs text-white/90 mb-2">
                    <span class="font-semibold uppercase tracking-wider text-[11px] text-brand-sky">Resumen Formativo</span>
                    <span class="font-bold text-sm text-white">{{ $estadisticas['activas'] ?? 0 }} Cursos</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-center text-xs mt-2">
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-white">{{ $estadisticas['activas'] ?? 0 }}</span>
                        <span class="text-[10px] text-slate-300">Asignadas</span>
                    </div>
                    <div class="bg-white/10 rounded-xl p-2.5">
                        <span class="block text-xl font-heading font-extrabold text-emerald-300">{{ $estadisticas['completadas'] ?? 0 }}</span>
                        <span class="text-[10px] text-slate-300">Aprobadas</span>
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

    <!-- NOTIFICACIONES FLASH -->
    @if(session('success_modulo'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs" role="status">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success_modulo') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-2xs" role="alert">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ========================================================
         TARJETAS DE RESUMEN KPI
         ======================================================== -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Capacitaciones Activas -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-muted">Capacitaciones</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-2xl font-heading font-extrabold text-brand-dark">{{ $estadisticas['activas'] ?? 0 }}</p>
            <p class="text-[11px] text-muted mt-0.5">En tu plan actual</p>
        </div>

        <!-- 2. En Progreso -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-muted">En Progreso</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-2xl font-heading font-extrabold text-brand-dark">{{ $estadisticas['en_progreso'] ?? 0 }}</p>
            <p class="text-[11px] text-amber-700 font-medium mt-0.5">Módulos en curso</p>
        </div>

        <!-- 3. Finalizadas / Historial -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-muted">Finalizadas</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-2xl font-heading font-extrabold text-brand-dark">{{ $estadisticas['completadas'] ?? 0 }}</p>
            <p class="text-[11px] text-emerald-700 font-medium mt-0.5">Aprobadas con éxito</p>
        </div>

        <!-- 4. Certificados Listos -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-line/70 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-muted">Certificados</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-brand-sky flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 002.996-2.67V5.625A2.625 2.625 0 0014.875 3h-5.75A2.625 2.625 0 006.5 5.625v7.08a3 3 0 002.996 2.67" />
                    </svg>
                </div>
            </div>
            <p class="mt-2 text-2xl font-heading font-extrabold text-brand-dark">{{ $estadisticas['certificados'] ?? 0 }}</p>
            <p class="text-[11px] text-brand-blue font-medium mt-0.5">Listos para descargar</p>
        </div>
    </div>

    <!-- ========================================================
         SECCIÓN PRINCIPAL DE CAPACITACIONES E INDUCCIONES
         ======================================================== -->
    <div class="space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-heading font-bold text-lg sm:text-xl text-brand-dark">
                    Cursos e Inducciones Asignadas
                </h2>
                <p class="text-xs text-muted">
                    Ingresa a cualquier curso para visualizar libremente el contenido. Los módulos se desbloquean en secuencia.
                </p>
            </div>

            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-semibold">
                <span class="px-3 py-1.5 rounded-lg bg-brand-blue text-white shadow-xs">
                    Todas ({{ count($capacitaciones) }})
                </span>
            </div>
        </div>

        <!-- GRID DE TARJETAS DE CAPACITACIONES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
            @forelse($capacitaciones as $cap)
            @php
                $titulo = $cap->titulo ?? ($cap['titulo'] ?? 'Capacitación');
                $descripcion = $cap->descripcion ?? ($cap['descripcion'] ?? '');
                $imagen = is_object($cap) ? $cap->url_imagen : (empty($cap['imagen']) ? asset('images/Img-login.jpg') : asset(ltrim($cap['imagen'], '/')));
                $duracion = $cap->duracion_estimada ? $cap->duracion_estimada . ' horas' : ($cap['duracion'] ?? '4 horas');
                $modulosCount = isset($cap->modulos) ? $cap->modulos->count() : ($cap['modulos_count'] ?? 0);
                $modulosCompletados = $cap->modulos_completados_count ?? ($cap['modulos_completados'] ?? 0);
                $porcentaje = $cap->porcentaje_calculado ?? ($cap['porcentaje'] ?? 0);
                $aprobacion = $cap->porcentaje_aprobacion ?? ($cap['porcentaje_aprobacion'] ?? 80);
                $intentos = $cap->intentos_permitidos ?? ($cap['intentos_permitidos'] ?? 3);
                $estadoUsuario = $cap->estado_usuario ?? ($cap['estado'] ?? 'PENDIENTE');
            @endphp
            <div class="bg-white rounded-3xl border border-line/75 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between group">
                
                <!-- Hacer clic en cualquier parte del cuadro redirige a la visualización del contenido de la capacitación -->
                <a href="{{ route('empleado.capacitaciones.ver', $cap) }}" class="block text-inherit flex-1 group/card" title="Haz clic para ver el contenido y módulos de {{ $titulo }}">
                    <div>
                        <!-- Contenedor de Imagen -->
                        <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-brand-light">
                            <img src="{{ $imagen }}" 
                                 alt="{{ $titulo }}" 
                                 class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-500">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/30 to-transparent"></div>

                            <div class="absolute top-3.5 left-3.5">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border backdrop-blur-md bg-white/20 text-white border-white/30">
                                    <i class="fa-solid fa-award text-brand-sky text-xs"></i> Capacitación Oficial
                                </span>
                            </div>

                            <div class="absolute top-3.5 right-3.5">
                                @if($estadoUsuario === 'COMPLETADA')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-600 text-white backdrop-blur-xs">
                                        <i class="fa-solid fa-circle-check text-xs"></i> Aprobada
                                    </span>
                                @elseif($porcentaje >= 100 || $estadoUsuario === 'MODULOS_COMPLETOS')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-600 text-white backdrop-blur-xs">
                                        <i class="fa-solid fa-list-check text-xs"></i> Módulos Completos
                                    </span>
                                @elseif($estadoUsuario === 'EN_PROGRESO' || $porcentaje > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/90 text-white backdrop-blur-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        En Progreso
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-600/90 text-white backdrop-blur-xs">
                                        <i class="fa-solid fa-sparkles text-xs"></i> Disponible
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-3 left-4 right-4 text-white">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-brand-sky flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-xs"></i> Duración: {{ $duracion }} · {{ $modulosCount }} Módulos
                                </span>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight mt-0.5 line-clamp-1 group-hover/card:text-brand-sky transition-colors">
                                    {{ $titulo }}
                                </h3>
                            </div>

                            <svg class="absolute -bottom-1 left-0 w-full h-4 text-white" viewBox="0 0 600 20" preserveAspectRatio="none">
                                <path d="M0,5 C100,18 200,3 300,14 C400,22 500,6 600,15 L600,20 L0,20 Z" fill="currentColor"/>
                            </svg>
                        </div>

                        <!-- Cuerpo de la tarjeta -->
                        <div class="p-5 sm:p-6 space-y-4">
                            <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $descripcion }}
                            </p>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700">Progreso del curso</span>
                                    <span class="font-bold text-brand-blue">{{ $porcentaje }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand-blue h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-muted">
                                    <span>{{ $modulosCompletados }} de {{ $modulosCount }} módulos completados</span>
                                    <span>Mínimo aprobación: {{ $aprobacion }}%</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-line/60 text-[11px] text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-rotate-right text-brand-blue"></i>
                                    <span>{{ $intentos }} intentos de examen</span>
                                </div>
                                <div class="flex items-center gap-1.5 justify-end">
                                    <i class="fa-solid fa-graduation-cap text-brand-blue"></i>
                                    <span>Certificado Oficial</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Botón de Acción Principal: Iniciar, Continuar o Finalizar la Capacitación -->
                <div class="px-5 pb-5 sm:px-6 sm:pb-6 pt-0">
                    @if($estadoUsuario === 'COMPLETADA')
                        <a href="{{ route('empleado.certificados') }}"
                           class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-emerald-600 hover:bg-emerald-700 text-white hover:-translate-y-0.5">
                            <i class="fa-solid fa-graduation-cap text-base"></i>
                            <span>Capacitación Finalizada · Ver Certificado</span>
                        </a>
                    @elseif($porcentaje >= 100 || $estadoUsuario === 'MODULOS_COMPLETOS')
                        <form method="POST" action="{{ route('empleado.capacitaciones.finalizar', $cap) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-emerald-600 hover:bg-emerald-700 text-white hover:-translate-y-0.5 cursor-pointer">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                <span>Finalizar Capacitación</span>
                            </button>
                        </form>
                    @elseif($estadoUsuario === 'EN_PROGRESO' || $porcentaje > 0)
                        <a href="{{ route('empleado.capacitaciones.ver', $cap) }}"
                           class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-brand-blue hover:bg-brand-deep text-white shadow-brand-blue/20 hover:shadow-brand-blue/30 hover:-translate-y-0.5">
                            <i class="fa-solid fa-circle-play text-base"></i>
                            <span>Continuar Capacitación ({{ $porcentaje }}%)</span>
                        </a>
                    @else
                        <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $cap) }}" class="w-full">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-brand-blue hover:bg-brand-deep text-white shadow-brand-blue/20 hover:shadow-brand-blue/30 hover:-translate-y-0.5 cursor-pointer">
                                <i class="fa-solid fa-play text-base"></i>
                                <span>Iniciar Capacitación</span>
                            </button>
                        </form>
                    @endif
                </div>

            </div>
            @empty
            <div class="col-span-full bg-white rounded-3xl border border-line/80 p-10 text-center space-y-3">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">No hay capacitaciones asignadas actualmente</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">
                    Tan pronto el administrador o jefe de área te asigne un nuevo plan de inducción o capacitación, lo verás disponible en este panel.
                </p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
