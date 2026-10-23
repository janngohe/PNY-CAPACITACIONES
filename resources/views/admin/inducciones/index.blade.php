@extends('layouts.admin')

@section('title', 'Inducciones Publicadas · Panel Administrador')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-100 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                    <i class="fa-solid fa-chalkboard-user mr-1"></i> Control de Inducciones
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight font-serif">Inducciones Publicadas</h1>
            <p class="text-xs text-slate-500">Administra el catálogo de inducciones institucionales, diapositivas multimedia y el código PIN de facilitadores.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.inducciones.create') }}"
               class="px-4 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Inducción</span>
            </a>

            <a href="{{ route('admin.inducciones.sesiones') }}"
               class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-cyan-600"></i>
                <span>Ver Sesiones</span>
            </a>

            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Sala Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Mensajes Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tarjeta Principal: PIN Institucional de Facilitador -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-56 h-56 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mb-2.5">
                    <i class="fa-solid fa-key"></i> Código de Acceso de Facilitadores
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight">PIN Institucional de Inducción</h2>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                    Código de 4 dígitos para que los facilitadores y jefes autorizados inicien charlas en las salas de proyección.
                </p>
            </div>

            <!-- Caja del PIN y Botón de Regenerar -->
            <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-950/60 p-4 sm:p-5 rounded-2xl border border-slate-700/80">
                <div class="text-center sm:text-left">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PIN Actual Vigente</div>
                    <div class="flex items-center gap-3 mt-1">
                        <span id="pinDisplay" class="font-mono text-3xl font-black tracking-widest text-cyan-400 select-all">
                            {{ $pinActual }}
                        </span>
                        <button type="button" onclick="togglePinVisibilidad()"
                                class="p-1.5 text-slate-400 hover:text-white transition-colors cursor-pointer"
                                title="Ocultar / Mostrar PIN">
                            <i id="pinEyeIcon" class="fa-solid fa-eye-slash text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="sm:border-l sm:border-slate-800 sm:pl-4">
                    <button type="button" onclick="abrirModalRegenerarPin()"
                            class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Regenerar PIN</span>
                    </button>
                    <div class="text-[10px] text-slate-400 mt-1 text-center sm:text-left">Requiere contraseña</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas del Módulo -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Inducciones</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $estadisticas['total_inducciones'] }}</div>
            <div class="text-[11px] text-cyan-600 font-semibold mt-0.5">En catálogo</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Inducciones Activas</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $estadisticas['inducciones_activas'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Listas para dictar</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones Dictadas</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $estadisticas['total_sesiones'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Charlas registradas</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Asistencias</div>
            <div class="text-2xl font-black text-cyan-700 mt-1">{{ $estadisticas['total_asistencias'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Firmas registradas</div>
        </div>
    </div>

    <!-- SECCIÓN: CATÁLOGO DE INDUCCIONES (CRUD) -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-2xs">
        <!-- Cabecera de la sección con buscador -->
        <div class="p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-shapes text-cyan-600"></i> Inducciones Creadas y Listas
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                        {{ $inducciones->total() }} registradas
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Gestiona contenidos multimedia, edita información, cambia estados o elimina registros</p>
            </div>

            <!-- Buscador en tiempo real de inducciones -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text"
                           id="filtroInduccionesInput"
                           oninput="filtrarInduccionesEnVivo()"
                           placeholder="Buscar inducción..."
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 outline-none transition-all">
                    <button type="button"
                            id="btnLimpiarFiltroInd"
                            onclick="limpiarFiltroInducciones()"
                            class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <a href="{{ route('admin.inducciones.create') }}"
                   class="px-3.5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus"></i>
                    <span class="hidden sm:inline">Nueva Inducción</span>
                </a>
            </div>
        </div>

        @if($inducciones->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-cyan-50 border border-cyan-200 text-cyan-600 flex items-center justify-center text-2xl mx-auto">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">No hay inducciones creadas todavía</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Crea tu primera inducción con presentaciones PowerPoint, videos o documentos PDF para proyectar en sala.</p>
                <div class="pt-2">
                    <a href="{{ route('admin.inducciones.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                        <i class="fa-solid fa-plus"></i>
                        <span>Crear Primera Inducción</span>
                    </a>
                </div>
            </div>
        @else
            <!-- VISTA EN MÓVIL: TARJETAS EN COLUMNA (CERO SCROLL HORIZONTAL) -->
            <div class="block md:hidden p-4 space-y-3.5" id="contenedorMovilInducciones">
                @foreach($inducciones as $ind)
                    @php
                        $recursosList = $ind->lista_recursos;
                        $numPptx = collect($recursosList)->where('tipo', 'pptx')->count();
                        $numVideo = collect($recursosList)->where('tipo', 'video')->count();
                        $numPdf = collect($recursosList)->where('tipo', 'pdf')->count();
                        $numImg = collect($recursosList)->where('tipo', 'imagen')->count();
                        $tieneRegistros = ($ind->sesiones_count > 0 || ($ind->asistencias_count ?? 0) > 0);
                    @endphp
                    <div class="card-induccion-item bg-white rounded-2xl border border-slate-200 shadow-2xs p-4 space-y-3"
                         data-search="{{ strtolower($ind->titulo . ' ' . $ind->descripcion) }}">

                        <!-- Fila Superior: Portada + Título -->
                        <div class="flex items-start gap-3">
                            <img src="{{ $ind->imagen_portada_url }}"
                                 alt="Portada"
                                 class="w-16 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <!-- Toggle Estado -->
                                    <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        @if($ind->estado)
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Activa</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Inactiva</span>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                    {{ $ind->titulo }}
                                </h4>
                            </div>
                        </div>

                        <!-- Descripción -->
                        @if($ind->descripcion)
                            <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                {{ $ind->descripcion }}
                            </p>
                        @endif

                        <!-- Columna de Datos: Recursos y Sesiones -->
                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex flex-wrap items-center gap-1">
                                @if($numPptx > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                        <i class="fa-solid fa-file-powerpoint text-orange-600"></i> {{ $numPptx }} PPTX
                                    </span>
                                @endif
                                @if($numVideo > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        <i class="fa-solid fa-video text-red-600"></i> {{ $numVideo }} Video
                                    </span>
                                @endif
                                @if($numPdf > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-file-pdf text-rose-600"></i> {{ $numPdf }} PDF
                                    </span>
                                @endif
                                @if($numImg > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                                        <i class="fa-solid fa-image text-cyan-600"></i> {{ $numImg }} Img
                                    </span>
                                @endif
                                @if(empty($recursosList))
                                    <span class="text-[10px] text-slate-400 italic">Sin diapositivas</span>
                                @endif
                            </div>

                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600">
                                <i class="fa-solid fa-chalkboard text-cyan-600"></i>
                                <span>{{ $ind->sesiones_count }} sesiones</span>
                                @if(($ind->asistencias_count ?? 0) > 0)
                                    <span class="text-slate-400">·</span>
                                    <i class="fa-solid fa-users text-slate-500"></i>
                                    <span>{{ $ind->asistencias_count }}</span>
                                @endif
                            </span>
                        </div>

                        <!-- Barra de Acciones Móvil -->
                        <div class="grid grid-cols-4 gap-1.5 pt-2 border-t border-slate-100">
                            <button type="button"
                                    onclick="abrirModalVisualizar({{ $ind->id }})"
                                    class="py-2 rounded-xl bg-slate-100 hover:bg-cyan-50 text-slate-700 hover:text-cyan-800 text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1 cursor-pointer"
                                    title="Ver qué elementos y contenidos tiene esta inducción">
                                <i class="fa-solid fa-eye text-cyan-600"></i>
                                <span>Ver</span>
                            </button>

                            <a href="{{ route('induccion.index') }}"
                               target="_blank"
                               class="py-2 rounded-xl bg-slate-100 hover:bg-cyan-50 text-slate-700 hover:text-cyan-800 text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1">
                                <i class="fa-solid fa-chalkboard-user"></i>
                                <span>Sala</span>
                            </a>

                            <a href="{{ route('admin.inducciones.edit', $ind->id) }}"
                               class="py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Editar</span>
                            </a>

                            @if($tieneRegistros)
                                <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    @if($ind->estado)
                                        <button type="submit"
                                                class="w-full h-full py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1 border border-amber-200 cursor-pointer"
                                                title="Tiene registros: Clic para Inactivar">
                                            <i class="fa-solid fa-ban text-amber-700"></i>
                                            <span>Inactivar</span>
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="w-full h-full py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1 border border-emerald-200 cursor-pointer"
                                                title="Tiene registros: Clic para Activar">
                                            <i class="fa-solid fa-check text-emerald-700"></i>
                                            <span>Activar</span>
                                        </button>
                                    @endif
                                </form>
                            @else
                                <button type="button"
                                        onclick="confirmarEliminarInduccion('{{ route('admin.inducciones.destroy', $ind->id) }}', '{{ addslashes($ind->titulo) }}')"
                                        class="py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold text-center transition-colors flex items-center justify-center gap-1 border border-rose-200 cursor-pointer"
                                        title="Eliminar inducción recién creada">
                                    <i class="fa-solid fa-trash-can"></i>
                                    <span>Eliminar</span>
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- VISTA EN ESCRITORIO: TABLA COMPLETA -->
            <div class="hidden md:block overflow-hidden">
                <table class="w-full text-left text-xs" id="tablaEscritorioInducciones">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Inducción</th>
                            <th class="py-3 px-4">Recursos Multimedia</th>
                            <th class="py-3 px-4 text-center">Sesiones Dictadas</th>
                            <th class="py-3 px-4 text-center">Estado</th>
                            <th class="py-3 px-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($inducciones as $ind)
                            @php
                                $recursosList = $ind->lista_recursos;
                                $numPptx = collect($recursosList)->where('tipo', 'pptx')->count();
                                $numVideo = collect($recursosList)->where('tipo', 'video')->count();
                                $numPdf = collect($recursosList)->where('tipo', 'pdf')->count();
                                $numImg = collect($recursosList)->where('tipo', 'imagen')->count();
                                $tieneRegistros = ($ind->sesiones_count > 0 || ($ind->asistencias_count ?? 0) > 0);
                            @endphp
                            <tr class="row-induccion-item hover:bg-slate-50/70 transition-colors"
                                data-search="{{ strtolower($ind->titulo . ' ' . $ind->descripcion) }}">
                                <!-- Portada + Título + Descripción -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $ind->imagen_portada_url }}"
                                             alt="Portada"
                                             class="w-14 h-10 rounded-xl object-cover border border-slate-200 shadow-2xs shrink-0">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.inducciones.edit', $ind->id) }}"
                                               class="font-bold text-slate-900 hover:text-cyan-700 transition-colors text-sm line-clamp-1">
                                                {{ $ind->titulo }}
                                            </a>
                                            @if($ind->descripcion)
                                                <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $ind->descripcion }}</p>
                                            @endif
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                Creado el {{ $ind->created_at ? $ind->created_at->format('d/m/Y') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Badges de Recursos -->
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @if($numPptx > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200" title="{{ $numPptx }} diapositivas PowerPoint">
                                                <i class="fa-solid fa-file-powerpoint text-orange-600"></i> {{ $numPptx }} PPTX
                                            </span>
                                        @endif
                                        @if($numVideo > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-red-50 text-red-700 border border-red-200" title="{{ $numVideo }} videos">
                                                <i class="fa-solid fa-video text-red-600"></i> {{ $numVideo }} Video
                                            </span>
                                        @endif
                                        @if($numPdf > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200" title="{{ $numPdf }} documentos PDF">
                                                <i class="fa-solid fa-file-pdf text-rose-600"></i> {{ $numPdf }} PDF
                                            </span>
                                        @endif
                                        @if($numImg > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200" title="{{ $numImg }} imágenes">
                                                <i class="fa-solid fa-image text-cyan-600"></i> {{ $numImg }} Img
                                            </span>
                                        @endif
                                        @if(empty($recursosList))
                                            <span class="text-[11px] text-slate-400 italic">Sin diapositivas</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Sesiones Dictadas y Participantes -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200" title="{{ $ind->sesiones_count }} sesiones dictadas">
                                            <i class="fa-solid fa-chalkboard text-[10px] text-cyan-700"></i> {{ $ind->sesiones_count }}
                                        </span>
                                        @if(($ind->asistencias_count ?? 0) > 0)
                                            <span class="text-[10px] text-slate-400 font-semibold mt-0.5 flex items-center gap-1">
                                                <i class="fa-solid fa-users text-[9px]"></i> {{ $ind->asistencias_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Estado con Botón Toggle Rápido -->
                                <td class="py-3.5 px-4 text-center">
                                    <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        @if($ind->estado)
                                            <button type="submit"
                                                    title="Haz clic para desactivar esta inducción"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors cursor-pointer">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Activa</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    title="Haz clic para activar esta inducción"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Inactiva</span>
                                            </button>
                                        @endif
                                    </form>
                                </td>

                                <!-- Acciones CRUD -->
                                <td class="py-3.5 px-4 text-right">
                                        <button type="button"
                                                onclick="abrirModalVisualizar({{ $ind->id }})"
                                                class="p-2 rounded-xl text-slate-500 hover:text-cyan-700 hover:bg-cyan-50 transition-colors border border-transparent hover:border-cyan-200 cursor-pointer"
                                                title="Ver elementos y contenidos de esta inducción">
                                            <i class="fa-solid fa-eye text-xs text-cyan-600"></i>
                                        </button>

                                        <a href="{{ route('induccion.index') }}"
                                           target="_blank"
                                           class="p-2 rounded-xl text-slate-500 hover:text-cyan-700 hover:bg-cyan-50 transition-colors border border-transparent hover:border-cyan-200"
                                           title="Proyectar en sala de inducción">
                                            <i class="fa-solid fa-chalkboard-user text-xs"></i>
                                        </a>

                                        <a href="{{ route('admin.inducciones.edit', $ind->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-cyan-700 hover:text-white text-slate-700 font-bold text-xs transition-all shadow-2xs"
                                           title="Editar inducción y recursos">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            <span>Editar</span>
                                        </a>

                                        @if($tieneRegistros)
                                            <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                @if($ind->estado)
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                            title="Inducción protegida (tiene registros): Inactivar">
                                                        <i class="fa-solid fa-ban text-xs text-amber-700"></i>
                                                        <span class="hidden xl:inline">Inactivar</span>
                                                    </button>
                                                @else
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                            title="Inducción protegida (tiene registros): Activar">
                                                        <i class="fa-solid fa-check text-xs text-emerald-700"></i>
                                                        <span class="hidden xl:inline">Activar</span>
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <button type="button"
                                                    onclick="confirmarEliminarInduccion('{{ route('admin.inducciones.destroy', $ind->id) }}', '{{ addslashes($ind->titulo) }}')"
                                                    class="p-2 rounded-xl text-rose-500 hover:text-white hover:bg-rose-600 transition-colors border border-transparent hover:border-rose-600 cursor-pointer"
                                                    title="Eliminar inducción recién creada (sin registros)">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="p-4 border-t border-slate-200">
                {{ $inducciones->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal para Eliminar Inducción -->
<div id="modalEliminarInduccionIndex" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-rose-600 font-bold text-sm uppercase tracking-wider">
                <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación
            </div>
            <button type="button" onclick="cerrarModalEliminarIndex()" class="text-slate-400 hover:text-slate-700 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 class="text-lg font-black text-slate-900 mb-2">¿Eliminar esta inducción?</h3>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            Se eliminará del catálogo la inducción <strong id="tituloInduccionEliminar"></strong> y sus recursos asociados. Los registros de asistencia previos se mantendrán intactos.
        </p>

        <form id="formEliminarInduccion" action="" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="cerrarModalEliminarIndex()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Sí, Eliminar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Regenerar PIN con Contraseña del Administrador -->
<div id="modalRegenerarPin" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-rose-600 font-bold text-sm uppercase tracking-wider">
                <i class="fa-solid fa-shield-halved"></i> Seguridad de Acceso
            </div>
            <button type="button" onclick="cerrarModalRegenerarPin()" class="text-slate-400 hover:text-slate-700 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 class="text-lg font-black text-slate-900 mb-2">¿Regenerar el PIN de Inducción?</h3>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            Al regenerar el PIN, el código anterior quedará invalidado de inmediato. Cualquier facilitador deberá usar el nuevo PIN de 4 dígitos para acceder.
        </p>

        <form action="{{ route('admin.inducciones.regenerar_pin') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="password_confirmacion" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Contraseña del Administrador <span class="text-rose-500">*</span>
                </label>
                <input type="password"
                       name="password_confirmacion"
                       id="password_confirmacion"
                       placeholder="Ingresa tu contraseña actual"
                       required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-slate-900 text-sm focus:outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="cerrarModalRegenerarPin()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Confirmar y Regenerar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Visualizar Contenidos y Elementos de la Inducción -->
<div id="modalVisualizarInduccion" onclick="if (event.target === this) cerrarModalVisualizar();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-3xl max-w-3xl w-full shadow-2xl relative animate-fade-in max-h-[92vh] flex flex-col overflow-hidden">
        
        <!-- Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-700 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-cyan-700 tracking-wider">Explorador de Contenidos</span>
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
                    <div id="vis-badges-recursos" class="flex flex-wrap items-center gap-1.5 mb-2"></div>
                    <div class="text-xs text-slate-300 flex flex-wrap items-center gap-3">
                        <span id="vis-creador" class="flex items-center gap-1">
                            <i class="fa-solid fa-user-tie text-cyan-400"></i>
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
                    <i class="fa-solid fa-circle-info text-cyan-700"></i>
                    <span>Descripción y Objetivos de la Inducción</span>
                </h4>
                <p id="vis-descripcion" class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"></p>
            </div>

            <!-- Sedes y Áreas Asignadas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-2xs space-y-1.5">
                    <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                        <span>Sedes Autorizadas / Sugeridas</span>
                    </span>
                    <div id="vis-sedes" class="flex flex-wrap gap-1 pt-1"></div>
                </div>

                <div class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-2xs space-y-1.5">
                    <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-sitemap text-blue-500"></i>
                        <span>Áreas Citadas / Convocadas</span>
                    </span>
                    <div id="vis-areas" class="flex flex-wrap gap-1 pt-1"></div>
                </div>
            </div>

            <!-- Lista de Recursos Multimedia y Materiales -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-cyan-700"></i>
                        <span>Elementos y Materiales de Apoyo (<span id="vis-contador-recursos">0</span>)</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Videos, diapositivas y guías de esta inducción</span>
                </div>

                <div id="vis-contenedor-recursos" class="space-y-3"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <button type="button" onclick="cerrarModalVisualizar()"
                    class="px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer">
                Cerrar
            </button>

            <div class="flex items-center gap-2">
                <a id="btn-editar-desde-visualizador" href="#"
                   class="px-4 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Editar Inducción</span>
                </a>
                <a href="{{ route('induccion.index') }}" target="_blank"
                   class="px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <span>Ver Sala</span>
                </a>
            </div>
        </div>
    </div>
</div>

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
        if (!ind.tiene_pptx && !ind.tiene_video && !ind.tiene_pdf && !ind.tiene_imagen) badgesCont.innerHTML += `<span class="px-2 py-0.5 rounded-md bg-slate-800/80 text-slate-200 text-[10px] font-bold">Presencial</span>`;

        // Sedes
        const sedesCont = document.getElementById('vis-sedes');
        sedesCont.innerHTML = '';
        if (ind.sedes && ind.sedes.length > 0) {
            ind.sedes.forEach(s => {
                sedesCont.innerHTML += `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-100"><i class="fa-solid fa-location-dot text-[8px]"></i>${s}</span>`;
            });
        } else {
            sedesCont.innerHTML = `<span class="text-[11px] text-slate-400 italic">Aplica a todas las sedes institucionales</span>`;
        }

        // Áreas
        const areasCont = document.getElementById('vis-areas');
        areasCont.innerHTML = '';
        if (ind.areas && ind.areas.length > 0) {
            ind.areas.forEach(a => {
                areasCont.innerHTML += `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-bold border border-blue-100"><i class="fa-solid fa-diagram-project text-[8px]"></i>${a}</span>`;
            });
        } else {
            areasCont.innerHTML = `<span class="text-[11px] text-slate-400 italic">Aplica a todas las áreas convocadas</span>`;
        }

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
                    actionHtml = r.archivo_url ? `<a href="${r.archivo_url}" download class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-700 text-xs font-bold transition flex items-center gap-1.5"><i class="fa-solid fa-download"></i><span>Descargar PPTX</span></a>` : '';
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

        const btnEditar = document.getElementById('btn-editar-desde-visualizador');
        if (btnEditar) {
            btnEditar.href = `/admin/inducciones/${ind.id}/editar`;
        }

        document.getElementById('modalVisualizarInduccion').classList.remove('hidden');
    }

    function cerrarModalVisualizar() {
        const iframes = document.querySelectorAll('#modalVisualizarInduccion iframe');
        iframes.forEach(f => f.src = f.src);

        document.getElementById('modalVisualizarInduccion').classList.add('hidden');
        visualizandoInduccionId = null;
    }

    let pinOculto = false;
    const pinReal = @json($pinActual);

    function togglePinVisibilidad() {
        const pinDisplay = document.getElementById('pinDisplay');
        const icon = document.getElementById('pinEyeIcon');

        if (pinOculto) {
            pinDisplay.textContent = pinReal;
            icon.className = "fa-solid fa-eye-slash text-sm";
            pinOculto = false;
        } else {
            pinDisplay.textContent = "••••";
            icon.className = "fa-solid fa-eye text-sm";
            pinOculto = true;
        }
    }

    function abrirModalRegenerarPin() {
        document.getElementById('modalRegenerarPin').classList.remove('hidden');
    }

    function cerrarModalRegenerarPin() {
        document.getElementById('modalRegenerarPin').classList.add('hidden');
    }

    function confirmarEliminarInduccion(url, titulo) {
        document.getElementById('formEliminarInduccion').action = url;
        document.getElementById('tituloInduccionEliminar').textContent = `"${titulo}"`;
        document.getElementById('modalEliminarInduccionIndex').classList.remove('hidden');
    }

    function cerrarModalEliminarIndex() {
        document.getElementById('modalEliminarInduccionIndex').classList.add('hidden');
    }

    // Filtro instantáneo en vivo
    function filtrarInduccionesEnVivo() {
        const input = document.getElementById('filtroInduccionesInput');
        const q = (input.value || '').toLowerCase().trim();
        const btnClear = document.getElementById('btnLimpiarFiltroInd');

        if (btnClear) {
            btnClear.classList.toggle('hidden', q.length === 0);
        }

        // Filtrar tarjetas móvil
        document.querySelectorAll('.card-induccion-item').forEach(el => {
            const data = el.getAttribute('data-search') || '';
            el.style.display = data.includes(q) ? '' : 'none';
        });

        // Filtrar filas de escritorio
        document.querySelectorAll('.row-induccion-item').forEach(el => {
            const data = el.getAttribute('data-search') || '';
            el.style.display = data.includes(q) ? '' : 'none';
        });
    }

    function limpiarFiltroInducciones() {
        const input = document.getElementById('filtroInduccionesInput');
        input.value = '';
        filtrarInduccionesEnVivo();
        input.focus();
    }
</script>
@endsection
