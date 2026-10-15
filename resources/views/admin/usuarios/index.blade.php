@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('page_title', 'Gestión de Usuarios')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Seguridad y Accesos',
        'titulo' => 'Usuarios de la Plataforma',
        'descripcion' => 'Registra nuevos colaboradores, edita sus datos, asigna roles y áreas, o desactiva cuentas cuando se requiera (se conserva todo su historial).',
        'icono' => 'fa-users-gear',
    ])

    <!-- BARRA SUPERIOR DE ACCIONES Y RESUMEN -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div id="contenedor-resumen-usuarios" class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-line text-xs font-semibold text-slate-700 shadow-2xs">
                Total: <strong class="text-brand-dark">{{ $resumen['total'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800">
                Activos: <strong>{{ $resumen['activos'] }}</strong>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 border border-line text-xs font-semibold text-slate-600">
                Inactivos: <strong>{{ $resumen['inactivos'] }}</strong>
            </span>
        </div>

        <a href="{{ route('admin.usuarios.create') }}" id="btn-crear-usuario"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors w-full sm:w-auto cursor-pointer">
            <i class="fa-solid fa-user-plus"></i>
            <span>Registrar Nuevo Usuario</span>
        </a>
    </div>

    <!-- FILTROS DE BÚSQUEDA EN TIEMPO REAL -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-line/80 shadow-2xs">
        <form method="GET" action="{{ route('admin.usuarios.index') }}" id="form-filtro-usuarios" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-1">
                    <label for="filtro-q" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">Buscar por nombre o cédula</label>
                    <span id="badge-filtro-vivo" class="text-[10px] font-semibold text-brand-blue flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        En vivo
                    </span>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" id="filtro-q" name="q" value="{{ $filtros['q'] }}"
                           placeholder="Escribe nombre o cédula..."
                           autocomplete="off"
                           class="w-full pl-9 pr-8 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark placeholder:text-slate-400 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                    <button type="button" id="btn-borrar-q" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer {{ $filtros['q'] ? '' : 'hidden' }}" title="Limpiar texto">
                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                    </button>
                </div>
            </div>

            <div>
                <label for="filtro-rol" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Rol</label>
                <select id="filtro-rol" name="rol"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todos los roles</option>
                    @foreach ($roles as $clave => $nombre)
                        <option value="{{ $clave }}" {{ $filtros['rol'] === $clave ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-area" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Área</label>
                <select id="filtro-area" name="area_id"
                        class="w-full px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ (string)$filtros['areaId'] === (string)$area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filtro-estado" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Estado</label>
                <div class="flex gap-2">
                    <select id="filtro-estado" name="estado"
                            class="flex-1 px-3 py-2 rounded-xl border border-line bg-slate-50/50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue transition-colors cursor-pointer">
                        <option value="">Todos</option>
                        <option value="1" {{ $filtros['estado'] === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ $filtros['estado'] === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                    <button type="button" id="btn-limpiar-filtros-usuarios"
                            class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 text-xs font-bold transition-colors inline-flex items-center justify-center gap-1.5 cursor-pointer shrink-0 {{ ($filtros['q'] || $filtros['rol'] || $filtros['areaId'] || $filtros['estado'] !== null) ? '' : 'hidden' }}"
                            title="Limpiar todos los filtros">
                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                        <span>Limpiar</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- LISTADO DE USUARIOS (RESPONSIVE: TARJETAS EN MÓVIL, TABLA EN ESCRITORIO) -->
    <div id="contenedor-tabla-usuarios" class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-opacity duration-200">
        
        <!-- VISTA MÓVIL: TARJETAS TOUCH-FRIENDLY (md:hidden) -->
        <div class="block md:hidden divide-y divide-line/60">
            @forelse ($usuarios as $u)
                @php
                    $rolBadge = match($u->rol) {
                        'ADMINISTRADOR' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'JEFE_AREA' => 'bg-blue-50 text-brand-blue border-blue-200',
                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                    };
                @endphp
                <div class="p-4 space-y-3 transition-colors {{ $u->estado ? 'bg-white' : 'opacity-70 bg-slate-50/60' }}">
                    <!-- Fila 1: Avatar, Nombre, Estado -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                {{ strtoupper(substr($u->nombre_completo, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-heading font-bold text-sm text-brand-dark truncate leading-tight">{{ $u->nombre_completo }}</h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[11px] font-mono text-slate-500 font-semibold">C.C. {{ $u->identificacion }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Estado Badge -->
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0 {{ $u->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $u->estado ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            {{ $u->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    @if ($u->usuario_nuevo)
                        <div>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                <i class="fa-solid fa-clock-rotate-left text-[9px]"></i> Primer ingreso pendiente
                            </span>
                        </div>
                    @endif

                    <!-- Fila 2: Rol y Área -->
                    <div class="grid grid-cols-2 gap-2 text-xs pt-1 border-t border-line/40">
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-line/60">
                            <span class="text-[9px] uppercase font-bold text-muted block mb-0.5">Rol Institucional</span>
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $rolBadge }}">
                                {{ $roles[$u->rol] ?? $u->rol }}
                            </span>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-xl border border-line/60">
                            <span class="text-[9px] uppercase font-bold text-muted block mb-0.5">Área Asignada</span>
                            <span class="text-xs font-semibold text-slate-700 truncate block">
                                {{ $u->area->nombre ?? '— Sin área —' }}
                            </span>
                        </div>
                    </div>

                    <!-- Fila 3: Acciones touch-friendly en móvil -->
                    <div class="flex items-center gap-2 pt-2 border-t border-line/50">
                        <a href="{{ route('admin.usuarios.edit', $u) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-slate-100 hover:bg-brand-light text-slate-700 hover:text-brand-blue font-heading font-bold text-xs transition-colors shadow-2xs">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span>Editar</span>
                        </a>

                        <form method="POST" action="{{ route('admin.usuarios.restablecer', $u) }}" class="flex-1 m-0"
                              onsubmit="return confirm('¿Restablecer la contraseña de {{ $u->nombre_completo }} a su número de cédula ({{ $u->identificacion }})?');">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-heading font-bold text-xs border border-amber-200 transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-key text-xs"></i>
                                <span>Clave Cédula</span>
                            </button>
                        </form>

                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.usuarios.estado', $u) }}" class="m-0"
                                  onsubmit="return confirm('¿{{ $u->estado ? 'Desactivar' : 'Activar' }} al usuario {{ $u->nombre_completo }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-1.5 p-2 rounded-xl border transition-colors shadow-2xs cursor-pointer {{ $u->estado ? 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}"
                                        title="{{ $u->estado ? 'Desactivar acceso' : 'Activar acceso' }}"
                                        aria-label="{{ $u->estado ? 'Desactivar acceso' : 'Activar acceso' }}">
                                    <i class="fa-solid {{ $u->estado ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-muted">
                    <i class="fa-solid fa-users text-3xl mb-2 text-slate-300 block"></i>
                    No se encontraron usuarios con los filtros aplicados.
                </div>
            @endforelse
        </div>

        <!-- VISTA ESCRITORIO / TABLET: TABLA COMPLETA (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                    <tr>
                        <th class="px-5 sm:px-6 py-3.5 font-bold">Colaborador</th>
                        <th class="px-4 py-3.5 font-bold">Identificación (C.C.)</th>
                        <th class="px-4 py-3.5 font-bold">Rol</th>
                        <th class="px-4 py-3.5 font-bold">Área Asignada</th>
                        <th class="px-4 py-3.5 font-bold text-center">Estado</th>
                        <th class="px-5 sm:px-6 py-3.5 font-bold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line/50">
                    @forelse ($usuarios as $u)
                        <tr class="hover:bg-brand-light/30 transition-colors {{ $u->estado ? '' : 'opacity-60 bg-slate-50/50' }}">
                            <td class="px-5 sm:px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                        {{ strtoupper(substr($u->nombre_completo, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-brand-dark truncate leading-tight">{{ $u->nombre_completo }}</p>
                                        @if ($u->usuario_nuevo)
                                            <span class="inline-block text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.2 rounded mt-0.5">Primer ingreso pendiente</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700 font-semibold">{{ $u->identificacion }}</td>
                            <td class="px-4 py-3.5">
                                @php
                                    $rolBadge = match($u->rol) {
                                        'ADMINISTRADOR' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'JEFE_AREA' => 'bg-blue-50 text-brand-blue border-blue-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $rolBadge }}">
                                    {{ $roles[$u->rol] ?? $u->rol }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600">
                                {{ $u->area->nombre ?? '— Sin área —' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $u->estado ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $u->estado ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $u->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 sm:px-6 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.usuarios.edit', $u) }}"
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-600 transition-colors"
                                       title="Editar usuario">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.usuarios.restablecer', $u) }}" class="m-0"
                                          onsubmit="return confirm('¿Restablecer la contraseña de {{ $u->nombre_completo }} a su número de cédula ({{ $u->identificacion }})? Se le solicitará cambiarla al ingresar.');">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 rounded-lg bg-slate-100 hover:bg-amber-100 hover:text-amber-800 text-slate-600 transition-colors cursor-pointer"
                                                title="Restablecer contraseña a la cédula">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                    </form>

                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.usuarios.estado', $u) }}" class="m-0"
                                              onsubmit="return confirm('¿{{ $u->estado ? 'Desactivar' : 'Activar' }} al usuario {{ $u->nombre_completo }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="p-2 rounded-lg border transition-colors cursor-pointer {{ $u->estado ? 'border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-700' : 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}"
                                                    title="{{ $u->estado ? 'Desactivar acceso' : 'Activar acceso' }}">
                                                <i class="fa-solid {{ $u->estado ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-muted">
                                <i class="fa-solid fa-users text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron usuarios con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usuarios->hasPages())
            <div class="px-6 py-4 border-t border-line/60 bg-slate-50/50">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formFiltro = document.getElementById('form-filtro-usuarios');
    const inputQ = document.getElementById('filtro-q');
    const selectRol = document.getElementById('filtro-rol');
    const selectArea = document.getElementById('filtro-area');
    const selectEstado = document.getElementById('filtro-estado');
    const btnBorrarQ = document.getElementById('btn-borrar-q');
    const btnLimpiar = document.getElementById('btn-limpiar-filtros-usuarios');
    const contenedorTabla = document.getElementById('contenedor-tabla-usuarios');
    const contenedorResumen = document.getElementById('contenedor-resumen-usuarios');

    let debounceTimer = null;
    let abortController = null;

    function ejecutarFiltroEnVivo() {
        if (!formFiltro) return;

        // Cancelar petición anterior si aún está pendiente
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        const formData = new FormData(formFiltro);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value && value.toString().trim()) {
                params.append(key, value.toString().trim());
            }
        }

        const url = `${formFiltro.action}${params.toString() ? '?' + params.toString() : ''}`;

        // Indicar carga suave en el contenedor sin alterar los iconos del input
        if (contenedorTabla) {
            contenedorTabla.classList.add('opacity-50');
        }

        // Control del botón borrar texto rápido
        if (btnBorrarQ && inputQ) {
            if (inputQ.value.trim().length > 0) {
                btnBorrarQ.classList.remove('hidden');
            } else {
                btnBorrarQ.classList.add('hidden');
            }
        }

        // Control del botón de limpiar filtros
        const hayFiltros = Boolean(
            (inputQ && inputQ.value.trim()) ||
            (selectRol && selectRol.value) ||
            (selectArea && selectArea.value) ||
            (selectEstado && selectEstado.value !== '')
        );
        if (btnLimpiar) {
            if (hayFiltros) btnLimpiar.classList.remove('hidden');
            else btnLimpiar.classList.add('hidden');
        }

        fetch(url, {
            signal: abortController.signal,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error al filtrar usuarios');
            return response.text();
        })
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Actualizar tarjetas de resumen numérico superior
            const nuevoResumen = doc.getElementById('contenedor-resumen-usuarios');
            if (nuevoResumen && contenedorResumen) {
                contenedorResumen.innerHTML = nuevoResumen.innerHTML;
            }

            // Actualizar tabla y tarjetas responsive de usuarios
            const nuevaTabla = doc.getElementById('contenedor-tabla-usuarios');
            if (nuevaTabla && contenedorTabla) {
                contenedorTabla.innerHTML = nuevaTabla.innerHTML;
                vincularPaginacion();
            }

            // Actualizar la URL sin recargar para permitir compartir o refrescar
            window.history.replaceState({}, '', url);
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error en búsqueda en vivo:', err);
            }
        })
        .finally(() => {
            if (contenedorTabla) {
                contenedorTabla.classList.remove('opacity-50');
            }
        });
    }

    // Interceptar enlaces de paginación para paginar por AJAX sin perder la posición
    function vincularPaginacion() {
        if (!contenedorTabla) return;
        contenedorTabla.querySelectorAll('nav[role="navigation"] a, .pagination a').forEach(enlace => {
            enlace.addEventListener('click', function (e) {
                e.preventDefault();
                const pageUrl = this.getAttribute('href');
                if (!pageUrl) return;

                if (abortController) abortController.abort();
                abortController = new AbortController();

                if (contenedorTabla) contenedorTabla.classList.add('opacity-50');

                fetch(pageUrl, {
                    signal: abortController.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const nuevaTabla = doc.getElementById('contenedor-tabla-usuarios');
                    if (nuevaTabla && contenedorTabla) {
                        contenedorTabla.innerHTML = nuevaTabla.innerHTML;
                        vincularPaginacion();
                        contenedorTabla.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    window.history.replaceState({}, '', pageUrl);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        window.location.href = pageUrl;
                    }
                })
                .finally(() => {
                    if (contenedorTabla) contenedorTabla.classList.remove('opacity-50');
                });
            });
        });
    }

    // Filtrado con debounce de 250ms al escribir nombre o cédula
    inputQ?.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(ejecutarFiltroEnVivo, 250);
    });

    // Botón borrar texto rápido
    btnBorrarQ?.addEventListener('click', function () {
        if (inputQ) {
            inputQ.value = '';
            inputQ.focus();
            ejecutarFiltroEnVivo();
        }
    });

    // Filtrado instantáneo al seleccionar Rol, Área o Estado
    selectRol?.addEventListener('change', ejecutarFiltroEnVivo);
    selectArea?.addEventListener('change', ejecutarFiltroEnVivo);
    selectEstado?.addEventListener('change', ejecutarFiltroEnVivo);

    // Evitar recarga completa de formulario
    formFiltro?.addEventListener('submit', function (e) {
        e.preventDefault();
        clearTimeout(debounceTimer);
        ejecutarFiltroEnVivo();
    });

    // Botón para limpiar todos los filtros
    btnLimpiar?.addEventListener('click', function (e) {
        e.preventDefault();
        if (inputQ) inputQ.value = '';
        if (selectRol) selectRol.value = '';
        if (selectArea) selectArea.value = '';
        if (selectEstado) selectEstado.value = '';
        ejecutarFiltroEnVivo();
    });

    vincularPaginacion();
});
</script>
@endpush
