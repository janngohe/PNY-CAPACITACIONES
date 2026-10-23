@extends('layouts.empleado')

@section('title', $capacitacion->titulo . ' · Capacitación')
@section('page_title', 'Detalle de Capacitación')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    .no-scrollbar {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
    .pptx-slide-wrapper {
        position: relative;
        transform-origin: 0 0;
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.35);
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
    }
    .pptx-slide-wrapper .pptx-canvas {
        transform-origin: 0 0;
    }
    body.pptx-body-fullscreen {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
    }
    .pptx-interactive-viewer:fullscreen,
    .pptx-interactive-viewer:-webkit-full-screen,
    .pptx-interactive-viewer.is-fullscreen-fallback {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        max-width: none !important;
        max-height: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        background: #020617 !important;
        z-index: 999999 !important;
    }
    .pptx-interactive-viewer:fullscreen .pptx-stage,
    .pptx-interactive-viewer:-webkit-full-screen .pptx-stage,
    .pptx-interactive-viewer.is-fullscreen-fallback .pptx-stage {
        flex: 1 1 0% !important;
        height: 100% !important;
        max-height: none !important;
        min-height: 0 !important;
        padding: 6px !important;
    }
    .pptx-interactive-viewer:fullscreen .pptx-btn-float-fs,
    .pptx-interactive-viewer:-webkit-full-screen .pptx-btn-float-fs,
    .pptx-interactive-viewer.is-fullscreen-fallback .pptx-btn-float-fs {
        display: none !important;
    }
    .pptx-interactive-viewer:fullscreen .pptx-btn-exit-fs,
    .pptx-interactive-viewer:-webkit-full-screen .pptx-btn-exit-fs,
    .pptx-interactive-viewer.is-fullscreen-fallback .pptx-btn-exit-fs {
        display: inline-flex !important;
    }
    .pptx-dots-container {
        -webkit-overflow-scrolling: touch;
    }
</style>
<div class="space-y-6 sm:space-y-8 max-w-7xl mx-auto">

    <!-- ========================================================
         BANNER SUPERIOR DEL CURSO CON OLAS
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-6 sm:p-8 md:p-10 overflow-hidden shadow-lg border border-brand-blue/20">
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-3">
            <a href="{{ route('empleado.capacitaciones') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-sky hover:text-white transition-colors mb-1">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver a Mis Capacitaciones
            </a>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/20 text-brand-sky">
                    Área: {{ $usuario->area->nombre ?? 'Producción Piscícola' }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-400 text-amber-950 shadow-xs">
                    <i class="fa-solid fa-star text-xs"></i> +{{ $capacitacion->puntos ?? 20 }} Puntos
                </span>
                @if($capacitacion->duracion_estimada)
                    <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-white border border-white/20">
                        <i class="fa-regular fa-clock text-brand-sky mr-1"></i> {{ $capacitacion->duracion_estimada }} Horas
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-white border border-white/20">
                    <i class="fa-solid fa-bullseye text-brand-sky mr-1"></i> Aprobación: {{ rtrim(rtrim(number_format((float) $capacitacion->porcentaje_aprobacion, 2), '0'), '.') }}%
                </span>
            </div>

            <h1 class="font-heading font-extrabold text-2xl sm:text-3xl md:text-4xl text-white tracking-tight leading-tight">
                {{ $capacitacion->titulo }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-200/90 max-w-3xl leading-relaxed">
                {{ $capacitacion->descripcion }}
            </p>

            @if($haIniciado)
                <!-- Barra de Progreso General -->
                <div class="pt-2 max-w-xl space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold text-white">
                        <span>Progreso del Curso ({{ $modulosCompletados }}/{{ $totalModulos }} Módulos)</span>
                        <span class="text-brand-sky">{{ $porcentajeProgreso }}%</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-cyan-300 to-brand-sky h-full rounded-full transition-all duration-500" style="width: {{ $porcentajeProgreso }}%"></div>
                    </div>
                </div>

                @if($todosModulosCompletados)
                    <div class="pt-2">
                        <form method="POST" action="{{ route('empleado.capacitaciones.finalizar', $capacitacion) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-heading font-extrabold text-xs shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                                <span>Finalizar Capacitación y Obtener Certificado</span>
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <!-- Modo Vista Previa / Botón Iniciar Capacitación -->
                <div class="mt-4 pt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/10 p-4 sm:p-5 rounded-2xl backdrop-blur-md border border-white/20">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-200 text-xs font-bold border border-amber-300/30">
                            <i class="fa-solid fa-eye text-xs"></i> Modo Visualización Previa
                        </div>
                        <p class="text-xs sm:text-sm text-slate-100 font-medium">
                            Estás explorando el temario descriptivo. Para reproducir videos, acceder a documentos y registrar tu avance, debes iniciar la capacitación.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $capacitacion) }}" class="shrink-0">
                        @csrf
                        <button type="submit"
                                style="background-color: #0056b3; color: #ffffff;"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-play text-sm"></i>
                            <span>Iniciar Capacitación</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="relative block w-full h-5 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
            </svg>
        </div>
    </div>

    <!-- MENSAJES FLASH DE NOTIFICACIÓN -->
    @if(session('success_modulo'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs" role="status">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success_modulo') }}</span>
        </div>
    @endif

    @if(session('success_evaluacion'))
        <div class="p-5 rounded-3xl bg-emerald-500 text-white font-semibold text-xs sm:text-sm flex items-center gap-3 shadow-lg" role="status">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <p class="font-extrabold text-base">¡Evaluación Aprobada!</p>
                <p class="text-emerald-100 text-xs mt-0.5">{{ session('success_evaluacion') }}</p>
            </div>
        </div>
    @endif

    @if(session('error_evaluacion') || session('error'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm font-semibold flex items-center gap-2.5 shadow-xs" role="alert">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            <span>{{ session('error_evaluacion') ?? session('error') }}</span>
        </div>
    @endif

    @php
        // Identificar el módulo activo inicial para el recorrido asistido:
        $primerModuloEnCurso = $capacitacion->modulos->first(function($m) {
            return !$m->bloqueado_usuario && !$m->completado_usuario;
        });

        $idModuloInicial = session('modulo_activo_id');
        if (!$idModuloInicial) {
            if ($primerModuloEnCurso) {
                $idModuloInicial = $primerModuloEnCurso->id;
            } elseif ($capacitacion->modulos->isNotEmpty()) {
                $idModuloInicial = $capacitacion->modulos->first()->id;
            } else {
                $idModuloInicial = null;
            }
        }
    @endphp

    <!-- ========================================================
         SECCIÓN 1: MÓDULOS DE ESTUDIO (RECORRIDO ACOMPAÑADO)
         ======================================================== -->
    <div id="contenedor-recorrido-modulos" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-line/70 pb-3">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-blue animate-pulse"></span>
                    <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark">Recorrido de Aprendizaje Asistido</h2>
                </div>
                <p class="text-xs text-muted mt-0.5">
                    @if($haIniciado)
                        Avanza paso a paso. Completa cada módulo para habilitar el siguiente de manera continua y acompañada.
                    @else
                        Visualización descriptiva del temario. Haz clic en "Iniciar Capacitación" para desbloquear videos, recursos y evaluaciones.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($totalModulos > 1)
                    <button type="button"
                            id="btn-toggle-vista-modulos"
                            onclick="alternarModoVista()"
                            class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-200 transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i id="icono-toggle-vista" class="fa-solid fa-list-check text-slate-500 text-[11px]"></i>
                        <span id="texto-toggle-vista">Ver todos los módulos</span>
                    </button>
                @endif
                <span class="px-3 py-1.5 rounded-full bg-brand-light text-brand-blue text-xs font-bold shrink-0">
                    {{ $totalModulos }} {{ $totalModulos === 1 ? 'Módulo' : 'Módulos' }}
                </span>
            </div>
        </div>

        @if($totalModulos > 0)
            <!-- Stepper / Barra de Ruta Acompañada -->
            <div class="bg-white rounded-3xl border border-line/80 p-4 sm:p-5 shadow-xs space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-line/60 pb-3">
                    <div class="flex items-center justify-between sm:justify-start gap-2.5 w-full sm:w-auto">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                <i class="fa-solid fa-route text-sm"></i>
                            </div>
                            <div>
                                <span class="font-heading font-extrabold text-xs sm:text-sm text-brand-dark block">Ruta de Módulos</span>
                                <span class="text-[11px] text-muted hidden sm:inline">Selecciona un paso para visualizar su material interactivo</span>
                            </div>
                        </div>
                        <!-- Controles táctiles móviles < > -->
                        <div class="flex items-center gap-1 sm:hidden">
                            <button type="button" onclick="scrollModulosStepperEmpleado(-1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs" title="Módulo anterior">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button type="button" onclick="scrollModulosStepperEmpleado(1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs" title="Siguiente módulo">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto pt-1 sm:pt-0">
                        <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                            <span class="text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider shrink-0">Progreso</span>
                            <div class="flex-1 sm:w-32 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $porcentajeProgreso }}%"></div>
                            </div>
                        </div>
                        <span class="text-xs font-extrabold text-slate-800 font-mono px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200 shrink-0">
                            {{ $modulosCompletados }}/{{ $totalModulos }} ({{ $porcentajeProgreso }}%)
                        </span>
                    </div>
                </div>

                <!-- Chips del Stepper sin barra de desplazamiento visible -->
                <div class="no-scrollbar flex items-stretch gap-2 sm:gap-2.5 overflow-x-auto py-1.5 px-0.5 scroll-smooth w-full snap-x snap-mandatory"
                     style="scrollbar-width: none; -ms-overflow-style: none;"
                     id="stepper-modulos-scroll-container">
                    @foreach($capacitacion->modulos as $sIdx => $sMod)
                        @php
                            $sBloqueado = $haIniciado ? (bool) $sMod->bloqueado_usuario : false;
                            $sCompletado = $haIniciado ? (bool) $sMod->completado_usuario : false;
                            $sEsActivo = ($sMod->id == $idModuloInicial);

                            // Limpiar redundancia de "Módulo 1: " en el título para que quede conciso y elegante
                            $tituloLimpio = preg_replace('/^(Paso|M[óo]dulo)\s*\d+[\s:\.\-–—]*/iu', '', trim($sMod->titulo));
                            if (empty($tituloLimpio)) {
                                $tituloLimpio = $sMod->titulo;
                            }
                        @endphp
                        <button type="button"
                                onclick="{{ (!$sBloqueado || !$haIniciado) ? "seleccionarModuloRecorrido({$sMod->id})" : "void(0)" }}"
                                id="stepper-tab-{{ $sMod->id }}"
                                data-modulo-id="{{ $sMod->id }}"
                                data-es-completado="{{ $sCompletado ? '1' : '0' }}"
                                data-es-bloqueado="{{ $sBloqueado ? '1' : '0' }}"
                                class="stepper-tab snap-start shrink-0 w-[78vw] max-w-[270px] sm:w-auto sm:max-w-none sm:shrink sm:flex-1 flex items-center gap-2 sm:gap-2.5 p-2.5 sm:p-3 rounded-2xl text-xs font-heading border transition-all duration-200 {{ $sEsActivo ? 'bg-gradient-to-r from-brand-blue to-[#004a99] text-white border-brand-blue shadow-md ring-2 ring-brand-blue/30 scale-[1.01]' : ($sCompletado ? 'bg-white hover:bg-emerald-50/60 text-slate-800 border-emerald-200 hover:border-emerald-400 shadow-2xs cursor-pointer' : ($sBloqueado ? 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-75' : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200 hover:border-brand-blue cursor-pointer')) }}"
                                {{ $sBloqueado && $haIniciado ? 'disabled' : '' }}>
                            
                            <div class="stepper-badge-icon w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $sEsActivo ? 'bg-white text-brand-blue font-black text-xs shadow-xs' : ($sCompletado ? 'bg-emerald-500 text-white font-bold text-xs shadow-xs' : ($sBloqueado ? 'bg-slate-200 text-slate-500 font-bold text-xs' : 'bg-brand-light text-brand-blue font-bold text-xs')) }}">
                                @if($sCompletado)
                                    <i class="fa-solid fa-check"></i>
                                @elseif($sBloqueado && $haIniciado)
                                    <i class="fa-solid fa-lock text-[11px]"></i>
                                @else
                                    <span>{{ $sIdx + 1 }}</span>
                                @endif
                            </div>

                            <div class="flex flex-col text-left min-w-0 flex-1 leading-tight">
                                <div class="flex items-center gap-1.5">
                                    <span class="stepper-label-text text-[10px] font-extrabold uppercase tracking-wider {{ $sEsActivo ? 'text-white/80' : 'text-slate-400' }}">
                                        Módulo {{ $sIdx + 1 }}
                                    </span>
                                    <span class="stepper-badge-tag text-[9px] px-1.5 py-0.2 rounded-md font-bold {{ $sEsActivo ? 'bg-white/20 text-white' : ($sCompletado ? 'bg-emerald-100 text-emerald-800' : ($sBloqueado ? 'bg-slate-200 text-slate-600 font-medium' : 'bg-cyan-100 text-cyan-800')) }}">
                                        {{ $sEsActivo ? 'En curso' : ($sCompletado ? 'Listo' : ($sBloqueado ? 'Bloqueado' : 'Disponible')) }}
                                    </span>
                                </div>
                                <span class="stepper-title-text truncate text-xs {{ $sEsActivo ? 'font-bold text-white' : 'font-semibold text-slate-800' }}" title="{{ $sMod->titulo }}">
                                    {{ $tituloLimpio }}
                                </span>
                            </div>
                        </button>
                    @endforeach

                    @if($evaluaciones->isNotEmpty())
                        <a href="#seccion-evaluaciones"
                           id="stepper-tab-evaluacion"
                           class="stepper-tab snap-start shrink-0 w-[78vw] max-w-[270px] sm:w-auto sm:max-w-none sm:shrink sm:flex-1 flex items-center gap-2 sm:gap-2.5 p-2.5 sm:p-3 rounded-2xl text-xs font-heading border transition-all duration-200 {{ $todosModulosCompletados ? 'bg-gradient-to-r from-emerald-600 to-teal-700 text-white border-emerald-600 shadow-md ring-2 ring-emerald-500/30' : 'bg-slate-50 text-slate-400 border-slate-200 opacity-75' }}">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $todosModulosCompletados ? 'bg-white text-emerald-700 font-black text-xs shadow-xs' : 'bg-slate-200 text-slate-500 font-bold text-xs' }}">
                                <i class="fa-solid fa-graduation-cap {{ $todosModulosCompletados ? 'text-sm' : 'text-xs' }}"></i>
                            </div>
                            <div class="flex flex-col text-left min-w-0 flex-1 leading-tight">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $todosModulosCompletados ? 'text-white/80' : 'text-slate-400' }}">Examen</span>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded-md font-bold {{ $todosModulosCompletados ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-500 font-medium' }}">
                                        {{ $todosModulosCompletados ? '¡Listo!' : 'Al terminar' }}
                                    </span>
                                </div>
                                <span class="truncate text-xs font-bold {{ $todosModulosCompletados ? 'text-white' : 'text-slate-500' }}">
                                    Evaluación Final
                                </span>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div class="space-y-5">
            @forelse($capacitacion->modulos as $idx => $mod)
                @php
                    $esBloqueado = $haIniciado ? (bool) $mod->bloqueado_usuario : false;
                    $esCompletado = $haIniciado ? (bool) $mod->completado_usuario : false;
                    $esUltimoModulo = ($idx + 1 === $totalModulos) || $loop->last;

                    $contenidosVideo = $mod->contenidos->filter(function($c) {
                        if ($c->tipo !== 'VIDEO') return false;
                        if ($c->ruta_archivo) return true;
                        if (filter_var(trim($c->contenido), FILTER_VALIDATE_URL)) {
                            return (bool) preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', trim($c->contenido));
                        }
                        return false;
                    });
                    $totalVideosModulo = $contenidosVideo->count();
                @endphp

                <div id="modulo-{{ $mod->id }}"
                     data-modulo-id="{{ $mod->id }}"
                     data-idx="{{ $idx }}"
                     class="modulo-recorrido-item rounded-3xl border transition-all overflow-hidden {{ $mod->id == $idModuloInicial ? '' : 'hidden' }} {{ $esBloqueado ? 'bg-slate-100/70 border-slate-200 opacity-85' : ($esCompletado ? 'bg-emerald-50/40 border-emerald-200 shadow-2xs' : 'bg-white border-line shadow-xs') }}">
                    
                    @if($esCompletado)
                        <div class="px-5 py-3 bg-emerald-50/90 border-b border-emerald-200 text-emerald-800 text-xs flex flex-wrap items-center justify-between gap-2">
                            <span class="flex items-center gap-2 font-medium">
                                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                                Estás repasando el <strong>Módulo {{ $idx + 1 }} (Ya completado)</strong>.
                            </span>
                            @if($primerModuloEnCurso && $primerModuloEnCurso->id !== $mod->id)
                                <button type="button"
                                        onclick="seleccionarModuloRecorrido({{ $primerModuloEnCurso->id }})"
                                        class="font-bold underline text-emerald-900 hover:text-emerald-700 cursor-pointer">
                                    Ir al Módulo en Curso (Módulo {{ ($capacitacion->modulos->search(fn($m) => $m->id == $primerModuloEnCurso->id) + 1) }}) →
                                </button>
                            @endif
                        </div>
                    @endif

                    <!-- Encabezado del Módulo -->
                    <div class="p-5 flex items-start justify-between gap-4 border-b {{ $esBloqueado ? 'border-slate-200 bg-slate-100/50' : 'border-line/60 bg-gradient-to-r from-slate-50 via-white to-white' }}">
                        <div class="flex items-start gap-3.5">
                            <div class="w-9 h-9 rounded-2xl font-heading font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs {{ $esBloqueado ? 'bg-slate-300 text-slate-600' : ($esCompletado ? 'bg-emerald-600 text-white' : 'bg-brand-blue text-white') }}">
                                {{ $idx + 1 }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-heading font-bold text-base sm:text-lg text-brand-dark">
                                        {{ $mod->titulo }}
                                    </h3>
                                    @if(!$haIniciado)
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-300 flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-slate-500 text-[10px]"></i> Visualización
                                        </span>
                                    @elseif($esCompletado)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold border border-emerald-300">
                                            {{ $esUltimoModulo ? '✓ Finalizado' : '✓ Completado' }}
                                        </span>
                                    @elseif($esBloqueado)
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[11px] font-bold border border-slate-300 flex items-center gap-1">
                                            <i class="fa-solid fa-lock text-slate-500 text-[10px]"></i> Bloqueado
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-cyan-100 text-cyan-800 text-[11px] font-bold border border-cyan-300 flex items-center gap-1">
                                            <i class="fa-solid fa-lock-open text-cyan-600 text-[10px]"></i> Disponible
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    {{ $mod->descripcion }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del Módulo / Estado de Bloqueo -->
                    <div class="p-5 sm:p-6 space-y-4">
                        @if($esBloqueado)
                            <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3 shadow-2xs">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <div>
                                    <p class="font-bold">Módulo Bloqueado · Paso previo requerido</p>
                                    <p class="text-amber-800 text-xs mt-0.5 leading-relaxed">
                                        Debes completar la lectura y marcar como completado el <strong>Módulo {{ $idx }}</strong> para desbloquear el material de estudio de esta sección.
                                    </p>
                                </div>
                            </div>
                        @else
                            <!-- Lista de contenidos de estudio -->
                            <div class="space-y-3">
                                <span class="block text-xs font-extrabold text-brand-dark uppercase tracking-wider">
                                    Material de estudio ({{ $mod->contenidos->count() }})
                                </span>

                                <div class="grid grid-cols-1 gap-3">
                                    @forelse($mod->contenidos as $cont)
                                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-line/75 space-y-3 text-xs overflow-hidden">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-bold text-brand-dark text-sm flex items-center gap-2 min-w-0">
                                                    @if($cont->tipo === 'VIDEO') 
                                                        <i class="fa-solid fa-video text-red-500 shrink-0"></i> 
                                                    @elseif($cont->tipo === 'PDF') 
                                                        <i class="fa-solid fa-file-pdf text-red-600 shrink-0"></i> 
                                                    @elseif($cont->tipo === 'IMAGEN') 
                                                        <i class="fa-solid fa-image text-emerald-600 shrink-0"></i> 
                                                    @elseif($cont->tipo === 'PPTX')
                                                        <i class="fa-solid fa-file-powerpoint text-orange-500 shrink-0"></i>
                                                    @elseif($cont->tipo === 'ENLACE') 
                                                        <i class="fa-solid fa-link text-brand-blue shrink-0"></i> 
                                                    @else 
                                                        <i class="fa-solid fa-file-lines text-blue-600 shrink-0"></i> 
                                                    @endif
                                                    <span class="truncate">{{ $cont->titulo }}</span>
                                                </span>
                                                <span class="text-[10px] px-2.5 py-0.5 rounded-md bg-slate-200 text-slate-700 font-mono font-bold shrink-0">
                                                    {{ $cont->tipo }}
                                                </span>
                                            </div>

                                            @if($cont->tipo === 'VIDEO')
                                                @if(!$haIniciado)
                                                    <div class="p-4 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs my-1">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                                                <i class="fa-solid fa-video-slash text-sm"></i>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="font-bold text-slate-800 text-xs">Video Protegido</p>
                                                                <p class="text-[11px] text-slate-500">Debes iniciar la capacitación para reproducir este video.</p>
                                                            </div>
                                                        </div>
                                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shrink-0 self-start sm:self-auto">
                                                            <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i> Bloqueado
                                                        </span>
                                                    </div>
                                                @elseif($cont->ruta_archivo)
                                                    <div class="relative w-full aspect-video rounded-xl overflow-hidden shadow-xs border border-line/80 my-2 bg-black flex items-center justify-center">
                                                        <video controls controlsList="nodownload" preload="metadata"
                                                               class="w-full h-full object-contain pny-video-local"
                                                               data-modulo-id="{{ $mod->id }}"
                                                               data-contenido-id="{{ $cont->id }}"
                                                               data-capacitacion-id="{{ $capacitacion->id }}">
                                                            <source src="{{ asset($cont->ruta_archivo) }}" type="video/mp4">
                                                            Tu navegador no soporta la reproducción de video HTML5.
                                                        </video>
                                                    </div>
                                                    <!-- Barra de seguimiento del requisito del video -->
                                                    <div id="progreso-video-{{ $cont->id }}"
                                                         class="p-3 rounded-xl bg-slate-100 border border-slate-200/80 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 transition-all"
                                                         data-modulo-id="{{ $mod->id }}"
                                                         data-contenido-id="{{ $cont->id }}">
                                                        <div class="flex items-center gap-2 min-w-0">
                                                            <i id="icono-progreso-{{ $cont->id }}" class="fa-solid fa-circle-play text-red-500 shrink-0 text-sm"></i>
                                                            <div class="min-w-0">
                                                                <span class="font-bold text-slate-800 block sm:inline">Requisito de Visualización:</span>
                                                                <span id="texto-progreso-{{ $cont->id }}" class="text-slate-600 font-medium">0% visto (Mínimo 50% requerido para desbloquear avance)</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2 shrink-0">
                                                            <div class="w-28 sm:w-36 bg-slate-200 rounded-full h-2 overflow-hidden">
                                                                <div id="barra-progreso-{{ $cont->id }}" class="bg-brand-blue h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                                                            </div>
                                                            <span id="badge-pct-{{ $cont->id }}" class="text-[11px] font-mono font-bold text-brand-dark px-1.5 py-0.5 rounded bg-white border border-slate-200">0%</span>
                                                        </div>
                                                    </div>
                                                @elseif(filter_var(trim($cont->contenido), FILTER_VALIDATE_URL))
                                                    @php
                                                        $videoUrl = trim($cont->contenido);
                                                        $youtubeId = null;
                                                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $videoUrl, $m)) {
                                                            $youtubeId = $m[1];
                                                        }
                                                    @endphp
                                                    @if($youtubeId)
                                                        <div class="relative w-full aspect-video rounded-xl overflow-hidden shadow-xs border border-line/80 my-2 bg-black">
                                                            <iframe id="yt-player-{{ $cont->id }}"
                                                                    class="w-full h-full pny-video-youtube"
                                                                    src="https://www.youtube.com/embed/{{ $youtubeId }}?enablejsapi=1"
                                                                    title="{{ $cont->titulo }}"
                                                                    frameborder="0"
                                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                    allowfullscreen
                                                                    data-modulo-id="{{ $mod->id }}"
                                                                    data-contenido-id="{{ $cont->id }}"
                                                                    data-capacitacion-id="{{ $capacitacion->id }}"></iframe>
                                                        </div>
                                                        <!-- Barra de seguimiento del requisito del video de YouTube -->
                                                        <div id="progreso-video-{{ $cont->id }}"
                                                             class="p-3 rounded-xl bg-slate-100 border border-slate-200/80 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 transition-all"
                                                             data-modulo-id="{{ $mod->id }}"
                                                             data-contenido-id="{{ $cont->id }}">
                                                            <div class="flex items-center gap-2 min-w-0">
                                                                <i id="icono-progreso-{{ $cont->id }}" class="fa-brands fa-youtube text-red-600 shrink-0 text-sm"></i>
                                                                <div class="min-w-0">
                                                                    <span class="font-bold text-slate-800 block sm:inline">Requisito de Visualización:</span>
                                                                    <span id="texto-progreso-{{ $cont->id }}" class="text-slate-600 font-medium">0% visto (Mínimo 50% requerido para desbloquear avance)</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <div class="w-28 sm:w-36 bg-slate-200 rounded-full h-2 overflow-hidden">
                                                                    <div id="barra-progreso-{{ $cont->id }}" class="bg-red-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                                                                </div>
                                                                <span id="badge-pct-{{ $cont->id }}" class="text-[11px] font-mono font-bold text-brand-dark px-1.5 py-0.5 rounded bg-white border border-slate-200">0%</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-slate-700 text-xs sm:text-sm bg-white p-3.5 rounded-xl border border-line/60 break-all leading-relaxed">
                                                            <a href="{{ $videoUrl }}" target="_blank" rel="noopener" class="text-brand-blue hover:underline inline-flex items-center gap-1.5 font-semibold">
                                                                <i class="fa-solid fa-circle-play text-red-500"></i> {{ $videoUrl }} ↗
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif

                                            @if($cont->tipo === 'PPTX')
                                                @if(!$haIniciado)
                                                    <div class="p-4 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs my-1">
                                                        <div class="flex items-center gap-3 min-w-0">
                                                            <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                                                <i class="fa-solid fa-file-powerpoint text-sm"></i>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="font-bold text-slate-800 text-xs">Presentación PowerPoint Protegida</p>
                                                                <p class="text-[11px] text-slate-500">Debes unirte / iniciar la capacitación para visualizar las diapositivas de este tema.</p>
                                                            </div>
                                                        </div>
                                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shrink-0 self-start sm:self-auto">
                                                            <i class="fa-solid fa-lock text-slate-400 text-[10px]"></i> Bloqueada
                                                        </span>
                                                    </div>
                                                @elseif($cont->ruta_archivo)
                                                    @php
                                                        $rutaRelativaPptx = preg_replace('#^storage/#', '', $cont->ruta_archivo);
                                                        $pptxData = \App\Services\PptxRenderer::render($rutaRelativaPptx);
                                                        $slidesPptx = $pptxData['slides'] ?? [];
                                                        $totalSlidesPptx = count($slidesPptx);
                                                    @endphp

                                                    @if($totalSlidesPptx > 0)
                                                        <div class="pptx-interactive-viewer bg-slate-900 rounded-2xl border border-slate-800 text-white overflow-hidden shadow-lg my-2"
                                                             id="pptx-viewer-{{ $cont->id }}"
                                                             data-viewer-id="{{ $cont->id }}"
                                                             data-total-slides="{{ $totalSlidesPptx }}"
                                                             data-current-slide="0">

                                                            <!-- Barra Superior del Visor -->
                                                            <div class="flex flex-wrap items-center justify-between gap-2.5 px-4 py-3 bg-slate-950 border-b border-slate-800 text-xs">
                                                                <div class="flex items-center gap-2.5 min-w-0">
                                                                    <span class="w-7 h-7 rounded-lg bg-orange-500/20 text-orange-400 border border-orange-500/30 flex items-center justify-center text-xs shrink-0">
                                                                        <i class="fa-solid fa-file-powerpoint"></i>
                                                                    </span>
                                                                    <div class="min-w-0">
                                                                        <span class="font-bold text-white text-xs block truncate">{{ $cont->titulo }}</span>
                                                                        <span class="text-[10px] text-slate-400">Presentación interactiva de diapositivas</span>
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2 shrink-0">
                                                                    <!-- Contador de Diapositiva -->
                                                                    <div class="px-2.5 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700/80 font-mono text-[11px] text-slate-300">
                                                                        <span class="hidden sm:inline">Diapositiva </span><span id="pptx-curr-{{ $cont->id }}" class="font-black text-cyan-400">1</span> / <span class="font-bold text-white">{{ $totalSlidesPptx }}</span>
                                                                    </div>

                                                                    <!-- Botón Agrandar / Pantalla Completa (Header) -->
                                                                    <button type="button"
                                                                            onclick="togglePantallaCompletaPptx('pptx-viewer-{{ $cont->id }}')"
                                                                            class="pptx-btn-toggle-fs inline-flex items-center justify-center gap-1.5 px-3 py-1.5 min-h-[36px] rounded-xl bg-cyan-500/20 hover:bg-cyan-600 active:scale-95 text-cyan-300 hover:text-white border border-cyan-500/40 hover:border-cyan-400 transition-all cursor-pointer font-bold text-xs shadow-sm"
                                                                            title="Agrandar diapositiva / Pantalla completa">
                                                                        <i class="fa-solid fa-expand text-sm sm:text-xs"></i>
                                                                        <span class="text-[11px] font-bold">Agrandar</span>
                                                                    </button>

                                                                    <!-- Botón Descargar PPTX -->
                                                                    <a href="{{ asset($cont->ruta_archivo) }}"
                                                                       download
                                                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-800 hover:bg-orange-600 active:scale-95 text-slate-300 hover:text-white border border-slate-700 transition-all shadow-sm"
                                                                       title="Descargar archivo PowerPoint (.pptx)">
                                                                        <i class="fa-solid fa-download text-xs"></i>
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <!-- Escenario de Diapositivas -->
                                                            <div class="pptx-stage relative w-full overflow-hidden flex items-center justify-center p-3 sm:p-5 bg-slate-950 select-none min-h-[360px] sm:min-h-[460px]"
                                                                 tabindex="0">

                                                                <!-- Botón Flotante para Agrandar en Móvil y Pantalla Táctil -->
                                                                <button type="button"
                                                                        onclick="togglePantallaCompletaPptx('pptx-viewer-{{ $cont->id }}')"
                                                                        class="pptx-btn-float-fs absolute top-3 right-3 z-30 inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-slate-900/90 hover:bg-cyan-600 active:scale-95 text-white border border-cyan-500/40 backdrop-blur-md shadow-xl transition-all cursor-pointer text-xs font-bold"
                                                                        title="Agrandar diapositiva a pantalla completa">
                                                                    <i class="fa-solid fa-expand text-cyan-400 text-sm"></i>
                                                                    <span class="text-[11px]">Agrandar</span>
                                                                </button>

                                                                <!-- Botón Flotante para Salir de Pantalla Completa -->
                                                                <button type="button"
                                                                        onclick="togglePantallaCompletaPptx('pptx-viewer-{{ $cont->id }}')"
                                                                        class="pptx-btn-exit-fs hidden absolute top-3 right-3 z-40 items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-600/90 hover:bg-red-700 active:scale-95 text-white border border-red-400/50 backdrop-blur-md shadow-2xl transition-all cursor-pointer text-xs font-bold"
                                                                        title="Reducir / Salir de pantalla completa">
                                                                    <i class="fa-solid fa-compress text-sm"></i>
                                                                    <span class="text-[11px]">Reducir</span>
                                                                </button>

                                                                @foreach($slidesPptx as $sIdx => $slideHtml)
                                                                    <div class="pptx-slide-pane {{ $sIdx === 0 ? 'active-pptx-slide flex' : 'hidden' }} w-full h-full items-center justify-center transition-opacity duration-300"
                                                                         data-slide-index="{{ $sIdx }}">
                                                                        <div class="pptx-slide-wrapper shadow-2xl rounded-xl overflow-hidden bg-white"
                                                                             data-width="{{ $pptxData['width'] ?? 1920 }}"
                                                                             data-height="{{ $pptxData['height'] ?? 1080 }}">
                                                                            {!! $slideHtml !!}
                                                                        </div>
                                                                    </div>
                                                                @endforeach

                                                                <!-- Botón Flotante Anterior -->
                                                                <button type="button"
                                                                        onclick="cambiarSlidePptx('pptx-viewer-{{ $cont->id }}', -1)"
                                                                        class="pptx-btn-nav-prev absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-cyan-600 text-white border border-white/20 backdrop-blur-xs flex items-center justify-center shadow-lg transition-all cursor-pointer opacity-80 hover:opacity-100 disabled:opacity-20 disabled:pointer-events-none z-20"
                                                                        title="Diapositiva anterior">
                                                                    <i class="fa-solid fa-chevron-left text-sm"></i>
                                                                </button>

                                                                <!-- Botón Flotante Siguiente -->
                                                                <button type="button"
                                                                        onclick="cambiarSlidePptx('pptx-viewer-{{ $cont->id }}', 1)"
                                                                        class="pptx-btn-nav-next absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/80 hover:bg-cyan-600 text-white border border-white/20 backdrop-blur-xs flex items-center justify-center shadow-lg transition-all cursor-pointer opacity-80 hover:opacity-100 disabled:opacity-20 disabled:pointer-events-none z-20"
                                                                        title="Siguiente diapositiva">
                                                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                                                </button>
                                                            </div>

                                                            <!-- Barra Inferior de Navegación -->
                                                            <div class="p-3 bg-slate-900 border-t border-slate-800 space-y-2">
                                                                <div class="flex items-center justify-between gap-2 sm:gap-3">
                                                                    <button type="button"
                                                                            onclick="cambiarSlidePptx('pptx-viewer-{{ $cont->id }}', -1)"
                                                                            class="pptx-btn-prev inline-flex items-center gap-1.5 px-3.5 py-2 min-h-[38px] rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-white text-xs font-bold border border-slate-700 transition-all cursor-pointer disabled:opacity-30 disabled:pointer-events-none shrink-0">
                                                                        <i class="fa-solid fa-arrow-left text-[11px]"></i>
                                                                        <span class="hidden sm:inline">Anterior</span>
                                                                    </button>

                                                                    <!-- Selector de Diapositivas (Dots) -->
                                                                    <div class="flex items-center gap-1.5 overflow-x-auto max-w-[50%] sm:max-w-[60%] py-2 no-scrollbar pptx-dots-container">
                                                                        @for($d = 0; $d < $totalSlidesPptx; $d++)
                                                                            <button type="button"
                                                                                    onclick="irASlidePptx('pptx-viewer-{{ $cont->id }}', {{ $d }})"
                                                                                    class="pptx-dot rounded-full transition-all cursor-pointer shrink-0 {{ $d === 0 ? 'bg-cyan-400 w-5 h-2.5' : 'bg-slate-700 hover:bg-slate-500 w-2.5 h-2.5' }}"
                                                                                    title="Ir a diapositiva {{ $d + 1 }}"></button>
                                                                        @endfor
                                                                    </div>

                                                                    <button type="button"
                                                                            onclick="cambiarSlidePptx('pptx-viewer-{{ $cont->id }}', 1)"
                                                                            class="pptx-btn-next inline-flex items-center gap-1.5 px-3.5 py-2 min-h-[38px] rounded-xl bg-cyan-700 hover:bg-cyan-600 active:scale-95 text-white text-xs font-bold transition-all shadow-md cursor-pointer disabled:opacity-30 disabled:pointer-events-none shrink-0">
                                                                        <span class="hidden sm:inline">Siguiente</span>
                                                                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                                                                    </button>
                                                                </div>

                                                                <!-- Barra de Progreso de lectura -->
                                                                <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                                                    <div id="pptx-bar-{{ $cont->id }}"
                                                                         class="h-full bg-gradient-to-r from-cyan-500 to-brand-blue rounded-full transition-all duration-300"
                                                                         style="width: {{ round((1 / $totalSlidesPptx) * 100) }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="p-4 rounded-xl bg-orange-50 border border-orange-200 space-y-3 my-2 text-xs">
                                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                                <span class="font-bold text-orange-950 flex items-center gap-2">
                                                                    <i class="fa-solid fa-file-powerpoint text-orange-600 text-base"></i>
                                                                    <span>Presentación PowerPoint adjunta: {{ basename($cont->ruta_archivo) }}</span>
                                                                </span>
                                                                <a href="{{ asset($cont->ruta_archivo) }}" download
                                                                   class="px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-xs self-start sm:self-auto">
                                                                    <i class="fa-solid fa-download"></i> Descargar Presentación
                                                                </a>
                                                            </div>
                                                            <div class="rounded-xl overflow-hidden border border-orange-200 bg-white">
                                                                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(asset($cont->ruta_archivo)) }}"
                                                                        class="w-full h-96 border-0"
                                                                        title="Visor PowerPoint"></iframe>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif

                                            @if($cont->contenido && !filter_var(trim($cont->contenido), FILTER_VALIDATE_URL))
                                                <div class="text-slate-700 text-xs sm:text-sm bg-white p-3.5 rounded-xl border border-line/60 whitespace-pre-line leading-relaxed break-words overflow-hidden">
                                                    {{ $cont->contenido }}
                                                </div>
                                            @elseif($cont->tipo === 'ENLACE' && filter_var(trim($cont->contenido), FILTER_VALIDATE_URL))
                                                @if(!$haIniciado)
                                                    <div class="p-3.5 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-600 flex items-center justify-between gap-3 text-xs">
                                                        <div class="flex items-center gap-2.5 min-w-0">
                                                            <i class="fa-solid fa-link-slash text-slate-400"></i>
                                                            <span class="text-slate-600 text-xs truncate">Enlace externo protegido. Inicia la capacitación para acceder.</span>
                                                        </div>
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 bg-white px-2 py-1 rounded-md border border-slate-200 shrink-0">
                                                            <i class="fa-solid fa-lock text-[10px]"></i> Bloqueado
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="text-slate-700 text-xs sm:text-sm bg-white p-3.5 rounded-xl border border-line/60 break-all leading-relaxed">
                                                        <a href="{{ trim($cont->contenido) }}" target="_blank" rel="noopener" class="text-brand-blue hover:underline inline-flex items-center gap-1.5 font-semibold">
                                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> {{ trim($cont->contenido) }} ↗
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif

                                            @if($cont->ruta_archivo && !in_array($cont->tipo, ['VIDEO', 'PPTX'], true))
                                                <div class="pt-1">
                                                    @if(!$haIniciado)
                                                        <div class="inline-flex max-w-full items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-500 text-xs font-medium">
                                                            <i class="fa-solid fa-file-shield text-slate-400 text-sm shrink-0"></i>
                                                            <span class="truncate">Archivo adjunto protegido (Inicia la capacitación para acceder)</span>
                                                            <i class="fa-solid fa-lock text-[10px] text-slate-400 ml-1 shrink-0"></i>
                                                        </div>
                                                    @else
                                                        <a href="{{ asset($cont->ruta_archivo) }}" target="_blank" rel="noopener"
                                                           class="inline-flex max-w-full items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-light text-brand-blue hover:bg-brand-blue hover:text-white font-bold text-xs transition-colors shadow-2xs group">
                                                            <i class="fa-solid fa-file-pdf text-sm shrink-0"></i>
                                                            <span class="truncate">Ver Documento Adjunto</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] shrink-0 opacity-70 group-hover:opacity-100"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-400 italic">No hay archivos adicionales adjuntos en este módulo.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Botones de Navegación del Recorrido Acompañado -->
                            <div class="pt-4 border-t border-line/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 text-xs">
                                <!-- Lado Izquierdo: Módulo Anterior (Repasar) -->
                                <div class="flex items-center gap-2">
                                    @if($idx > 0)
                                        <button type="button"
                                                onclick="seleccionarModuloRecorrido({{ $capacitacion->modulos[$idx - 1]->id }})"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-200 shadow-2xs cursor-pointer">
                                            <i class="fa-solid fa-arrow-left text-[11px]"></i>
                                            <span>← Módulo Anterior (Paso {{ $idx }})</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400 italic flex items-center gap-1.5">
                                            <i class="fa-solid fa-flag text-slate-300"></i> Inicio del recorrido de aprendizaje
                                        </span>
                                    @endif
                                </div>

                                <!-- Lado Derecho: Avance / Siguiente Módulo / Completar -->
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$haIniciado)
                                        <div class="flex items-center gap-2 text-slate-500 italic">
                                            <i class="fa-solid fa-eye text-slate-400"></i>
                                            <span>Modo visualización · Inicia la capacitación para registrar avance.</span>
                                        </div>
                                        <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $capacitacion) }}" class="shrink-0 m-0">
                                            @csrf
                                            <button type="submit"
                                                    style="background-color: #0056b3; color: #ffffff;"
                                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-bold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                                <i class="fa-solid fa-play text-[10px]"></i>
                                                <span>Iniciar Capacitación</span>
                                            </button>
                                        </form>
                                    @elseif($esCompletado)
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-300">
                                                <i class="fa-solid fa-check text-emerald-600"></i> Módulo Listo
                                            </span>
                                            @if(!$esUltimoModulo && isset($capacitacion->modulos[$idx + 1]) && !$capacitacion->modulos[$idx + 1]->bloqueado_usuario)
                                                <button type="button"
                                                        onclick="seleccionarModuloRecorrido({{ $capacitacion->modulos[$idx + 1]->id }})"
                                                        style="background-color: #0056b3; color: #ffffff;"
                                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-bold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                                    <span>Continuar al Módulo {{ $idx + 2 }}</span>
                                                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                                                </button>
                                            @elseif($esUltimoModulo)
                                                @if($evaluaciones->isNotEmpty())
                                                    <a href="#seccion-evaluaciones"
                                                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                        <span>Ir a la Evaluación Final →</span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-300">
                                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                        <span>Capacitación Finalizada</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    @else
                                        <!-- Módulo en curso (requiere ser completado) -->
                                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 w-full">
                                            @if($totalVideosModulo > 0)
                                                <!-- Aviso de bloqueo hasta cumplir 50% -->
                                                <div id="aviso-bloqueo-modulo-{{ $mod->id }}"
                                                     class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold transition-all shadow-2xs">
                                                    <i id="aviso-icono-modulo-{{ $mod->id }}" class="fa-solid fa-lock text-amber-600 text-sm"></i>
                                                    <span id="aviso-texto-modulo-{{ $mod->id }}">Debes reproducir al menos el 50% del video para desbloquear este paso.</span>
                                                </div>
                                            @else
                                                <div></div>
                                            @endif

                                            <form method="POST" action="{{ route('empleado.modulos.completar', $mod) }}" class="shrink-0 m-0" id="form-completar-modulo-{{ $mod->id }}">
                                                @csrf
                                                @if($esUltimoModulo)
                                                    <button type="submit"
                                                            id="btn-completar-modulo-{{ $mod->id }}"
                                                            data-modulo-id="{{ $mod->id }}"
                                                            data-requiere-videos="{{ $totalVideosModulo }}"
                                                            style="background-color: #0056b3; color: #ffffff;"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer btn-completar-modulo">
                                                        <i id="btn-icono-modulo-{{ $mod->id }}" class="fa-solid fa-flag-checkered text-sm"></i>
                                                        <span>Finalizar Módulos y Pasar a Evaluación</span>
                                                    </button>
                                                @else
                                                    <button type="submit"
                                                            id="btn-completar-modulo-{{ $mod->id }}"
                                                            data-modulo-id="{{ $mod->id }}"
                                                            data-requiere-videos="{{ $totalVideosModulo }}"
                                                            style="background-color: #0056b3; color: #ffffff;"
                                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer btn-completar-modulo">
                                                        <i id="btn-icono-modulo-{{ $mod->id }}" class="fa-solid fa-arrow-right text-xs"></i>
                                                        <span>Completar Módulo y Continuar al Paso {{ $idx + 2 }}</span>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-line/80 p-8 text-center text-slate-500 text-xs">
                    No hay módulos publicados para esta capacitación.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================
         SECCIÓN 2: EVALUACIÓN DE APRENDIZAJE / EXAMEN DEL CURSO
         ======================================================== -->
    <div id="seccion-evaluaciones" class="space-y-4 pt-4">
        <div class="flex items-center justify-between border-b border-line/70 pb-3">
            <div>
                <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark">Evaluación de Aprendizaje</h2>
                <p class="text-xs text-muted">Examen de validación de conocimientos para la emisión de tu certificado digital.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                Examen Final
            </span>
        </div>

        @if($evaluaciones->isEmpty())
            <div class="bg-white rounded-3xl border border-line/80 p-6 sm:p-8 text-center space-y-2">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-cyan-50 text-brand-blue flex items-center justify-center font-bold text-xl">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3 class="font-heading font-bold text-sm text-brand-dark">Esta capacitación no requiere evaluación escrita</h3>
                <p class="text-xs text-muted max-w-md mx-auto">
                    Al completar todos los módulos de estudio, tu avance registrado concluirá satisfactoriamente.
                </p>
            </div>
        @else
            @foreach($evaluaciones as $eval)
                <div class="bg-white rounded-3xl border border-line/80 shadow-xs overflow-hidden">
                    <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 to-brand-dark text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full bg-brand-sky/20 text-brand-sky text-[10px] font-bold uppercase tracking-wider">
                                Evaluación de Aprendizaje
                            </span>
                            <h3 class="font-heading font-extrabold text-lg sm:text-xl text-white mt-1">
                                {{ $eval->titulo }}
                            </h3>
                            <p class="text-xs text-slate-300 mt-0.5">
                                {{ $eval->descripcion ?: 'Responde las preguntas para certificar tus conocimientos en este tema.' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 text-xs bg-white/10 p-3 rounded-2xl border border-white/15 shrink-0">
                            <div class="text-center px-2">
                                <span class="block text-lg font-heading font-extrabold text-cyan-300">{{ rtrim(rtrim(number_format((float) $eval->porcentaje_aprobacion, 2), '0'), '.') }}%</span>
                                <span class="text-[10px] text-slate-300">Mínimo Aprobatorio</span>
                            </div>
                            <div class="text-center px-2 border-l border-white/20">
                                <span class="block text-lg font-heading font-extrabold text-white">{{ $eval->intentos_restantes }}</span>
                                <span class="text-[10px] text-slate-300">Intentos Restantes</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 sm:p-7 space-y-5">
                        @if(!$haIniciado)
                            <div class="p-5 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">Evaluación Bloqueada · Capacitación No Iniciada</p>
                                        <p class="text-amber-800 text-xs mt-0.5">
                                            Para presentar el examen final debes primero iniciar la capacitación y completar los {{ $totalModulos }} módulos de estudio.
                                        </p>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $capacitacion) }}" class="shrink-0">
                                    @csrf
                                    <button type="submit"
                                            style="background-color: #0056b3; color: #ffffff;"
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-bold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                        <i class="fa-solid fa-play text-xs"></i>
                                        <span>Iniciar Capacitación</span>
                                    </button>
                                </form>
                            </div>
                        @elseif(!$todosModulosCompletados)
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <div>
                                    <p class="font-bold">Evaluación Bloqueada Temporalmente</p>
                                    <p class="text-amber-800 text-xs mt-0.5">
                                        Debes completar la lectura de los <strong>{{ $totalModulos }} módulos de estudio</strong> antes de habilitar el examen de certificación.
                                    </p>
                                </div>
                            </div>
                        @elseif($eval->aprobada)
                            <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-2xs">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-2xl shrink-0 shadow-sm">
                                        <i class="fa-solid fa-trophy"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-heading font-extrabold text-base sm:text-lg text-emerald-950 leading-tight">
                                            ¡Evaluación Aprobada con Éxito!
                                        </h4>
                                        <p class="text-emerald-800 text-xs sm:text-sm leading-relaxed">
                                            Obtuviste <strong class="font-bold text-emerald-900">{{ $eval->mejor_intento?->porcentaje }}%</strong>. Tu certificado oficial ya se encuentra emitido y disponible.
                                        </p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex justify-start md:justify-end">
                                    <a href="{{ route('empleado.certificados') }}"
                                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-extrabold text-xs sm:text-sm shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all text-center w-full sm:w-auto">
                                        <i class="fa-solid fa-graduation-cap text-base"></i>
                                        <span>Ver Mi Certificado</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($eval->intentos_restantes <= 0)
                            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-900 text-xs sm:text-sm flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-red-100 text-red-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                                <div>
                                    <p class="font-bold">Intentos Agotados</p>
                                    <p class="text-red-800 text-xs mt-0.5">
                                        Has utilizado los {{ $eval->intentos_permitidos }} intentos permitidos. Por favor, solicita a tu Jefe de Área o a Talento Humano que habilite un intento adicional.
                                    </p>
                                </div>
                            </div>
                        @else
                            <!-- FORMULARIO DE CUESTIONARIO / EXAMEN -->
                            <form method="POST" action="{{ route('empleado.evaluaciones.rendir', $eval) }}" class="space-y-6">
                                @csrf

                                <div class="space-y-5">
                                    <h4 class="font-heading font-extrabold text-sm text-brand-dark uppercase tracking-wider border-b border-line pb-2">
                                        Preguntas del Examen ({{ $eval->preguntas->count() }})
                                    </h4>

                                    @foreach($eval->preguntas as $pIdx => $pregunta)
                                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-line/80 space-y-3">
                                            <div class="flex items-start gap-2.5">
                                                <span class="w-6 h-6 rounded-lg bg-brand-blue text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $pIdx + 1 }}
                                                </span>
                                                <p class="font-heading font-bold text-sm text-brand-dark">
                                                    {{ $pregunta->pregunta }}
                                                </p>
                                            </div>

                                            <div class="space-y-2 pl-8">
                                                @foreach($pregunta->opciones as $opcion)
                                                    <label class="flex items-center gap-3 p-3 rounded-xl bg-white border border-line/70 hover:border-brand-blue cursor-pointer transition-colors text-xs font-semibold text-slate-700">
                                                        <input type="radio" name="respuestas[{{ $pregunta->id }}]" value="{{ $opcion->id }}" required
                                                               class="text-brand-blue focus:ring-brand-blue h-4 w-4">
                                                        <span>{{ $opcion->texto }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-end pt-3 border-t border-line/70">
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-brand-blue via-brand-deep to-brand-dark hover:brightness-110 text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                        <span><i class="fa-solid fa-paper-plane mr-1.5"></i> Enviar Respuestas y Calificar Examen</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const REQUISITO_PORCENTAJE = 50;
    const capacitacionId = {{ (int) $capacitacion->id }};

    // Mapa de videos por módulo: moduloId -> Set de contenidoIds
    const modulosVideos = {};
    // Mapa de videos cumplidos: moduloId -> Set de contenidoIds cumplidos
    const modulosCompletados = {};

    // 1. Detectar videos locales (<video>)
    const videosLocales = document.querySelectorAll('video.pny-video-local');
    videosLocales.forEach(video => {
        const modId = video.dataset.moduloId;
        const contId = video.dataset.contenidoId;
        if (!modId || !contId) return;

        if (!modulosVideos[modId]) {
            modulosVideos[modId] = new Set();
            modulosCompletados[modId] = new Set();
        }
        modulosVideos[modId].add(contId);

        // Verificar persistencia previa en la sesión
        const storageKey = `pny_vid_50_${capacitacionId}_${modId}_${contId}`;
        if (sessionStorage.getItem(storageKey) === 'true') {
            marcarVideoCumplido(modId, contId, true);
        }

        // Listener de progreso
        video.addEventListener('timeupdate', function () {
            if (!video.duration || isNaN(video.duration) || video.duration === 0) return;
            const pct = Math.min(100, Math.floor((video.currentTime / video.duration) * 100));
            actualizarUIProgreso(contId, pct);

            if (pct >= REQUISITO_PORCENTAJE) {
                marcarVideoCumplido(modId, contId);
            }
        });

        video.addEventListener('ended', function () {
            actualizarUIProgreso(contId, 100);
            marcarVideoCumplido(modId, contId);
        });
    });

    // 2. Detectar videos de YouTube (<iframe>)
    const ytIframes = document.querySelectorAll('iframe.pny-video-youtube');
    const ytPlayers = {};
    const ytIntervals = {};

    ytIframes.forEach(iframe => {
        const modId = iframe.dataset.moduloId;
        const contId = iframe.dataset.contenidoId;
        if (!modId || !contId) return;

        if (!modulosVideos[modId]) {
            modulosVideos[modId] = new Set();
            modulosCompletados[modId] = new Set();
        }
        modulosVideos[modId].add(contId);

        const storageKey = `pny_vid_50_${capacitacionId}_${modId}_${contId}`;
        if (sessionStorage.getItem(storageKey) === 'true') {
            marcarVideoCumplido(modId, contId, true);
        }
    });

    // Cargar API de YouTube si hay iframes presentes
    if (ytIframes.length > 0) {
        function initYTPlayers() {
            ytIframes.forEach(iframe => {
                const contId = iframe.dataset.contenidoId;
                const modId = iframe.dataset.moduloId;
                if (!contId || !window.YT || !window.YT.Player) return;

                try {
                    ytPlayers[contId] = new YT.Player(iframe.id, {
                        events: {
                            'onStateChange': function (event) {
                                onYTStateChange(event, modId, contId);
                            }
                        }
                    });
                } catch (e) {
                    console.warn('Error inicializando YT.Player:', e);
                }
            });
        }

        if (window.YT && window.YT.Player) {
            initYTPlayers();
        } else {
            const previousOnReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function () {
                if (typeof previousOnReady === 'function') previousOnReady();
                initYTPlayers();
            };

            if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
                const tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                const firstScript = document.getElementsByTagName('script')[0];
                firstScript.parentNode.insertBefore(tag, firstScript);
            }
        }
    }

    function onYTStateChange(event, modId, contId) {
        const player = ytPlayers[contId];
        if (!player) return;

        // YT.PlayerState.PLAYING === 1
        if (event.data === 1) {
            if (ytIntervals[contId]) clearInterval(ytIntervals[contId]);
            ytIntervals[contId] = setInterval(() => {
                try {
                    const dur = player.getDuration();
                    const cur = player.getCurrentTime();
                    if (dur && dur > 0) {
                        const pct = Math.min(100, Math.floor((cur / dur) * 100));
                        actualizarUIProgreso(contId, pct);
                        if (pct >= REQUISITO_PORCENTAJE) {
                            marcarVideoCumplido(modId, contId);
                            clearInterval(ytIntervals[contId]);
                        }
                    }
                } catch (e) {}
            }, 500);
        } else {
            if (ytIntervals[contId]) {
                clearInterval(ytIntervals[contId]);
            }
            if (event.data === 0) { // Finalizado
                actualizarUIProgreso(contId, 100);
                marcarVideoCumplido(modId, contId);
            }
        }
    }

    // 3. Funciones de UI
    function actualizarUIProgreso(contId, pct) {
        const barra = document.getElementById(`barra-progreso-${contId}`);
        const texto = document.getElementById(`texto-progreso-${contId}`);
        const badge = document.getElementById(`badge-pct-${contId}`);
        const contenedor = document.getElementById(`progreso-video-${contId}`);
        const icono = document.getElementById(`icono-progreso-${contId}`);

        if (barra) {
            barra.style.width = `${Math.min(100, pct)}%`;
            if (pct >= REQUISITO_PORCENTAJE) {
                barra.classList.remove('bg-brand-blue', 'bg-red-600');
                barra.classList.add('bg-emerald-500');
            }
        }
        if (badge) {
            badge.textContent = `${pct}%`;
        }

        if (pct >= REQUISITO_PORCENTAJE) {
            if (texto) {
                texto.innerHTML = `<strong class="text-emerald-700">✓ Requisito cumplido (${pct}% visto)</strong>. Ya puedes completar este módulo.`;
            }
            if (contenedor) {
                contenedor.classList.remove('bg-slate-100', 'border-slate-200/80');
                contenedor.classList.add('bg-emerald-50', 'border-emerald-200');
            }
            if (icono) {
                icono.className = 'fa-solid fa-circle-check text-emerald-600 shrink-0 text-sm';
            }
        } else {
            if (texto) {
                texto.textContent = `${pct}% visto (Mínimo 50% requerido para desbloquear avance)`;
            }
        }
    }

    function marcarVideoCumplido(modId, contId, silent = false) {
        const storageKey = `pny_vid_50_${capacitacionId}_${modId}_${contId}`;
        sessionStorage.setItem(storageKey, 'true');

        if (modulosCompletados[modId]) {
            modulosCompletados[modId].add(contId);
        }

        actualizarUIProgreso(contId, Math.max(50, silent ? 100 : 50));
        verificarDesbloqueoModulo(modId);
    }

    function verificarDesbloqueoModulo(modId) {
        const totalRequeridos = modulosVideos[modId] ? modulosVideos[modId].size : 0;
        const totalCumplidos = modulosCompletados[modId] ? modulosCompletados[modId].size : 0;

        if (totalRequeridos > 0 && totalCumplidos >= totalRequeridos) {
            desbloquearBotonModulo(modId);
        }
    }

    function desbloquearBotonModulo(modId) {
        const btn = document.getElementById(`btn-completar-modulo-${modId}`);
        const aviso = document.getElementById(`aviso-bloqueo-modulo-${modId}`);
        const avisoTexto = document.getElementById(`aviso-texto-modulo-${modId}`);
        const avisoIcono = document.getElementById(`aviso-icono-modulo-${modId}`);

        if (btn) {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            btn.classList.add('cursor-pointer', 'shadow-md', 'hover:-translate-y-0.5');
            btn.removeAttribute('title');
        }

        if (aviso) {
            aviso.classList.remove('bg-amber-50', 'border-amber-200', 'text-amber-900');
            aviso.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-800');
        }
        if (avisoIcono) {
            avisoIcono.className = 'fa-solid fa-circle-check text-emerald-600 text-sm';
        }
        if (avisoTexto) {
            avisoTexto.innerHTML = '<strong>✓ Requisito cumplido:</strong> Videos reproducidos al 50%. Ya puedes avanzar.';
        }
    }

    function bloquearBotonModulo(modId) {
        const btn = document.getElementById(`btn-completar-modulo-${modId}`);
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            btn.classList.remove('hover:-translate-y-0.5');
            btn.setAttribute('title', 'Debes reproducir al menos el 50% de los videos para continuar.');
        }
    }

    // 4. Bloqueo inicial para módulos con videos pendientes
    document.querySelectorAll('.btn-completar-modulo').forEach(btn => {
        const modId = btn.dataset.moduloId;
        const reqVideos = parseInt(btn.dataset.requiereVideos || '0', 10);

        if (reqVideos > 0 && modulosVideos[modId]) {
            const totalCumplidos = modulosCompletados[modId] ? modulosCompletados[modId].size : 0;
            if (totalCumplidos < modulosVideos[modId].size) {
                bloquearBotonModulo(modId);
            } else {
                desbloquearBotonModulo(modId);
            }
        }
    });

    // 5. Manejo del Recorrido Guiado Paso a Paso
    let modoVistaTodos = false;
    let moduloActivoActual = {{ $idModuloInicial ? (int)$idModuloInicial : 'null' }};

    window.seleccionarModuloRecorrido = function (moduloId, smoothScroll = true) {
        if (!moduloId) return;
        moduloActivoActual = moduloId;

        const items = document.querySelectorAll('.modulo-recorrido-item');
        let targetItem = null;

        items.forEach(item => {
            const mId = parseInt(item.dataset.moduloId, 10);
            if (modoVistaTodos) {
                item.classList.remove('hidden');
            } else {
                if (mId === moduloId) {
                    item.classList.remove('hidden');
                    targetItem = item;
                } else {
                    item.classList.add('hidden');
                }
            }
        });

        // Actualizar pestañas del Stepper
        document.querySelectorAll('.stepper-tab').forEach(tab => {
            const mId = parseInt(tab.dataset.moduloId, 10);
            const esCompletado = tab.dataset.esCompletado === '1';
            const esBloqueado = tab.dataset.esBloqueado === '1';

            const baseClasses = 'stepper-tab snap-start shrink-0 w-[78vw] max-w-[270px] sm:w-auto sm:max-w-none sm:shrink sm:flex-1 flex items-center gap-2 sm:gap-2.5 p-2.5 sm:p-3 rounded-2xl text-xs font-heading border transition-all duration-200';
            const badgeIcon = tab.querySelector('.stepper-badge-icon');
            const badgeTag = tab.querySelector('.stepper-badge-tag');
            const titleEl = tab.querySelector('.stepper-title-text');
            const labelEl = tab.querySelector('.stepper-label-text');

            if (mId === moduloId) {
                tab.className = `${baseClasses} bg-gradient-to-r from-brand-blue to-[#004a99] text-white border-brand-blue shadow-md ring-2 ring-brand-blue/30 scale-[1.01]`;
                if (badgeIcon) {
                    badgeIcon.className = 'stepper-badge-icon w-8 h-8 rounded-xl bg-white text-brand-blue flex items-center justify-center font-black text-xs shadow-xs';
                }
                if (badgeTag) {
                    badgeTag.className = 'stepper-badge-tag text-[9px] px-1.5 py-0.2 rounded-md font-bold bg-white/20 text-white';
                    badgeTag.textContent = 'En curso';
                }
                if (titleEl) {
                    titleEl.className = 'stepper-title-text truncate text-xs font-bold text-white';
                }
                if (labelEl) {
                    labelEl.className = 'stepper-label-text text-[10px] font-extrabold uppercase tracking-wider text-white/80';
                }
            } else if (esCompletado) {
                tab.className = `${baseClasses} bg-white hover:bg-emerald-50/60 text-slate-800 border-emerald-200 hover:border-emerald-400 shadow-2xs cursor-pointer`;
                if (badgeIcon) {
                    badgeIcon.className = 'stepper-badge-icon w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-xs';
                }
                if (badgeTag) {
                    badgeTag.className = 'stepper-badge-tag text-[9px] px-1.5 py-0.2 rounded-md font-bold bg-emerald-100 text-emerald-800';
                    badgeTag.textContent = 'Listo';
                }
                if (titleEl) {
                    titleEl.className = 'stepper-title-text truncate text-xs font-semibold text-slate-800';
                }
                if (labelEl) {
                    labelEl.className = 'stepper-label-text text-[10px] font-extrabold uppercase tracking-wider text-slate-400';
                }
            } else if (esBloqueado) {
                tab.className = `${baseClasses} bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-75`;
                if (badgeIcon) {
                    badgeIcon.className = 'stepper-badge-icon w-8 h-8 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs';
                }
                if (badgeTag) {
                    badgeTag.className = 'stepper-badge-tag text-[9px] px-1.5 py-0.2 rounded-md font-medium bg-slate-200 text-slate-600';
                    badgeTag.textContent = 'Bloqueado';
                }
                if (titleEl) {
                    titleEl.className = 'stepper-title-text truncate text-xs font-medium text-slate-500';
                }
                if (labelEl) {
                    labelEl.className = 'stepper-label-text text-[10px] font-extrabold uppercase tracking-wider text-slate-400';
                }
            } else {
                tab.className = `${baseClasses} bg-white hover:bg-slate-50 text-slate-700 border-slate-200 hover:border-brand-blue cursor-pointer`;
                if (badgeIcon) {
                    badgeIcon.className = 'stepper-badge-icon w-8 h-8 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-xs';
                }
                if (badgeTag) {
                    badgeTag.className = 'stepper-badge-tag text-[9px] px-1.5 py-0.2 rounded-md font-bold bg-cyan-100 text-cyan-800';
                    badgeTag.textContent = 'Disponible';
                }
                if (titleEl) {
                    titleEl.className = 'stepper-title-text truncate text-xs font-semibold text-slate-800';
                }
                if (labelEl) {
                    labelEl.className = 'stepper-label-text text-[10px] font-extrabold uppercase tracking-wider text-slate-400';
                }
            }
        });

        // Si hay un tab activo, asegurar que sea visible en el scroll horizontal
        const activeTab = document.getElementById(`stepper-tab-${moduloId}`);
        if (activeTab && activeTab.scrollIntoView) {
            activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        // Si tiene iframes de YouTube sin inicializar, inicializarlos
        if (window.YT && window.YT.Player && targetItem) {
            targetItem.querySelectorAll('iframe.pny-video-youtube').forEach(iframe => {
                const contId = iframe.dataset.contenidoId;
                const modId = iframe.dataset.moduloId;
                if (!ytPlayers[contId]) {
                    try {
                        ytPlayers[contId] = new YT.Player(iframe.id, {
                            events: {
                                'onStateChange': function (event) {
                                    onYTStateChange(event, modId, contId);
                                }
                            }
                        });
                    } catch (e) {}
                }
            });
        }

        // Scroll suave hacia el módulo o el contenedor del recorrido
        if (smoothScroll) {
            const container = document.getElementById('contenedor-recorrido-modulos') || document.getElementById(`modulo-${moduloId}`);
            if (container) {
                const yOffset = -80;
                const y = container.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
            }
        }

        // Actualizar URL sin saltos
        if (history.replaceState) {
            history.replaceState(null, null, '#modulo-' + moduloId);
        }

        if (typeof window.ajustarEscalaPptxViewers === 'function') {
            setTimeout(window.ajustarEscalaPptxViewers, 60);
        }
    };

    window.scrollModulosStepperEmpleado = function (direction) {
        const container = document.getElementById('stepper-modulos-scroll-container');
        if (container) {
            const scrollAmount = container.clientWidth * 0.75 * direction;
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    };

    window.alternarModoVista = function () {
        modoVistaTodos = !modoVistaTodos;
        const btnTexto = document.getElementById('texto-toggle-vista');
        const btnIcono = document.getElementById('icono-toggle-vista');

        if (modoVistaTodos) {
            document.querySelectorAll('.modulo-recorrido-item').forEach(item => item.classList.remove('hidden'));
            if (btnTexto) btnTexto.textContent = 'Modo Vista Paso a Paso';
            if (btnIcono) btnIcono.className = 'fa-solid fa-arrows-split-up-and-left text-slate-500 text-[11px]';
        } else {
            if (btnTexto) btnTexto.textContent = 'Ver todos los módulos';
            if (btnIcono) btnIcono.className = 'fa-solid fa-list-check text-slate-500 text-[11px]';
            seleccionarModuloRecorrido(moduloActivoActual, false);
        }

        if (typeof window.ajustarEscalaPptxViewers === 'function') {
            setTimeout(window.ajustarEscalaPptxViewers, 60);
        }
    };

    // ==========================================
    // PRESENTADOR DE DIAPOSITIVAS POWERPOINT (PPTX)
    // ==========================================
    window.ajustarEscalaPptxViewers = function () {
        document.querySelectorAll('.pptx-interactive-viewer').forEach(viewer => {
            const stage = viewer.querySelector('.pptx-stage');
            if (!stage || stage.offsetParent === null) return;

            const isFs = viewer.classList.contains('is-fullscreen-fallback') || !!(document.fullscreenElement || document.webkitFullscreenElement);
            const padX = isFs ? 12 : 16;
            const padY = isFs ? 12 : 20;

            const stageW = Math.max(120, stage.clientWidth - padX);
            const stageH = Math.max(120, stage.clientHeight - padY);

            viewer.querySelectorAll('.pptx-slide-wrapper').forEach(wrapper => {
                const origW = parseFloat(wrapper.dataset.width || 1920);
                const origH = parseFloat(wrapper.dataset.height || 1080);
                const scale = Math.min(stageW / origW, stageH / origH);

                const canvas = wrapper.querySelector('.pptx-canvas');
                if (canvas) {
                    canvas.style.transform = `scale(${scale})`;
                    canvas.style.transformOrigin = '0 0';
                    wrapper.style.width = `${origW * scale}px`;
                    wrapper.style.height = `${origH * scale}px`;
                }
            });
        });
    };

    window.irASlidePptx = function (viewerId, targetIndex) {
        const viewer = document.getElementById(viewerId);
        if (!viewer) return;

        const total = parseInt(viewer.dataset.totalSlides, 10) || 1;
        const newIdx = Math.max(0, Math.min(targetIndex, total - 1));
        viewer.dataset.currentSlide = newIdx;

        viewer.querySelectorAll('.pptx-slide-pane').forEach((pane, idx) => {
            if (idx === newIdx) {
                pane.classList.remove('hidden');
                pane.classList.add('active-pptx-slide', 'flex');
            } else {
                pane.classList.add('hidden');
                pane.classList.remove('active-pptx-slide', 'flex');
            }
        });

        const currNum = viewer.querySelector('#pptx-curr-' + viewer.dataset.viewerId);
        if (currNum) currNum.textContent = (newIdx + 1);

        const bar = viewer.querySelector('#pptx-bar-' + viewer.dataset.viewerId);
        if (bar) bar.style.width = `${Math.round(((newIdx + 1) / total) * 100)}%`;

        viewer.querySelectorAll('.pptx-dot').forEach((dot, idx) => {
            if (idx === newIdx) {
                dot.className = 'pptx-dot rounded-full transition-all cursor-pointer bg-cyan-400 w-5 h-2.5';
            } else {
                dot.className = 'pptx-dot rounded-full transition-all cursor-pointer bg-slate-700 hover:bg-slate-500 w-2.5 h-2.5';
            }
        });

        viewer.querySelectorAll('.pptx-btn-prev, .pptx-btn-nav-prev').forEach(btn => {
            btn.disabled = (newIdx === 0);
        });
        viewer.querySelectorAll('.pptx-btn-next, .pptx-btn-nav-next').forEach(btn => {
            btn.disabled = (newIdx === total - 1);
        });

        window.ajustarEscalaPptxViewers();
    };

    window.cambiarSlidePptx = function (viewerId, delta) {
        const viewer = document.getElementById(viewerId);
        if (!viewer) return;
        const current = parseInt(viewer.dataset.currentSlide || '0', 10);
        window.irASlidePptx(viewerId, current + delta);
    };

    function actualizarIconosPantallaCompleta(viewer, activo) {
        viewer.querySelectorAll('.pptx-btn-toggle-fs').forEach(btn => {
            const icon = btn.querySelector('i');
            const span = btn.querySelector('span');
            if (activo) {
                if (icon) icon.className = 'fa-solid fa-compress text-sm sm:text-xs text-red-400';
                if (span) span.textContent = 'Reducir';
                btn.title = 'Salir de pantalla completa';
            } else {
                if (icon) icon.className = 'fa-solid fa-expand text-sm sm:text-xs';
                if (span) span.textContent = 'Agrandar';
                btn.title = 'Agrandar diapositiva / Pantalla completa';
            }
        });
    }

    function activarFallbackFullscreen(viewer) {
        viewer.classList.add('is-fullscreen-fallback');
        document.body.classList.add('pptx-body-fullscreen');
        actualizarIconosPantallaCompleta(viewer, true);
        setTimeout(window.ajustarEscalaPptxViewers, 50);
        setTimeout(window.ajustarEscalaPptxViewers, 250);
    }

    function desactivarFallbackFullscreen(viewer) {
        viewer.classList.remove('is-fullscreen-fallback');
        document.body.classList.remove('pptx-body-fullscreen');
        actualizarIconosPantallaCompleta(viewer, false);
        setTimeout(window.ajustarEscalaPptxViewers, 50);
        setTimeout(window.ajustarEscalaPptxViewers, 250);
    }

    window.togglePantallaCompletaPptx = function (viewerId) {
        const viewer = document.getElementById(viewerId);
        if (!viewer) return;

        const isNativeFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement);
        const isFallbackFs = viewer.classList.contains('is-fullscreen-fallback');

        // Salir si ya está en pantalla completa
        if (isNativeFs) {
            if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
            return;
        }

        if (isFallbackFs) {
            desactivarFallbackFullscreen(viewer);
            return;
        }

        // Detección móvil / iOS (en iOS Safari o pantallas táctiles pequeñas, fallback da mejor resultado y no falla)
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        const reqFs = viewer.requestFullscreen || viewer.webkitRequestFullscreen || viewer.msRequestFullscreen;

        if (isIOS || !reqFs) {
            activarFallbackFullscreen(viewer);
            return;
        }

        try {
            const p = reqFs.call(viewer);
            if (p && p.catch) {
                p.catch(() => {
                    activarFallbackFullscreen(viewer);
                });
            }
        } catch (e) {
            activarFallbackFullscreen(viewer);
        }
    };

    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(ev => {
        document.addEventListener(ev, () => {
            const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement);
            document.querySelectorAll('.pptx-interactive-viewer').forEach(v => {
                actualizarIconosPantallaCompleta(v, isFs);
            });
            setTimeout(window.ajustarEscalaPptxViewers, 80);
            setTimeout(window.ajustarEscalaPptxViewers, 300);
        });
    });

    window.addEventListener('resize', () => {
        window.ajustarEscalaPptxViewers();
    });

    window.addEventListener('orientationchange', () => {
        setTimeout(window.ajustarEscalaPptxViewers, 100);
        setTimeout(window.ajustarEscalaPptxViewers, 400);
    });

    setTimeout(window.ajustarEscalaPptxViewers, 300);

    // Gestos táctiles de deslizamiento (Swipe) para teléfonos móviles
    document.querySelectorAll('.pptx-stage').forEach(stage => {
        let touchStartX = 0;
        let touchStartY = 0;

        stage.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        stage.addEventListener('touchend', (e) => {
            if (e.changedTouches.length === 1) {
                const diffX = e.changedTouches[0].clientX - touchStartX;
                const diffY = e.changedTouches[0].clientY - touchStartY;
                if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY) * 1.3) {
                    const viewer = stage.closest('.pptx-interactive-viewer');
                    if (viewer) {
                        if (diffX < 0) {
                            window.cambiarSlidePptx(viewer.id, 1);
                        } else {
                            window.cambiarSlidePptx(viewer.id, -1);
                        }
                    }
                }
            }
        }, { passive: true });
    });

    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
        const targetViewer = document.querySelector('.pptx-interactive-viewer.is-fullscreen-fallback')
            || document.fullscreenElement?.closest('.pptx-interactive-viewer')
            || document.querySelector('.modulo-recorrido-item:not(.hidden) .pptx-interactive-viewer');
        if (targetViewer) {
            if (e.key === 'ArrowRight' || e.key === 'Space') {
                e.preventDefault();
                window.cambiarSlidePptx(targetViewer.id, 1);
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                window.cambiarSlidePptx(targetViewer.id, -1);
            } else if (e.key === 'Escape' && targetViewer.classList.contains('is-fullscreen-fallback')) {
                desactivarFallbackFullscreen(targetViewer);
            }
        }
    });

    // Auto-scroll y activación inicial inteligente
    const hash = window.location.hash;
    if (hash && hash.startsWith('#modulo-')) {
        const idFromHash = parseInt(hash.replace('#modulo-', ''), 10);
        if (!isNaN(idFromHash)) {
            seleccionarModuloRecorrido(idFromHash, true);
        }
    } else if (hash === '#seccion-evaluaciones') {
        const evalSec = document.getElementById('seccion-evaluaciones');
        if (evalSec) {
            setTimeout(() => {
                const yOffset = -70;
                const y = evalSec.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
            }, 200);
        }
    } else if (moduloActivoActual) {
        @if(session('success_modulo'))
            setTimeout(() => {
                seleccionarModuloRecorrido(moduloActivoActual, true);
            }, 150);
        @endif
    }
});
</script>
@endpush
