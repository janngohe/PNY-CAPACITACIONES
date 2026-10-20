@extends('layouts.admin')

@section('title', 'Gestión de Capacitaciones')
@section('page_title', 'Capacitaciones e Inducciones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Gestión General de Formación',
        'titulo' => 'Capacitaciones e Inducciones',
        'descripcion' => 'Administra todo el catálogo formativo de la empresa: crea cursos con módulos interactivos, asigna áreas destinatarias, establece condiciones de aprobación y gestiona su disponibilidad.',
        'icono' => 'fa-book-open',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y PESTAÑAS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-4">
        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-line shadow-2xs" id="tabs-capacitaciones">
            <a href="{{ route('admin.capacitaciones.index', ['estado' => '1']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold {{ ($filtros['estado'] ?? '1') === '1' ? 'bg-white text-brand-blue shadow-xs border border-line/60' : 'text-slate-600 hover:text-brand-blue hover:bg-white/60' }} transition-all">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                <span>Publicadas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-bold" id="badge-total-activas">
                    {{ $estadisticas['activas'] }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.index', ['estado' => 'vencidas']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold {{ ($filtros['estado'] ?? '') === 'vencidas' ? 'bg-white text-rose-700 shadow-xs border border-line/60' : 'text-slate-600 hover:text-rose-600 hover:bg-white/60' }} transition-all">
                <i class="fa-solid fa-clock-rotate-left text-rose-500 text-xs"></i>
                <span>Vencidas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($estadisticas['vencidas'] ?? 0) > 0 ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }} font-bold" id="badge-total-vencidas">
                    {{ $estadisticas['vencidas'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.index', ['estado' => 'proximas']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold {{ ($filtros['estado'] ?? '') === 'proximas' ? 'bg-white text-amber-700 shadow-xs border border-line/60' : 'text-slate-600 hover:text-amber-600 hover:bg-white/60' }} transition-all">
                <i class="fa-solid fa-hourglass-half text-amber-500 text-xs"></i>
                <span>Por vencer</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($estadisticas['proximas'] ?? 0) > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700' }} font-bold" id="badge-total-proximas">
                    {{ $estadisticas['proximas'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.borradores') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold {{ ($filtros['estado'] ?? '') === '0' ? 'bg-white text-brand-blue shadow-xs border border-line/60' : 'text-slate-600 hover:text-brand-blue hover:bg-white/60' }} transition-all">
                <i class="fa-solid fa-file-pen text-amber-500 text-xs"></i>
                <span>Borradores</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold" id="badge-total-borradores">
                    {{ $estadisticas['borradores'] }}
                </span>
            </a>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.asignaciones.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-line hover:border-brand-blue hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                <i class="fa-solid fa-diagram-project"></i>
                <span>Asignar a Áreas</span>
            </a>
            <a href="{{ route('admin.capacitaciones.create') }}" id="btn-crear-capacitacion"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Capacitación</span>
            </a>
        </div>
    </div>

    <!-- BUSCADOR PREDICTIVO Y FILTROS EN TIEMPO REAL -->
    <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs w-full overflow-hidden">
        <form id="form-filtro-capacitaciones" method="GET" action="{{ route('admin.capacitaciones.index') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 w-full min-w-0">
            <!-- 1. Buscador predictivo (Porción 1/4) -->
            <div class="relative w-full min-w-0">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="filtro-q" class="block text-xs font-heading font-bold text-slate-700 uppercase tracking-wider truncate">
                        Buscador predictivo
                    </label>
                    <span class="text-[11px] font-semibold text-brand-blue flex items-center gap-1 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        En vivo
                    </span>
                </div>
                <div class="relative w-full min-w-0 flex items-center">
                    <!-- Icono lupa centrado verticalmente -->
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>

                    <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                           placeholder="Escribe para buscar..."
                           autocomplete="off"
                           style="padding-left: 2.75rem !important; padding-right: 2.25rem !important;"
                           class="w-full h-11 rounded-xl border border-line bg-slate-50/60 text-xs sm:text-sm text-brand-dark placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all shadow-2xs min-w-0">

                    <!-- Acciones derecha (borrar / spinner) -->
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1 z-10">
                        <button type="button" id="btn-borrar-q" class="text-slate-400 hover:text-slate-600 text-xs cursor-pointer p-1 {{ $filtros['q'] ? '' : 'hidden' }}" title="Limpiar texto">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </button>
                        <span id="spinner-busqueda" class="hidden text-brand-blue text-xs p-1">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>
                        </span>
                    </div>
                </div>

                <!-- Dropdown flotante de sugerencias predictivas -->
                <div id="dropdown-sugerencias" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl border border-line/90 shadow-2xl z-50 max-h-72 overflow-y-auto divide-y divide-line/40">
                </div>
            </div>

            <!-- 2. Filtro por Área (Porción 1/4) -->
            <div class="w-full min-w-0">
                <label for="filtro-area" class="block text-xs font-heading font-bold text-slate-700 uppercase tracking-wider mb-1.5 truncate">
                    Área
                </label>
                <div class="relative w-full min-w-0">
                    <select id="filtro-area" name="area_id"
                            class="w-full h-11 px-3.5 rounded-xl border border-line bg-slate-50/60 text-xs sm:text-sm text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all cursor-pointer shadow-2xs min-w-0 truncate">
                        <option value="">Todas las áreas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}" {{ (string)$filtros['area_id'] === (string)$area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                        @endforeach
                        <option value="sin_area" {{ $filtros['area_id'] === 'sin_area' ? 'selected' : '' }}>Sin área asignada</option>
                    </select>
                </div>
            </div>

            <!-- 3. Filtro por Estado (Porción 1/4) -->
            <div class="w-full min-w-0">
                <label for="filtro-estado" class="block text-xs font-heading font-bold text-slate-700 uppercase tracking-wider mb-1.5 truncate">
                    Estado
                </label>
                <div class="relative w-full min-w-0">
                    <select id="filtro-estado" name="estado"
                            class="w-full h-11 px-3.5 rounded-xl border border-line bg-slate-50/60 text-xs sm:text-sm text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all cursor-pointer shadow-2xs min-w-0 truncate">
                        <option value="todos" {{ $filtros['estado'] === 'todos' ? 'selected' : '' }}>Todos los estados</option>
                        <option value="1" {{ $filtros['estado'] === '1' ? 'selected' : '' }}>Publicadas</option>
                        <option value="vencidas" {{ $filtros['estado'] === 'vencidas' ? 'selected' : '' }}>Vencidas ({{ $estadisticas['vencidas'] ?? 0 }})</option>
                        <option value="proximas" {{ $filtros['estado'] === 'proximas' ? 'selected' : '' }}>Próximas a vencer ({{ $estadisticas['proximas'] ?? 0 }})</option>
                        <option value="0" {{ $filtros['estado'] === '0' ? 'selected' : '' }}>Borradores</option>
                    </select>
                </div>
            </div>

            <!-- 4. Filtro por Creado por (Porción 1/4) -->
            <div class="w-full min-w-0">
                <label for="filtro-creador" class="block text-xs font-heading font-bold text-slate-700 uppercase tracking-wider mb-1.5 truncate">
                    Creado por
                </label>
                <div class="relative w-full min-w-0">
                    <select id="filtro-creador" name="creado_por"
                            class="w-full h-11 px-3.5 rounded-xl border border-line bg-slate-50/60 text-xs sm:text-sm text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all cursor-pointer shadow-2xs min-w-0 truncate">
                        <option value="">Todos los creadores</option>
                        @foreach ($creadores as $creador)
                            <option value="{{ $creador->id }}" {{ (string)$filtros['creado_por'] === (string)$creador->id ? 'selected' : '' }}>
                                {{ $creador->nombre_completo }} ({{ $creador->rol === 'ADMINISTRADOR' ? 'Admin' : 'Jefe' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <!-- Barra inferior de estado y botón Limpiar filtros ordenado a la derecha -->
        <div class="mt-4 pt-3.5 border-t border-line/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span id="contador-resultados" class="font-heading font-bold text-brand-dark">
                    Mostrando <strong class="text-brand-blue">{{ $capacitaciones->count() }}</strong> {{ \Illuminate\Support\Str::plural('capacitación', $capacitaciones->count()) }}
                    @if ($filtros['q'] || $filtros['area_id'] || $filtros['estado'] !== '1' || $filtros['creado_por'])
                        <span class="text-emerald-700 font-semibold">(con filtros aplicados)</span>
                    @endif
                </span>
                <span class="text-slate-300 hidden sm:inline">|</span>
                <span class="inline-flex items-center gap-1.5 text-slate-500 text-[11px]">
                    <i class="fa-solid fa-bolt-lightning text-amber-500"></i>
                    <span>Búsqueda predictiva activa</span>
                </span>
            </div>

            <!-- Botón Limpiar filtros ordenado a la derecha -->
            <button type="button" id="btn-limpiar-filtros"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-heading font-bold text-xs transition-all shadow-2xs hover:shadow-xs cursor-pointer shrink-0 {{ ($filtros['q'] || $filtros['area_id'] || ($filtros['estado'] !== '1' && $filtros['estado'] !== '') || $filtros['creado_por']) ? '' : 'hidden' }}">
                <i class="fa-solid fa-rotate-left text-xs"></i>
                <span>Limpiar filtros</span>
            </button>
        </div>
    </div>

    <!-- LISTA DE CAPACITACIONES EN GRID -->
    <div id="contenedor-cards-capacitaciones" class="grid grid-cols-1 xl:grid-cols-2 gap-5 sm:gap-6 transition-opacity duration-200">
        @forelse ($capacitaciones as $cap)
            @php
                $imagen = $cap->url_imagen;
                $activa = (bool) $cap->estado;
                $vencida = $cap->estaVencida();
                $proxima = $cap->proximaAVencer();
            @endphp
            <article id="capacitacion-{{ $cap->id }}"
                     data-titulo="{{ strtolower($cap->titulo) }}"
                     class="bg-white rounded-3xl border {{ $vencida ? 'border-rose-300 ring-1 ring-rose-200 shadow-rose-100/50' : ($proxima ? 'border-amber-300 ring-1 ring-amber-200 shadow-amber-100/50' : 'border-line/80') }} shadow-2xs hover:shadow-md transition-all overflow-hidden flex flex-col sm:flex-row {{ $activa ? '' : 'opacity-85' }}">

                <!-- Portada -->
                <div class="relative sm:w-48 h-44 sm:h-auto shrink-0 overflow-hidden bg-brand-light">
                    <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}"
                         class="w-full h-full object-cover {{ $activa ? '' : 'grayscale' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/10 to-transparent sm:bg-gradient-to-r"></div>

                    @if ($vencida)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-rose-600/95 text-white shadow-xs">
                            <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                            <span>Vencida</span>
                        </span>
                    @elseif ($proxima)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-amber-500/95 text-white shadow-xs">
                            <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                            <span>Por vencer</span>
                        </span>
                    @elseif ($activa)
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-emerald-600/90 text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            <span>Activa</span>
                        </span>
                    @else
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-amber-600/90 text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                            <span>Borrador / Inactiva</span>
                        </span>
                    @endif
                </div>

                <!-- Detalle -->
                <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between gap-4 min-w-0">
                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1 flex-wrap">
                                <span class="text-[10px] uppercase font-bold tracking-wider text-muted">
                                    Creado por: <strong class="text-brand-dark">{{ $cap->creador->nombre_completo ?? 'Administración' }}</strong>
                                </span>
                                @if ($vencida)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold">
                                        <i class="fa-solid fa-triangle-exclamation text-rose-500"></i>
                                        <span>Venció el {{ $cap->fecha_limite->format('d/m/Y') }}</span>
                                    </span>
                                @elseif ($cap->proximaAVencer())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold">
                                        <i class="fa-solid fa-clock text-amber-500"></i>
                                        <span>Vence el {{ $cap->fecha_limite->format('d/m/Y') }}</span>
                                    </span>
                                @elseif ($cap->fecha_limite)
                                    <span class="text-[10px] text-muted">
                                        <i class="fa-regular fa-calendar-check text-[10px]"></i> Límite: {{ $cap->fecha_limite->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight text-brand-dark line-clamp-2">{{ $cap->titulo }}</h3>
                            <p class="text-[11px] text-muted mt-1">
                                {{ $cap->modulos_activos_count }} {{ \Illuminate\Support\Str::plural('módulo', $cap->modulos_activos_count) }}
                                · Min. Aprobación {{ rtrim(rtrim(number_format((float) $cap->porcentaje_aprobacion, 2), '0'), '.') }}%
                                · {{ $cap->evaluaciones_count }} {{ \Illuminate\Support\Str::plural('evaluación', $cap->evaluaciones_count) }}
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

                        <!-- PORCENTAJE COMPLETADO (REEMPLAZO DE 'COLABORADORES HAN PARTICIPADO') -->
                        <div class="space-y-1.5 pt-2 border-t border-line/60">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-chart-pie text-brand-blue text-xs"></i>
                                    <span>Porcentaje completado</span>
                                </span>
                                <span class="font-extrabold text-xs px-2 py-0.5 rounded-lg {{ $cap->porcentaje_completado >= 100 ? 'bg-emerald-100 text-emerald-800' : ($cap->porcentaje_completado > 0 ? 'bg-blue-100 text-brand-blue' : 'bg-slate-100 text-slate-600') }}">
                                    {{ $cap->porcentaje_completado }}%
                                </span>
                            </div>

                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $cap->porcentaje_completado >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-brand-blue to-cyan-500' }}"
                                     style="width: {{ $cap->porcentaje_completado }}%"></div>
                            </div>

                            <div class="text-[11px] text-slate-500 flex items-center justify-between pt-0.5">
                                <span><strong class="text-brand-dark font-semibold">{{ $cap->participantes_count }}</strong> {{ \Illuminate\Support\Str::plural('participante', $cap->participantes_count) }} @if($cap->usuarios_completaron_count > 0)· {{ $cap->usuarios_completaron_count }} aprobados @endif</span>
                                <span class="text-muted">Alcance: ~{{ $cap->empleados_objetivo }} empleados</span>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                        <a href="{{ route('admin.capacitaciones.edit', $cap) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Editar</span>
                        </a>

                        <a href="{{ route('admin.evaluaciones.create', ['capacitacion' => $cap->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-plus"></i>
                            <span>Evaluación</span>
                        </a>

                        <a href="{{ route('admin.asignaciones.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                            <i class="fa-solid fa-diagram-project"></i>
                            <span>Áreas</span>
                        </a>

                        <form method="POST" action="{{ route('admin.capacitaciones.estado', $cap) }}" class="ml-auto m-0"
                              onsubmit="return confirm('¿{{ $activa ? 'Desactivar esta capacitación? Dejará de mostrarse a los empleados pero conservará todo su historial.' : '¿Activar nuevamente esta capacitación?' }}');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-heading font-bold border transition-colors cursor-pointer {{ $activa ? 'border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                {{ $activa ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-3xl border border-line/80 p-12 text-center space-y-3">
                <i class="fa-solid fa-book-open text-4xl text-slate-300"></i>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">No se encontraron capacitaciones</h3>
                <p class="text-xs text-muted max-w-sm mx-auto">Prueba ajustando los términos de búsqueda o los filtros de área, estado o creador.</p>
                <div class="pt-2">
                    <button type="button" onclick="document.getElementById('btn-limpiar-filtros')?.click();"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Restablecer Filtros</span>
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- SCRIPT DE BÚSQUEDA PREDICTIVA Y FILTRADO EN VIVO --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sugerenciasData = @json($sugerencias ?? []);
    const formFiltro = document.getElementById('form-filtro-capacitaciones');
    const inputQ = document.getElementById('filtro-q');
    const btnBorrarQ = document.getElementById('btn-borrar-q');
    const spinnerBusqueda = document.getElementById('spinner-busqueda');
    const dropdownSugerencias = document.getElementById('dropdown-sugerencias');
    const selectArea = document.getElementById('filtro-area');
    const selectEstado = document.getElementById('filtro-estado');
    const selectCreador = document.getElementById('filtro-creador');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    const contenedorCards = document.getElementById('contenedor-cards-capacitaciones');
    const contadorResultados = document.getElementById('contador-resultados');

    let debounceTimer = null;
    let abortController = null;
    let indiceSugerenciaActiva = -1;

    // Renderizar sugerencias predictivas flotantes
    function actualizarSugerencias(query) {
        if (!dropdownSugerencias) return;
        const texto = query.trim().toLowerCase();

        if (texto.length === 0) {
            dropdownSugerencias.classList.add('hidden');
            dropdownSugerencias.innerHTML = '';
            indiceSugerenciaActiva = -1;
            return;
        }

        const coincidencias = sugerenciasData.filter(s =>
            s.titulo.toLowerCase().includes(texto) ||
            (s.areas && s.areas.toLowerCase().includes(texto))
        ).slice(0, 6);

        if (coincidencias.length === 0) {
            dropdownSugerencias.innerHTML = `
                <div class="px-4 py-3 text-xs text-slate-500 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-slate-400"></i>
                    <span>Sin coincidencias predictivas exactas para "<strong>${escapeHtml(query)}</strong>"</span>
                </div>
            `;
            dropdownSugerencias.classList.remove('hidden');
            indiceSugerenciaActiva = -1;
            return;
        }

        let html = '';
        coincidencias.forEach((item, index) => {
            const regex = new RegExp(`(${escapeRegex(texto)})`, 'gi');
            const tituloResaltado = escapeHtml(item.titulo).replace(regex, '<span class="bg-amber-100 text-brand-dark font-black px-0.5 rounded">$1</span>');

            html += `
                <div class="item-sugerencia px-4 py-2.5 hover:bg-slate-50 cursor-pointer transition-colors flex items-center justify-between gap-3 text-xs"
                     data-index="${index}" data-titulo="${escapeHtml(item.titulo)}" data-id="${item.id}">
                    <div class="min-w-0 flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-brand-light text-brand-blue flex items-center justify-center shrink-0 text-[11px]">
                            <i class="fa-solid fa-book-open"></i>
                        </span>
                        <div class="truncate">
                            <span class="font-heading font-bold text-slate-800 block truncate">${tituloResaltado}</span>
                            ${item.areas ? `<span class="text-[10px] text-muted block truncate">${escapeHtml(item.areas)}</span>` : ''}
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 ${item.estado === 'Vencida' ? 'bg-rose-50 text-rose-700 border border-rose-200' : (item.estado === 'Por vencer' ? 'bg-amber-50 text-amber-800 border border-amber-300' : (item.estado === 'Activa' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200'))}">
                        ${item.estado}
                    </span>
                </div>
            `;
        });

        dropdownSugerencias.innerHTML = html;
        dropdownSugerencias.classList.remove('hidden');
        indiceSugerenciaActiva = -1;

        // Clicks en sugerencias
        dropdownSugerencias.querySelectorAll('.item-sugerencia').forEach(el => {
            el.addEventListener('click', function () {
                const titulo = this.getAttribute('data-titulo');
                const id = this.getAttribute('data-id');
                if (inputQ) inputQ.value = titulo;
                dropdownSugerencias.classList.add('hidden');
                ejecutarFiltroEnVivo(() => {
                    const card = document.getElementById('capacitacion-' + id);
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        card.classList.add('ring-2', 'ring-brand-blue', 'scale-[1.01]');
                        setTimeout(() => card.classList.remove('ring-2', 'ring-brand-blue', 'scale-[1.01]'), 2000);
                    }
                });
            });
        });
    }

    // Navegación con teclado en las sugerencias
    inputQ?.addEventListener('keydown', function (e) {
        if (!dropdownSugerencias || dropdownSugerencias.classList.contains('hidden')) return;

        const items = dropdownSugerencias.querySelectorAll('.item-sugerencia');
        if (items.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            indiceSugerenciaActiva = (indiceSugerenciaActiva + 1) % items.length;
            resaltarItemSugerencia(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            indiceSugerenciaActiva = (indiceSugerenciaActiva - 1 + items.length) % items.length;
            resaltarItemSugerencia(items);
        } else if (e.key === 'Enter') {
            if (indiceSugerenciaActiva >= 0 && items[indiceSugerenciaActiva]) {
                e.preventDefault();
                items[indiceSugerenciaActiva].click();
            }
        } else if (e.key === 'Escape') {
            dropdownSugerencias.classList.add('hidden');
        }
    });

    function resaltarItemSugerencia(items) {
        items.forEach((it, idx) => {
            if (idx === indiceSugerenciaActiva) {
                it.classList.add('bg-brand-light');
                it.scrollIntoView({ block: 'nearest' });
            } else {
                it.classList.remove('bg-brand-light');
            }
        });
    }

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#filtro-q') && !e.target.closest('#dropdown-sugerencias')) {
            dropdownSugerencias?.classList.add('hidden');
        }
    });

    // Petición AJAX en tiempo real con debounce
    function ejecutarFiltroEnVivo(callback = null) {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        const params = new URLSearchParams();
        const qVal = inputQ?.value.trim() ?? '';
        const areaVal = selectArea?.value ?? '';
        const estadoVal = selectEstado?.value ?? '';
        const creadorVal = selectCreador?.value ?? '';

        if (qVal) params.set('q', qVal);
        if (areaVal) params.set('area_id', areaVal);
        if (estadoVal !== '' && estadoVal !== 'todos') params.set('estado', estadoVal);
        if (creadorVal) params.set('creado_por', creadorVal);

        const url = formFiltro ? formFiltro.action + (params.toString() ? '?' + params.toString() : '') : window.location.pathname;

        // Botón borrar texto
        if (btnBorrarQ && inputQ) {
            if (inputQ.value.trim().length > 0) btnBorrarQ.classList.remove('hidden');
            else btnBorrarQ.classList.add('hidden');
        }

        // Control botón limpiar
        const hayFiltros = Boolean(
            qVal ||
            areaVal ||
            (estadoVal !== '' && estadoVal !== 'todos' && estadoVal !== '1') ||
            creadorVal
        );
        if (btnLimpiar) {
            if (hayFiltros) {
                btnLimpiar.classList.remove('hidden');
            } else {
                btnLimpiar.classList.add('hidden');
            }
        }

        // Indicadores visuales de carga
        if (spinnerBusqueda) spinnerBusqueda.classList.remove('hidden');
        if (contenedorCards) contenedorCards.classList.add('opacity-50');

        fetch(url, {
            signal: abortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error al filtrar capacitaciones');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Actualizar grid de capacitaciones
            const nuevasCards = doc.getElementById('contenedor-cards-capacitaciones');
            if (nuevasCards && contenedorCards) {
                contenedorCards.innerHTML = nuevasCards.innerHTML;
            }

            // Actualizar contador
            const nuevoContador = doc.getElementById('contador-resultados');
            if (nuevoContador && contadorResultados) {
                contadorResultados.innerHTML = nuevoContador.innerHTML;
            }

            // Actualizar pestañas numéricas si cambiaron
            const nuevoTotalActivas = doc.getElementById('badge-total-activas');
            const totalActivas = document.getElementById('badge-total-activas');
            if (nuevoTotalActivas && totalActivas) totalActivas.innerHTML = nuevoTotalActivas.innerHTML;

            const nuevoTotalVencidas = doc.getElementById('badge-total-vencidas');
            const totalVencidas = document.getElementById('badge-total-vencidas');
            if (nuevoTotalVencidas && totalVencidas) totalVencidas.innerHTML = nuevoTotalVencidas.innerHTML;

            const nuevoTotalBorradores = doc.getElementById('badge-total-borradores');
            const totalBorradores = document.getElementById('badge-total-borradores');
            if (nuevoTotalBorradores && totalBorradores) totalBorradores.innerHTML = nuevoTotalBorradores.innerHTML;

            window.history.replaceState({}, '', url);

            if (typeof callback === 'function') callback();
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error en búsqueda de capacitaciones:', err);
            }
        })
        .finally(() => {
            if (spinnerBusqueda) spinnerBusqueda.classList.add('hidden');
            if (contenedorCards) contenedorCards.classList.remove('opacity-50');
        });
    }

    // Eventos de entrada con predictivo y debounce
    inputQ?.addEventListener('input', function () {
        const val = this.value;
        actualizarSugerencias(val);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(ejecutarFiltroEnVivo, 250);
    });

    inputQ?.addEventListener('focus', function () {
        if (this.value.trim().length > 0) {
            actualizarSugerencias(this.value);
        }
    });

    // Botón borrar texto de búsqueda
    btnBorrarQ?.addEventListener('click', function () {
        if (inputQ) {
            inputQ.value = '';
            inputQ.focus();
            if (dropdownSugerencias) dropdownSugerencias.classList.add('hidden');
            ejecutarFiltroEnVivo();
        }
    });

    // Filtros por selects instantáneos
    selectArea?.addEventListener('change', () => ejecutarFiltroEnVivo());
    selectEstado?.addEventListener('change', () => ejecutarFiltroEnVivo());
    selectCreador?.addEventListener('change', () => ejecutarFiltroEnVivo());

    // Botón Limpiar filtros
    btnLimpiar?.addEventListener('click', function (e) {
        e.preventDefault();
        if (inputQ) inputQ.value = '';
        if (selectArea) selectArea.value = '';
        if (selectEstado) selectEstado.value = '1';
        if (selectCreador) selectCreador.value = '';
        if (dropdownSugerencias) dropdownSugerencias.classList.add('hidden');
        ejecutarFiltroEnVivo();
    });

    // Evitar recarga tradicional del formulario
    formFiltro?.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        if (dropdownSugerencias) dropdownSugerencias.classList.add('hidden');
        ejecutarFiltroEnVivo();
    });

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
});
</script>
@endsection

