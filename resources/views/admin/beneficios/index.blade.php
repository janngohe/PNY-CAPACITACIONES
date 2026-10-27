@extends('layouts.admin')

@section('title', 'Catálogo de Beneficios, Puntos y Canjes')
@section('page_title', 'Beneficios e Incentivos')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Sistema de Incentivos',
        'titulo' => 'Catálogo de Beneficios, Puntos y Canjes',
        'descripcion' => 'Supervisa los incentivos otorgados, consulta qué colaboradores han obtenido y canjeado beneficios, prueba y visualiza los códigos QR de verificación y audita qué persona realizó la entrega física de cada beneficio.',
        'icono' => 'fa-gift',
    ])

    <!-- MÉTRICAS RÁPIDAS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Beneficios en Catálogo</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $beneficios->count() }}</span>
                    <span class="text-[10px] text-emerald-600 font-bold">({{ $beneficios->where('estado', true)->count() }} activos)</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-ticket"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Canjes Registrados</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-heading font-extrabold text-xl text-brand-dark">{{ $totalCanjes }}</span>
                    <span class="text-[10px] text-slate-500 font-bold">({{ $estadisticas['colaboradores_canje'] ?? 0 }} colabs)</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl {{ ($estadisticas['pendientes'] ?? 0) > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Pendientes de Entrega</span>
                <div class="flex items-baseline gap-1.5">
                    <span class="font-heading font-extrabold text-xl {{ ($estadisticas['pendientes'] ?? 0) > 0 ? 'text-amber-700' : 'text-brand-dark' }}">
                        {{ $estadisticas['pendientes'] ?? 0 }}
                    </span>
                    <span class="text-[10px] text-muted">por reclamar</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-line/80 p-4 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="text-[11px] text-muted font-bold block uppercase tracking-wider">Puntos Canjeados</span>
                <span class="font-heading font-extrabold text-xl text-brand-dark">{{ number_format($puntosCanjeados) }} pts</span>
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

    <!-- SELECTOR DE PESTAÑAS (TABS) -->
    <div class="flex flex-wrap items-center gap-2 border-b border-line/70 pb-2">
        <button type="button"
                id="tab-btn-canjes"
                onclick="cambiarPestana('canjes')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-brand-blue text-white border-brand-blue shadow-2xs">
            <i class="fa-solid fa-clipboard-list text-sm"></i>
            <span>Historial de Canjes y Entregas</span>
            <span id="badge-total-canjes-tab" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 text-white">
                {{ $totalCanjes }}
            </span>
        </button>

        <button type="button"
                id="tab-btn-catalogo"
                onclick="cambiarPestana('catalogo')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-white text-brand-dark border-line/80 hover:bg-slate-50">
            <i class="fa-solid fa-gift text-sm text-amber-500"></i>
            <span>Gestión del Catálogo</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                {{ $beneficios->count() }}
            </span>
        </button>
    </div>

    <!-- PESTAÑA 1: HISTORIAL DE CANJES, QR Y PERSONA QUE ENTREGÓ -->
    <div id="seccion-canjes" class="space-y-4">
        <!-- BARRA DE FILTROS Y BÚSQUEDA -->
        <div class="bg-white rounded-3xl border border-line/80 p-4 sm:p-5 shadow-2xs space-y-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <!-- Buscador en tiempo real -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text"
                           id="canjes-filtro-busqueda"
                           oninput="filtrarHistorialCanjes()"
                           placeholder="Buscar por colaborador, documento, código QR, incentivo, sede, área o quien entregó..."
                           class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-slate-50/80 text-brand-dark transition">
                </div>

                <!-- Filtro por Estado -->
                <div class="flex items-center gap-1.5 shrink-0 overflow-x-auto pb-1 lg:pb-0">
                    <button type="button"
                            onclick="setFiltroEstado('TODOS')"
                            id="filtro-estado-todos"
                            class="filtro-estado-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-brand-blue text-white shadow-2xs">
                        Todos ({{ $totalCanjes }})
                    </button>
                    <button type="button"
                            onclick="setFiltroEstado('SOLICITADO')"
                            id="filtro-estado-solicitado"
                            class="filtro-estado-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200">
                        Por Reclamar ({{ $estadisticas['pendientes'] ?? 0 }})
                    </button>
                    <button type="button"
                            onclick="setFiltroEstado('CANJEADO')"
                            id="filtro-estado-canjeado"
                            class="filtro-estado-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200">
                        Canjeados ({{ $estadisticas['canjeados'] ?? 0 }})
                    </button>
                </div>
            </div>

            <!-- Filtros secundarios: Sede y Beneficio -->
            <div class="flex flex-wrap items-center justify-between gap-2.5 pt-2 border-t border-line/60 text-xs text-muted">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold text-slate-600 uppercase">Sede:</span>
                        <select id="canjes-filtro-sede" onchange="filtrarHistorialCanjes()" class="py-1 px-2.5 text-xs rounded-lg border border-line bg-white text-brand-dark focus:outline-hidden focus:border-brand-blue">
                            <option value="">Todas las sedes</option>
                            @foreach($sedes as $s)
                                <option value="{{ strtolower($s->nombre) }}">{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold text-slate-600 uppercase">Incentivo:</span>
                        <select id="canjes-filtro-beneficio" onchange="filtrarHistorialCanjes()" class="py-1 px-2.5 text-xs rounded-lg border border-line bg-white text-brand-dark focus:outline-hidden focus:border-brand-blue">
                            <option value="">Todos los beneficios</option>
                            @foreach($beneficios as $b)
                                <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="font-bold text-brand-blue text-xs">
                    <span id="canjes-contador-visibles">{{ count($todosCanjesArray) }}</span> canjes encontrados
                </div>
            </div>
        </div>

        <!-- LISTADO DE CANJES: DUAL (TABLA EN DESKTOP / COLUMNA VERTICAL EN MÓVIL) -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden">
            <!-- VISTA ESCRITORIO: TABLA COMPLETA (oculta en móvil para evitar scroll horizontal) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs text-brand-dark">
                    <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line sticky top-0">
                        <tr>
                            <th class="py-3 px-4">Código</th>
                            <th class="py-3 px-4">Colaborador</th>
                            <th class="py-3 px-4">Incentivo / Beneficio</th>
                            <th class="py-3 px-4">Fecha Solicitud</th>
                            <th class="py-3 px-4">Estado</th>
                            <th class="py-3 px-4">Persona que Realizó la Entrega</th>
                            <th class="py-3 px-4 text-center">Código QR</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-canjes-tbody" class="divide-y divide-line/60 bg-white">
                        <!-- Rellenado dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- VISTA MÓVIL: INFORMACIÓN ORGANIZADA EN COLUMNAS (CERO SCROLL HORIZONTAL) -->
            <div id="cards-canjes-container" class="block md:hidden p-3 sm:p-4 space-y-3.5 bg-slate-50/50">
                <!-- Rellenado dinámicamente con tarjetas en columna vertical -->
            </div>

            <!-- ESTADO VACÍO -->
            <div id="canjes-vacio-aviso" class="hidden py-12 px-4 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto border border-amber-200">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h4 id="canjes-vacio-titulo" class="font-heading font-bold text-base text-brand-dark">Sin canjes encontrados</h4>
                <p id="canjes-vacio-desc" class="text-xs text-muted max-w-md mx-auto">No se encontraron registros de colaboradores que coincidan con los filtros aplicados.</p>
            </div>
        </div>
    </div>

    <!-- PESTAÑA 2: GESTIÓN DEL CATÁLOGO DE BENEFICIOS -->
    <div id="seccion-catalogo" class="hidden space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- FORMULARIO DE CREAR / EDITAR BENEFICIO -->
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-4">
                <div class="border-b border-line/60 pb-3 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Configuración</span>
                        <h3 id="form-beneficio-title" class="font-heading font-extrabold text-base text-brand-dark">Registrar Nuevo Beneficio</h3>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-gift"></i>
                    </div>
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
                        <button type="submit" id="btn-submit-beneficio" class="flex-1 py-2.5 px-4 bg-brand-blue hover:bg-brand-blue/90 text-white rounded-xl font-bold text-xs shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Guardar Beneficio</span>
                        </button>
                        <button type="button" id="btn-cancel-beneficio" onclick="resetBeneficioForm()" class="hidden py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition cursor-pointer">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTADO DE BENEFICIOS VIGENTES -->
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
                                            <button type="submit" title="Cambiar estado" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase transition cursor-pointer {{ $b->estado ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
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
                                                class="px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue transition flex items-center gap-1 cursor-pointer">
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

</div>

<!-- ======================================================== -->
<!-- MODAL UNIFICADO: CÓDIGO QR Y PRUEBA DE CANJE (ADMIN)       -->
<!-- ======================================================== -->
<div id="modal-qr-admin" onclick="if (event.target === this) cerrarModalQrAdmin();" class="fixed inset-0 z-50 hidden bg-brand-dark/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[94vh] flex flex-col overflow-hidden border border-line animate-in fade-in duration-200">
        <!-- Header -->
        <div class="p-4 sm:p-5 border-b border-line/70 flex items-center justify-between bg-slate-50/90 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider block">Código QR de Verificación</span>
                    <h3 id="modal-qr-titulo-beneficio" class="font-heading font-extrabold text-sm sm:text-base text-brand-dark leading-snug">Detalle del Canje</h3>
                </div>
            </div>
            <button type="button" onclick="cerrarModalQrAdmin()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto flex-1 p-5 space-y-4">
            <!-- Badge de Estado -->
            <div class="flex items-center justify-between">
                <span class="text-xs text-muted font-bold uppercase tracking-wider">Estado del Beneficio:</span>
                <span id="modal-qr-estado-badge" class="px-3 py-1 rounded-full text-xs font-extrabold uppercase"></span>
            </div>

            <!-- Contenedor QR Visual -->
            <div class="bg-slate-50 border border-line/80 rounded-2xl p-5 flex flex-col items-center justify-center space-y-3">
                <div class="p-3 bg-white rounded-2xl border-2 border-line/80 shadow-md">
                    <div id="modal-qr-container" class="w-[200px] h-[200px] flex items-center justify-center">
                        <!-- Código QR Renderizado -->
                    </div>
                </div>

                <!-- Código Alfanumérico Copiable -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted font-semibold">Código:</span>
                    <span id="modal-qr-codigo-texto" class="font-mono font-extrabold text-base text-brand-dark tracking-wider bg-white px-2.5 py-0.5 rounded-lg border border-line/70">
                    </span>
                    <button type="button" onclick="copiarCodigoCanje()" title="Copiar código" class="p-1.5 rounded-lg bg-white border border-line/70 text-slate-500 hover:text-brand-blue hover:bg-blue-50 transition cursor-pointer text-xs">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            <!-- Datos del Colaborador -->
            <div class="bg-white border border-line/80 rounded-2xl p-3.5 space-y-2 text-xs">
                <div class="flex items-center justify-between border-b border-line/60 pb-1.5">
                    <span class="font-bold text-slate-500 uppercase text-[10px]">Colaborador Beneficiado</span>
                    <span id="modal-qr-colaborador-puntos" class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200 font-extrabold text-[10px]"></span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-0.5">
                    <div>
                        <span class="text-[10px] text-muted block">Nombre Completo:</span>
                        <span id="modal-qr-colaborador-nombre" class="font-bold text-brand-dark"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-muted block">Identificación:</span>
                        <span id="modal-qr-colaborador-doc" class="font-bold text-brand-dark"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-muted block">Sede:</span>
                        <span id="modal-qr-colaborador-sede" class="font-semibold text-rose-700"></span>
                    </div>
                    <div>
                        <span class="text-[10px] text-muted block">Área / Cargo:</span>
                        <span id="modal-qr-colaborador-area" class="font-semibold text-brand-blue"></span>
                    </div>
                </div>
            </div>

            <!-- INFORMACIÓN AUDITORÍA: PERSONA QUE REALIZÓ LA ENTREGA -->
            <div id="modal-qr-responsable-contenedor" class="rounded-2xl p-3.5 space-y-2 text-xs border">
                <!-- Se llena dinámicamente con los datos de quien entregó o aviso de pendiente -->
            </div>
        </div>

        <!-- Footer con Acciones QR -->
        <div class="p-4 border-t border-line/70 bg-slate-50/90 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 shrink-0">
            <div class="flex items-center gap-2 text-xs text-muted">
                <i class="fa-solid fa-mobile-screen-button text-brand-blue text-sm"></i>
                <span class="text-[11px] font-medium text-slate-600">Escanea el QR directamente con tu celular</span>
            </div>
            <div class="flex items-center gap-2 justify-end">
                <button type="button"
                        onclick="copiarEnlaceVerificacion()"
                        class="py-2 px-3 rounded-xl font-semibold text-xs bg-white hover:bg-slate-100 text-slate-700 border border-line shadow-2xs transition flex items-center justify-center gap-1 cursor-pointer"
                        title="Copiar enlace de verificación al portapapeles">
                    <i class="fa-regular fa-clone text-[11px]"></i>
                    <span>Copiar Link</span>
                </button>
                <button type="button"
                        onclick="descargarQrPng()"
                        class="py-2 px-3 rounded-xl font-semibold text-xs bg-white hover:bg-slate-100 text-slate-700 border border-line shadow-2xs transition flex items-center justify-center gap-1 cursor-pointer"
                        title="Descargar imagen PNG del QR">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span>Descargar QR</span>
                </button>
                <button type="button"
                        onclick="cerrarModalQrAdmin()"
                        class="py-2 px-4 rounded-xl font-bold text-xs bg-slate-800 hover:bg-slate-900 text-white transition cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL DE CANJES POR BENEFICIO (DESDE EL CATÁLOGO)       -->
<!-- ======================================================== -->
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
                        <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Historial por Beneficio</span>
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
                       placeholder="Buscar por colaborador, documento, código o quien entregó..."
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
            <!-- VISTA ESCRITORIO: TABLA -->
            <div class="hidden md:block overflow-x-auto rounded-2xl border border-line/70">
                <table class="w-full text-left text-xs text-brand-dark">
                    <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line sticky top-0">
                        <tr>
                            <th class="py-2.5 px-3">Código & QR</th>
                            <th class="py-2.5 px-3">Colaborador</th>
                            <th class="py-2.5 px-3">Sede / Área</th>
                            <th class="py-2.5 px-3">Fecha Canje</th>
                            <th class="py-2.5 px-3">Persona que Entregó</th>
                            <th class="py-2.5 px-3 text-right">Estado & QR</th>
                        </tr>
                    </thead>
                    <tbody id="modal-canjes-tbody" class="divide-y divide-line/60 bg-white">
                        <!-- Rellenado dinámicamente -->
                    </tbody>
                </table>
            </div>

            <!-- VISTA MÓVIL: COLUMNA VERTICAL (sin scroll horizontal) -->
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

<!-- Scripts QR Code -->
<script src="{{ asset('qrcode.min.js') }}"></script>
<script>
if (typeof QRCode === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
}
</script>

<script>
// ==========================================
// DATOS MAESTROS DEL SISTEMA
// ==========================================
const todosCanjes = {!! json_encode($todosCanjesArray) !!};
const canjesPorBeneficio = {!! json_encode($canjesPorBeneficio) !!};

let filtroEstadoActual = 'TODOS';
let canjeSeleccionadoActual = null;
let currentQrInstance = null;
let currentModalBeneficioId = null;

// ==========================================
// CONTROL DE PESTAÑAS (TABS)
// ==========================================
function cambiarPestana(pestana) {
    const secCanjes = document.getElementById('seccion-canjes');
    const secCatalogo = document.getElementById('seccion-catalogo');
    const btnCanjes = document.getElementById('tab-btn-canjes');
    const btnCatalogo = document.getElementById('tab-btn-catalogo');

    if (pestana === 'canjes') {
        secCanjes.classList.remove('hidden');
        secCatalogo.classList.add('hidden');

        btnCanjes.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-brand-blue text-white border-brand-blue shadow-2xs';
        btnCatalogo.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-white text-brand-dark border-line/80 hover:bg-slate-50';
    } else {
        secCanjes.classList.add('hidden');
        secCatalogo.classList.remove('hidden');

        btnCatalogo.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-brand-blue text-white border-brand-blue shadow-2xs';
        btnCanjes.className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-heading font-bold text-xs transition border cursor-pointer bg-white text-brand-dark border-line/80 hover:bg-slate-50';
    }
}

// ==========================================
// FILTRADO Y RENDERIZADO DEL HISTORIAL DE CANJES
// ==========================================
function setFiltroEstado(estado) {
    filtroEstadoActual = estado;

    document.querySelectorAll('.filtro-estado-btn').forEach(b => {
        b.className = 'filtro-estado-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200';
    });

    const activo = document.getElementById('filtro-estado-' + estado.toLowerCase());
    if (activo) {
        activo.className = 'filtro-estado-btn px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer bg-brand-blue text-white shadow-2xs';
    }

    filtrarHistorialCanjes();
}

function filtrarHistorialCanjes() {
    const q = (document.getElementById('canjes-filtro-busqueda').value || '').toLowerCase().trim();
    const sede = (document.getElementById('canjes-filtro-sede').value || '').toLowerCase().trim();
    const beneficioId = (document.getElementById('canjes-filtro-beneficio').value || '').trim();

    const filtrados = todosCanjes.filter(c => {
        // Filtro por Estado
        if (filtroEstadoActual !== 'TODOS' && c.estado !== filtroEstadoActual) {
            return false;
        }

        // Filtro por Sede
        if (sede && !(c.sede && c.sede.toLowerCase().includes(sede))) {
            return false;
        }

        // Filtro por Beneficio
        if (beneficioId && String(c.beneficio_id) !== String(beneficioId)) {
            return false;
        }

        // Filtro por texto
        if (q) {
            const txt = [
                c.empleado || '',
                c.documento || '',
                c.codigo || '',
                c.beneficio_nombre || '',
                c.sede || '',
                c.area || '',
                c.cargo || '',
                c.entregado_por || '',
                c.responsable_nombre || '',
                c.responsable_cc || ''
            ].join(' ').toLowerCase();

            if (!txt.includes(q)) return false;
        }

        return true;
    });

    renderizarHistorialCanjes(filtrados);
}

function renderizarHistorialCanjes(lista) {
    const tbody = document.getElementById('tabla-canjes-tbody');
    const cardsCont = document.getElementById('cards-canjes-container');
    const vacio = document.getElementById('canjes-vacio-aviso');
    const contador = document.getElementById('canjes-contador-visibles');

    tbody.innerHTML = '';
    cardsCont.innerHTML = '';
    contador.textContent = lista.length;

    if (lista.length === 0) {
        vacio.classList.remove('hidden');
        return;
    }
    vacio.classList.add('hidden');

    lista.forEach(c => {
        const esCanjeado = c.estado === 'CANJEADO';

        // 1. FILA DE TABLA (ESCRITORIO)
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/70 transition';

        tr.innerHTML = `
            <td class="py-3 px-4 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 font-mono font-extrabold text-brand-blue text-xs bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-100">
                    <i class="fa-solid fa-qrcode text-[11px]"></i>
                    ${c.codigo}
                </span>
            </td>
            <td class="py-3 px-4">
                <div class="font-bold text-brand-dark text-xs">${c.empleado}</div>
                <div class="text-[10px] text-muted flex flex-wrap items-center gap-1.5 mt-0.5">
                    <span><strong class="text-slate-600 font-semibold">CC:</strong> ${c.documento}</span>
                    ${c.cargo && c.cargo !== '—' ? `<span>&bull;</span><span>${c.cargo}</span>` : ''}
                </div>
                <div class="flex flex-wrap items-center gap-1 mt-1">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[9.5px] font-semibold border border-rose-100">
                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                        <span>${c.sede}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[9.5px] font-semibold border border-blue-100">
                        <i class="fa-solid fa-diagram-project text-[8px]"></i>
                        <span>${c.area}</span>
                    </span>
                </div>
            </td>
            <td class="py-3 px-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid ${c.beneficio_icono || 'fa-gift'}"></i>
                    </div>
                    <div>
                        <div class="font-bold text-brand-dark text-xs">${c.beneficio_nombre}</div>
                        <div class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-md bg-amber-100/70 text-amber-800 text-[10px] font-extrabold">
                            <i class="fa-solid fa-star text-[9px] text-amber-500"></i>
                            <span>${Number(c.puntos).toLocaleString()} pts</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-3 px-4 text-muted text-[11px] whitespace-nowrap">
                <div class="font-medium text-slate-700">${c.fecha}</div>
            </td>
            <td class="py-3 px-4 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border ${esCanjeado ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-blue-100 text-blue-800 border-blue-200'}">
                    <i class="fa-solid ${esCanjeado ? 'fa-circle-check text-emerald-600' : 'fa-clock text-blue-600'} text-[9px]"></i>
                    ${esCanjeado ? 'Canjeado' : 'Por Reclamar'}
                </span>
            </td>
            <td class="py-3 px-4">
                ${esCanjeado ? `
                    <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl p-2 max-w-[260px] space-y-0.5">
                        <div class="flex items-center gap-1.5 text-emerald-900 font-bold text-xs truncate">
                            <i class="fa-solid fa-user-check text-emerald-600 text-xs shrink-0"></i>
                            <span class="truncate" title="${c.responsable_nombre || c.entregado_por || 'Registrado'}">${c.responsable_nombre || c.entregado_por || 'Registrado'}</span>
                        </div>
                        ${c.responsable_cc ? `
                            <div class="text-[10px] text-emerald-700 font-semibold pl-4">
                                CC: ${c.responsable_cc}
                            </div>
                        ` : ''}
                        ${c.responsable_sede || c.responsable_area ? `
                            <div class="text-[9.5px] text-slate-500 truncate pl-4">
                                ${[c.responsable_area, c.responsable_sede].filter(Boolean).join(' · ')}
                            </div>
                        ` : ''}
                        ${c.fecha_entrega ? `
                            <div class="text-[9.5px] text-slate-500 flex items-center gap-1 pt-0.5 pl-4">
                                <i class="fa-regular fa-calendar-check text-[9px]"></i>
                                <span>${c.fecha_entrega}</span>
                            </div>
                        ` : ''}
                    </div>
                ` : `
                    <div class="text-slate-400 text-[11px] italic flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-amber-500"></i>
                        <span>Pendiente de entrega física</span>
                    </div>
                `}
            </td>
            <td class="py-3 px-4 text-center whitespace-nowrap">
                <button type="button"
                        onclick='abrirModalQrAdmin(${JSON.stringify(c)})'
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer"
                        style="background-color: #fef3c7; color: #92400e;"
                        title="Ver Código QR para escanear con celular">
                    <i class="fa-solid fa-qrcode text-[11px] text-amber-600"></i>
                    <span>Ver QR</span>
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        // 2. VISTA MÓVIL: INFORMACIÓN ORGANIZADA EN COLUMNAS (CERO SCROLL HORIZONTAL)
        const card = document.createElement('div');
        card.className = 'bg-white border border-line/90 rounded-2xl p-4 space-y-3.5 shadow-2xs';
        card.innerHTML = `
            <!-- Fila 1: Colaborador y Estado -->
            <div class="flex items-start justify-between gap-2.5 border-b border-line/60 pb-3">
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Colaborador</span>
                    <h4 class="font-bold text-brand-dark text-sm leading-tight truncate">${c.empleado}</h4>
                    <p class="text-[11px] text-muted mt-0.5">CC: ${c.documento} ${c.cargo && c.cargo !== '—' ? `&bull; ${c.cargo}` : ''}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 border ${esCanjeado ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-blue-100 text-blue-800 border-blue-200'}">
                    <i class="fa-solid ${esCanjeado ? 'fa-circle-check text-emerald-600' : 'fa-clock text-blue-600'} text-[9px]"></i>
                    ${esCanjeado ? 'Canjeado' : 'Por Reclamar'}
                </span>
            </div>

            <!-- Fila 2 en Columnas: Código e Incentivo -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Código de Canje</span>
                    <span class="inline-flex items-center gap-1 font-mono font-bold text-brand-blue text-xs bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">
                        <i class="fa-solid fa-qrcode text-[10px]"></i>
                        ${c.codigo}
                    </span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Incentivo</span>
                    <div class="font-bold text-brand-dark text-xs truncate leading-tight">${c.beneficio_nombre}</div>
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-amber-800 mt-0.5">
                        <i class="fa-solid fa-star text-amber-500 text-[9px]"></i>${Number(c.puntos).toLocaleString()} pts
                    </span>
                </div>
            </div>

            <!-- Fila 3 en Columnas: Ubicación y Fecha de Solicitud -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Ubicación</span>
                    <div class="text-[11px] text-rose-700 font-semibold truncate flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                        <span>${c.sede}</span>
                    </div>
                    <div class="text-[10px] text-brand-blue truncate mt-0.5">
                        ${c.area}
                    </div>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Fecha Solicitud</span>
                    <div class="text-[11px] text-slate-700 font-medium flex items-center gap-1">
                        <i class="fa-regular fa-calendar text-[10px] text-slate-400"></i>
                        <span>${c.fecha}</span>
                    </div>
                </div>
            </div>

            <!-- Persona que Realizó la Entrega -->
            <div class="pt-1">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Auditoría de Entrega</span>
                ${esCanjeado ? `
                    <div class="bg-emerald-50/90 border border-emerald-200/90 rounded-xl p-3 space-y-1 text-xs">
                        <div class="flex items-center gap-1.5 text-emerald-950 font-bold">
                            <i class="fa-solid fa-user-check text-emerald-600 text-sm shrink-0"></i>
                            <span>${c.responsable_nombre || c.entregado_por || 'Responsable registrado'}</span>
                        </div>
                        ${c.responsable_cc ? `
                            <div class="text-[11px] text-emerald-800 font-semibold pl-5">
                                Cédula: CC ${c.responsable_cc}
                            </div>
                        ` : ''}
                        ${c.responsable_area || c.responsable_sede ? `
                            <div class="text-[10px] text-slate-600 pl-5">
                                ${[c.responsable_area, c.responsable_sede].filter(Boolean).join(' · ')}
                            </div>
                        ` : ''}
                        ${c.fecha_entrega ? `
                            <div class="text-[10px] text-slate-500 flex items-center gap-1 pt-1 border-t border-emerald-200/60 pl-5">
                                <i class="fa-regular fa-calendar-check text-[9px]"></i>
                                <span>Entregado el: <strong>${c.fecha_entrega}</strong></span>
                            </div>
                        ` : ''}
                    </div>
                ` : `
                    <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80 text-[11px] text-amber-800 flex items-center gap-2">
                        <i class="fa-regular fa-clock text-amber-600 text-xs shrink-0"></i>
                        <span>Pendiente por reclamar físicamente.</span>
                    </div>
                `}
            </div>

            <!-- Botón Único de Acción Móvil: Ver Código QR (Sin 'Probar') -->
            <div class="pt-2 border-t border-line/60">
                <button type="button"
                        onclick='abrirModalQrAdmin(${JSON.stringify(c)})'
                        class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer"
                        style="background-color: #fef3c7; color: #92400e;">
                    <i class="fa-solid fa-qrcode text-sm text-amber-700"></i>
                    <span>Ver Código QR</span>
                </button>
            </div>
        `;
        cardsCont.appendChild(card);
    });
}

// ========================================================
// MODAL UNIFICADO: CÓDIGO QR Y PRUEBA DE CANJE (ADMIN)
// ========================================================
function abrirModalQrAdmin(canje) {
    if (!canje) return;
    canjeSeleccionadoActual = canje;

    // Titulo y datos
    document.getElementById('modal-qr-titulo-beneficio').textContent = canje.beneficio_nombre || 'Beneficio';
    document.getElementById('modal-qr-codigo-texto').textContent = canje.codigo;

    // Badge de estado
    const badgeEstado = document.getElementById('modal-qr-estado-badge');
    const esCanjeado = canje.estado === 'CANJEADO';
    if (esCanjeado) {
        badgeEstado.textContent = 'Canjeado y Entregado';
        badgeEstado.className = 'px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200';
    } else {
        badgeEstado.textContent = 'Por Reclamar (Pendiente)';
        badgeEstado.className = 'px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-blue-100 text-blue-800 border border-blue-200';
    }

    // Datos del colaborador
    document.getElementById('modal-qr-colaborador-nombre').textContent = canje.empleado || '—';
    document.getElementById('modal-qr-colaborador-doc').textContent = 'CC ' + (canje.documento || '—');
    document.getElementById('modal-qr-colaborador-sede').textContent = canje.sede || 'Sin sede';
    document.getElementById('modal-qr-colaborador-area').textContent = (canje.area || 'Sin área') + (canje.cargo && canje.cargo !== '—' ? ' · ' + canje.cargo : '');
    document.getElementById('modal-qr-colaborador-puntos').textContent = Number(canje.puntos || 0).toLocaleString() + ' pts';

    // Responsable de entrega
    const respCont = document.getElementById('modal-qr-responsable-contenedor');
    if (esCanjeado) {
        respCont.className = 'bg-emerald-50 border-emerald-200 rounded-2xl p-4 space-y-1.5 text-xs border';
        respCont.innerHTML = `
            <div class="flex items-center gap-2 text-emerald-950 font-bold text-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                <span>Incentivo Entregado Satisfactoriamente</span>
            </div>
            <div class="pt-1.5 pl-6 space-y-1">
                <div><span class="text-slate-500 font-semibold">Entregado por:</span> <strong class="text-emerald-900">${canje.responsable_nombre || canje.entregado_por || 'Colaborador autorizado'}</strong></div>
                ${canje.responsable_cc ? `<div><span class="text-slate-500 font-semibold">Cédula responsable:</span> <strong class="text-emerald-900">CC ${canje.responsable_cc}</strong></div>` : ''}
                ${canje.responsable_area || canje.responsable_sede ? `<div><span class="text-slate-500 font-semibold">Ubicación responsable:</span> <span class="text-slate-700">${[canje.responsable_area, canje.responsable_sede].filter(Boolean).join(' · ')}</span></div>` : ''}
                ${canje.fecha_entrega ? `<div><span class="text-slate-500 font-semibold">Fecha y hora de entrega:</span> <strong class="text-emerald-900">${canje.fecha_entrega}</strong></div>` : ''}
            </div>
        `;
    } else {
        respCont.className = 'bg-blue-50/80 border-blue-200 rounded-2xl p-4 space-y-1.5 text-xs border';
        respCont.innerHTML = `
            <div class="flex items-center gap-2 text-brand-dark font-bold text-sm">
                <i class="fa-solid fa-clock-rotate-left text-brand-blue text-base shrink-0"></i>
                <span>Incentivo Pendiente de Entrega</span>
            </div>
            <p class="text-slate-600 pl-6 leading-relaxed">
                El colaborador aún no ha reclamado físicamente este incentivo. Para probar el flujo de validación, escaneo y confirmación de entrega en vivo, haz clic en el botón <strong>"Probar Verificación en Vivo"</strong>.
            </p>
        `;
    }

    // Mostrar modal
    document.getElementById('modal-qr-admin').classList.remove('hidden');

    // Renderizar QR Code
    const qrContainer = document.getElementById('modal-qr-container');
    qrContainer.innerHTML = '';

    const qrText = canje.url_verificacion || canje.codigo;

    if (typeof QRCode !== 'undefined') {
        try {
            currentQrInstance = new QRCode(qrContainer, {
                text: qrText,
                width: 190,
                height: 190,
                colorDark: "#002d5e",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        } catch (e) {
            console.warn('Fallback imagen QR:', e);
            qrContainer.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=190x190&data=${encodeURIComponent(qrText)}" alt="QR" class="w-[190px] h-[190px] object-contain">`;
        }
    } else {
        qrContainer.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=190x190&data=${encodeURIComponent(qrText)}" alt="QR" class="w-[190px] h-[190px] object-contain">`;
    }
}

function cerrarModalQrAdmin() {
    document.getElementById('modal-qr-admin').classList.add('hidden');
    canjeSeleccionadoActual = null;
}

function probarVerificacionCanje() {
    if (!canjeSeleccionadoActual || !canjeSeleccionadoActual.url_verificacion) return;
    window.open(canjeSeleccionadoActual.url_verificacion, '_blank');
}

function copiarEnlaceVerificacion() {
    if (!canjeSeleccionadoActual || !canjeSeleccionadoActual.url_verificacion) return;
    navigator.clipboard.writeText(canjeSeleccionadoActual.url_verificacion).then(() => {
        alert('¡Enlace de verificación copiado al portapapeles!\n' + canjeSeleccionadoActual.url_verificacion);
    }).catch(() => {
        prompt('Copia el enlace de verificación:', canjeSeleccionadoActual.url_verificacion);
    });
}

function copiarCodigoCanje() {
    if (!canjeSeleccionadoActual || !canjeSeleccionadoActual.codigo) return;
    navigator.clipboard.writeText(canjeSeleccionadoActual.codigo).then(() => {
        alert('Código ' + canjeSeleccionadoActual.codigo + ' copiado al portapapeles.');
    });
}

function descargarQrPng() {
    const qrContainer = document.getElementById('modal-qr-container');
    const canvas = qrContainer.querySelector('canvas');
    const img = qrContainer.querySelector('img');
    let dataUrl = null;

    if (canvas) {
        dataUrl = canvas.toDataURL("image/png");
    } else if (img && img.src) {
        dataUrl = img.src;
    }

    if (dataUrl) {
        const a = document.createElement('a');
        a.download = 'QR-' + (canjeSeleccionadoActual ? canjeSeleccionadoActual.codigo : 'Canje') + '.png';
        a.href = dataUrl;
        a.click();
    }
}

// ========================================================
// MODAL DE CANJES POR BENEFICIO (CATÁLOGO)
// ========================================================
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

    renderizarFilasModalBeneficio(item.canjes, (item.canjes || []).length);

    document.getElementById('modal-canjes-beneficio').classList.remove('hidden');
}

function renderizarFilasModalBeneficio(lista, totalOriginal) {
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
        const esCanjeado = c.estado === 'CANJEADO';

        // 1. Fila de tabla para escritorio
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition';
        tr.innerHTML = `
            <td class="py-2.5 px-3 whitespace-nowrap">
                <span class="inline-flex items-center gap-1 font-mono font-bold text-brand-blue text-[11px] bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">
                    <i class="fa-solid fa-qrcode text-[10px]"></i>
                    ${c.codigo}
                </span>
            </td>
            <td class="py-2.5 px-3">
                <div class="font-bold text-brand-dark text-xs">${c.empleado}</div>
                <div class="text-[10px] text-muted flex items-center gap-1.5 mt-0.5">
                    <span>CC: ${c.documento}</span>
                    ${c.cargo && c.cargo !== '—' ? `<span>&bull;</span><span>${c.cargo}</span>` : ''}
                </div>
            </td>
            <td class="py-2.5 px-3">
                <div class="flex flex-col gap-1">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100 truncate max-w-[140px]">
                        <i class="fa-solid fa-location-dot text-[8px]"></i>
                        <span>${c.sede}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-md bg-blue-50 text-brand-blue text-[10px] font-semibold border border-blue-100 truncate max-w-[140px]">
                        <i class="fa-solid fa-diagram-project text-[8px]"></i>
                        <span>${c.area}</span>
                    </span>
                </div>
            </td>
            <td class="py-2.5 px-3 text-muted text-[11px] whitespace-nowrap">${c.fecha}</td>
            <td class="py-2.5 px-3">
                ${esCanjeado ? `
                    <div class="text-[11px] space-y-0.5 max-w-[200px]">
                        <div class="font-bold text-emerald-800 flex items-center gap-1 truncate">
                            <i class="fa-solid fa-user-check text-emerald-600 text-[10px] shrink-0"></i>
                            <span class="truncate">${c.responsable_nombre || c.entregado_por || 'Registrado'}</span>
                        </div>
                        ${c.responsable_cc ? `<div class="text-[9.5px] text-slate-500 pl-3.5">CC: ${c.responsable_cc}</div>` : ''}
                        ${c.fecha_entrega ? `<div class="text-[9px] text-slate-400 pl-3.5">${c.fecha_entrega}</div>` : ''}
                    </div>
                ` : `
                    <span class="text-[10px] text-amber-700 italic flex items-center gap-1">
                        <i class="fa-regular fa-clock text-[9px]"></i>
                        <span>Pendiente</span>
                    </span>
                `}
            </td>
            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                <div class="flex items-center justify-end gap-1.5">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${esCanjeado ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'}">
                        ${esCanjeado ? 'Canjeado' : 'Por Reclamar'}
                    </span>
                    <button type="button"
                            onclick='abrirModalQrAdmin(${JSON.stringify(c)})'
                            title="Ver Código QR"
                            class="p-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition cursor-pointer text-xs"
                            style="background-color: #fef3c7; color: #92400e;">
                        <i class="fa-solid fa-qrcode text-[11px]"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);

        // 2. VISTA MÓVIL: INFORMACIÓN ORGANIZADA EN COLUMNAS (CERO SCROLL HORIZONTAL)
        const card = document.createElement('div');
        card.className = 'bg-white border border-line/90 rounded-2xl p-4 space-y-3.5 shadow-2xs';
        card.innerHTML = `
            <!-- Fila 1: Colaborador y Estado -->
            <div class="flex items-start justify-between gap-2.5 border-b border-line/60 pb-2.5">
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Colaborador</span>
                    <h4 class="font-bold text-brand-dark text-sm leading-tight truncate">${c.empleado}</h4>
                    <p class="text-[11px] text-muted mt-0.5">CC: ${c.documento} ${c.cargo && c.cargo !== '—' ? `&bull; ${c.cargo}` : ''}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase shrink-0 border ${esCanjeado ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-blue-100 text-blue-800 border-blue-200'}">
                    <i class="fa-solid ${esCanjeado ? 'fa-circle-check text-emerald-600' : 'fa-clock text-blue-600'} text-[9px]"></i>
                    ${esCanjeado ? 'Canjeado' : 'Por Reclamar'}
                </span>
            </div>

            <!-- Fila 2 en Columnas: Código y Fecha -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Código de Canje</span>
                    <span class="inline-flex items-center gap-1 font-mono font-bold text-brand-blue text-xs bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">
                        <i class="fa-solid fa-qrcode text-[10px]"></i>
                        ${c.codigo}
                    </span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Fecha Canje</span>
                    <div class="text-[11px] text-slate-700 font-medium flex items-center gap-1">
                        <i class="fa-regular fa-calendar text-[10px] text-slate-400"></i>
                        <span>${c.fecha}</span>
                    </div>
                </div>
            </div>

            <!-- Fila 3 en Columnas: Sede y Área -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Sede</span>
                    <div class="text-[11px] text-rose-700 font-semibold truncate flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                        <span>${c.sede}</span>
                    </div>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-line/70">
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Área</span>
                    <div class="text-[11px] text-brand-blue font-semibold truncate flex items-center gap-1">
                        <i class="fa-solid fa-diagram-project text-[9px]"></i>
                        <span>${c.area}</span>
                    </div>
                </div>
            </div>

            <!-- Fila 4: Persona que Realizó la Entrega -->
            <div class="pt-0.5">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Persona que Realizó la Entrega</span>
                ${esCanjeado ? `
                    <div class="p-3 bg-emerald-50/90 border border-emerald-200/90 rounded-xl text-xs space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-emerald-950">
                            <i class="fa-solid fa-user-check text-emerald-600 text-sm shrink-0"></i>
                            <span class="truncate">${c.responsable_nombre || c.entregado_por || 'Responsable registrado'}</span>
                        </div>
                        ${c.responsable_cc ? `<div class="text-[11px] text-emerald-800 font-semibold pl-5">Cédula: CC ${c.responsable_cc}</div>` : ''}
                        ${c.fecha_entrega ? `
                            <div class="text-[10px] text-slate-500 flex items-center gap-1 pt-1 border-t border-emerald-200/60 pl-5">
                                <i class="fa-regular fa-calendar-check text-[9px]"></i>
                                <span>Entregado: <strong>${c.fecha_entrega}</strong></span>
                            </div>
                        ` : ''}
                    </div>
                ` : `
                    <div class="p-2.5 bg-amber-50/70 border border-amber-200/80 rounded-xl text-[11px] text-amber-800 flex items-center gap-2">
                        <i class="fa-regular fa-clock text-amber-600 text-xs shrink-0"></i>
                        <span>Pendiente de entrega física</span>
                    </div>
                `}
            </div>

            <!-- Botón Único de Acción Móvil: Ver Código QR -->
            <div class="pt-1">
                <button type="button"
                        onclick='abrirModalQrAdmin(${JSON.stringify(c)})'
                        class="w-full py-2.5 px-4 rounded-xl font-bold text-xs bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 shadow-2xs transition flex items-center justify-center gap-2 cursor-pointer"
                        style="background-color: #fef3c7; color: #92400e;">
                    <i class="fa-solid fa-qrcode text-sm text-amber-700"></i>
                    <span>Ver Código QR</span>
                </button>
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
        renderizarFilasModalBeneficio(todos, todos.length);
        return;
    }

    const filtrados = todos.filter(c => {
        return (c.empleado && c.empleado.toLowerCase().includes(query)) ||
               (c.documento && c.documento.toLowerCase().includes(query)) ||
               (c.sede && c.sede.toLowerCase().includes(query)) ||
               (c.area && c.area.toLowerCase().includes(query)) ||
               (c.cargo && c.cargo.toLowerCase().includes(query)) ||
               (c.codigo && c.codigo.toLowerCase().includes(query)) ||
               (c.entregado_por && c.entregado_por.toLowerCase().includes(query)) ||
               (c.responsable_nombre && c.responsable_nombre.toLowerCase().includes(query)) ||
               (c.responsable_cc && c.responsable_cc.toLowerCase().includes(query));
    });

    renderizarFilasModalBeneficio(filtrados, todos.length);
}

function cerrarModalCanjes() {
    document.getElementById('modal-canjes-beneficio').classList.add('hidden');
    currentModalBeneficioId = null;
}

// ==========================================
// FORMULARIO DE BENEFICIOS (CREAR / EDITAR)
// ==========================================
function editarBeneficio(data, updateUrl) {
    cambiarPestana('catalogo');

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

// Inicializar renderizado del historial al cargar
document.addEventListener('DOMContentLoaded', function () {
    renderizarHistorialCanjes(todosCanjes);
});
</script>
@endsection
