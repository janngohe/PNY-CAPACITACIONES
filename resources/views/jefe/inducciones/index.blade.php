@extends('layouts.jefe')

@section('title', 'Inducciones y Código PIN')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-line">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                    <i class="fa-solid fa-chalkboard-user mr-1"></i> Módulo de Inducciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-brand-dark tracking-tight font-serif">Inducciones Presenciales y Código PIN</h1>
            <p class="text-xs text-slate-500">Consulta el PIN de acceso vigente para coordinar inducciones en tu área o proyectarlas en sala.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Portal Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Tarjeta Destacada con el PIN de 4 Dígitos -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-3">
                    <i class="fa-solid fa-key"></i> Clave de Facilitador
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">PIN Vigente para Inducciones</h2>
                <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                    Comparte este código de 4 dígitos con cualquier colaborador o facilitador de tu equipo que vaya a dictar una charla o inducción.
                    Solo necesitarán ingresar su <strong>cédula</strong> y este <strong>PIN</strong> en la pantalla de inducción.
                </p>
            </div>

            <!-- Caja Visual del PIN -->
            <div class="flex items-center gap-4 bg-slate-950/70 p-4 sm:p-5 rounded-2xl border border-slate-700/80">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PIN Oficial Actual</div>
                    <div class="font-mono text-3xl font-black tracking-widest text-cyan-400 select-all mt-1">
                        {{ $pinActual }}
                    </div>
                </div>

                <div class="border-l border-slate-800 pl-4">
                    <button type="button" onclick="copiarPin('{{ $pinActual }}')"
                            id="btnCopiarPin"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copiar</span>
                    </button>
                    <div class="text-[9px] text-slate-500 mt-1 text-center">4 dígitos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Catálogo de Inducciones Aplicables a tu Área (Pase Directo de una en una) -->
    <div class="bg-white rounded-3xl border border-line/90 p-5 sm:p-6 shadow-2xs space-y-4 relative overflow-hidden" id="carruselInduccionesWrapper">
        <!-- Encabezado con Título y Controles -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-line/70">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-brand-light text-brand-blue border border-brand-blue/20">
                        <i class="fa-solid fa-chalkboard-user text-[9px] text-brand-blue"></i> Catálogo de Inducciones
                    </span>
                    <span class="text-xs text-slate-400">• Navegación individual</span>
                </div>
                <h3 class="text-base sm:text-lg font-heading font-extrabold text-brand-dark flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i> Inducciones Institucionales Disponibles
                </h3>
                <p class="text-xs text-slate-500">
                    Pasa de una en una entre las inducciones oficiales disponibles para tu área y proyecta la que necesites.
                </p>
            </div>

            <!-- Controles del Slider (Flechas y Contador Directo) -->
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <span id="carruselIndicadorIndice" class="text-xs font-mono font-bold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200/80">
                    Inducción 1 de {{ $inducciones->count() }}
                </span>

                <div class="flex items-center gap-1 bg-slate-100/90 p-1 rounded-2xl border border-line/80">
                    <button type="button"
                            onclick="moverCarrusel(-1)"
                            title="Inducción anterior"
                            class="w-9 h-9 rounded-xl bg-white hover:bg-brand-blue hover:text-white text-slate-700 flex items-center justify-center transition-all shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <button type="button"
                            onclick="moverCarrusel(1)"
                            title="Siguiente inducción"
                            class="w-9 h-9 rounded-xl bg-white hover:bg-brand-blue hover:text-white text-slate-700 flex items-center justify-center transition-all shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        @if($inducciones->isEmpty())
            <div class="p-10 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i class="fa-solid fa-chalkboard-user text-3xl text-slate-300 mb-2"></i>
                <p class="font-bold text-slate-600">No hay inducciones institucionales activas en este momento.</p>
            </div>
        @else
            <!-- Contenedor con overflow hidden para el slider de una en una -->
            <div class="relative overflow-hidden py-1" id="carruselViewport">
                <!-- Flechas Flotantes Laterales -->
                <button type="button"
                        onclick="moverCarrusel(-1)"
                        title="Inducción anterior"
                        class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-2xl bg-white/90 hover:bg-brand-blue text-slate-700 hover:text-white border border-line shadow-md backdrop-blur-xs flex items-center justify-center transition-all hover:scale-105 cursor-pointer">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>

                <button type="button"
                        onclick="moverCarrusel(1)"
                        title="Siguiente inducción"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-2xl bg-white/90 hover:bg-brand-blue text-slate-700 hover:text-white border border-line shadow-md backdrop-blur-xs flex items-center justify-center transition-all hover:scale-105 cursor-pointer">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>

                <!-- Pista deslizable (Track) -->
                <div id="carruselTrack"
                     class="flex transition-transform duration-300 ease-out select-none will-change-transform"
                     style="transform: translateX(0%);">
                    @foreach($inducciones as $index => $ind)
                        <div class="carrusel-slide w-full min-w-full shrink-0 px-1"
                             data-index="{{ $index }}">
                            <div class="bg-white rounded-3xl border border-line/90 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden grid grid-cols-1 md:grid-cols-12 group">
                                <!-- Columna Izquierda: Fotico de la Inducción (5/12 en desktop) -->
                                <div class="md:col-span-5 relative h-56 sm:h-64 md:h-auto md:min-h-[280px] bg-slate-900 overflow-hidden">
                                    <img src="{{ $ind->imagen_portada_url }}"
                                         alt="{{ $ind->titulo }}"
                                         loading="lazy"
                                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    
                                    <!-- Gradiente protector inferior -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>

                                    <!-- Insignia Superior Izquierda (Número) -->
                                    <div class="absolute top-3.5 left-3.5 flex items-center gap-1.5 z-10">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider bg-slate-900/85 text-cyan-300 border border-white/15 backdrop-blur-xs shadow-xs">
                                            <i class="fa-solid fa-chalkboard-user text-[9px]"></i> Inducción #{{ $index + 1 }}
                                        </span>
                                    </div>

                                    <!-- Iconos de Recursos Superior Derecha -->
                                    <div class="absolute top-3.5 right-3.5 flex items-center gap-1 bg-slate-900/80 backdrop-blur-xs p-1 rounded-xl border border-white/10 shadow-xs z-10">
                                        @if($ind->video_url)
                                            <span class="w-6 h-6 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-xs" title="Video explicativo">
                                                <i class="fa-brands fa-youtube"></i>
                                            </span>
                                        @endif
                                        @if($ind->archivo_presentacion)
                                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs" title="Diapositivas PowerPoint">
                                                <i class="fa-solid fa-file-powerpoint"></i>
                                            </span>
                                        @endif
                                        <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xs" title="Asistencia QR habilitada">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </span>
                                    </div>

                                    <!-- Sede Inferior Sobre la Foto -->
                                    <div class="absolute bottom-3 left-3.5 right-3.5 flex items-center justify-between text-white/90 text-xs z-10">
                                        <span class="inline-flex items-center gap-1.5 font-semibold truncate">
                                            <i class="fa-solid fa-location-dot text-cyan-400 text-xs"></i>
                                            <span>Todas las Sedes PNY</span>
                                        </span>
                                        <span class="text-[10px] font-mono text-cyan-300 font-bold bg-cyan-950/70 px-2.5 py-0.5 rounded-full border border-cyan-500/30">
                                            Oficial
                                        </span>
                                    </div>
                                </div>

                                <!-- Columna Derecha: Información y Acción (7/12 en desktop) -->
                                <div class="md:col-span-7 p-5 sm:p-7 flex flex-col justify-between space-y-4 bg-white min-w-0">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Disponible para Dictar
                                            </span>
                                            <span class="text-xs text-slate-400 font-mono">ID #{{ $ind->id }}</span>
                                        </div>

                                        <h4 class="font-heading font-extrabold text-base sm:text-xl text-slate-900 group-hover:text-brand-blue transition-colors leading-snug">
                                            {{ $ind->titulo }}
                                        </h4>

                                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-2.5 line-clamp-3 sm:line-clamp-4">
                                            {{ $ind->descripcion ?: 'Capacitación e inducción institucional estructurada para el equipo de trabajo.' }}
                                        </p>
                                    </div>

                                    <div class="pt-4 border-t border-line/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="flex items-center gap-3 text-xs text-slate-500">
                                            <span class="flex items-center gap-1 font-semibold text-slate-700">
                                                <i class="fa-solid fa-users text-brand-blue text-[11px]"></i> Presencial
                                            </span>
                                            <span>•</span>
                                            <span class="flex items-center gap-1 text-slate-600">
                                                <i class="fa-solid fa-qrcode text-emerald-600 text-[11px]"></i> Registro con QR
                                            </span>
                                        </div>

                                        <a href="{{ route('induccion.index') }}"
                                           target="_blank"
                                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-xs transition-all hover:scale-[1.02] cursor-pointer shrink-0">
                                            <i class="fa-solid fa-play text-xs"></i>
                                            <span>Dictar Esta Inducción</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Paginador / Indicadores (Dots) Inferiores -->
            <div class="flex items-center justify-center gap-1.5 pt-1" id="carruselDotsContainer">
                {{-- Se genera dinámicamente con JS --}}
            </div>
        @endif
    </div>

    <!-- Inducciones de Área (Sesiones dictadas por colaboradores de tu área o el jefe hacia otros) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-6 shadow-sm ring-1 ring-slate-900/5 my-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3.5 border-b border-slate-100">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-brand-blue border border-blue-200 uppercase tracking-wider shadow-2xs">
                        <i class="fa-solid fa-chalkboard-user text-brand-blue"></i> Inducciones de Área: {{ $areaNombre ?? 'Tu Área' }}
                    </span>
                </div>
                <h3 class="text-base sm:text-lg font-heading font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chalkboard-user text-brand-blue"></i> Inducciones de Área
                </h3>
                <p class="text-xs text-slate-500">
                    Historial de inducciones dictadas a otros colaboradores o externos por el personal de tu área o por ti como jefe, con sus firmas digitales y actas oficiales.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold px-3 py-1 rounded-full bg-slate-100 text-slate-700 self-start sm:self-center border border-slate-200/80 shadow-2xs shrink-0">
                <i class="fa-solid fa-list-check text-slate-500"></i>
                <span>{{ $sesionesArea->count() }} {{ $sesionesArea->count() === 1 ? 'sesión dictada' : 'sesiones dictadas' }}</span>
            </span>
        </div>

        @if($sesionesArea->isEmpty())
            <div class="py-12 px-4 text-center space-y-3 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-brand-blue flex items-center justify-center text-xl mx-auto shadow-2xs">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h4 class="font-bold text-sm text-slate-800">Aún no hay inducciones dictadas por tu área</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                    Cuando tú o los colaboradores de tu área inicien y faciliten una inducción presencial a otros colaboradores o externos, las sesiones aparecerán aquí con sus participantes y actas oficiales.
                </p>
            </div>
        @else
            <!-- VISTA MÓVIL: TARJETAS EN COLUMNA (block md:hidden) -->
            <div class="block md:hidden space-y-3.5">
                @foreach($sesionesArea as $ses)
                    <div class="rounded-2xl border border-slate-300/80 bg-white p-4 shadow-sm ring-1 ring-slate-900/5 space-y-3 transition-all hover:border-brand-blue/40">
                        <!-- Cabecera Móvil: Código y Estado -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-cyan-50 border border-cyan-200 text-cyan-900 font-mono font-extrabold text-xs shadow-2xs">
                                <i class="fa-solid fa-qrcode text-cyan-600"></i>
                                {{ $ses->codigo_sesion }}
                            </span>
                            <div>
                                @if($ses->estaActiva())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En curso
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="fa-solid fa-check text-[9px] text-emerald-600"></i> Finalizada
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Título de la Inducción y Facilitador del Área -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200/80 text-brand-blue flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                <i class="fa-solid fa-chalkboard-user text-xs"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">
                                    {{ $ses->induccion->titulo }}
                                </h4>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1 text-xs text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-user-tie text-indigo-500 text-xs"></i>
                                        <span class="font-semibold text-slate-700">{{ $ses->facilitador->nombre_completo ?? 'N/A' }}</span>
                                    </span>
                                    @if($ses->facilitador_id === auth()->id())
                                        <span class="text-[9.5px] font-extrabold text-cyan-800 bg-cyan-50 border border-cyan-200 px-1.5 py-0.2 rounded-md">Tú (Jefe)</span>
                                    @else
                                        <span class="text-[9.5px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.2 rounded-md">Colaborador del Área</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sede a lo largo (Horizontal completa) -->
                        <div class="flex items-center gap-2 text-xs text-rose-800 bg-rose-50/90 border border-rose-200/90 px-3 py-1.5 rounded-xl shadow-2xs">
                            <i class="fa-solid fa-location-dot text-rose-500 text-xs shrink-0"></i>
                            <span class="font-bold truncate">{{ $ses->sede->nombre ?? 'Sede Principal PNY' }}</span>
                        </div>

                        <!-- Metadatos en Recuadro: Fecha y Asistentes Totales -->
                        <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/80">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Fecha y Hora</span>
                                <div class="font-semibold text-slate-700 text-xs flex items-center gap-1">
                                    <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                                    <span>{{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y h:i A') : 'Sin fecha' }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5 text-right">Asistentes</span>
                                <div class="flex justify-end items-center gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-bold bg-blue-50 text-brand-blue border border-blue-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $ses->total_asistencias_count }} {{ $ses->total_asistencias_count == 1 ? 'persona' : 'personas' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones Móvil -->
                        <div class="pt-1 flex items-center gap-2">
                            <button type="button"
                                    onclick="abrirModalParticipantes({{ $ses->id }})"
                                    class="flex-1 py-2 px-3 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-signature"></i>
                                <span>Ver Asistentes ({{ $ses->total_asistencias_count }})</span>
                            </button>
                            <a href="{{ route('induccion.acta.pdf', $ses->codigo_sesion) }}"
                               class="py-2 px-3 rounded-xl bg-white hover:bg-rose-50 hover:text-rose-700 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 border border-slate-300 transition-colors shadow-2xs shrink-0"
                               title="Descargar Acta Oficial en PDF">
                                <i class="fa-solid fa-file-pdf text-rose-500"></i>
                                <span>Acta</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- VISTA ESCRITORIO: TABLA (hidden md:block) Totalmente adaptada al ancho sin scroll horizontal -->
            <div class="hidden md:block overflow-hidden rounded-2xl border border-slate-300/80 bg-white shadow-sm ring-1 ring-slate-900/5">
                <table class="w-full text-left border-collapse table-auto">
                    <!-- Cabecera Ejecutiva Navy / Dark Corporativa PNY -->
                    <thead class="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white">
                        <tr class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-200">
                            <th class="py-3 px-3 w-24 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-qrcode text-cyan-400 text-xs"></i>
                                    <span>Código</span>
                                </div>
                            </th>
                            <th class="py-3 px-3">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-chalkboard-user text-sky-400 text-xs"></i>
                                    <span>Inducción</span>
                                </div>
                            </th>
                            <th class="py-3 px-2.5 w-40 lg:w-48">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-tie text-indigo-400 text-xs"></i>
                                    <span>Facilitador (Área)</span>
                                </div>
                            </th>
                            <th class="py-3 px-2.5 w-28 lg:w-32">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-rose-400 text-xs"></i>
                                    <span>Sede</span>
                                </div>
                            </th>
                            <th class="py-3 px-2.5 w-24 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-amber-400 text-xs"></i>
                                    <span>Fecha</span>
                                </div>
                            </th>
                            <th class="py-3 px-3 w-28 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-users text-emerald-400 text-xs"></i>
                                    <span>Asistentes</span>
                                </div>
                            </th>
                            <th class="py-3 px-3 text-right w-36 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <i class="fa-solid fa-gears text-slate-300 text-xs"></i>
                                    <span>Acciones</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80 text-slate-700 bg-white">
                        @foreach($sesionesArea as $ses)
                            <tr class="hover:bg-sky-50/50 even:bg-slate-50/40 transition-colors">
                                <!-- Código -->
                                <td class="py-3 px-3 font-mono font-bold text-cyan-900 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-cyan-50/90 border border-cyan-200 text-cyan-900 text-[11px] shadow-2xs">
                                        <i class="fa-solid fa-qrcode text-cyan-600 text-[10px]"></i>
                                        <span>{{ $ses->codigo_sesion }}</span>
                                    </span>
                                </td>

                                <!-- Inducción -->
                                <td class="py-3 px-3">
                                    <div class="flex items-start gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200/80 text-brand-blue flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                            <i class="fa-solid fa-chalkboard-user text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs leading-snug line-clamp-2" title="{{ $ses->induccion->titulo }}">
                                                {{ $ses->induccion->titulo }}
                                            </div>
                                            @if($ses->estaActiva())
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mt-0.5 shadow-2xs animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En curso
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 mt-0.5">
                                                    <i class="fa-solid fa-check text-[8px] text-emerald-600"></i> Finalizada
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Facilitador -->
                                <td class="py-3 px-2.5">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center shrink-0 shadow-2xs">
                                            <i class="fa-solid fa-circle-user text-xs"></i>
                                        </div>
                                        <div class="min-w-0 truncate" title="{{ $ses->facilitador->nombre_completo ?? 'N/A' }}">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-semibold text-slate-800 text-xs leading-tight truncate">
                                                    {{ $ses->facilitador->nombre_completo ?? 'N/A' }}
                                                </span>
                                                @if($ses->facilitador_id === auth()->id())
                                                    <span class="shrink-0 text-[9px] font-extrabold text-cyan-800 bg-cyan-50 border border-cyan-200 px-1.5 py-0.2 rounded-md">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Sede -->
                                <td class="py-3 px-2.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-800 text-[11px] font-semibold border border-rose-200/80 shadow-2xs truncate max-w-full" title="{{ $ses->sede->nombre ?? 'Sede Principal' }}">
                                        <i class="fa-solid fa-location-dot text-rose-500 text-[10px] shrink-0"></i>
                                        <span class="truncate">{{ $ses->sede->nombre ?? 'Sede Principal' }}</span>
                                    </span>
                                </td>

                                <!-- Fecha / Hora -->
                                <td class="py-3 px-2.5 text-center whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-700 leading-tight">
                                        <span>{{ $ses->fecha_inicio ? $ses->fecha_inicio->format('d/m/Y') : '-' }}</span>
                                        @if($ses->fecha_inicio)
                                            <span class="block text-[10px] text-slate-400 font-mono font-normal">{{ $ses->fecha_inicio->format('h:i A') }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Asistentes -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-50 text-brand-blue border border-blue-200 shadow-2xs">
                                        <i class="fa-solid fa-users text-[11px]"></i>
                                        <span>{{ $ses->total_asistencias_count }}</span>
                                    </span>
                                </td>

                                <!-- Acciones -->
                                <td class="py-3 px-3 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 justify-end">
                                        <button type="button"
                                                onclick="abrirModalParticipantes({{ $ses->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs transition-all shadow-2xs hover:shadow-xs cursor-pointer"
                                                title="Ver lista de participantes y sus firmas digitales">
                                            <i class="fa-solid fa-signature text-[11px]"></i>
                                            <span>Firmas</span>
                                        </button>
                                        <a href="{{ route('induccion.acta.pdf', $ses->codigo_sesion) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 font-bold text-xs transition-all border border-slate-300 shadow-2xs hover:shadow-xs"
                                           title="Descargar Acta Oficial en PDF">
                                            <i class="fa-solid fa-file-pdf text-rose-500 text-[11px]"></i>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL PARA VISUALIZAR PARTICIPANTES Y FIRMAS DIGITALES -->
<div id="modalParticipantesSesion" onclick="if (event.target === this) cerrarModalParticipantes();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden border border-slate-200 animate-fade-in">
        
        <!-- Modal Header -->
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/90 shrink-0">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="modalPartCodigo" class="font-mono text-xs font-black px-2.5 py-0.5 rounded-lg bg-cyan-100 text-cyan-900 border border-cyan-200 tracking-wider"></span>
                        <span id="modalPartEstado" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"></span>
                        <span id="modalPartFecha" class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                            <i class="fa-regular fa-calendar text-[10px]"></i>
                            <span></span>
                        </span>
                    </div>
                    <h3 id="modalPartTitulo" class="font-heading font-black text-base sm:text-lg text-slate-900 leading-snug"></h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 pt-0.5">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-user-tie text-slate-400"></i>
                            <span>Facilitador: <strong id="modalPartFacilitador" class="text-slate-800 font-semibold"></strong></span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                            <span>Sede: <strong id="modalPartSede" class="text-slate-800 font-semibold"></strong></span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a id="modalPartDescargarPdf"
                       href="#"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs transition-colors shadow-2xs">
                        <i class="fa-solid fa-file-pdf text-red-400"></i>
                        <span class="hidden sm:inline">Descargar</span>
                        <span>Acta PDF</span>
                    </a>
                    <button type="button" onclick="cerrarModalParticipantes()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Barra de búsqueda interna y filtro de asistentes -->
        <div class="p-3 sm:p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white shrink-0">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text"
                       id="modalPartBusqueda"
                       oninput="filtrarParticipantesModal()"
                       placeholder="Buscar por colaborador, cédula, cargo..."
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:border-cyan-600 bg-slate-50 text-slate-800">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl text-[11px] font-bold">
                    <button type="button" onclick="cambiarFiltroTipoPart('todos')" id="btnPartTipoTodos" class="px-2.5 py-1 rounded-lg bg-white text-brand-blue shadow-2xs font-bold">Todos</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('empleados')" id="btnPartTipoEmpleados" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Nómina</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('externos')" id="btnPartTipoExternos" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Externos</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('mi_area')" id="btnPartTipoMiArea" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">De Mi Área</button>
                </div>
                <span id="modalPartContador" class="px-3 py-1 rounded-full bg-cyan-50 text-cyan-800 font-bold text-xs border border-cyan-100 shrink-0">
                    0 participantes
                </span>
            </div>
        </div>

        <!-- Modal Body (Dual: Tabla en Escritorio / Fichas en Columna en Móvil sin scroll horizontal) -->
        <div class="overflow-y-auto overflow-x-hidden flex-1 p-3 sm:p-5">
            <!-- Spinner de carga -->
            <div id="modalPartCargando" class="py-12 text-center space-y-3">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-cyan-600"></i>
                <p class="text-xs font-bold text-slate-600">Cargando participantes y firmas digitales...</p>
            </div>

            <!-- CONTENEDOR DE CONTENIDO (Mantiene separación estricta: Tabla en Escritorio / Fichas en Columna en Móvil) -->
            <div id="modalPartContenido" class="hidden space-y-3.5">
                <!-- VISTA ESCRITORIO: TABLA DE PARTICIPANTES Y FIRMAS (hidden md:block) -->
                <div id="modalPartTablaContainer" class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-800">
                        <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="py-2.5 px-3 text-center">#</th>
                                <th class="py-2.5 px-3">Colaborador / Participante</th>
                                <th class="py-2.5 px-3">Sede</th>
                                <th class="py-2.5 px-3">Área</th>
                                <th class="py-2.5 px-3">Fecha y Hora</th>
                                <th class="py-2.5 px-3 text-center">Firma Digital</th>
                            </tr>
                        </thead>
                        <tbody id="modalPartTbody" class="divide-y divide-slate-100 bg-white">
                            <!-- Rellenado dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- VISTA MÓVIL: EN COLUMNA VERTICAL (TABLA EN COLUMNA POR PARTICIPANTE) (block md:hidden) -->
                <div id="modalPartCardsContainer" class="block md:hidden space-y-3.5 w-full">
                    <!-- Rellenado dinámicamente con tarjetas estructuradas en columna -->
                </div>
            </div>

            <!-- Estado Vacío -->
            <div id="modalPartVacio" class="hidden py-10 text-center space-y-2 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h4 class="font-heading font-bold text-sm text-slate-800" id="modalPartVacioTitulo">Sin participantes registrados</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto" id="modalPartVacioDesc">No se encontraron asistentes para el filtro seleccionado.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 sm:p-4 border-t border-slate-200 bg-slate-50/80 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500 flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                <span>Firmas digitales registradas con trazabilidad y fecha oficial.</span>
            </span>
            <button type="button" onclick="cerrarModalParticipantes()" class="py-2 px-5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition shadow-2xs cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- MODAL PARA AMPLIAR FIRMA DIGITAL (ZOOM) -->
<div id="modalZoomFirma" onclick="if (event.target === this) cerrarModalZoomFirma();" class="fixed inset-0 z-60 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4 animate-fade-in text-center">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div class="text-left">
                <span class="text-[10px] uppercase font-bold text-cyan-700 tracking-wider">Firma Digital Registrada</span>
                <h4 id="zoomFirmaNombre" class="font-bold text-sm text-slate-800"></h4>
            </div>
            <button type="button" onclick="cerrarModalZoomFirma()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 flex items-center justify-center min-h-[160px]">
            <img id="zoomFirmaImg" src="" alt="Firma Ampliada" class="max-h-48 max-w-full object-contain filter contrast-125" />
        </div>
        <button type="button" onclick="cerrarModalZoomFirma()" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
            Volver a la lista
        </button>
    </div>
</div>

<script>
    function copiarPin(pin) {
        navigator.clipboard.writeText(pin).then(() => {
            const btn = document.getElementById('btnCopiarPin');
            btn.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> <span>Copiado</span>';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copiar</span>';
            }, 2000);
        });
    }

    // ==========================================
    // LÓGICA DEL MODAL DE PARTICIPANTES Y FIRMAS
    // ==========================================
    let participantesActuales = [];
    let tipoParticipanteFiltro = 'todos'; // 'todos' | 'empleados' | 'externos' | 'mi_area'

    async function abrirModalParticipantes(sesionId) {
        const modal = document.getElementById('modalParticipantesSesion');
        const cargando = document.getElementById('modalPartCargando');
        const contenido = document.getElementById('modalPartContenido');
        const vacio = document.getElementById('modalPartVacio');

        modal.classList.remove('hidden');
        cargando.classList.remove('hidden');
        contenido.classList.add('hidden');
        vacio.classList.add('hidden');

        document.getElementById('modalPartBusqueda').value = '';
        tipoParticipanteFiltro = 'todos';
        actualizarBotonesTipoPart('todos');

        try {
            const res = await fetch(`/jefe/inducciones/sesiones/${sesionId}/participantes-json`);
            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Error al cargar los participantes.');
                cerrarModalParticipantes();
                return;
            }

            const sesion = data.sesion;
            participantesActuales = data.participantes || [];

            // Llenar encabezado del modal
            document.getElementById('modalPartCodigo').textContent = sesion.codigo;
            document.getElementById('modalPartTitulo').textContent = sesion.induccion_titulo;
            document.getElementById('modalPartFacilitador').textContent = sesion.facilitador;
            document.getElementById('modalPartSede').textContent = sesion.sede;
            document.getElementById('modalPartFecha').querySelector('span').textContent = sesion.fecha_inicio;
            document.getElementById('modalPartDescargarPdf').href = sesion.acta_pdf_url;

            const estadoBadge = document.getElementById('modalPartEstado');
            if (sesion.es_activa) {
                estadoBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800';
                estadoBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse mr-1"></span> En Curso';
            } else {
                estadoBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 text-slate-700';
                estadoBadge.textContent = 'Finalizada';
            }

            cargando.classList.add('hidden');
            contenido.classList.remove('hidden');

            filtrarParticipantesModal();

        } catch (err) {
            console.error('Error al consultar participantes:', err);
            alert('Ocurrió un error de conexión al cargar las asistencias.');
            cerrarModalParticipantes();
        }
    }

    function renderizarParticipantesModal(lista) {
        const tbody = document.getElementById('modalPartTbody');
        const cardsCont = document.getElementById('modalPartCardsContainer');
        const contenido = document.getElementById('modalPartContenido');
        const vacio = document.getElementById('modalPartVacio');
        const contador = document.getElementById('modalPartContador');

        tbody.innerHTML = '';
        cardsCont.innerHTML = '';
        contador.textContent = `${lista.length} participante(s)`;

        if (lista.length === 0) {
            vacio.classList.remove('hidden');
            contenido.classList.add('hidden');
            return;
        }

        vacio.classList.add('hidden');
        contenido.classList.remove('hidden');

        lista.forEach((p, idx) => {
            const num = idx + 1;
            const badgeMiArea = p.es_mi_area
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-100 text-brand-blue text-[9.5px] font-extrabold uppercase border border-blue-200">De Tu Área</span>'
                : '';

            const badgeTipo = p.es_externo
                ? '<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[9.5px] font-bold uppercase">Externo</span>'
                : '<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[9.5px] font-bold uppercase">Nómina</span>';

            const firmaHtml = p.tiene_firma && p.firma_url
                ? `<div onclick="ampliarFirma('${p.firma_url}', '${p.nombre_completo.replace(/'/g, "\\'")}')"
                        class="bg-white p-1 rounded-xl border border-slate-200 shadow-2xs inline-flex items-center justify-center cursor-pointer hover:border-cyan-500 transition hover:scale-105 group"
                        title="Clic para ampliar firma">
                       <img src="${p.firma_url}" alt="Firma de ${p.nombre_completo}" class="h-9 max-w-[130px] object-contain" />
                       <i class="fa-solid fa-magnifying-glass-plus text-slate-300 group-hover:text-cyan-600 text-[10px] ml-1"></i>
                   </div>`
                : `<span class="text-[11px] text-slate-400 italic font-medium">Sin firma registrada</span>`;

            // 1. Fila de tabla para escritorio
            const tr = document.createElement('tr');
            tr.className = `hover:bg-slate-50/70 transition-colors ${p.es_mi_area ? 'bg-blue-50/20' : ''}`;
            tr.innerHTML = `
                <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-400 font-bold">${num}</td>
                <td class="py-2.5 px-3">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-bold text-slate-900 text-xs">${p.nombre_completo}</span>
                        ${badgeMiArea}
                        ${badgeTipo}
                    </div>
                    <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                        <span>CC: <strong>${p.identificacion}</strong></span>
                        <span>&bull;</span>
                        <span>${p.cargo}</span>
                    </div>
                </td>
                <td class="py-2.5 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100">
                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                        <span>${p.sede}</span>
                    </span>
                </td>
                <td class="py-2.5 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md ${p.es_mi_area ? 'bg-blue-100/80 text-brand-blue font-bold border border-blue-200' : 'bg-slate-100 text-slate-700 text-[10px]'}">
                        <i class="fa-solid fa-sitemap text-[8px]"></i>
                        <span>${p.area}</span>
                    </span>
                </td>
                <td class="py-2.5 px-3 text-slate-500 text-[11px] font-mono whitespace-nowrap">
                    ${p.fecha_registro}
                </td>
                <td class="py-2 px-3 text-center whitespace-nowrap">
                    ${firmaHtml}
                </td>
            `;
            tbody.appendChild(tr);

            // 2. Ficha en formato Columna estructurada para vista móvil (Tabla vertical organizada)
            const card = document.createElement('div');
            card.className = `rounded-2xl border ${p.es_mi_area ? 'border-blue-300 bg-white shadow-xs' : 'border-slate-200 bg-white shadow-2xs'} overflow-hidden transition-all`;
            card.innerHTML = `
                <!-- Encabezado de la Ficha: Número, Nombre y Etiquetas -->
                <div class="${p.es_mi_area ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200/80' : 'bg-slate-50 border-b border-slate-200'} p-3.5 flex items-start justify-between gap-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-lg ${p.es_mi_area ? 'bg-brand-blue text-white' : 'bg-slate-200 text-slate-700'} font-mono font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            ${num}
                        </span>
                        <div class="min-w-0">
                            <h4 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                                ${p.nombre_completo}
                            </h4>
                            <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                ${badgeMiArea}
                                ${badgeTipo}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filas de Información en Columna (Estructura de Tabla Vertical) -->
                <div class="divide-y divide-slate-100 text-xs bg-white">
                    <!-- Fila Cédula -->
                    <div class="p-2.5 sm:p-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-id-card text-slate-400 text-xs w-4"></i>
                            <span>Cédula:</span>
                        </span>
                        <span class="font-mono font-bold text-slate-900 text-xs bg-slate-100 px-2 py-0.5 rounded-md">
                            ${p.identificacion}
                        </span>
                    </div>

                    <!-- Fila Cargo -->
                    <div class="p-2.5 sm:p-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-briefcase text-slate-400 text-xs w-4"></i>
                            <span>Cargo:</span>
                        </span>
                        <span class="font-medium text-slate-800 text-right truncate">
                            ${p.cargo || 'No registrado'}
                        </span>
                    </div>

                    <!-- Fila Sede -->
                    <div class="p-2.5 sm:p-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-location-dot text-rose-500 text-xs w-4"></i>
                            <span>Sede:</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold text-[10.5px] border border-rose-100 truncate">
                            ${p.sede}
                        </span>
                    </div>

                    <!-- Fila Área -->
                    <div class="p-2.5 sm:p-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
                            <i class="fa-solid fa-sitemap text-indigo-500 text-xs w-4"></i>
                            <span>Área:</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md ${p.es_mi_area ? 'bg-blue-50 text-brand-blue font-extrabold border border-blue-200' : 'bg-slate-100 text-slate-700 font-semibold'} text-[10.5px] truncate">
                            ${p.area}
                        </span>
                    </div>

                    <!-- Fila Fecha de Asistencia -->
                    <div class="p-2.5 sm:p-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
                            <i class="fa-regular fa-clock text-cyan-600 text-xs w-4"></i>
                            <span>Asistencia:</span>
                        </span>
                        <span class="font-mono text-slate-700 text-[11px] bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                            ${p.fecha_registro}
                        </span>
                    </div>

                    <!-- Fila Firma Digital -->
                    <div class="p-3 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <span class="text-[11px] text-slate-600 font-extrabold uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-signature text-brand-blue text-xs"></i>
                            <span>Firma Digital:</span>
                        </span>
                        <div class="flex items-center justify-end">
                            ${firmaHtml}
                        </div>
                    </div>
                </div>
            `;
            cardsCont.appendChild(card);
        });
    }

    function filtrarParticipantesModal() {
        const query = (document.getElementById('modalPartBusqueda')?.value || '').toLowerCase().trim();

        const filtrados = participantesActuales.filter(p => {
            // Filtro por tipo
            if (tipoParticipanteFiltro === 'mi_area' && !p.es_mi_area) return false;
            if (tipoParticipanteFiltro === 'empleados' && p.es_externo) return false;
            if (tipoParticipanteFiltro === 'externos' && !p.es_externo) return false;

            // Filtro por texto
            if (!query) return true;

            return (p.nombre_completo && p.nombre_completo.toLowerCase().includes(query)) ||
                   (p.identificacion && p.identificacion.toLowerCase().includes(query)) ||
                   (p.cargo && p.cargo.toLowerCase().includes(query)) ||
                   (p.area && p.area.toLowerCase().includes(query)) ||
                   (p.sede && p.sede.toLowerCase().includes(query));
        });

        renderizarParticipantesModal(filtrados);
    }

    function cambiarFiltroTipoPart(tipo) {
        tipoParticipanteFiltro = tipo;
        actualizarBotonesTipoPart(tipo);
        filtrarParticipantesModal();
    }

    function actualizarBotonesTipoPart(tipo) {
        const btnMiArea = document.getElementById('btnPartTipoMiArea');
        const btnTodos = document.getElementById('btnPartTipoTodos');
        const btnEmp = document.getElementById('btnPartTipoEmpleados');
        const btnExt = document.getElementById('btnPartTipoExternos');

        const activeClass = 'px-2.5 py-1 rounded-lg bg-white text-brand-blue shadow-2xs font-bold';
        const inactiveClass = 'px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900';

        if (btnMiArea) btnMiArea.className = tipo === 'mi_area' ? activeClass : inactiveClass;
        if (btnTodos) btnTodos.className = tipo === 'todos' ? activeClass : inactiveClass;
        if (btnEmp) btnEmp.className = tipo === 'empleados' ? activeClass : inactiveClass;
        if (btnExt) btnExt.className = tipo === 'externos' ? activeClass : inactiveClass;
    }

    function cerrarModalParticipantes() {
        document.getElementById('modalParticipantesSesion').classList.add('hidden');
        participantesActuales = [];
    }

    // Modal Zoom Firma
    function ampliarFirma(url, nombre) {
        document.getElementById('zoomFirmaImg').src = url;
        document.getElementById('zoomFirmaNombre').textContent = nombre;
        document.getElementById('modalZoomFirma').classList.remove('hidden');
    }

    function cerrarModalZoomFirma() {
        document.getElementById('modalZoomFirma').classList.add('hidden');
    }

    // =========================================================================
    // SLIDER DE INDUCCIONES (PASO DIRECTO DE UNA EN UNA - SIN AUTOPLAY)
    // =========================================================================
    let carruselIndex = 0;
    let touchStartX = 0;
    let touchEndX = 0;

    function getTotalSlides() {
        return document.querySelectorAll('.carrusel-slide').length;
    }

    function actualizarCarrusel() {
        const track = document.getElementById('carruselTrack');
        if (!track) return;

        const total = getTotalSlides();
        if (total === 0) return;

        if (carruselIndex >= total) {
            carruselIndex = 0;
        } else if (carruselIndex < 0) {
            carruselIndex = total - 1;
        }

        // Desplazamiento 100% directo a la siguiente inducción
        track.style.transform = `translateX(-${carruselIndex * 100}%)`;

        // Indicador directo: Inducción X de Y
        const indicador = document.getElementById('carruselIndicadorIndice');
        if (indicador) {
            indicador.textContent = `Inducción ${carruselIndex + 1} de ${total}`;
        }

        actualizarDotsCarrusel(total);
    }

    function moverCarrusel(direccion) {
        const total = getTotalSlides();
        if (total <= 1) return;

        carruselIndex += direccion;

        if (carruselIndex >= total) {
            carruselIndex = 0;
        } else if (carruselIndex < 0) {
            carruselIndex = total - 1;
        }

        actualizarCarrusel();
    }

    function irASlide(index) {
        carruselIndex = index;
        actualizarCarrusel();
    }

    function renderizarDotsCarrusel() {
        const container = document.getElementById('carruselDotsContainer');
        if (!container) return;

        const total = getTotalSlides();
        if (total <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';
        for (let i = 0; i < total; i++) {
            const activo = (i === carruselIndex);
            html += `
                <button type="button"
                        onclick="irASlide(${i})"
                        title="Ir a inducción ${i + 1}"
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer ${activo ? 'w-6 bg-brand-blue' : 'w-2 bg-slate-300 hover:bg-slate-400'}">
                </button>
            `;
        }
        container.innerHTML = html;
    }

    function actualizarDotsCarrusel(total) {
        const container = document.getElementById('carruselDotsContainer');
        if (!container) return;

        const dots = container.querySelectorAll('button');
        if (dots.length !== total) {
            renderizarDotsCarrusel();
            return;
        }

        dots.forEach((dot, i) => {
            if (i === carruselIndex) {
                dot.className = 'h-2 rounded-full transition-all duration-300 cursor-pointer w-6 bg-brand-blue';
            } else {
                dot.className = 'h-2 rounded-full transition-all duration-300 cursor-pointer w-2 bg-slate-300 hover:bg-slate-400';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('carruselInduccionesWrapper');
        if (wrapper && getTotalSlides() > 0) {
            renderizarDotsCarrusel();
            actualizarCarrusel();

            // Soporte para gestos táctiles (Swipe directo) en móviles y tablets
            const viewport = document.getElementById('carruselViewport');
            if (viewport) {
                viewport.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });

                viewport.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    const diff = touchStartX - touchEndX;
                    if (Math.abs(diff) > 40) {
                        if (diff > 0) moverCarrusel(1);
                        else moverCarrusel(-1);
                    }
                }, { passive: true });
            }

            // Teclas de flecha izquierda/derecha al tener foco
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight') {
                    moverCarrusel(1);
                } else if (e.key === 'ArrowLeft') {
                    moverCarrusel(-1);
                }
            });
        }
    });
</script>
@endsection
