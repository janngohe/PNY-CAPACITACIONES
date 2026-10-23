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
            <!-- VISTA EN TARJETAS: RESPONSIVA PARA ESCRITORIO Y MÓVIL -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 sm:p-6" id="contenedorTarjetasInducciones">
                @foreach($inducciones as $ind)
                    @php
                        $recursosList = $ind->lista_recursos;
                        $numPptx = collect($recursosList)->where('tipo', 'pptx')->count();
                        $numVideo = collect($recursosList)->where('tipo', 'video')->count();
                        $numPdf = collect($recursosList)->where('tipo', 'pdf')->count();
                        $numImg = collect($recursosList)->where('tipo', 'imagen')->count();
                        $numTexto = collect($recursosList)->where('tipo', 'texto')->count();
                        $tieneRegistros = ($ind->sesiones_count > 0 || ($ind->asistencias_count ?? 0) > 0);
                    @endphp
                    <div class="card-induccion-item group bg-white rounded-3xl border border-slate-200/90 hover:border-cyan-400 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden relative"
                         data-search="{{ strtolower($ind->titulo . ' ' . ($ind->descripcion ?? '')) }}">

                        {{-- CABECERA / PORTADA CON OVERLAY --}}
                        <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-slate-900 shrink-0">
                            <img src="{{ $ind->imagen_portada_url }}"
                                 alt="{{ $ind->titulo }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-90 group-hover:opacity-100">

                            {{-- Gradiente oscuro superior e inferior --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/30 to-slate-950/60 pointer-events-none"></div>

                            {{-- Barra Superior: Estado (Activa/Inactiva) a la izquierda + Badge Sesiones a la derecha --}}
                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 z-10">
                                <!-- Toggle Estado -->
                                <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($ind->estado)
                                        <button type="submit"
                                                title="Inducción Activa · Clic para Desactivar"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-500/95 hover:bg-emerald-600 text-white backdrop-blur-md shadow-xs border border-white/20 transition-all cursor-pointer">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            <span>Activa</span>
                                        </button>
                                    @else
                                        <button type="submit"
                                                title="Inducción Inactiva · Clic para Activar"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-800/95 hover:bg-slate-700 text-slate-300 backdrop-blur-md shadow-xs border border-white/10 transition-all cursor-pointer">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            <span>Inactiva</span>
                                        </button>
                                    @endif
                                </form>

                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-900/85 text-cyan-300 backdrop-blur-md border border-cyan-500/30 shadow-xs">
                                    <i class="fa-solid fa-chalkboard text-[10px]"></i>
                                    <span>{{ $ind->sesiones_count }} {{ $ind->sesiones_count === 1 ? 'sesión' : 'sesiones' }}</span>
                                </span>
                            </div>

                            {{-- Bottom Overlay de Portada: Badges de recursos multimedia --}}
                            <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center gap-1 z-10">
                                @if($numPptx > 0)
                                    <span class="px-2 py-0.5 rounded-md bg-orange-600/90 text-white text-[10px] font-bold backdrop-blur-xs shadow-xs border border-white/20" title="{{ $numPptx }} diapositivas PowerPoint">
                                        <i class="fa-solid fa-file-powerpoint mr-1"></i> {{ $numPptx }} PPTX
                                    </span>
                                @endif
                                @if($numVideo > 0)
                                    <span class="px-2 py-0.5 rounded-md bg-rose-600/90 text-white text-[10px] font-bold backdrop-blur-xs shadow-xs border border-white/20" title="{{ $numVideo }} videos">
                                        <i class="fa-brands fa-youtube mr-1"></i> {{ $numVideo }} Video
                                    </span>
                                @endif
                                @if($numPdf > 0)
                                    <span class="px-2 py-0.5 rounded-md bg-red-600/90 text-white text-[10px] font-bold backdrop-blur-xs shadow-xs border border-white/20" title="{{ $numPdf }} documentos PDF">
                                        <i class="fa-solid fa-file-pdf mr-1"></i> {{ $numPdf }} PDF
                                    </span>
                                @endif
                                @if($numImg > 0)
                                    <span class="px-2 py-0.5 rounded-md bg-cyan-600/90 text-white text-[10px] font-bold backdrop-blur-xs shadow-xs border border-white/20" title="{{ $numImg }} infografías">
                                        <i class="fa-solid fa-image mr-1"></i> {{ $numImg }} Img
                                    </span>
                                @endif
                                @if($numTexto > 0)
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-600/90 text-white text-[10px] font-bold backdrop-blur-xs shadow-xs border border-white/20" title="{{ $numTexto }} diapositivas de texto">
                                        <i class="fa-solid fa-file-lines mr-1"></i> {{ $numTexto }} Texto
                                    </span>
                                @endif
                                @if(empty($recursosList))
                                    <span class="px-2 py-0.5 rounded-md bg-slate-800/80 text-slate-300 text-[10px] font-bold backdrop-blur-xs">
                                        Presencial
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- CUERPO DE LA TARJETA --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <!-- Metadatos: Fecha de Creación y Participantes -->
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="flex items-center gap-1.5 truncate">
                                        <i class="fa-solid fa-calendar-day text-slate-400 text-[10px]"></i>
                                        <span>{{ $ind->created_at ? $ind->created_at->format('d/m/Y') : 'Reciente' }}</span>
                                    </span>
                                    @if(($ind->asistencias_count ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1 font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-users text-slate-500 text-[9px]"></i>
                                            <span>{{ $ind->asistencias_count }} participantes</span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Título -->
                                <h3 onclick="abrirModalVisualizar({{ $ind->id }})"
                                    class="font-heading font-extrabold text-slate-900 group-hover:text-cyan-700 transition-colors text-base leading-snug line-clamp-2 cursor-pointer"
                                    title="Clic para previsualizar: {{ $ind->titulo }}">
                                    {{ $ind->titulo }}
                                </h3>

                                <!-- Descripción -->
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $ind->descripcion ?: 'Inducción formativa institucional para colaboradores y personal convocado.' }}
                                </p>
                            </div>

                            <!-- Barra de Botones de Acción -->
                            <div class="pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 items-center">
                                <!-- Ver Contenido / Diapositivas -->
                                <button type="button"
                                        onclick="abrirModalVisualizar({{ $ind->id }})"
                                        class="py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-cyan-50 text-slate-700 hover:text-cyan-800 text-[11px] font-bold text-center transition-all flex items-center justify-center gap-1.5 cursor-pointer border border-slate-200 shadow-2xs"
                                        title="Ver qué elementos y contenidos tiene esta inducción">
                                    <i class="fa-solid fa-eye text-cyan-600 text-xs"></i>
                                    <span>Ver</span>
                                </button>

                                <!-- Editar Inducción -->
                                <a href="{{ route('admin.inducciones.edit', $ind->id) }}"
                                   class="py-2 px-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-[11px] font-bold text-center transition-all flex items-center justify-center gap-1 shadow-2xs hover:shadow-xs"
                                   title="Editar inducción y diapositivas">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    <span>Editar</span>
                                </a>

                                <!-- Inactivar o Eliminar -->
                                @if($tieneRegistros)
                                    <form action="{{ route('admin.inducciones.toggle_estado', $ind->id) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('PATCH')
                                        @if($ind->estado)
                                            <button type="submit"
                                                    class="w-full py-2 px-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-[11px] font-bold text-center transition-all flex items-center justify-center gap-1 border border-amber-200 cursor-pointer shadow-2xs"
                                                    title="Tiene registros: Clic para Inactivar">
                                                <i class="fa-solid fa-ban text-amber-700 text-xs"></i>
                                                <span>Inactivar</span>
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="w-full py-2 px-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold text-center transition-all flex items-center justify-center gap-1 border border-emerald-200 cursor-pointer shadow-2xs"
                                                    title="Tiene registros: Clic para Activar">
                                                <i class="fa-solid fa-check text-emerald-700 text-xs"></i>
                                                <span>Activar</span>
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <button type="button"
                                            onclick="confirmarEliminarInduccion('{{ route('admin.inducciones.destroy', $ind->id) }}', '{{ addslashes($ind->titulo) }}')"
                                            class="w-full py-2 px-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold text-center transition-all flex items-center justify-center gap-1 border border-rose-200 cursor-pointer shadow-2xs"
                                            title="Eliminar inducción recién creada (sin registros)">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                        <span>Eliminar</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            @if($inducciones->hasPages())
                <div class="p-4 sm:px-6 sm:py-4 border-t border-slate-200/80 bg-slate-50/50">
                    {{ $inducciones->links() }}
                </div>
            @endif
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

@include('induccion._modal_visor_pptx')

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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            cerrarModalVisualizar();
            cerrarModalRegenerarPin();
            cerrarModalEliminarIndex();
            cerrarVisorPptxModal();
        }
    });
</script>
@endsection
