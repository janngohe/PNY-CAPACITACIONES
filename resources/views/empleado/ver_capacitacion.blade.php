@extends('layouts.empleado')

@section('title', $capacitacion->titulo . ' · Capacitación')
@section('page_title', 'Detalle de Capacitación')

@section('content')
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

    <!-- ========================================================
         SECCIÓN 1: MÓDULOS DE ESTUDIO (SECUENCIALES)
         ======================================================== -->
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-line/70 pb-3">
            <div>
                <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark">Módulos y Contenido de Estudio</h2>
                <p class="text-xs text-muted">
                    @if($haIniciado)
                        Accede al material de estudio interactivo. Los módulos se desbloquean en secuencia conforme los completas.
                    @else
                        Visualización descriptiva del temario. Haz clic en "Iniciar Capacitación" para desbloquear videos, recursos interactivos y evaluaciones.
                    @endif
                </p>
            </div>
            <span class="px-3 py-1 rounded-full bg-brand-light text-brand-blue text-xs font-bold">
                {{ $totalModulos }} {{ $totalModulos === 1 ? 'Módulo' : 'Módulos' }}
            </span>
        </div>

        <div class="space-y-5">
            @forelse($capacitacion->modulos as $idx => $mod)
                @php
                    $esBloqueado = $haIniciado ? (bool) $mod->bloqueado_usuario : false;
                    $esCompletado = $haIniciado ? (bool) $mod->completado_usuario : false;
                    $esUltimoModulo = ($idx + 1 === $totalModulos) || $loop->last;
                @endphp

                <div id="modulo-{{ $mod->id }}" class="rounded-3xl border transition-all overflow-hidden {{ $esBloqueado ? 'bg-slate-100/70 border-slate-200 opacity-85' : ($esCompletado ? 'bg-emerald-50/40 border-emerald-200 shadow-2xs' : 'bg-white border-line shadow-xs') }}">
                    
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
                                                        <video controls controlsList="nodownload" preload="metadata" class="w-full h-full object-contain">
                                                            <source src="{{ asset($cont->ruta_archivo) }}" type="video/mp4">
                                                            Tu navegador no soporta la reproducción de video HTML5.
                                                        </video>
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
                                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}" title="{{ $cont->titulo }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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

                                            @if($cont->ruta_archivo && $cont->tipo !== 'VIDEO')
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

                            <!-- Botón para marcar el módulo como completado -->
                            <div class="pt-3 border-t border-line/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 text-xs">
                                @if(!$haIniciado)
                                    <div class="flex items-center gap-2 text-slate-500 italic">
                                        <i class="fa-solid fa-eye text-slate-400"></i>
                                        <span>Modo solo visualización · Inicia la capacitación para registrar avance y completar este módulo.</span>
                                    </div>
                                    <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $capacitacion) }}" class="shrink-0">
                                        @csrf
                                        <button type="submit"
                                                style="background-color: #0056b3; color: #ffffff;"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-bold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                            <i class="fa-solid fa-play text-[10px]"></i>
                                            <span>Iniciar Capacitación</span>
                                        </button>
                                    </form>
                                @elseif($esCompletado)
                                    <div></div>
                                    <span class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-300">
                                        @if($esUltimoModulo)
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                            <span>Capacitación Finalizada</span>
                                        @else
                                            <span>✓ Módulo Completado</span>
                                        @endif
                                    </span>
                                @else
                                    <div></div>
                                    <form method="POST" action="{{ route('empleado.modulos.completar', $mod) }}">
                                        @csrf
                                        @if($esUltimoModulo)
                                            <button type="submit"
                                                    style="background-color: #0056b3; color: #ffffff;"
                                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                                <i class="fa-solid fa-flag-checkered text-sm"></i>
                                                <span>Finalizar Capacitación</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    style="background-color: #0056b3; color: #ffffff;"
                                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#0056b3] hover:bg-[#003d80] text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                                <i class="fa-solid fa-arrow-right text-xs"></i>
                                                <span>Continuar al Siguiente Módulo</span>
                                            </button>
                                        @endif
                                    </form>
                                @endif
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
