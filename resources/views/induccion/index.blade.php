@extends('layouts.induccion')

@section('title', 'Panel de Inducciones')

@section('content')
<div class="space-y-8">

    <!-- Encabezado y Acción Principal -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-100/60 blur-3xl pointer-events-none"></div>

        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-50 text-cyan-800 border border-cyan-200">
                    <i class="fa-solid fa-chalkboard-user"></i> Sesiones en Vivo
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-serif">Módulo de Inducciones Presenciales</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Crea o selecciona una inducción, proyéctala en pantalla con su <strong class="text-brand-blue">código QR dinámico</strong> y recolecta firmas de asistencia en tiempo real desde el celular de los colaboradores.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('induccion.create') }}"
               class="px-5 py-3 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Crear Nueva Inducción</span>
            </a>
        </div>
    </div>

    <!-- Catálogo de Inducciones Disponibles -->
    <div>
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i> Catálogo de Inducciones
                </h2>
                <p class="text-xs text-slate-500">Selecciona una inducción para iniciar su proyección y generar el QR de asistencia</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                {{ $inducciones->count() }} registradas
            </span>
        </div>

        @if($inducciones->isEmpty())
            <div class="text-center py-16 px-4 bg-white rounded-3xl border border-dashed border-slate-300 shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-presentation-screen"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Aún no hay inducciones creadas</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">
                    Comienza creando la primera inducción subiendo tus diapositivas o enlazando un video para reproducir en sala.
                </p>
                <a href="{{ route('induccion.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                    <i class="fa-solid fa-plus"></i> Crear Primera Inducción
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($inducciones as $ind)
                    @php
                        $recursos = $ind->lista_recursos;
                        $tienePptx = collect($recursos)->contains('tipo', 'pptx');
                        $tieneVideo = collect($recursos)->contains('tipo', 'video');
                        $tienePdf = collect($recursos)->contains('tipo', 'pdf');
                        $tieneImg = collect($recursos)->contains('tipo', 'imagen');
                    @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 hover:border-brand-blue/60 overflow-hidden flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md group">
                        <div>
                            <!-- Imagen de Portada con clic para explorar -->
                            <div onclick="abrirModalVisualizar({{ $ind->id }})" class="h-40 w-full bg-slate-100 relative overflow-hidden flex items-center justify-center cursor-pointer group/cover">
                                <img src="{{ $ind->imagen_portada_url }}" alt="{{ $ind->titulo }}" class="w-full h-full object-cover group-hover/cover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/20 to-transparent"></div>

                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/95 text-brand-dark backdrop-blur-xs shadow-xs border border-white/40">
                                        <i class="fa-solid fa-graduation-cap text-brand-blue mr-1"></i> Inducción
                                    </span>
                                </div>

                                <div class="absolute top-3 right-3 opacity-0 group-hover/cover:opacity-100 transition-opacity">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-blue/90 text-white backdrop-blur-xs shadow-xs flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-[9px]"></i> Explorar
                                    </span>
                                </div>

                                <!-- Badges de Recursos Disponibles -->
                                <div class="absolute bottom-2.5 left-3 right-3 flex flex-wrap items-center gap-1.5 text-[10px] font-bold">
                                    @if($tienePptx)
                                        <span class="px-2 py-0.5 rounded-md bg-orange-600/90 text-white backdrop-blur-xs shadow-xs border border-white/20">
                                            <i class="fa-solid fa-file-powerpoint mr-1"></i> PPTX
                                        </span>
                                    @endif
                                    @if($tieneVideo)
                                        <span class="px-2 py-0.5 rounded-md bg-rose-600/90 text-white backdrop-blur-xs shadow-xs border border-white/20">
                                            <i class="fa-brands fa-youtube mr-1"></i> Video
                                        </span>
                                    @endif
                                    @if($tienePdf)
                                        <span class="px-2 py-0.5 rounded-md bg-red-600/90 text-white backdrop-blur-xs shadow-xs border border-white/20">
                                            <i class="fa-solid fa-file-pdf mr-1"></i> PDF
                                        </span>
                                    @endif
                                    @if($tieneImg)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-600/90 text-white backdrop-blur-xs shadow-xs border border-white/20">
                                            <i class="fa-solid fa-image mr-1"></i> Imagen
                                        </span>
                                    @endif
                                    @if(!$tienePptx && !$tieneVideo && !$tienePdf && !$tieneImg)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-800/80 text-slate-200 backdrop-blur-xs">
                                            Presencial
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Contenido de la Tarjeta -->
                            <div class="p-5">
                                <h3 onclick="abrirModalVisualizar({{ $ind->id }})" class="text-base font-bold text-slate-900 hover:text-brand-blue transition-colors mb-2 leading-snug line-clamp-2 font-serif cursor-pointer" title="Clic para ver elementos y contenidos">
                                    {{ $ind->titulo }}
                                </h3>

                                <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">
                                    {{ $ind->descripcion ?? 'Inducción para colaboradores, nuevos ingresos y contratistas.' }}
                                </p>

                                <!-- Sedes y Áreas asignadas -->
                                <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between gap-2">
                                    <span class="flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-location-dot text-brand-blue shrink-0"></i>
                                        <span class="truncate">{{ $ind->sedes->pluck('nombre')->first() ?? 'Sedes institucionales' }}</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold shrink-0">
                                        {{ count($recursos) }} elemento(s)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción: Ver Contenido e Iniciar Inducción -->
                        <div class="p-5 pt-0 grid grid-cols-2 gap-2">
                            <button type="button"
                                    onclick="abrirModalVisualizar({{ $ind->id }})"
                                    class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-brand-blue font-bold text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs"
                                    title="Ver qué elementos y contenidos tiene esta inducción">
                                <i class="fa-solid fa-eye text-brand-blue"></i>
                                <span>Ver Contenido</span>
                            </button>

                            <button type="button"
                                    onclick="abrirModalIniciar({{ $ind->id }}, '{{ addslashes($ind->titulo) }}')"
                                    class="py-2.5 px-3 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs hover:shadow-md">
                                <i class="fa-solid fa-play text-[10px]"></i>
                                <span>Iniciar</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sesiones Recientes en Vivo y Descarga de Actas -->
    <div class="pt-6 border-t border-slate-200">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 font-serif">
                    <i class="fa-solid fa-clock-rotate-left text-brand-blue"></i> Mis Sesiones Dictadas
                </h2>
                <p class="text-xs text-slate-500">Historial individual de sesiones que has iniciado, monitoreo y actas con firmas</p>
            </div>
        </div>

        @if($sesionesRecientes->isEmpty())
            <div class="p-8 text-center bg-white rounded-3xl border border-slate-200 text-xs text-slate-500 shadow-2xs">
                Aún no has iniciado ninguna sesión en vivo. Al proyectar una inducción aparecerá registrada aquí.
            </div>
        @else
            <!-- 1. VISTA MÓVIL: POR COLUMNA / TARJETAS TOUCH (block md:hidden) -->
            <div class="block md:hidden space-y-3.5">
                @foreach($sesionesRecientes as $ses)
                    <div class="bg-white rounded-3xl border border-slate-200 p-4.5 shadow-xs space-y-3">
                        <!-- Cabecera de la Tarjeta: Código y Estado -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-xl bg-cyan-50 border border-cyan-200 text-brand-blue flex items-center justify-center text-xs font-bold font-mono">
                                    <i class="fa-solid fa-qrcode text-[11px]"></i>
                                </span>
                                <div>
                                    <span class="font-mono font-black text-brand-blue text-xs tracking-wider block">
                                        {{ $ses->codigo_sesion }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block font-normal">
                                        {{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                @if($ses->estaActiva())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En Curso
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-check text-[9px] text-emerald-600"></i> Finalizada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Título de la Inducción y Facilitador -->
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm leading-snug font-serif">
                                {{ $ses->induccion->titulo }}
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-tie text-slate-400 text-[10px]"></i>
                                <span>{{ $ses->facilitador->nombre_completo ?? 'Facilitador' }}</span>
                            </p>
                        </div>

                        <!-- Información en Cuadrícula: Sede, Asistentes y Áreas -->
                        <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50/70 p-3 rounded-2xl border border-slate-200/80">
                            <div>
                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Sede</span>
                                <div class="font-semibold text-slate-800 text-[11px] flex items-center gap-1 truncate">
                                    <i class="fa-solid fa-location-dot text-brand-blue text-[10px] shrink-0"></i>
                                    <span class="truncate">{{ $ses->sede->nombre ?? 'Sede Principal' }}</span>
                                </div>
                            </div>

                            <div>
                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 text-right">Asistentes</span>
                                <div class="flex justify-end">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->asistencias_count }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-span-2 pt-2 border-t border-slate-200/60">
                                <span class="text-[9.5px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Áreas Dictadas</span>
                                <div class="flex flex-wrap gap-1 items-center">
                                    @php
                                        $areasConvocadas = $ses->areas_convocadas_nombres_array ?? [];
                                    @endphp
                                    @if(empty($areasConvocadas))
                                        <span class="inline-flex text-[10px] text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                            Todas las áreas
                                        </span>
                                    @else
                                        @foreach($areasConvocadas as $nomArea)
                                            <span class="inline-flex text-[10px] font-semibold text-brand-dark bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">
                                                {{ $nomArea }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción Táctiles (Mobile) -->
                        <div class="pt-1">
                            @if($ses->estaActiva())
                                <a href="{{ route('induccion.presentacion', ['codigo' => $ses->codigo_sesion]) }}"
                                   class="w-full py-2.5 px-4 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-xs text-center cursor-pointer">
                                    <i class="fa-solid fa-play text-xs"></i>
                                    <span>Continuar Presentación en Vivo</span>
                                </a>
                            @else
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="abrirModalDetalles('{{ $ses->codigo_sesion }}')"
                                            class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue font-bold text-xs flex items-center justify-center gap-1.5 border border-slate-200 transition-all cursor-pointer shadow-2xs">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detalles</span>
                                    </button>
                                    <a href="{{ route('induccion.acta.pdf', ['codigo' => $ses->codigo_sesion]) }}"
                                       class="py-2.5 px-3 rounded-xl bg-blue-50 hover:bg-brand-blue text-brand-blue hover:text-white font-bold text-xs flex items-center justify-center gap-1.5 border border-blue-200 hover:border-transparent transition-all shadow-2xs text-center">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                        <span>Acta (PDF)</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 2. VISTA ESCRITORIO / TABLET: TABLA COMPLETA (hidden md:block) -->
            <div class="hidden md:block overflow-hidden bg-white border border-slate-200 rounded-3xl shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                            <tr>
                                <th class="py-3.5 px-4">Código Sesión</th>
                                <th class="py-3.5 px-4">Inducción</th>
                                <th class="py-3.5 px-4">Facilitador</th>
                                <th class="py-3.5 px-4">Sede y Áreas Dictadas</th>
                                <th class="py-3.5 px-4 text-center">Asistentes</th>
                                <th class="py-3.5 px-4">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($sesionesRecientes as $ses)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-brand-blue text-xs">
                                        {{ $ses->codigo_sesion }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                                        <div class="truncate max-w-xs sm:max-w-sm">{{ $ses->induccion->titulo }}</div>
                                        <div class="text-[11px] text-slate-400 font-normal">
                                            {{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600">
                                        {{ $ses->facilitador->nombre_completo ?? 'Facilitador' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-800 flex items-center gap-1.5 text-xs">
                                            <i class="fa-solid fa-location-dot text-brand-blue text-[11px]"></i>
                                            <span>{{ $ses->sede->nombre ?? 'Sede Principal' }}</span>
                                        </div>
                                        <div class="mt-1 flex flex-wrap gap-1 items-center">
                                            @php
                                                $areasConvocadas = $ses->areas_convocadas_nombres_array ?? [];
                                            @endphp
                                            @if(empty($areasConvocadas))
                                                <span class="inline-flex text-[10px] text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                                    Todas las áreas
                                                </span>
                                            @else
                                                @foreach(array_slice($areasConvocadas, 0, 2) as $nomArea)
                                                    <span class="inline-flex text-[10px] font-semibold text-brand-dark bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">
                                                        {{ $nomArea }}
                                                    </span>
                                                @endforeach
                                                @if(count($areasConvocadas) > 2)
                                                    <span class="inline-flex text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-md border border-slate-200 cursor-help" title="{{ implode(', ', $areasConvocadas) }}">
                                                        +{{ count($areasConvocadas) - 2 }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                            <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->asistencias_count }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($ses->estaActiva())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En Curso
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <i class="fa-solid fa-check text-[9px] text-emerald-600"></i> Finalizada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($ses->estaActiva())
                                                <a href="{{ route('induccion.presentacion', ['codigo' => $ses->codigo_sesion]) }}"
                                                   class="px-3 py-1.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs flex items-center gap-1.5 transition-all shadow-2xs"
                                                   title="Continuar presentación en vivo">
                                                    <i class="fa-solid fa-play text-[10px]"></i>
                                                    <span>En Vivo</span>
                                                </a>
                                            @else
                                                <button type="button" onclick="abrirModalDetalles('{{ $ses->codigo_sesion }}')"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue font-bold text-xs flex items-center gap-1.5 border border-slate-200 transition-all cursor-pointer shadow-2xs"
                                                        title="Ver Sede, Áreas y Participantes">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                    <span>Detalles</span>
                                                </button>
                                                <a href="{{ route('induccion.acta.pdf', ['codigo' => $ses->codigo_sesion]) }}"
                                                   class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-brand-blue text-brand-blue hover:text-white font-bold text-xs flex items-center gap-1.5 border border-blue-200 hover:border-transparent transition-all shadow-2xs"
                                                   title="Descargar Acta Oficial PDF">
                                                    <i class="fa-solid fa-file-pdf text-xs"></i>
                                                    <span>Acta (PDF)</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal para Visualizar Contenidos y Elementos de la Inducción -->
<div id="modalVisualizarInduccion" onclick="if (event.target === this) cerrarModalVisualizar();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full shadow-2xl relative animate-fade-in max-h-[92vh] flex flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Explorador de Contenidos</span>
                    <h3 id="vis-titulo" class="text-base sm:text-lg font-bold text-slate-900 leading-snug font-serif"></h3>
                </div>
            </div>
            <button type="button" onclick="cerrarModalVisualizar()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center cursor-pointer transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto flex-1 p-5 sm:p-6 space-y-5">
            <!-- Portada y Banner descriptivo -->
            <div class="relative rounded-2xl overflow-hidden h-44 bg-slate-900 shadow-xs border border-slate-200">
                <img id="vis-portada" src="" alt="" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent p-4 flex flex-col justify-end">
                    <div id="vis-badges-recursos" class="flex flex-wrap items-center gap-1.5 mb-2">
                        <!-- Badges dinámicos de recursos -->
                    </div>
                    <div class="text-xs text-slate-300 flex flex-wrap items-center gap-3">
                        <span id="vis-creador" class="flex items-center gap-1">
                            <i class="fa-solid fa-user-tie text-brand-blue"></i>
                            <span></span>
                        </span>
                        <span>&bull;</span>
                        <span id="vis-fecha" class="flex items-center gap-1">
                            <i class="fa-solid fa-calendar-day text-slate-400"></i>
                            <span></span>
                        </span>
                        <span>&bull;</span>
                        <span id="vis-sesiones" class="flex items-center gap-1 text-emerald-300 font-semibold">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Descripción y Objetivos -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-brand-blue"></i>
                    <span>Descripción y Objetivos de la Inducción</span>
                </h4>
                <p id="vis-descripcion" class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"></p>
            </div>


            <!-- Lista de Recursos Multimedia y Materiales -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-brand-blue"></i>
                        <span>Elementos y Materiales de Apoyo (<span id="vis-contador-recursos">0</span>)</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Videos, diapositivas y guías de esta inducción</span>
                </div>

                <div id="vis-contenedor-recursos" class="space-y-3">
                    <!-- Tarjetas dinámicas de recursos -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <button type="button" onclick="cerrarModalVisualizar()"
                    class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer">
                Cerrar
            </button>

            <button type="button" id="btn-iniciar-desde-visualizador"
                    class="px-6 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white text-xs font-bold uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-play text-xs"></i>
                <span>Iniciar Esta Inducción</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal para Configurar e Iniciar Inducción en Vivo -->
<div id="modalIniciarSesion" onclick="if (event.target === this) cerrarModalIniciar();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-brand-blue text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-play"></i> Iniciar Inducción
            </div>
            <button type="button" onclick="cerrarModalIniciar()" class="text-slate-400 hover:text-slate-600 text-lg cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 id="modalInduccionTitulo" class="text-lg font-bold text-slate-900 mb-1 leading-snug font-serif"></h3>
        <p class="text-xs text-slate-500 mb-5 leading-relaxed">
            Configura la sede y las áreas convocadas a las que se les dictará la inducción. Solo los colaboradores pertenecientes a estas áreas podrán firmar asistencia.
        </p>

        <form id="formIniciarSesion" method="POST" action="" class="space-y-4">
            @csrf

            <!-- Selección de Sede -->
            <div>
                <label for="sede_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    <i class="fa-solid fa-location-dot text-brand-blue mr-1.5"></i> Sede de la Inducción <span class="text-rose-500">*</span>
                </label>
                <select name="sede_id" id="sede_id" required onchange="filtrarAreasModal(this.value)"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-slate-800 text-sm focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20">
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }} ({{ $s->ciudad }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Áreas Convocadas -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        <i class="fa-solid fa-sitemap text-brand-blue mr-1.5"></i> Áreas Convocadas
                    </label>
                    <button type="button" onclick="toggleTodasAreasModal()" id="btnToggleAreasModal"
                            class="text-[11px] font-bold text-brand-blue hover:underline cursor-pointer">
                        Desmarcar todas
                    </button>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 max-h-44 overflow-y-auto space-y-1 text-xs" id="contenedorAreasModal">
                    <div class="text-[11px] text-slate-500 pb-1 mb-1 border-b border-slate-200">
                        Marca las áreas citadas a esta inducción:
                    </div>
                    @foreach($areas as $a)
                        <label class="area-item-modal flex items-center gap-2 p-1.5 rounded-xl hover:bg-white cursor-pointer text-slate-700 transition-colors" data-sede-id="{{ $a->sede_id ?? '' }}">
                            <input type="checkbox" name="areas_convocadas[]" value="{{ $a->id }}" checked
                                   class="area-checkbox-modal w-4 h-4 rounded text-brand-blue focus:ring-brand-blue bg-white border-slate-300">
                            <span class="truncate">{{ $a->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Si dejas todas desmarcadas, aplicará para cualquier colaborador de la sede seleccionada.</p>
            </div>

            <!-- Opción para Personal Externo -->
            <div class="pt-1">
                <label class="flex items-start gap-3 p-3 rounded-2xl bg-amber-50/80 border border-amber-200 cursor-pointer text-xs text-amber-900 transition-colors hover:bg-amber-100/70">
                    <input type="checkbox" name="permitir_externos" value="1" checked
                           class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-amber-300 mt-0.5">
                    <div>
                        <span class="font-bold block">Permitir registro de personal externo</span>
                        <span class="text-[11px] text-amber-800 leading-tight block mt-0.5">
                            Si se habilita, personas no registradas en nómina podrán llenar sus datos básicos y firmar como personal externo.
                        </span>
                    </div>
                </label>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalIniciar()"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white text-xs font-bold tracking-wider uppercase shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-play text-xs"></i>
                    <span>Iniciar Inducción</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Completo de Detalles de la Inducción Finalizada -->
<div id="modalDetallesSesion" onclick="if (event.target === this) cerrarModalDetalles();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-4xl w-full p-5 sm:p-7 shadow-2xl relative animate-fade-in max-h-[92vh] flex flex-col overflow-hidden">
        
        <!-- Header del Modal -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-100 shrink-0 gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span id="modalDetallesBadgeEstado" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                        Finalizada
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-500">
                        Código: <strong id="modalDetallesCodigoTexto" class="text-brand-blue select-all"></strong>
                    </span>
                </div>
                <h3 id="modalDetallesTituloTexto" class="text-lg sm:text-xl font-black text-slate-900 font-heading leading-snug"></h3>
            </div>
            <button type="button" onclick="cerrarModalDetalles()" class="text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer shrink-0">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Contenido con scroll -->
        <div class="flex-1 overflow-y-auto py-4 space-y-5">
            
            <!-- Resumen Informativo: Sede, Áreas, Facilitador y Horarios -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <!-- Sede y Áreas convocadas -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Sede Dictada</span>
                        <div class="font-bold text-slate-900 flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-location-dot text-brand-blue"></i>
                            <span id="modalDetallesSedeTexto"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Áreas a las que se dio esta inducción</span>
                        <div id="modalDetallesAreasContainer" class="flex flex-wrap gap-1.5">
                            <!-- Se puebla dinámicamente -->
                        </div>
                    </div>
                </div>

                <!-- Facilitador y Horario -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Facilitador Responsable</span>
                        <div class="font-bold text-slate-900 flex items-center gap-1.5 text-xs">
                            <i class="fa-solid fa-chalkboard-user text-brand-blue"></i>
                            <span id="modalDetallesFacilitadorTexto"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/60">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Inicio</span>
                            <div id="modalDetallesFechaInicioTexto" class="font-medium text-slate-800 text-[11px] mt-0.5"></div>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Cierre</span>
                            <div id="modalDetallesFechaCierreTexto" class="font-medium text-slate-800 text-[11px] mt-0.5"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fila de Tarjetas / Métricas de Asistencia -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-blue-50/50 p-3.5 rounded-2xl border border-blue-200">
                    <div class="text-[10px] font-bold text-slate-500 uppercase">Total Asistentes</div>
                    <div id="modalDetallesTotalAsistentes" class="text-2xl font-black text-brand-blue font-mono mt-0.5">0</div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                    <div class="text-[10px] font-bold text-slate-500 uppercase">Colaboradores PNY</div>
                    <div id="modalDetallesTotalInternos" class="text-2xl font-black text-slate-800 font-mono mt-0.5">0</div>
                </div>
                <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                    <div class="text-[10px] font-bold text-slate-500 uppercase">Personal Externo</div>
                    <div id="modalDetallesTotalExternos" class="text-2xl font-black text-amber-700 font-mono mt-0.5">0</div>
                </div>
                <div class="bg-emerald-50/60 p-3.5 rounded-2xl border border-emerald-200">
                    <div class="text-[10px] font-bold text-emerald-800 uppercase">Firmas Digitales</div>
                    <div class="text-2xl font-black text-emerald-700 font-mono mt-0.5">100%</div>
                </div>
            </div>

            <!-- Listado Oficial de Participantes que Asistieron -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users text-brand-blue"></i>
                        <span>Participantes que asistieron a esta inducción</span>
                    </div>
                    <div class="relative w-full sm:w-60">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="filtroParticipantesModal" onkeyup="filtrarParticipantesModal()" placeholder="Buscar participante..."
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-blue shadow-2xs">
                    </div>
                </div>

                <div class="max-h-60 overflow-y-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] sticky top-0 border-b border-slate-200">
                            <tr>
                                <th class="py-2 px-3 text-center w-10">#</th>
                                <th class="py-2 px-3">Participante</th>
                                <th class="py-2 px-3">Documento</th>
                                <th class="py-2 px-3">Cargo / Área</th>
                                <th class="py-2 px-3 text-center">Hora</th>
                                <th class="py-2 px-3 text-center">Firma Digital</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaParticipantesModal" class="divide-y divide-slate-100 text-slate-700">
                            <!-- Filas dinámicas -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Footer del Modal: Descarga y Cierre -->
        <div class="pt-4 border-t border-slate-100 shrink-0 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-[11px] text-slate-500">
                Acta oficial con firmas digitales capturadas lista para archivo o auditoría.
            </div>
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button type="button" onclick="cerrarModalDetalles()"
                        class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider cursor-pointer border border-slate-200 transition-colors">
                    Cerrar
                </button>
                <a id="modalDetallesBtnDescargaActa" href="#" target="_blank"
                   class="flex-1 sm:flex-none px-5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 shadow-sm transition-all">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Descargar Acta (PDF)</span>
                </a>
            </div>
        </div>

    </div>
</div>

<!-- Modal Zoom Firma Digital -->
<div id="modalZoomFirma" onclick="if (event.target === this) cerrarModalFirma();" class="fixed inset-0 z-60 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-sm w-full p-6 text-center relative shadow-2xl animate-fade-in">
        <button type="button" onclick="cerrarModalFirma()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h4 class="text-sm font-bold text-slate-900 mb-1" id="modalFirmaNombre">Firma Digital</h4>
        <p class="text-[11px] text-slate-500 mb-3">Firma digital estampada en el celular</p>
        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center min-h-[140px]">
            <img id="modalFirmaImg" src="" alt="Firma" class="max-h-36 max-w-full object-contain">
        </div>
        <button type="button" onclick="cerrarModalFirma()" class="mt-4 px-5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">
            Cerrar
        </button>
    </div>
</div>

@include('induccion._modal_visor_pptx')

@push('scripts')
<script>
    const catalogoInducciones = {!! json_encode($detalleInducciones) !!};
    let visualizandoInduccionId = null;

    function abrirModalVisualizar(induccionId) {
        const ind = catalogoInducciones[induccionId];
        if (!ind) return;

        visualizandoInduccionId = induccionId;
        document.getElementById('vis-titulo').textContent = ind.titulo;
        document.getElementById('vis-portada').src = ind.imagen_portada_url;
        document.getElementById('vis-creador').querySelector('span').textContent = 'Por: ' + ind.creador;
        document.getElementById('vis-fecha').querySelector('span').textContent = ind.fecha_creacion;
        document.getElementById('vis-sesiones').querySelector('span').textContent = ind.sesiones_count + ' sesiones dictadas';
        document.getElementById('vis-descripcion').textContent = ind.descripcion || 'Sin descripción detallada registrada para esta inducción.';

        // Badges en banner
        const badgesCont = document.getElementById('vis-badges-recursos');
        badgesCont.innerHTML = '';
        if (ind.tiene_pptx) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-orange-600/90 text-white text-[10px] font-bold"><i class="fa-solid fa-file-powerpoint mr-1"></i> Presentación PPTX</span>`;
        if (ind.tiene_video) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-rose-600/90 text-white text-[10px] font-bold"><i class="fa-brands fa-youtube mr-1"></i> Video Explicativo</span>`;
        if (ind.tiene_pdf) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-red-600/90 text-white text-[10px] font-bold"><i class="fa-solid fa-file-pdf mr-1"></i> Documentos PDF</span>`;
        if (ind.tiene_imagen) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-emerald-600/90 text-white text-[10px] font-bold"><i class="fa-solid fa-image mr-1"></i> Infografías</span>`;
        if (ind.tiene_texto) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-indigo-600/90 text-white text-[10px] font-bold"><i class="fa-solid fa-file-lines mr-1"></i> Contenido de Texto</span>`;
        if (!ind.tiene_pptx && !ind.tiene_video && !ind.tiene_pdf && !ind.tiene_imagen && !ind.tiene_texto) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-slate-800/80 text-slate-200 text-[10px] font-bold">Presencial</span>`;


        // Recursos
        const recursosCont = document.getElementById('vis-contenedor-recursos');
        const contadorRecursos = document.getElementById('vis-contador-recursos');
        recursosCont.innerHTML = '';

        const recs = ind.recursos || [];
        contadorRecursos.textContent = recs.length;

        if (recs.length === 0) {
            recursosCont.innerHTML = `
                <div class="p-6 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-500">
                    <i class="fa-solid fa-chalkboard text-slate-300 text-2xl mb-2 block"></i>
                    Esta inducción está configurada para impartirse de manera presencial con el facilitador en sala.
                </div>
            `;
        } else {
            recs.forEach((r, idx) => {
                let iconHtml = '';
                let tipoBadge = '';
                let actionHtml = '';
                let mediaPreviewHtml = '';

                if (r.tipo === 'video') {
                    iconHtml = '<i class="fa-brands fa-youtube text-red-600 text-lg"></i>';
                    tipoBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-red-100 text-red-700">Video</span>';
                    if (r.embed_url) {
                        mediaPreviewHtml = `
                            <div class="mt-3 relative w-full aspect-video rounded-xl overflow-hidden border border-slate-200 bg-black shadow-inner">
                                <iframe src="${r.embed_url}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                            </div>
                        `;
                    }
                    actionHtml = r.url ? `<a href="${r.url}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-700 hover:text-red-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-brands fa-youtube"></i><span>Abrir en YouTube</span></a>` : '';
                } else if (r.tipo === 'pptx') {
                    iconHtml = '<i class="fa-solid fa-file-powerpoint text-orange-600 text-lg"></i>';
                    tipoBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-orange-100 text-orange-700">Diapositivas PPTX</span>';
                    actionHtml = '';
                    if (r.archivo || ind.tiene_pptx) {
                        actionHtml += `<button type="button" onclick="abrirVisorPptxModal(${ind.id}, '${encodeURIComponent(r.archivo || '')}', '${encodeURIComponent(r.titulo || ind.titulo)}')" class="px-3.5 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:scale-95 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer"><i class="fa-solid fa-play"></i><span>Ver Diapositivas</span></button>`;
                    }
                    if (r.archivo_url) {
                        actionHtml += `<a href="${r.archivo_url}" download class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-solid fa-download"></i><span>Descargar PPTX</span></a>`;
                    }
                    if (r.embed_url && (r.embed_url.includes('google') || r.embed_url.includes('docs.google'))) {
                        actionHtml += `<a href="${r.embed_url}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Google Slides</span></a>`;
                    }
                } else if (r.tipo === 'pdf') {
                    iconHtml = '<i class="fa-solid fa-file-pdf text-rose-600 text-lg"></i>';
                    tipoBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-rose-100 text-rose-700">Documento PDF</span>';
                    actionHtml = r.archivo_url ? `<a href="${r.archivo_url}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Abrir / Descargar PDF</span></a>` : '';
                } else if (r.tipo === 'imagen') {
                    iconHtml = '<i class="fa-solid fa-image text-emerald-600 text-lg"></i>';
                    tipoBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-700">Infografía / Imagen</span>';
                    if (r.archivo_url) {
                        mediaPreviewHtml = `
                            <div class="mt-3 rounded-xl overflow-hidden border border-slate-200 max-h-60 bg-slate-100 flex items-center justify-center">
                                <img src="${r.archivo_url}" alt="${r.titulo}" class="max-h-60 w-auto object-contain">
                            </div>
                        `;
                        actionHtml = `<a href="${r.archivo_url}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-solid fa-magnifying-glass-plus"></i><span>Ver a tamaño completo</span></a>`;
                    }
                } else if (r.tipo === 'texto') {
                    iconHtml = '<i class="fa-solid fa-file-lines text-indigo-600 text-lg"></i>';
                    tipoBadge = '<span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-indigo-100 text-indigo-700">Texto / Guía</span>';
                    if (r.texto) {
                        mediaPreviewHtml = `
                            <div class="mt-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line max-h-48 overflow-y-auto">
                                ${r.texto}
                            </div>
                        `;
                    }
                }

                recursosCont.innerHTML += `
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100">
                                    ${iconHtml}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-xs font-bold text-slate-900">${r.titulo}</h5>
                                        ${tipoBadge}
                                    </div>
                                    <span class="text-[10px] text-slate-400">Elemento multimedia #${idx + 1}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                ${actionHtml}
                            </div>
                        </div>
                        ${mediaPreviewHtml}
                    </div>
                `;
            });
        }

        // Botón iniciar desde visualizador
        const btnIniciar = document.getElementById('btn-iniciar-desde-visualizador');
        btnIniciar.onclick = function() {
            cerrarModalVisualizar();
            abrirModalIniciar(ind.id, ind.titulo);
        };

        document.getElementById('modalVisualizarInduccion').classList.remove('hidden');
    }

    function cerrarModalVisualizar() {
        // Detener reproducciones de video al cerrar
        const iframes = document.querySelectorAll('#modalVisualizarInduccion iframe');
        iframes.forEach(f => f.src = f.src);

        document.getElementById('modalVisualizarInduccion').classList.add('hidden');
        visualizandoInduccionId = null;
    }

    function abrirModalIniciar(induccionId, titulo) {
        document.getElementById('modalInduccionTitulo').textContent = titulo;
        document.getElementById('formIniciarSesion').action = `/induccion/${induccionId}/iniciar`;
        document.getElementById('modalIniciarSesion').classList.remove('hidden');
        const sedeSelect = document.getElementById('sede_id');
        if (sedeSelect) {
            filtrarAreasModal(sedeSelect.value);
        }
    }

    function cerrarModalIniciar() {
        document.getElementById('modalIniciarSesion').classList.add('hidden');
    }

    let todasMarcadas = true;
    function toggleTodasAreasModal() {
        const checkboxes = document.querySelectorAll('.area-checkbox-modal:not([disabled])');
        todasMarcadas = !todasMarcadas;
        checkboxes.forEach(cb => {
            if (cb.closest('.area-item-modal').style.display !== 'none') {
                cb.checked = todasMarcadas;
            }
        });
        const btn = document.getElementById('btnToggleAreasModal');
        if (btn) btn.textContent = todasMarcadas ? 'Desmarcar todas' : 'Marcar todas';
    }

    function filtrarAreasModal(sedeId) {
        const items = document.querySelectorAll('.area-item-modal');
        let countVisible = 0;
        items.forEach(it => {
            const itemSedeId = it.dataset.sedeId;
            if (!itemSedeId || itemSedeId === '' || itemSedeId == sedeId) {
                it.style.display = 'flex';
                const cb = it.querySelector('.area-checkbox-modal');
                if (cb) cb.checked = true;
                countVisible++;
            } else {
                it.style.display = 'none';
                const cb = it.querySelector('.area-checkbox-modal');
                if (cb) cb.checked = false;
            }
        });
        todasMarcadas = true;
        const btn = document.getElementById('btnToggleAreasModal');
        if (btn) btn.textContent = 'Desmarcar todas';
    }

    // ==========================================
    // MODAL DE DETALLES DE SESIÓN FINALIZADA
    // ==========================================
    let listaParticipantesModalCache = [];

    async function abrirModalDetalles(codigo) {
        document.getElementById('modalDetallesCodigoTexto').textContent = codigo;
        document.getElementById('modalDetallesTituloTexto').textContent = 'Cargando detalles de la inducción...';
        document.getElementById('modalDetallesAreasContainer').innerHTML = '<span class="text-slate-400 text-xs italic">Cargando áreas...</span>';
        document.getElementById('modalDetallesSedeTexto').textContent = '...';
        document.getElementById('modalDetallesFacilitadorTexto').textContent = '...';
        document.getElementById('modalDetallesFechaInicioTexto').textContent = '-';
        document.getElementById('modalDetallesFechaCierreTexto').textContent = '-';
        document.getElementById('cuerpoTablaParticipantesModal').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-slate-400"><i class="fa-solid fa-circle-notch fa-spin text-base mr-2 text-brand-blue"></i>Cargando participantes...</td></tr>';
        document.getElementById('modalDetallesSesion').classList.remove('hidden');

        try {
            const response = await fetch(`/induccion/sesion/${codigo}/detalles`);
            if (!response.ok) throw new Error('No se pudo cargar la información');
            const data = await response.json();

            document.getElementById('modalDetallesTituloTexto').textContent = data.induccion.titulo;
            document.getElementById('modalDetallesSedeTexto').textContent = `${data.sede.nombre} ${data.sede.ciudad ? '(' + data.sede.ciudad + ')' : ''}`;
            document.getElementById('modalDetallesFacilitadorTexto').textContent = data.facilitador;
            document.getElementById('modalDetallesFechaInicioTexto').textContent = data.fecha_inicio || '-';
            document.getElementById('modalDetallesFechaCierreTexto').textContent = data.fecha_cierre || 'En curso';
            document.getElementById('modalDetallesTotalAsistentes').textContent = data.total_asistentes;
            document.getElementById('modalDetallesTotalInternos').textContent = data.total_internos;
            document.getElementById('modalDetallesTotalExternos').textContent = data.total_externos;
            document.getElementById('modalDetallesBtnDescargaActa').href = data.acta_url;

            const badgeEstado = document.getElementById('modalDetallesBadgeEstado');
            if (badgeEstado) {
                if (data.esta_activa) {
                    badgeEstado.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 animate-pulse";
                    badgeEstado.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1"></span> En Curso';
                } else {
                    badgeEstado.className = "px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200";
                    badgeEstado.innerHTML = '<i class="fa-solid fa-check text-[9px] text-emerald-600 mr-1"></i> Finalizada';
                }
            }

            // Áreas convocadas
            const areasCont = document.getElementById('modalDetallesAreasContainer');
            if (data.todas_las_areas || data.areas_convocadas.length === 0) {
                areasCont.innerHTML = '<span class="inline-flex text-[11px] font-semibold text-slate-600 bg-white px-2.5 py-1 rounded-lg border border-slate-200"><i class="fa-solid fa-sitemap text-brand-blue mr-1"></i> Todas las áreas convocadas</span>';
            } else {
                areasCont.innerHTML = data.areas_convocadas.map(a => 
                    `<span class="inline-flex items-center text-[11px] font-bold text-brand-dark bg-white px-2.5 py-1 rounded-lg border border-blue-200 shadow-2xs"><i class="fa-solid fa-tag text-brand-blue text-[9px] mr-1"></i> ${a}</span>`
                ).join('');
            }

            // Participantes
            listaParticipantesModalCache = data.asistencias || [];
            renderizarTablaParticipantesModal(listaParticipantesModalCache);

        } catch (err) {
            console.error(err);
            document.getElementById('modalDetallesTituloTexto').textContent = 'Error al cargar detalles';
            document.getElementById('cuerpoTablaParticipantesModal').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-rose-500">Ocurrió un error al consultar los detalles de la sesión.</td></tr>';
        }
    }

    function cerrarModalDetalles() {
        document.getElementById('modalDetallesSesion').classList.add('hidden');
        if (window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    function renderizarTablaParticipantesModal(asistentes) {
        const tbody = document.getElementById('cuerpoTablaParticipantesModal');
        if (!tbody) return;

        if (asistentes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-slate-400">No se registraron participantes en esta sesión.</td></tr>';
            return;
        }

        tbody.innerHTML = asistentes.map((a, idx) => {
            const ini = (a.nombre || '').split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
            const firma = a.firma_url 
                ? `<button type="button" onclick="verFirmaModal('${a.firma_url}', '${(a.nombre || '').replace(/'/g, "\\'")}')" class="inline-block p-1 bg-white rounded-lg border border-slate-200 hover:border-brand-blue shadow-2xs transition-all cursor-pointer group" title="Ver firma ampliada">
                     <img src="${a.firma_url}" alt="Firma" class="h-7 max-w-[80px] object-contain mx-auto group-hover:scale-105 transition-transform">
                   </button>`
                : '<span class="text-[10px] text-slate-400 italic">Sin firma</span>';

            const externoBadge = a.es_externo 
                ? `<span class="inline-flex text-[9px] font-bold text-amber-700 bg-amber-50 px-1 py-0.2 rounded border border-amber-200 ml-1">[EXT]</span>`
                : '';

            return `
                <tr class="hover:bg-blue-50/30 transition-colors fila-modal-asistente">
                    <td class="py-2.5 px-3 font-mono font-bold text-slate-400 text-center text-xs">${idx + 1}</td>
                    <td class="py-2.5 px-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-brand-blue text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                                ${ini}
                            </div>
                            <div class="font-bold text-slate-900 text-xs truncate max-w-[180px] part-nombre">${a.nombre} ${externoBadge}</div>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 font-mono font-bold text-brand-blue text-xs part-doc">${a.identificacion}</td>
                    <td class="py-2.5 px-3">
                        <div class="text-slate-800 text-xs">${a.cargo || 'Colaborador'}</div>
                        <div class="text-[10px] text-slate-400">${a.area || 'General'}</div>
                    </td>
                    <td class="py-2.5 px-3 text-center font-mono text-slate-500 text-xs">${a.hora || '-'}</td>
                    <td class="py-2.5 px-3 text-center">${firma}</td>
                </tr>
            `;
        }).join('');
    }

    function filtrarParticipantesModal() {
        const query = (document.getElementById('filtroParticipantesModal')?.value || '').toLowerCase().trim();
        const filas = document.querySelectorAll('.fila-modal-asistente');
        filas.forEach(f => {
            const nom = (f.querySelector('.part-nombre')?.textContent || '').toLowerCase();
            const doc = (f.querySelector('.part-doc')?.textContent || '').toLowerCase();
            if (!query || nom.includes(query) || doc.includes(query)) {
                f.style.display = '';
            } else {
                f.style.display = 'none';
            }
        });
    }

    function verFirmaModal(firmaUrl, nombre) {
        document.getElementById('modalFirmaImg').src = firmaUrl;
        document.getElementById('modalFirmaNombre').textContent = `Firma de ${nombre}`;
        document.getElementById('modalZoomFirma').classList.remove('hidden');
    }

    function cerrarModalFirma() {
        document.getElementById('modalZoomFirma').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const verSesion = urlParams.get('ver_sesion');
        if (verSesion) {
            abrirModalDetalles(verSesion);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            cerrarModalVisualizar();
            cerrarModalIniciar();
            cerrarModalFirma();
            cerrarModalDetalles();
            cerrarVisorPptxModal();
        }
    });
</script>
@endpush
@endsection
