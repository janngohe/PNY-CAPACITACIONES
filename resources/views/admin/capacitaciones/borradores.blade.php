@extends('layouts.admin')

@section('title', 'Borradores de Capacitaciones')
@section('page_title', 'Borradores de Capacitaciones')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Bandeja de Borradores',
        'titulo' => 'Borradores de Capacitaciones',
        'descripcion' => 'Capacitaciones institucionales en proceso de creación o edición. No son visibles para los empleados hasta que sean publicadas formalmente.',
        'icono' => 'fa-file-lines',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y PESTAÑAS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-4">
        <div class="inline-flex p-1 bg-slate-100 rounded-2xl border border-line shadow-2xs">
            <a href="{{ route('admin.capacitaciones.index', ['estado' => '1']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold text-slate-600 hover:text-brand-blue hover:bg-white/60 transition-all">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                <span>Publicadas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-700 font-bold">
                    {{ $estadisticas['activas'] }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.index', ['estado' => 'vencidas']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold text-slate-600 hover:text-rose-600 hover:bg-white/60 transition-all">
                <i class="fa-solid fa-clock-rotate-left text-rose-500 text-xs"></i>
                <span>Vencidas</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($estadisticas['vencidas'] ?? 0) > 0 ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }} font-bold">
                    {{ $estadisticas['vencidas'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.index', ['estado' => 'proximas']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold text-slate-600 hover:text-amber-600 hover:bg-white/60 transition-all">
                <i class="fa-solid fa-hourglass-half text-amber-500 text-xs"></i>
                <span>Por vencer</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ ($estadisticas['proximas'] ?? 0) > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700' }} font-bold">
                    {{ $estadisticas['proximas'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.capacitaciones.borradores') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-heading font-bold bg-white text-brand-blue shadow-xs border border-line/60 transition-all">
                <i class="fa-solid fa-file-pen text-amber-500 text-xs"></i>
                <span>Borradores</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-bold">
                    {{ $estadisticas['borradores'] }}
                </span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.capacitaciones.create') }}" id="btn-crear-capacitacion"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Nueva Capacitación</span>
            </a>
        </div>
    </div>

    <!-- BUSCADOR PREDICTIVO Y FILTROS EN BORRADORES -->
    <div class="bg-white p-5 rounded-3xl border border-line/80 shadow-2xs w-full overflow-hidden">
        <form id="form-filtro-borradores" method="GET" action="{{ route('admin.capacitaciones.borradores') }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4 w-full min-w-0">
            <!-- 1. Buscador predictivo (Porción 1/3) -->
            <div class="relative w-full min-w-0">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="filtro-q" class="block text-xs font-heading font-bold text-slate-700 uppercase tracking-wider truncate">
                        Buscador predictivo
                    </label>
                    <span class="text-[11px] font-semibold text-brand-blue flex items-center gap-1.5 shrink-0">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Borradores
                    </span>
                </div>
                <div class="relative w-full min-w-0 flex items-center">
                    <!-- Icono lupa centrado verticalmente -->
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>

                    <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                           placeholder="Buscar en borradores..."
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

            <!-- 2. Filtro por Área (Porción 1/3) -->
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

            <!-- 3. Filtro por Creado por (Porción 1/3) -->
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
                                {{ $creador->nombre_completo }}
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
                    Mostrando <strong class="text-brand-blue">{{ $capacitaciones->count() }}</strong> {{ \Illuminate\Support\Str::plural('borrador', $capacitaciones->count()) }}
                    @if ($filtros['q'] || $filtros['area_id'] || $filtros['creado_por'])
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
            @php
                $hayFiltrosActivos = (bool) ($filtros['q'] || $filtros['area_id'] || $filtros['creado_por']);
            @endphp
            <button type="button" id="btn-limpiar-filtros"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-heading font-bold text-xs transition-all shadow-2xs hover:shadow-xs cursor-pointer shrink-0 {{ $hayFiltrosActivos ? '' : 'hidden' }}">
                <i class="fa-solid fa-rotate-left text-xs"></i>
                <span>Limpiar filtros</span>
            </button>
        </div>
    </div>

    <!-- LISTA DE BORRADORES EN GRID -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-heading font-bold text-base text-brand-dark">Catálogo en Borrador</h3>
                <p class="text-xs text-muted">Revisa contenidos, asignaciones a áreas y publica cuando esté completado.</p>
            </div>
            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                {{ $capacitaciones->count() }} {{ \Illuminate\Support\Str::plural('borrador', $capacitaciones->count()) }}
            </span>
        </div>

        <div id="contenedor-cards-capacitaciones" class="grid grid-cols-1 xl:grid-cols-2 gap-5 sm:gap-6 transition-opacity duration-200">
            @forelse ($capacitaciones as $cap)
                @php
                    $imagen = $cap->url_imagen;
                @endphp
                <article id="borrador-{{ $cap->id }}"
                         class="bg-white rounded-3xl border-2 border-dashed border-amber-200/90 shadow-2xs hover:shadow-md transition-all overflow-hidden flex flex-col sm:flex-row bg-gradient-to-br from-white via-white to-amber-50/20">

                    <!-- Portada -->
                    <div class="relative sm:w-48 h-44 sm:h-auto shrink-0 overflow-hidden bg-slate-200">
                        <img src="{{ $imagen }}" alt="Portada de {{ $cap->titulo }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/70 via-brand-dark/20 to-transparent sm:bg-gradient-to-r"></div>

                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-amber-500/90 text-white shadow-xs">
                            <i class="fa-solid fa-file-pen text-[10px]"></i>
                            Borrador
                        </span>
                    </div>

                    <!-- Detalle -->
                    <div class="flex-1 p-5 sm:p-6 flex flex-col justify-between gap-4 min-w-0">
                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-muted">
                                        Creado por: <strong class="text-brand-dark">{{ $cap->creador->nombre_completo ?? 'Administración' }}</strong>
                                    </span>
                                </div>
                                <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight text-brand-dark line-clamp-2">{{ $cap->titulo }}</h3>
                                <p class="text-[11px] text-muted mt-1">
                                    {{ $cap->modulos_activos_count }} {{ \Illuminate\Support\Str::plural('módulo', $cap->modulos_activos_count) }}
                                    · {{ $cap->evaluaciones_count }} {{ \Illuminate\Support\Str::plural('evaluación', $cap->evaluaciones_count) }}
                                    @if ($cap->duracion_estimada) · {{ $cap->duracion_estimada }} h @endif
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
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-line/60">
                            <!-- Publicar Ahora -->
                            <form method="POST" action="{{ route('admin.capacitaciones.publicar', $cap) }}" class="m-0"
                                  onsubmit="return confirm('¿Deseas publicar esta capacitación institucional? Estará activa de inmediato para las áreas asignadas.');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:brightness-110 text-white font-heading font-extrabold text-xs shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                    <span>Publicar Ahora</span>
                                </button>
                            </form>

                            <a href="{{ route('admin.capacitaciones.edit', $cap) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-xs transition-colors">
                                <i class="fa-regular fa-pen-to-square text-xs"></i>
                                <span>Editar</span>
                            </a>

                            <!-- Descartar Borrador -->
                            <form method="POST" action="{{ route('admin.capacitaciones.destroy', $cap) }}" class="ml-auto m-0"
                                  onsubmit="return confirm('¿Estás seguro de descartar este borrador institucional definitivamente?');">
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
                <div class="col-span-full bg-white rounded-3xl border border-line/80 p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fa-solid fa-file-circle-check text-2xl"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">No hay capacitaciones en borrador</h3>
                    <p class="text-xs text-muted max-w-md mx-auto">
                        Todas las capacitaciones institucionales se encuentran publicadas o no coinciden con los filtros aplicados.
                    </p>
                    <div class="pt-2 flex justify-center gap-3">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sugerenciasData = @json($sugerencias ?? []);
    const formFiltro = document.getElementById('form-filtro-borradores');
    const inputQ = document.getElementById('filtro-q');
    const btnBorrarQ = document.getElementById('btn-borrar-q');
    const spinnerBusqueda = document.getElementById('spinner-busqueda');
    const dropdownSugerencias = document.getElementById('dropdown-sugerencias');
    const selectArea = document.getElementById('filtro-area');
    const selectCreador = document.getElementById('filtro-creador');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    const contenedorCards = document.getElementById('contenedor-cards-capacitaciones');

    let debounceTimer = null;
    let abortController = null;

    function actualizarSugerencias(query) {
        if (!dropdownSugerencias) return;
        const texto = query.trim().toLowerCase();
        if (texto.length === 0) {
            dropdownSugerencias.classList.add('hidden');
            dropdownSugerencias.innerHTML = '';
            return;
        }

        const coincidencias = sugerenciasData.filter(s =>
            s.titulo.toLowerCase().includes(texto) ||
            (s.areas && s.areas.toLowerCase().includes(texto))
        ).slice(0, 6);

        if (coincidencias.length === 0) {
            dropdownSugerencias.innerHTML = `
                <div class="px-4 py-3 text-xs text-slate-500">
                    Sin coincidencias para "<strong>${query}</strong>"
                </div>
            `;
            dropdownSugerencias.classList.remove('hidden');
            return;
        }

        let html = '';
        coincidencias.forEach(item => {
            html += `
                <div class="item-sugerencia px-4 py-2.5 hover:bg-slate-50 cursor-pointer transition-colors flex items-center justify-between gap-3 text-xs"
                     data-titulo="${item.titulo}">
                    <span class="font-heading font-bold text-slate-800 truncate">${item.titulo}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        Borrador
                    </span>
                </div>
            `;
        });

        dropdownSugerencias.innerHTML = html;
        dropdownSugerencias.classList.remove('hidden');

        dropdownSugerencias.querySelectorAll('.item-sugerencia').forEach(el => {
            el.addEventListener('click', function () {
                if (inputQ) inputQ.value = this.getAttribute('data-titulo');
                dropdownSugerencias.classList.add('hidden');
                ejecutarFiltroEnVivo();
            });
        });
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#filtro-q') && !e.target.closest('#dropdown-sugerencias')) {
            dropdownSugerencias?.classList.add('hidden');
        }
    });

    function ejecutarFiltroEnVivo() {
        if (abortController) abortController.abort();
        abortController = new AbortController();

        const params = new URLSearchParams();
        const qVal = inputQ?.value.trim() ?? '';
        const areaVal = selectArea?.value ?? '';
        const creadorVal = selectCreador?.value ?? '';

        if (qVal) params.set('q', qVal);
        if (areaVal) params.set('area_id', areaVal);
        if (creadorVal) params.set('creado_por', creadorVal);

        const url = formFiltro ? formFiltro.action + (params.toString() ? '?' + params.toString() : '') : window.location.pathname;

        if (btnBorrarQ && inputQ) {
            if (inputQ.value.trim().length > 0) btnBorrarQ.classList.remove('hidden');
            else btnBorrarQ.classList.add('hidden');
        }

        const hayFiltros = Boolean(qVal || areaVal || creadorVal);
        if (btnLimpiar) {
            if (hayFiltros) {
                btnLimpiar.classList.remove('hidden');
            } else {
                btnLimpiar.classList.add('hidden');
            }
        }

        if (spinnerBusqueda) spinnerBusqueda.classList.remove('hidden');
        if (contenedorCards) contenedorCards.classList.add('opacity-50');

        fetch(url, {
            signal: abortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const nuevasCards = doc.getElementById('contenedor-cards-capacitaciones');
            if (nuevasCards && contenedorCards) {
                contenedorCards.innerHTML = nuevasCards.innerHTML;
            }

            const nuevoContador = doc.getElementById('contador-resultados');
            const contadorEl = document.getElementById('contador-resultados');
            if (nuevoContador && contadorEl) {
                contadorEl.innerHTML = nuevoContador.innerHTML;
            }

            window.history.replaceState({}, '', url);
        })
        .catch(err => {
            if (err.name !== 'AbortError') console.error(err);
        })
        .finally(() => {
            if (spinnerBusqueda) spinnerBusqueda.classList.add('hidden');
            if (contenedorCards) contenedorCards.classList.remove('opacity-50');
        });
    }

    inputQ?.addEventListener('input', function () {
        actualizarSugerencias(this.value);
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(ejecutarFiltroEnVivo, 250);
    });

    btnBorrarQ?.addEventListener('click', function () {
        if (inputQ) {
            inputQ.value = '';
            inputQ.focus();
            dropdownSugerencias?.classList.add('hidden');
            ejecutarFiltroEnVivo();
        }
    });

    selectArea?.addEventListener('change', ejecutarFiltroEnVivo);
    selectCreador?.addEventListener('change', ejecutarFiltroEnVivo);

    btnLimpiar?.addEventListener('click', function (e) {
        e.preventDefault();
        if (inputQ) inputQ.value = '';
        if (selectArea) selectArea.value = '';
        if (selectCreador) selectCreador.value = '';
        dropdownSugerencias?.classList.add('hidden');
        ejecutarFiltroEnVivo();
    });

    formFiltro?.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        dropdownSugerencias?.classList.add('hidden');
        ejecutarFiltroEnVivo();
    });
});
</script>
@endsection
