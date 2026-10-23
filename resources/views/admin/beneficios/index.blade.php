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

                            <div class="pt-3 border-t border-line/60 flex items-center justify-between text-xs">
                                @if($b->canjes_count > 0)
                                    <button type="button"
                                            onclick="abrirModalCanjesBeneficio({{ $b->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 hover:bg-amber-100 transition cursor-pointer border border-amber-200 shadow-2xs"
                                            title="Ver colaboradores que canjearon este beneficio">
                                        <i class="fa-solid fa-users text-[11px] text-amber-600"></i>
                                        <span>{{ $b->canjes_count }} canje(s)</span>
                                    </button>
                                @else
                                    <span class="text-[11px] text-muted flex items-center gap-1.5 py-1">
                                        <i class="fa-solid fa-ticket text-slate-300"></i>
                                        <span>0 canjes</span>
                                    </span>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    @if($b->canjes_count > 0)
                                        <button type="button"
                                                onclick="filtrarTablaPorBeneficio({{ $b->id }})"
                                                class="px-2 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-amber-50 text-slate-600 hover:text-amber-700 transition flex items-center gap-1"
                                                title="Filtrar este beneficio en la tabla inferior">
                                            <i class="fa-solid fa-list-check text-[10px]"></i>
                                            <span class="hidden sm:inline">Ver Lista</span>
                                        </button>
                                    @endif

                                    <button type="button"
                                            onclick='editarBeneficio(@json($b), "{{ route('admin.beneficios.update', $b) }}")'
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                                        <span>Editar</span>
                                    </button>

                                    <form method="POST" action="{{ route('admin.beneficios.destroy', $b) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este beneficio?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Eliminar">
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

    <!-- SECCIÓN COMPLETA: REGISTRO Y AUDITORÍA DE CANJES POR BENEFICIO, SEDE Y ÁREA -->
    <div id="seccion-registro-canjes" class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 space-y-5 mt-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-line/60 pb-4">
            <div>
                <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Control y Auditoría de Redenciones</span>
                <h3 class="font-heading font-extrabold text-lg text-brand-dark flex items-center gap-2">
                    <i class="fa-solid fa-users-rectangle text-brand-blue"></i>
                    <span>Colaboradores con Canjes Completados (Sede y Área)</span>
                </h3>
                <p class="text-xs text-muted mt-0.5">Visualiza qué colaboradores han completado el canje de cada beneficio, su sede física y su área asignada.</p>
            </div>

            <!-- Resumen numérico -->
            <div class="flex items-center gap-2 shrink-0">
                <div class="px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-100 text-brand-blue text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-ticket"></i>
                    <span id="contador-canjes-visibles">{{ $todosCanjes->count() }}</span> canje(s)
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-purple-50 border border-purple-100 text-purple-700 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-user-check"></i>
                    <span>{{ $estadisticas['colaboradores_canje'] ?? 0 }} colaborador(es)</span>
                </div>
            </div>
        </div>

        <!-- BARRA DE FILTROS (BENEFICIO, SEDE, ÁREA Y BÚSQUEDA) -->
        <div class="bg-slate-50/80 rounded-2xl p-4 border border-line/70 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Filtro Beneficio -->
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    <i class="fa-solid fa-gift text-amber-500 mr-1"></i>Beneficio
                </label>
                <select id="filtro-beneficio" onchange="aplicarFiltrosCanjes()" class="w-full py-2 px-3 text-xs rounded-xl border border-line bg-white text-brand-dark focus:border-brand-blue focus:outline-hidden font-medium">
                    <option value="">Todos los beneficios</option>
                    @foreach($beneficios as $b)
                        <option value="{{ $b->id }}">{{ $b->nombre }} ({{ $b->canjes_count }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Sede -->
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i>Sede
                </label>
                <select id="filtro-sede" onchange="aplicarFiltrosCanjes()" class="w-full py-2 px-3 text-xs rounded-xl border border-line bg-white text-brand-dark focus:border-brand-blue focus:outline-hidden font-medium">
                    <option value="">Todas las sedes</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Área -->
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    <i class="fa-solid fa-diagram-project text-blue-500 mr-1"></i>Área
                </label>
                <select id="filtro-area" onchange="aplicarFiltrosCanjes()" class="w-full py-2 px-3 text-xs rounded-xl border border-line bg-white text-brand-dark focus:border-brand-blue focus:outline-hidden font-medium">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }} ({{ $a->sede?->nombre ?? 'Sin sede' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Buscador -->
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 mr-1"></i>Buscar Colaborador
                </label>
                <div class="relative">
                    <input type="text"
                           id="filtro-busqueda"
                           oninput="aplicarFiltrosCanjes()"
                           placeholder="Nombre, documento, código..."
                           class="w-full py-2 pl-8 pr-7 text-xs rounded-xl border border-line bg-white text-brand-dark focus:border-brand-blue focus:outline-hidden">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-search text-[11px]"></i>
                    </span>
                    <button type="button"
                            onclick="limpiarTodosFiltros()"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                            title="Limpiar filtros">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLA COMPLETA DE CANJES -->
        @if($todosCanjes->isEmpty())
            <div class="rounded-2xl border border-line/80 p-10 text-center space-y-3 bg-slate-50/50">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h4 class="font-heading font-bold text-sm text-brand-dark">Aún no se registran canjes de beneficios</h4>
                <p class="text-xs text-muted max-w-md mx-auto">Cuando los colaboradores acumulen puntos y rediman beneficios desde su portal, aquí podrás auditar quién lo canjeó, su sede y su área asignada.</p>
            </div>
        @else
            <div class="overflow-x-auto rounded-2xl border border-line/70">
                <table class="w-full text-left text-xs text-brand-dark">
                    <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line">
                        <tr>
                            <th class="py-3 px-3.5">Código Canje</th>
                            <th class="py-3 px-3.5">Colaborador</th>
                            <th class="py-3 px-3.5">Sede</th>
                            <th class="py-3 px-3.5">Área</th>
                            <th class="py-3 px-3.5">Beneficio Canjeado</th>
                            <th class="py-3 px-3.5 text-center">Puntos</th>
                            <th class="py-3 px-3.5">Fecha</th>
                            <th class="py-3 px-3.5 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-canjes-tbody" class="divide-y divide-line/60 bg-white">
                        @foreach($todosCanjes as $c)
                            @php
                                $u = $c->usuario;
                                $sedeActual = $u?->sedeActual();
                                $areaActual = $u?->area;
                                $sedeId = $u?->sede_id ?? ($areaActual?->sede_id ?? '');
                                $areaId = $u?->area_id ?? '';
                                $searchStr = strtolower(
                                    ($u?->nombre_completo ?? '') . ' ' .
                                    ($u?->identificacion ?? '') . ' ' .
                                    ($c->codigo_canje ?? '') . ' ' .
                                    ($c->beneficio?->nombre ?? '') . ' ' .
                                    ($sedeActual?->nombre ?? '') . ' ' .
                                    ($areaActual?->nombre ?? '')
                                );
                            @endphp
                            <tr class="fila-canje hover:bg-slate-50/60 transition"
                                data-beneficio-id="{{ $c->beneficio_id }}"
                                data-sede-id="{{ $sedeId }}"
                                data-area-id="{{ $areaId }}"
                                data-search="{{ $searchStr }}">
                                <td class="py-3 px-3.5 font-mono font-bold text-brand-blue text-[11px] whitespace-nowrap">
                                    {{ $c->codigo_canje }}
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-brand-dark text-xs">{{ $u?->nombre_completo ?? 'Colaborador no encontrado' }}</div>
                                    <div class="text-[10px] text-muted flex items-center gap-1.5 mt-0.5">
                                        <span>CC: {{ $u?->identificacion ?? '—' }}</span>
                                        @if($u?->cargoOficial())
                                            <span>&bull;</span>
                                            <span>{{ $u->cargoOficial() }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($sedeActual)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-semibold border border-rose-100">
                                            <i class="fa-solid fa-location-dot text-[9px]"></i>
                                            <span>{{ $sedeActual->nombre }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Sin sede</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    @if($areaActual)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[11px] font-semibold border border-blue-100">
                                            <i class="fa-solid fa-diagram-project text-[9px]"></i>
                                            <span>{{ $areaActual->nombre }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Sin área</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3.5">
                                    <div class="font-bold text-brand-dark flex items-center gap-1.5">
                                        <i class="fa-solid fa-gift text-amber-500 text-xs"></i>
                                        <span>{{ $c->beneficio?->nombre ?? 'Beneficio eliminado' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 font-extrabold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 text-[11px]">
                                        <i class="fa-solid fa-star text-[9px] text-amber-500"></i>
                                        {{ number_format($c->puntos_utilizados) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3.5 text-muted whitespace-nowrap text-[11px]">
                                    {{ $c->fecha_canje ? $c->fecha_canje->format('d/m/Y H:i') : ($c->created_at ? $c->created_at->format('d/m/Y H:i') : '—') }}
                                </td>
                                <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700">
                                        {{ $c->estado ?? 'CANJEADO' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div id="sin-resultados-filtros" class="hidden py-10 text-center bg-slate-50/50 space-y-2">
                    <div class="text-slate-400 text-2xl">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </div>
                    <p class="text-xs font-bold text-brand-dark">No hay canjes que coincidan con los filtros seleccionados</p>
                    <p class="text-[11px] text-muted">Prueba seleccionando otra sede, área o beneficio, o restablece los filtros.</p>
                    <button type="button" onclick="limpiarTodosFiltros()" class="mt-1 px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-700 transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Restablecer Filtros</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- MODAL DE CANJES POR BENEFICIO -->
<div id="modal-canjes-beneficio" class="fixed inset-0 z-50 hidden bg-brand-dark/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-3xl overflow-hidden border border-line flex flex-col max-h-[90vh] animate-in fade-in duration-200">
        <!-- Modal Header -->
        <div class="p-5 border-b border-line/60 flex items-center justify-between bg-slate-50/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-brand-blue tracking-wider">Historial del Beneficio</span>
                    <h3 id="modal-canjes-titulo" class="font-heading font-extrabold text-base text-brand-dark">Colaboradores con Canje</h3>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCanjes()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Modal Search & Counter -->
        <div class="p-4 border-b border-line/60 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text"
                       id="modal-canjes-busqueda"
                       oninput="filtrarModalCanjes()"
                       placeholder="Buscar colaborador, sede o área..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-line focus:outline-hidden focus:border-brand-blue bg-slate-50 text-brand-dark">
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-muted">
                <span id="modal-canjes-contador" class="px-2.5 py-1 rounded-full bg-blue-50 text-brand-blue font-bold text-[11px]">
                    0 canjes
                </span>
            </div>
        </div>

        <!-- Modal Body (Table) -->
        <div class="overflow-y-auto flex-1 p-5">
            <table class="w-full text-left text-xs text-brand-dark">
                <thead class="bg-slate-50 text-muted uppercase text-[10px] font-bold border-b border-line sticky top-0">
                    <tr>
                        <th class="py-2.5 px-3">Código</th>
                        <th class="py-2.5 px-3">Colaborador</th>
                        <th class="py-2.5 px-3">Sede</th>
                        <th class="py-2.5 px-3">Área</th>
                        <th class="py-2.5 px-3">Fecha</th>
                        <th class="py-2.5 px-3 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody id="modal-canjes-tbody" class="divide-y divide-line/60">
                    <!-- Rellenado dinámicamente -->
                </tbody>
            </table>
            <div id="modal-canjes-vacio" class="hidden py-8 text-center text-muted text-xs">
                No se encontraron colaboradores que coincidan con la búsqueda.
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-line/60 bg-slate-50/80 flex items-center justify-between">
            <button type="button"
                    id="btn-ver-en-tabla-general"
                    class="text-xs font-bold text-brand-blue hover:underline flex items-center gap-1">
                <i class="fa-solid fa-arrow-down text-[10px]"></i>
                <span>Ver en la tabla general con más filtros</span>
            </button>
            <button type="button" onclick="cerrarModalCanjes()" class="py-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold text-xs transition">
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
    document.getElementById('modal-canjes-titulo').textContent = 'Colaboradores que canjearon: ' + item.nombre;
    document.getElementById('modal-canjes-busqueda').value = '';

    const btnVerGeneral = document.getElementById('btn-ver-en-tabla-general');
    btnVerGeneral.onclick = function() {
        cerrarModalCanjes();
        filtrarTablaPorBeneficio(beneficioId);
    };

    renderizarFilasModal(item.canjes);

    document.getElementById('modal-canjes-beneficio').classList.remove('hidden');
}

function renderizarFilasModal(lista) {
    const tbody = document.getElementById('modal-canjes-tbody');
    const vacio = document.getElementById('modal-canjes-vacio');
    const contador = document.getElementById('modal-canjes-contador');
    tbody.innerHTML = '';

    contador.textContent = lista.length + ' canje(s)';

    if (lista.length === 0) {
        vacio.classList.remove('hidden');
        return;
    }

    vacio.classList.add('hidden');

    lista.forEach(c => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-slate-50/60 transition';
        tr.innerHTML = `
            <td class="py-2.5 px-3 font-mono font-bold text-brand-blue text-[11px] whitespace-nowrap">${c.codigo}</td>
            <td class="py-2.5 px-3">
                <div class="font-bold text-brand-dark text-xs">${c.empleado}</div>
                <div class="text-[10px] text-muted">CC: ${c.documento} &bull; ${c.cargo}</div>
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
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                    ${c.estado}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function filtrarModalCanjes() {
    if (!currentModalBeneficioId || !canjesPorBeneficio[currentModalBeneficioId]) return;
    const query = document.getElementById('modal-canjes-busqueda').value.toLowerCase().trim();
    const todos = canjesPorBeneficio[currentModalBeneficioId].canjes;

    if (!query) {
        renderizarFilasModal(todos);
        return;
    }

    const filtrados = todos.filter(c => {
        return c.empleado.toLowerCase().includes(query) ||
               c.documento.toLowerCase().includes(query) ||
               c.sede.toLowerCase().includes(query) ||
               c.area.toLowerCase().includes(query) ||
               c.codigo.toLowerCase().includes(query);
    });

    renderizarFilasModal(filtrados);
}

function cerrarModalCanjes() {
    document.getElementById('modal-canjes-beneficio').classList.add('hidden');
    currentModalBeneficioId = null;
}

// Filtros interactivos en tiempo real para la tabla general
function aplicarFiltrosCanjes() {
    const beneficioId = document.getElementById('filtro-beneficio')?.value || '';
    const sedeId = document.getElementById('filtro-sede')?.value || '';
    const areaId = document.getElementById('filtro-area')?.value || '';
    const busqueda = (document.getElementById('filtro-busqueda')?.value || '').toLowerCase().trim();

    const filas = document.querySelectorAll('.fila-canje');
    let visibles = 0;

    filas.forEach(fila => {
        const rowBeneficio = fila.getAttribute('data-beneficio-id') || '';
        const rowSede = fila.getAttribute('data-sede-id') || '';
        const rowArea = fila.getAttribute('data-area-id') || '';
        const rowSearch = fila.getAttribute('data-search') || '';

        const matchBeneficio = !beneficioId || rowBeneficio === beneficioId;
        const matchSede = !sedeId || rowSede === sedeId;
        const matchArea = !areaId || rowArea === areaId;
        const matchSearch = !busqueda || rowSearch.includes(busqueda);

        if (matchBeneficio && matchSede && matchArea && matchSearch) {
            fila.classList.remove('hidden');
            visibles++;
        } else {
            fila.classList.add('hidden');
        }
    });

    const contador = document.getElementById('contador-canjes-visibles');
    if (contador) contador.textContent = visibles;

    const sinResultados = document.getElementById('sin-resultados-filtros');
    if (sinResultados) {
        if (visibles === 0 && filas.length > 0) {
            sinResultados.classList.remove('hidden');
        } else {
            sinResultados.classList.add('hidden');
        }
    }
}

function limpiarTodosFiltros() {
    if (document.getElementById('filtro-beneficio')) document.getElementById('filtro-beneficio').value = '';
    if (document.getElementById('filtro-sede')) document.getElementById('filtro-sede').value = '';
    if (document.getElementById('filtro-area')) document.getElementById('filtro-area').value = '';
    if (document.getElementById('filtro-busqueda')) document.getElementById('filtro-busqueda').value = '';
    aplicarFiltrosCanjes();
}

function filtrarTablaPorBeneficio(beneficioId) {
    const sel = document.getElementById('filtro-beneficio');
    if (sel) {
        sel.value = String(beneficioId);
        aplicarFiltrosCanjes();
        document.getElementById('seccion-registro-canjes')?.scrollIntoView({ behavior: 'smooth' });
    }
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
