@extends('layouts.admin')

@section('title', 'Historial de Sesiones de Inducción · Panel Administrador')

@section('content')
<div class="space-y-6">

    <!-- Encabezado de Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.inducciones.index') }}" class="text-xs text-slate-500 hover:text-cyan-700 transition-colors flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-arrow-left text-[11px]"></i> Volver a Inducciones
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight font-serif">Historial de Sesiones</h1>
            <p class="text-xs text-slate-500">Supervisa las inducciones dictadas, filtra por sede o área convocada y visualiza participantes con sus firmas digitales en tiempo real.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.inducciones.index') }}"
               class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-shapes text-cyan-600"></i>
                <span>Ver Catálogo</span>
            </a>

            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Abrir Sala Facilitador</span>
            </a>
        </div>
    </div>

    <!-- Métricas del Historial -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Sesiones</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ $metricas['total_sesiones'] }}</div>
            <div class="text-[11px] text-cyan-600 font-semibold mt-0.5">Histórico general</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones En Curso</div>
            <div class="text-2xl font-black text-emerald-600 mt-1 flex items-center gap-2">
                <span>{{ $metricas['en_curso'] }}</span>
                @if($metricas['en_curso'] > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                @endif
            </div>
            <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">Transmitiendo en vivo</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sesiones Finalizadas</div>
            <div class="text-2xl font-black text-slate-700 mt-1">{{ $metricas['finalizadas'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Actas y firmas registradas</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Asistentes</div>
            <div class="text-2xl font-black text-cyan-800 mt-1">{{ $metricas['total_asistencias'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">{{ $metricas['asistencias_externos'] }} externos registrados</div>
        </div>
    </div>

    <!-- SECCIÓN PRINCIPAL: HISTORIAL Y FILTROS EN TIEMPO REAL -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-2xs">

        <!-- Barra de Control y Buscador en Tiempo Real con Filtros por Sede y Área -->
        <div class="p-5 border-b border-slate-200 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-cyan-600"></i> Registro de Sesiones Realizadas
                    </h3>
                    <p class="text-xs text-slate-500">Filtra por sede o área convocada sin importar el estado y visualiza participantes con firmas digitales directamente.</p>
                </div>

                <!-- Pestañas Rápidas de Estado en Tiempo Real -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-2xl self-start md:self-center">
                    <button type="button"
                            onclick="cambiarFiltroEstado('todos')"
                            id="btnFiltroTodos"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-800 shadow-2xs cursor-pointer">
                        Todas ({{ $metricas['total_sesiones'] }})
                    </button>
                    <button type="button"
                            onclick="cambiarFiltroEstado('en_curso')"
                            id="btnFiltroEnCurso"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-emerald-700 cursor-pointer flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 {{ $metricas['en_curso'] > 0 ? 'animate-pulse' : '' }}"></span>
                        <span>En Curso ({{ $metricas['en_curso'] }})</span>
                    </button>
                    <button type="button"
                            onclick="cambiarFiltroEstado('finalizada')"
                            id="btnFiltroFinalizada"
                            class="filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-slate-900 cursor-pointer">
                        Finalizadas ({{ $metricas['finalizadas'] }})
                    </button>
                </div>
            </div>

            <!-- FILTROS EN TIEMPO REAL: SEDE, ÁREA Y BUSCADOR -->
            <div class="bg-slate-50/80 rounded-2xl p-3.5 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Filtro Sede -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> Sede de la Inducción
                    </label>
                    <select id="filtroSede" onchange="cambiarFiltroSede(this.value)"
                            class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:border-cyan-600 focus:outline-hidden font-medium">
                        <option value="">Todas las sedes</option>
                        @foreach($sedes as $s)
                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro Área Convocada / Aplicada -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        <i class="fa-solid fa-sitemap text-blue-500 mr-1"></i> Área Convocada / Aplicada
                    </label>
                    <select id="filtroArea" onchange="aplicarFiltrosEnVivo()"
                            class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:border-cyan-600 focus:outline-hidden font-medium">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}" data-sede-id="{{ $a->sede_id }}">{{ $a->nombre }} ({{ $a->sede?->nombre ?? 'Sin sede' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Buscador de texto en vivo -->
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 mr-1"></i> Búsqueda Rápida
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="buscadorSesiones"
                               oninput="aplicarFiltrosEnVivo()"
                               placeholder="Código (IND-...), título de inducción, facilitador o área..."
                               class="w-full py-2 pl-8 pr-8 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:border-cyan-600 focus:outline-hidden">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-search text-[11px]"></i>
                        </span>
                        <button type="button"
                                id="btnLimpiarBuscador"
                                onclick="limpiarTodosFiltros()"
                                class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer"
                                title="Restablecer filtros">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Barra de estado de filtros activos y contador dinámico -->
            <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
                <span id="contadorSesionesVisibles" class="font-bold text-slate-700">
                    Mostrando {{ $sesiones->count() }} sesiones
                </span>
                <button type="button"
                        onclick="limpiarTodosFiltros()"
                        class="text-xs font-bold text-cyan-700 hover:text-cyan-900 hover:underline cursor-pointer flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Restablecer filtros</span>
                </button>
            </div>
        </div>

        @if($sesiones->isEmpty())
            <div class="p-12 text-center space-y-2">
                <i class="fa-solid fa-file-circle-question text-3xl text-slate-300"></i>
                <h4 class="text-sm font-bold text-slate-700">No se han registrado sesiones aún</h4>
                <p class="text-xs text-slate-400">Cuando un facilitador ingrese al portal con el PIN e inicie una charla, aparecerá en este historial.</p>
            </div>
        @else

            <!-- VISTA EN MÓVIL: TARJETAS EN COLUMNA (CERO SCROLL HORIZONTAL) -->
            <div class="block md:hidden p-4 space-y-3.5" id="contenedorMovilSesiones">
                @foreach($sesiones as $s)
                    @php
                        $esActiva = $s->estaActiva();
                        $estadoSlug = $esActiva ? 'en_curso' : 'finalizada';
                        $sedeId = (string)($s->sede_id ?? '');

                        $areasConvocadasIds = is_array($s->areas_convocadas) ? array_map('strval', $s->areas_convocadas) : [];
                        $areasAsistenciasIds = $s->asistencias->pluck('usuario.area_id')->filter()->map('strval')->unique()->toArray();
                        $todasAreasIds = array_values(array_unique(array_merge($areasConvocadasIds, $areasAsistenciasIds)));
                        $esGeneral = empty($areasConvocadasIds);

                        $searchData = strtolower(
                            $s->codigo_sesion . ' ' .
                            ($s->induccion->titulo ?? '') . ' ' .
                            ($s->facilitador->nombre_completo ?? '') . ' ' .
                            ($s->sede->nombre ?? '') . ' ' .
                            ($s->areas_convocadas_nombres ?? '') . ' ' .
                            ($esActiva ? 'en curso viva activa' : 'finalizada cerrada')
                        );
                    @endphp
                    <div class="item-sesion-card bg-white rounded-2xl border border-slate-200 shadow-2xs p-4 space-y-3"
                         data-estado="{{ $estadoSlug }}"
                         data-sede-id="{{ $sedeId }}"
                         data-areas-ids="{{ implode(',', $todasAreasIds) }}"
                         data-areas-general="{{ $esGeneral ? '1' : '0' }}"
                         data-search="{{ $searchData }}">

                        <!-- Fila 1: Código + Estado -->
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5">
                            <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-cyan-50 text-cyan-800 border border-cyan-200 tracking-wider">
                                {{ $s->codigo_sesion }}
                            </span>

                            @if($esActiva)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>En Curso</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Finalizada
                                </span>
                            @endif
                        </div>

                        <!-- Fila 2: Inducción -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                {{ $s->induccion->titulo ?? 'Inducción sin título' }}
                            </h4>
                        </div>

                        <!-- Fila 3: Facilitador, Sede, Áreas y Fecha en Columna -->
                        <div class="bg-slate-50/80 rounded-xl p-2.5 space-y-2 text-xs text-slate-600 border border-slate-100">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user-tie text-slate-400 w-3.5 text-center"></i>
                                <span class="truncate">Facilitador: <strong class="text-slate-800 font-semibold">{{ $s->facilitador->nombre_completo ?? 'N/A' }}</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-rose-500 w-3.5 text-center"></i>
                                <span class="truncate">Sede: <strong class="text-slate-800 font-semibold">{{ $s->sede->nombre ?? 'Sede Principal' }}</strong></span>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fa-solid fa-sitemap text-blue-500 w-3.5 text-center mt-0.5"></i>
                                <div class="flex-1">
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Áreas Convocadas:</span>
                                    <span class="text-slate-800 font-medium text-xs leading-tight block">{{ $s->areas_convocadas_nombres }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500 pt-1 border-t border-slate-200/60">
                                <i class="fa-regular fa-calendar text-slate-400 w-3.5 text-center"></i>
                                <span>{{ $s->fecha_inicio ? $s->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Fila 4: Asistentes y Botones de Acción -->
                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-100">
                            <button type="button"
                                    onclick="abrirModalParticipantes({{ $s->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-cyan-700 hover:bg-cyan-800 text-white transition-colors shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-signature"></i>
                                <span>Ver Firmas ({{ $s->asistencias_count }})</span>
                            </button>

                            <a href="{{ route('admin.inducciones.acta_pdf', $s->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold text-xs transition-colors shadow-2xs">
                                <i class="fa-solid fa-file-pdf text-red-500"></i>
                                <span>Acta PDF</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- VISTA EN ESCRITORIO: TABLA COMPLETA CON ÁREAS Y FIRMAS -->
            <div class="hidden md:block overflow-hidden">
                <table class="w-full text-left text-xs" id="tablaEscritorioSesiones">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3.5">Código</th>
                            <th class="py-3 px-3.5">Inducción</th>
                            <th class="py-3 px-3.5">Facilitador</th>
                            <th class="py-3 px-3.5">Sede</th>
                            <th class="py-3 px-3.5">Áreas Convocadas</th>
                            <th class="py-3 px-3.5">Fecha y Hora</th>
                            <th class="py-3 px-3 text-center">Asistentes</th>
                            <th class="py-3 px-3 text-center">Estado</th>
                            <th class="py-3 px-3.5 text-right">Firmas / Acta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @foreach($sesiones as $s)
                            @php
                                $esActiva = $s->estaActiva();
                                $estadoSlug = $esActiva ? 'en_curso' : 'finalizada';
                                $sedeId = (string)($s->sede_id ?? '');

                                $areasConvocadasIds = is_array($s->areas_convocadas) ? array_map('strval', $s->areas_convocadas) : [];
                                $areasAsistenciasIds = $s->asistencias->pluck('usuario.area_id')->filter()->map('strval')->unique()->toArray();
                                $todasAreasIds = array_values(array_unique(array_merge($areasConvocadasIds, $areasAsistenciasIds)));
                                $esGeneral = empty($areasConvocadasIds);

                                $searchData = strtolower(
                                    $s->codigo_sesion . ' ' .
                                    ($s->induccion->titulo ?? '') . ' ' .
                                    ($s->facilitador->nombre_completo ?? '') . ' ' .
                                    ($s->sede->nombre ?? '') . ' ' .
                                    ($s->areas_convocadas_nombres ?? '') . ' ' .
                                    ($esActiva ? 'en curso viva activa' : 'finalizada cerrada')
                                );
                            @endphp
                            <tr class="item-sesion-row hover:bg-slate-50/70 transition-colors"
                                data-estado="{{ $estadoSlug }}"
                                data-sede-id="{{ $sedeId }}"
                                data-areas-ids="{{ implode(',', $todasAreasIds) }}"
                                data-areas-general="{{ $esGeneral ? '1' : '0' }}"
                                data-search="{{ $searchData }}">
                                <td class="py-3.5 px-3.5 font-mono font-bold text-cyan-800 whitespace-nowrap">
                                    {{ $s->codigo_sesion }}
                                </td>
                                <td class="py-3.5 px-3.5 font-bold text-slate-800">
                                    {{ $s->induccion->titulo ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-700 font-medium">
                                    {{ $s->facilitador->nombre_completo ?? 'N/A' }}
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-600 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[11px] font-semibold border border-rose-100">
                                        <i class="fa-solid fa-location-dot text-[9px]"></i>
                                        <span>{{ $s->sede->nombre ?? 'Sede Principal' }}</span>
                                    </span>
                                </td>
                                <td class="py-3.5 px-3.5 max-w-xs">
                                    @if(empty($s->areas_convocadas))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium border border-slate-200">
                                            <i class="fa-solid fa-sitemap text-[8px] text-slate-400"></i>
                                            <span>Todas las áreas convocadas</span>
                                        </span>
                                    @else
                                        <div class="flex flex-wrap gap-1" title="{{ $s->areas_convocadas_nombres }}">
                                            @foreach(array_slice($s->areas_convocadas_nombres_array, 0, 3) as $nombreArea)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-bold border border-blue-100 truncate max-w-[140px]">
                                                    <i class="fa-solid fa-sitemap text-[8px]"></i>
                                                    <span>{{ $nombreArea }}</span>
                                                </span>
                                            @endforeach
                                            @if(count($s->areas_convocadas_nombres_array) > 3)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[9px] font-bold">
                                                    +{{ count($s->areas_convocadas_nombres_array) - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                    {{ $s->fecha_inicio ? $s->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $s->asistencias_count }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                    @if($esActiva)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> En Curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            Finalizada
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                onclick="abrirModalParticipantes({{ $s->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs transition-colors shadow-2xs cursor-pointer"
                                                title="Ver lista de participantes y sus firmas digitales">
                                            <i class="fa-solid fa-signature"></i>
                                            <span>Firmas</span>
                                        </button>

                                        <a href="{{ route('admin.inducciones.acta_pdf', $s->id) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 font-bold text-xs transition-colors shadow-2xs"
                                           title="Descargar Acta Oficial en PDF">
                                            <i class="fa-solid fa-file-pdf text-red-500"></i>
                                            <span>PDF</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mensaje cuando la búsqueda o filtros no arrojan resultados -->
            <div id="sinResultadosFiltro" class="hidden p-12 text-center space-y-2">
                <i class="fa-solid fa-filter-circle-xmark text-3xl text-slate-300 mb-1"></i>
                <h4 class="text-xs font-bold text-slate-700">No se encontraron sesiones con los filtros aplicados</h4>
                <p class="text-[11px] text-slate-400">Prueba cambiando la sede, área o el término de búsqueda, o haz clic en "Restablecer filtros".</p>
                <button type="button" onclick="limpiarTodosFiltros()" class="mt-2 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Restablecer Filtros</span>
                </button>
            </div>

            <!-- Paginación -->
            <div class="p-4 border-t border-slate-200">
                {{ $sesiones->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL PARA VISUALIZAR PARTICIPANTES Y FIRMAS DIGITALES EN TIEMPO REAL -->
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
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 pt-0.5">
                        <i class="fa-solid fa-sitemap text-blue-500 text-[11px]"></i>
                        <span>Áreas Convocadas: <strong id="modalPartAreas" class="text-slate-800 font-semibold"></strong></span>
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
                       placeholder="Buscar por colaborador, cédula, cargo o área..."
                       class="w-full pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:border-cyan-600 bg-slate-50 text-slate-800">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-xl text-[11px] font-bold">
                    <button type="button" onclick="cambiarFiltroTipoPart('todos')" id="btnPartTipoTodos" class="px-2.5 py-1 rounded-lg bg-white text-slate-800 shadow-2xs">Todos</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('empleados')" id="btnPartTipoEmpleados" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Nómina</button>
                    <button type="button" onclick="cambiarFiltroTipoPart('externos')" id="btnPartTipoExternos" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900">Externos</button>
                </div>
                <span id="modalPartContador" class="px-3 py-1 rounded-full bg-cyan-50 text-cyan-800 font-bold text-xs border border-cyan-100 shrink-0">
                    0 participantes
                </span>
            </div>
        </div>

        <!-- Modal Body (Dual: Tabla en Escritorio / Tarjetas en Columna en Móvil sin scroll horizontal) -->
        <div class="overflow-y-auto overflow-x-hidden flex-1 p-3 sm:p-5">
            <!-- Spinner de carga -->
            <div id="modalPartCargando" class="py-12 text-center space-y-3">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-cyan-600"></i>
                <p class="text-xs font-bold text-slate-600">Cargando participantes y firmas digitales...</p>
            </div>

            <!-- VISTA ESCRITORIO: TABLA DE PARTICIPANTES Y FIRMAS -->
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

            <!-- VISTA MÓVIL: REPRESENTADA EN COLUMNA VERTICAL (CERO SCROLL HORIZONTAL) -->
            <div id="modalPartCardsContainer" class="block md:hidden space-y-3 w-full">
                <!-- Rellenado dinámicamente con tarjetas en columna vertical -->
            </div>

            <!-- Estado Vacío -->
            <div id="modalPartVacio" class="hidden py-10 text-center space-y-2 bg-slate-50/60 rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
                <h4 class="font-heading font-bold text-sm text-slate-800" id="modalPartVacioTitulo">Sin participantes registrados</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto" id="modalPartVacioDesc">Aún no se han firmado asistencias para esta sesión de inducción.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-3 sm:p-4 border-t border-slate-200 bg-slate-50/80 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500">
                <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> Firmas digitales con validez para actas oficiales SST.
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

        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex items-center justify-center min-h-[160px]">
            <img id="zoomFirmaImg" src="" alt="Firma Ampliada" class="max-h-48 max-w-full object-contain filter drop-shadow-xs">
        </div>

        <div class="flex items-center justify-end">
            <button type="button" onclick="cerrarModalZoomFirma()" class="w-full py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition cursor-pointer">
                Aceptar
            </button>
        </div>
    </div>
</div>

<script>
    let estadoFiltroActivo = 'todos'; // 'todos' | 'en_curso' | 'finalizada'

    function cambiarFiltroEstado(nuevoEstado) {
        estadoFiltroActivo = nuevoEstado;

        // Actualizar estilos de botones
        const botones = {
            'todos': document.getElementById('btnFiltroTodos'),
            'en_curso': document.getElementById('btnFiltroEnCurso'),
            'finalizada': document.getElementById('btnFiltroFinalizada')
        };

        Object.keys(botones).forEach(k => {
            const btn = botones[k];
            if (!btn) return;
            if (k === nuevoEstado) {
                btn.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-900 shadow-2xs cursor-pointer flex items-center gap-1.5';
            } else {
                btn.className = 'filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:text-slate-900 cursor-pointer flex items-center gap-1.5';
            }
        });

        aplicarFiltrosEnVivo();
    }

    function cambiarFiltroSede(sedeId) {
        const selectArea = document.getElementById('filtroArea');
        const optionsArea = selectArea.querySelectorAll('option');

        // Filtrar las opciones de áreas según la sede elegida
        optionsArea.forEach(opt => {
            if (!opt.value) return; // 'Todas las áreas'
            const optSedeId = opt.getAttribute('data-sede-id');
            if (!sedeId || optSedeId === sedeId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });

        // Si la opción seleccionada ya no pertenece a la sede, resetear a 'Todas las áreas'
        const selectedOpt = selectArea.options[selectArea.selectedIndex];
        if (selectedOpt && selectedOpt.value && sedeId && selectedOpt.getAttribute('data-sede-id') !== sedeId) {
            selectArea.value = '';
        }

        aplicarFiltrosEnVivo();
    }

    function aplicarFiltrosEnVivo() {
        const input = document.getElementById('buscadorSesiones');
        const q = (input.value || '').toLowerCase().trim();
        const sedeId = (document.getElementById('filtroSede')?.value || '').trim();
        const areaId = (document.getElementById('filtroArea')?.value || '').trim();
        const btnClear = document.getElementById('btnLimpiarBuscador');

        if (btnClear) {
            btnClear.classList.toggle('hidden', q.length === 0 && !sedeId && !areaId && estadoFiltroActivo === 'todos');
        }

        let visiblesContador = 0;

        function evaluaSesion(estadoItem, itemSedeId, itemAreasIds, itemAreasGeneral, searchData) {
            // 1. Filtro de Estado
            const coincideEstado = (estadoFiltroActivo === 'todos') || (estadoItem === estadoFiltroActivo);
            if (!coincideEstado) return false;

            // 2. Filtro de Sede
            if (sedeId && itemSedeId !== sedeId) {
                return false;
            }

            // 3. Filtro de Área
            if (areaId) {
                const listaAreas = itemAreasIds ? itemAreasIds.split(',') : [];
                const esGeneral = itemAreasGeneral === '1';
                const coincideArea = esGeneral || listaAreas.includes(areaId);
                if (!coincideArea) return false;
            }

            // 4. Buscador de Texto
            if (q.length > 0 && !searchData.includes(q)) {
                return false;
            }

            return true;
        }

        // Filtrar tarjetas móvil
        document.querySelectorAll('.item-sesion-card').forEach(el => {
            const estadoItem = el.getAttribute('data-estado');
            const itemSedeId = el.getAttribute('data-sede-id') || '';
            const itemAreasIds = el.getAttribute('data-areas-ids') || '';
            const itemAreasGeneral = el.getAttribute('data-areas-general') || '0';
            const searchData = el.getAttribute('data-search') || '';

            const visible = evaluaSesion(estadoItem, itemSedeId, itemAreasIds, itemAreasGeneral, searchData);
            el.style.display = visible ? '' : 'none';
            if (visible) visiblesContador++;
        });

        // Filtrar filas escritorio
        document.querySelectorAll('.item-sesion-row').forEach(el => {
            const estadoItem = el.getAttribute('data-estado');
            const itemSedeId = el.getAttribute('data-sede-id') || '';
            const itemAreasIds = el.getAttribute('data-areas-ids') || '';
            const itemAreasGeneral = el.getAttribute('data-areas-general') || '0';
            const searchData = el.getAttribute('data-search') || '';

            const visible = evaluaSesion(estadoItem, itemSedeId, itemAreasIds, itemAreasGeneral, searchData);
            el.style.display = visible ? '' : 'none';
        });

        // Actualizar contador
        const contadorTxt = document.getElementById('contadorSesionesVisibles');
        if (contadorTxt) {
            contadorTxt.textContent = `Mostrando ${visiblesContador} sesión(es) coincidente(s)`;
        }

        // Mostrar u ocultar mensaje vacío
        const sinResultados = document.getElementById('sinResultadosFiltro');
        if (sinResultados) {
            sinResultados.classList.toggle('hidden', visiblesContador > 0);
        }
    }

    function limpiarTodosFiltros() {
        document.getElementById('buscadorSesiones').value = '';
        document.getElementById('filtroSede').value = '';
        cambiarFiltroSede('');
        document.getElementById('filtroArea').value = '';
        cambiarFiltroEstado('todos');
        aplicarFiltrosEnVivo();
    }

    // ==========================================
    // LÓGICA DEL MODAL DE PARTICIPANTES Y FIRMAS
    // ==========================================
    let participantesActuales = [];
    let tipoParticipanteFiltro = 'todos'; // 'todos' | 'empleados' | 'externos'

    async function abrirModalParticipantes(sesionId) {
        const modal = document.getElementById('modalParticipantesSesion');
        const cargando = document.getElementById('modalPartCargando');
        const tabla = document.getElementById('modalPartTablaContainer');
        const cards = document.getElementById('modalPartCardsContainer');
        const vacio = document.getElementById('modalPartVacio');

        modal.classList.remove('hidden');
        cargando.classList.remove('hidden');
        tabla.classList.add('hidden');
        cards.classList.add('hidden');
        vacio.classList.add('hidden');

        document.getElementById('modalPartBusqueda').value = '';
        tipoParticipanteFiltro = 'todos';
        actualizarBotonesTipoPart('todos');

        try {
            const res = await fetch(`/admin/inducciones/sesiones/${sesionId}/participantes-json`);
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
            document.getElementById('modalPartAreas').textContent = sesion.areas_convocadas;
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
            tabla.classList.remove('hidden');
            cards.classList.remove('hidden');

            renderizarParticipantesModal(participantesActuales);

        } catch (err) {
            console.error('Error al consultar participantes:', err);
            alert('Ocurrió un error de conexión al cargar las asistencias.');
            cerrarModalParticipantes();
        }
    }

    function renderizarParticipantesModal(lista) {
        const tbody = document.getElementById('modalPartTbody');
        const cardsCont = document.getElementById('modalPartCardsContainer');
        const vacio = document.getElementById('modalPartVacio');
        const contador = document.getElementById('modalPartContador');

        tbody.innerHTML = '';
        cardsCont.innerHTML = '';
        contador.textContent = `${lista.length} participante(s)`;

        if (lista.length === 0) {
            vacio.classList.remove('hidden');
            return;
        }

        vacio.classList.add('hidden');

        lista.forEach((p, idx) => {
            const num = idx + 1;
            const badgeExterno = p.es_externo
                ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold uppercase">Externo</span>'
                : '<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-blue-100 text-brand-blue text-[9px] font-bold uppercase">Nómina</span>';

            const firmaHtml = p.tiene_firma && p.firma_url
                ? `<div onclick="ampliarFirma('${p.firma_url}', '${p.nombre_completo.replace(/'/g, "\\'")}')"
                        class="bg-white p-1 rounded-xl border border-slate-200 shadow-2xs inline-flex items-center justify-center cursor-pointer hover:border-cyan-500 transition hover:scale-105 group"
                        title="Clic para ampliar firma">
                       <img src="${p.firma_url}" alt="Firma de ${p.nombre_completo}" class="h-9 max-w-[130px] object-contain" />
                       <i class="fa-solid fa-magnifying-glass-plus text-slate-300 group-hover:text-cyan-600 text-[10px] ml-1"></i>
                   </div>`
                : `<span class="text-[11px] text-slate-400 italic">Sin firma</span>`;

            // 1. Fila de tabla para escritorio
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 transition-colors';
            tr.innerHTML = `
                <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-400 font-bold">${num}</td>
                <td class="py-2.5 px-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-xs">${p.nombre_completo}</span>
                        ${badgeExterno}
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
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-semibold border border-blue-100">
                        <i class="fa-solid fa-diagram-project text-[8px]"></i>
                        <span>${p.area}</span>
                    </span>
                </td>
                <td class="py-2.5 px-3 text-slate-500 text-[11px] font-mono whitespace-nowrap">
                    ${p.fecha_registro}
                </td>
                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                    ${firmaHtml}
                </td>
            `;
            tbody.appendChild(tr);

            // 2. Tarjeta en columna para móvil (sin scroll horizontal)
            const card = document.createElement('div');
            card.className = 'bg-slate-50/90 border border-slate-200 rounded-2xl p-3.5 space-y-2.5 w-full';
            card.innerHTML = `
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                    <span class="font-mono font-bold text-slate-400 text-xs">#${num}</span>
                    ${badgeExterno}
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-xs leading-snug">${p.nombre_completo}</div>
                    <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-1.5 mt-0.5">
                        <span>CC: <strong>${p.identificacion}</strong></span>
                        <span>&bull;</span>
                        <span>${p.cargo}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400 text-[10px] uppercase font-bold w-12 shrink-0">Sede:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-semibold border border-rose-100 truncate">
                            <i class="fa-solid fa-location-dot text-[8px]"></i>
                            <span>${p.sede}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400 text-[10px] uppercase font-bold w-12 shrink-0">Área:</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-brand-blue text-[10px] font-semibold border border-blue-100 truncate">
                            <i class="fa-solid fa-diagram-project text-[8px]"></i>
                            <span>${p.area}</span>
                        </span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between gap-2">
                    <span class="text-[10px] text-slate-400 font-mono">
                        <i class="fa-regular fa-clock mr-1"></i>${p.fecha_registro}
                    </span>
                    <div>
                        ${firmaHtml}
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
        const btnTodos = document.getElementById('btnPartTipoTodos');
        const btnEmp = document.getElementById('btnPartTipoEmpleados');
        const btnExt = document.getElementById('btnPartTipoExternos');

        [btnTodos, btnEmp, btnExt].forEach(b => {
            if (b) b.className = 'px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-colors cursor-pointer';
        });

        if (tipo === 'todos' && btnTodos) {
            btnTodos.className = 'px-2.5 py-1 rounded-lg bg-white text-slate-900 shadow-2xs font-bold cursor-pointer';
        } else if (tipo === 'empleados' && btnEmp) {
            btnEmp.className = 'px-2.5 py-1 rounded-lg bg-white text-brand-blue shadow-2xs font-bold cursor-pointer';
        } else if (tipo === 'externos' && btnExt) {
            btnExt.className = 'px-2.5 py-1 rounded-lg bg-white text-amber-800 shadow-2xs font-bold cursor-pointer';
        }
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
</script>
@endsection
