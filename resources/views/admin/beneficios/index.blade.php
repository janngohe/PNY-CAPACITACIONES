@extends('layouts.admin')

@section('title', 'Catálogo de Beneficios y Puntos')
@section('page_title', 'Beneficios e Incentivos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Sistema de Incentivos',
        'titulo' => 'Catálogo de Beneficios y Puntos Acumulables',
        'descripcion' => 'Administra los beneficios desbloqueables por puntos para los colaboradores. Los empleados acumulan puntos al completar satisfactoriamente capacitaciones configuradas tanto por la administración como por jefes de área.',
        'icono' => 'fa-gift',
    ])

    <!-- MÉTRICAS RÁPIDAS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Total Beneficios</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $beneficios->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Activos</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $beneficios->where('estado', true)->count() }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Canjes Realizados</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalCanjes ?? ($estadisticas['total_canjes'] ?? 0) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Puntos Canjeados</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ number_format($puntosCanjeados ?? ($estadisticas['puntos_canjeados'] ?? 0)) }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 text-sm">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- FORMULARIO DE CREAR / EDITAR BENEFICIO -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
            <div class="border-b border-line/60 pb-3">
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Configuración</span>
                <h3 id="form-beneficio-title" class="font-heading font-extrabold text-base text-brand-dark">Registrar Nuevo Beneficio</h3>
            </div>

            <form id="form-beneficio" method="POST" action="{{ route('admin.beneficios.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-beneficio-method" value="POST">

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Nombre del Beneficio *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                            <i class="fa-solid fa-gift text-xs"></i>
                        </span>
                        <input type="text"
                               name="nombre"
                               id="beneficio-nombre"
                               required
                               placeholder="Ej: Día libre de descanso, Bono almuerzo..."
                               value="{{ old('nombre') }}"
                               class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark">
                    </div>
                    @error('nombre')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Descripción <span class="text-slate-400 font-normal">(Opcional)</span></label>
                    <textarea name="descripcion"
                              id="beneficio-descripcion"
                              rows="3"
                              placeholder="Detalla en qué consiste el beneficio y cómo el colaborador podrá disfrutarlo (opcional)..."
                              class="w-full p-3 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark resize-none">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-brand-dark mb-1">Puntos Requeridos para Canje *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-amber-500 font-bold">
                            <i class="fa-solid fa-star text-xs"></i>
                        </span>
                        <input type="number"
                               name="puntos_requeridos"
                               id="beneficio-puntos"
                               min="1"
                               max="100000"
                               step="1"
                               required
                               value="{{ old('puntos_requeridos', 50) }}"
                               placeholder="Ej: 50, 100, 250..."
                               class="w-full pl-9 pr-3 py-2 text-xs font-bold rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-white text-brand-dark">
                    </div>
                    @error('puntos_requeridos')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ícono por defecto: cajita de regalo -->
                <input type="hidden" name="icono" id="beneficio-icono" value="fa-gift">

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox"
                           name="estado"
                           id="beneficio-estado"
                           value="1"
                           {{ old('estado', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-brand-blue focus:ring-brand-blue border-line">
                    <label for="beneficio-estado" class="text-xs font-semibold text-brand-dark cursor-pointer">
                        Beneficio activo y visible en el catálogo
                    </label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" id="btn-submit-beneficio" class="flex-1 py-2.5 px-4 bg-brand-blue hover:bg-brand-blue/90 text-white rounded-xl font-bold text-xs shadow-2xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Beneficio</span>
                    </button>
                    <button type="button" id="btn-cancel-beneficio" onclick="resetBeneficioForm()" class="hidden py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- LISTADO DE BENEFICIOS -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-brand-dark">Catálogo Vigente</h3>
                    <p class="text-xs text-muted">Beneficios configurados para desbloqueo por puntos acumulables.</p>
                </div>
            </div>

            @if($beneficios->isEmpty())
                <div class="bg-white rounded-3xl border border-line/80 p-8 text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <h4 class="font-heading font-bold text-base text-brand-dark">Sin beneficios registrados</h4>
                    <p class="text-xs text-muted max-w-sm mx-auto">Crea el primer beneficio en el formulario lateral para que los colaboradores comiencen a canjear sus puntos acumulados.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($beneficios as $b)
                        <div class="bg-white rounded-2xl border border-line/80 p-5 shadow-2xs space-y-3 flex flex-col justify-between hover:border-brand-blue/40 transition">
                            <div class="space-y-2.5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl {{ $b->estado ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-solid {{ $b->icono ?: 'fa-gift' }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-bold text-sm text-brand-dark line-clamp-1">{{ $b->nombre }}</h4>
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-100/70 text-amber-800 text-[11px] font-extrabold">
                                                <i class="fa-solid fa-star text-[10px]"></i>
                                                <span>{{ number_format($b->puntos_requeridos) }} puntos</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado toggle -->
                                    <form method="POST" action="{{ route('admin.beneficios.toggle-estado', $b) }}">
                                        @csrf
                                        <button type="submit" title="Cambiar estado" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition {{ $b->estado ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                            {{ $b->estado ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </div>

                                @if($b->descripcion)
                                    <p class="text-xs text-muted line-clamp-3 leading-relaxed">{{ $b->descripcion }}</p>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-line/60 flex items-center justify-between gap-2 text-xs">
                                <button type="button"
                                        onclick="abrirModalCanjesBeneficio({{ $b->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $b->canjes_count > 0 ? 'bg-amber-50 text-amber-900 hover:bg-amber-100 border border-amber-200 shadow-2xs' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-200' }} transition cursor-pointer"
                                        title="Ver colaboradores que canjearon este beneficio">
                                    <i class="fa-solid fa-list-check text-[11px] {{ $b->canjes_count > 0 ? 'text-amber-600' : 'text-slate-400' }}"></i>
                                    <span>Ver Lista ({{ $b->canjes_count }})</span>
                                </button>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button"
                                            onclick='editarBeneficio(@json($b), "{{ route('admin.beneficios.update', $b) }}")'
                                            class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Editar</span>
                                    </button>

                                    <form method="POST" action="{{ route('admin.beneficios.destroy', $b) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este beneficio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>


</div>

<!-- MODAL DE CANJES POR BENEFICIO (TABLA EN DESKTOP / COLUMNA VERTICAL EN MÓVIL SIN SCROLL HORIZONTAL) -->
<div id="modal-canjes-beneficio" onclick="if (event.target === this) cerrarModalCanjes();" class="fixed inset-0 z-50 hidden bg-brand-dark/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden border border-line animate-in fade-in duration-200">
        <!-- Modal Header -->
        <div class="p-4 sm:p-5 border-b border-line/70 flex items-center justify-between bg-slate-50/90 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center text-lg shrink-0">
                    <i id="modal-canjes-icono" class="fa-solid fa-gift"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Historial de Canjes</span>
                        <span id="modal-canjes-puntos-badge" class="px-2 py-0.5 rounded-md bg-amber-100/80 text-amber-900 font-extrabold text-[10px]"></span>
                    </div>
                    <h3 id="modal-canjes-titulo" class="font-heading font-extrabold text-sm sm:text-base text-brand-dark leading-snug">Colaboradores con Canje</h3>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCanjes()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Search & Counter -->
        <div class="p-3 sm:p-4 border-b border-line/60 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white shrink-0">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text"
                       id="modal-canjes-busqueda"
                       oninput="filtrarModalCanjes()"
                       placeholder="Buscar por colaborador, documento, sede o área..."
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-slate-50 text-brand-dark">
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-muted w-full sm:w-auto justify-between sm:justify-end">
                <span id="modal-canjes-contador" class="px-3 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-xs border border-blue-100">
                    0 colaboradores
                </span>
            </div>
        </div>

        <!-- Modal Body (Dual: Table for Desktop, Vertical Column Cards for Mobile) -->
        <div class="overflow-y-auto overflow-x-hidden flex-1 p-3 sm:p-5">
            <!-- VISTA ESCRITORIO: TABLA (oculta en móvil para que no se mueva) -->
            <div class="hidden md:block overflow-x-auto rounded-2xl border border-line/70">
                <table class="w-full text-left text-xs text-brand-dark">
                    <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3">Código</th>
                            <th class="py-2.5 px-3">Colaborador</th>
                            <th class="py-2.5 px-3">Sede</th>
                            <th class="py-2.5 px-3">Área</th>
                            <th class="py-2.5 px-3">Fecha Canje</th>
                            <th class="py-2.5 px-3 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="modal-canjes-tbody" class="divide-y divide-line/60 bg-white">
                        <!-- Rellenado dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- VISTA MÓVIL: REPRESENTADA EN COLUMNA VERTICAL (no permite mover horizontalmente) -->
            <div id="modal-canjes-cards" class="block md:hidden space-y-3 w-full">
                <!-- Rellenado dinámicamente con tarjetas en columna vertical -->
            </div>

            <!-- Estado Vacío -->
            <div id="modal-canjes-vacio" class="hidden py-10 text-center space-y-2 bg-slate-50/60 rounded-2xl border border-dashed border-line">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h4 class="font-heading font-bold text-sm text-brand-dark" id="modal-canjes-vacio-titulo">Sin colaboradores registrados</h4>
                <p class="text-xs text-muted max-w-sm mx-auto" id="modal-canjes-vacio-desc">Ningún colaborador ha realizado el canje de este beneficio todavía.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 sm:p-4 border-t border-line/60 bg-slate-50/80 flex items-center justify-end shrink-0">
            <button type="button" onclick="cerrarModalCanjes()" class="py-2 px-5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition shadow-2xs cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
// Datos de canjes por beneficio para el modal
const canjesPorBeneficio = {!! json_encode($canjesPorBeneficio) !!};

let currentModalBeneficioId = null;

function abrirModalCanjesBeneficio(beneficioId) {
    const item = canjesPorBeneficio[beneficioId];
    if (!item) return;

    currentModalBeneficioId = beneficioId;
    document.getElementById('modal-canjes-titulo').textContent = 'Colaboradores con Canje: ' + item.nombre;
    document.getElementById('modal-canjes-busqueda').value = '';

    const badgePuntos = document.getElementById('modal-canjes-puntos-badge');
    if (badgePuntos) badgePuntos.textContent = Number(item.puntos || 0).toLocaleString() + ' pts';

    const icono = document.getElementById('modal-canjes-icono');
    if (icono) {
        icono.className = 'fa-solid ' + (item.icono || 'fa-gift');
    }

    renderizarFilasModal(item.canjes, (item.canjes || []).length);

    document.getElementById('modal-canjes-beneficio').classList.remove('hidden');
}

function renderizarFilasModal(lista, totalOriginal) {
    const tbody = document.getElementById('modal-canjes-tbody');
    const cardsCont = document.getElementById('modal-canjes-cards');
    const vacio = document.getElementById('modal-canjes-vacio');
    const vacioTitulo = document.getElementById('modal-canjes-vacio-titulo');
    const vacioDesc = document.getElementById('modal-canjes-vacio-desc');
    const contador = document.getElementById('modal-canjes-contador');

    tbody.innerHTML = '';
    cardsCont.innerHTML = '';
    contador.textContent = (lista ? lista.length : 0) + ' colaborador(es)';

    if (!lista || lista.length === 0) {
        vacio.classList.remove('hidden');
        if (totalOriginal === 0) {
            vacioTitulo.textContent = 'Sin canjes registrados';
            vacioDesc.textContent = 'Ningún colaborador ha redimido este beneficio hasta el momento.';
        } else {
            vacioTitulo.textContent = 'Sin coincidencias';
            vacioDesc.textContent = 'No se encontraron colaboradores que coincidan con el término de búsqueda.';
        }
        return;
    }

    vacio.classList.add('hidden');

    lista.forEach(c => {
        // 1. Fila de tabla para escritorio (oculta en móvil)
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition';
        tr.innerHTML = `
            <td class="py-2.5 px-3 font-mono font-bold text-brand-blue text-[11px] whitespace-nowrap">${c.codigo}</td>
            <td class="py-2.5 px-3">
                <div class="font-bold text-brand-dark text-xs">${c.empleado}</div>
                <div class="text-[10px] text-muted flex items-center gap-1.5 mt-0.5">
                    <span>CC: ${c.documento}</span>
                    ${c.cargo && c.cargo !== '—' ? `<span>&bull;</span><span>${c.cargo}</span>` : ''}
                </div>
            </td>
            <td class="py-2.5 px-3 whitespace-nowrap">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100">
                    <i class="fa-solid fa-location-dot text-[8px]"></i>
                    <span>${c.sede}</span>
                </span>
            </td>
            <td class="py-2.5 px-3 whitespace-nowrap">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-semibold border border-blue-100">
                    <i class="fa-solid fa-diagram-project text-[8px]"></i>
                    <span>${c.area}</span>
                </span>
            </td>
            <td class="py-2.5 px-3 text-muted text-[11px] whitespace-nowrap">${c.fecha}</td>
            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                    ${c.estado}
                </span>
            </td>
        `;
        tbody.appendChild(tr);

        // 2. Tarjeta representada en columna vertical para móvil (sin scroll horizontal)
        const card = document.createElement('div');
        card.className = 'bg-slate-50/90 border border-line/80 rounded-2xl p-3.5 space-y-2.5 w-full';
        card.innerHTML = `
            <div class="flex items-center justify-between border-b border-line/60 pb-2">
                <span class="font-mono font-bold text-brand-blue text-xs tracking-wider">${c.codigo}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                    ${c.estado}
                </span>
            </div>
            <div>
                <div class="font-bold text-brand-dark text-xs leading-snug">${c.empleado}</div>
                <div class="text-[11px] text-muted flex flex-wrap items-center gap-1.5 mt-0.5">
                    <span><strong class="text-slate-600 font-medium">CC:</strong> ${c.documento}</span>
                    ${c.cargo && c.cargo !== '—' ? `<span>&bull;</span><span>${c.cargo}</span>` : ''}
                </div>
            </div>
            <div class="flex flex-col gap-1.5 pt-1 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 text-[10px] uppercase font-bold w-12 shrink-0">Sede:</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100 truncate">
                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                        <span>${c.sede}</span>
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 text-[10px] uppercase font-bold w-12 shrink-0">Área:</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-semibold border border-blue-100 truncate">
                        <i class="fa-solid fa-diagram-project text-[8px]"></i>
                        <span>${c.area}</span>
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-line/50 text-[11px] text-muted">
                <span class="flex items-center gap-1">
                    <i class="fa-regular fa-calendar text-[10px]"></i>
                    <span>${c.fecha}</span>
                </span>
                <span class="font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 text-[10px]">
                    <i class="fa-solid fa-star text-[9px] text-amber-500 mr-0.5"></i>${c.puntos} pts
                </span>
            </div>
        `;
        cardsCont.appendChild(card);
    });
}

function filtrarModalCanjes() {
    if (!currentModalBeneficioId || !canjesPorBeneficio[currentModalBeneficioId]) return;
    const query = document.getElementById('modal-canjes-busqueda').value.toLowerCase().trim();
    const todos = canjesPorBeneficio[currentModalBeneficioId].canjes || [];

    if (!query) {
        renderizarFilasModal(todos, todos.length);
        return;
    }

    const filtrados = todos.filter(c => {
        return (c.empleado && c.empleado.toLowerCase().includes(query)) ||
               (c.documento && c.documento.toLowerCase().includes(query)) ||
               (c.sede && c.sede.toLowerCase().includes(query)) ||
               (c.area && c.area.toLowerCase().includes(query)) ||
               (c.cargo && c.cargo.toLowerCase().includes(query)) ||
               (c.codigo && c.codigo.toLowerCase().includes(query));
    });

    renderizarFilasModal(filtrados, todos.length);
}

function cerrarModalCanjes() {
    document.getElementById('modal-canjes-beneficio').classList.add('hidden');
    currentModalBeneficioId = null;
}

function editarBeneficio(data, updateUrl) {
    document.getElementById('form-beneficio-title').textContent = 'Editar Beneficio';
    document.getElementById('form-beneficio').action = updateUrl;
    document.getElementById('form-beneficio-method').value = 'PUT';

    document.getElementById('beneficio-nombre').value = data.nombre || '';
    document.getElementById('beneficio-descripcion').value = data.descripcion || '';
    document.getElementById('beneficio-puntos').value = data.puntos_requeridos || 50;
    document.getElementById('beneficio-estado').checked = !!data.estado;

    document.getElementById('btn-submit-beneficio').innerHTML = '<i class="fa-solid fa-check"></i><span>Actualizar Beneficio</span>';
    document.getElementById('btn-cancel-beneficio').classList.remove('hidden');

    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function resetBeneficioForm() {
    document.getElementById('form-beneficio-title').textContent = 'Registrar Nuevo Beneficio';
    document.getElementById('form-beneficio').action = "{{ route('admin.beneficios.store') }}";
    document.getElementById('form-beneficio-method').value = 'POST';

    document.getElementById('beneficio-nombre').value = '';
    document.getElementById('beneficio-descripcion').value = '';
    document.getElementById('beneficio-puntos').value = 50;
    document.getElementById('beneficio-estado').checked = true;

    document.getElementById('btn-submit-beneficio').innerHTML = '<i class="fa-solid fa-floppy-disk"></i><span>Guardar Beneficio</span>';
    document.getElementById('btn-cancel-beneficio').classList.add('hidden');
}
</script>
@endsection
