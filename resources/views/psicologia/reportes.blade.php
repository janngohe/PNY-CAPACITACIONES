@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.psicologia')

@section('title', 'Reportes · Psicología')
@section('page_title', 'Reportes y Consolidado Excel')

@section('content')
<div class="space-y-5">

    <!-- Encabezado con Filtros Avanzados (Año, Mes, Sede, Área) + Botón Descarga Excel -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 shadow-2xs">
                    <i class="fa-solid fa-file-excel text-xl"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200/60">
                        <i class="fa-solid fa-table"></i>
                        CONSOLIDADO OFICIAL DE PRUEBAS PSICOTÉCNICAS
                    </span>
                    <h1 class="mt-1 font-heading text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        Reporte de Pruebas Aplicadas ({{ $anioSeleccionado }}{{ $mesSeleccionado ? ' · ' . ($mesesDisponibles[$mesSeleccionado] ?? '') : '' }})
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Filtra por año, mes, sede y área, busca en tiempo real y descarga el consolidado en formato Excel (<code>.xlsx</code>).
                    </p>
                </div>
            </div>

            <!-- Botón de Descarga Excel con los Filtros Aplicados -->
            @php
                $rutaExcel = request()->routeIs('admin.*') ? 'admin.psicologia.reportes.excel' : 'psicologia.reportes.excel';
                $parametrosDescarga = array_filter([
                    'anio' => $anioSeleccionado,
                    'mes' => $mesSeleccionado,
                    'sede_id' => $sedeIdSeleccionada,
                    'area' => $areaSeleccionada,
                    'buscar' => $buscar ?? null,
                ], fn($val) => $val !== null && $val !== '');
            @endphp
            <div class="shrink-0">
                <a href="{{ route($rutaExcel, $parametrosDescarga) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-xs font-bold shadow-sm transition-colors">
                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                    <span>Descargar Excel (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- FORMULARIO DE FILTROS EN VIVO + BUSCADOR EN TIEMPO REAL -->
        <div class="mt-5 pt-4 border-t border-slate-100">
            @php
                $rutaForm = request()->routeIs('admin.*') ? route('admin.psicologia.reportes') : route('psicologia.reportes');
                $hayFiltrosActivos = $mesSeleccionado !== null || $sedeIdSeleccionada !== null || $areaSeleccionada !== null || !empty($buscar);
            @endphp
            <form method="GET" action="{{ $rutaForm }}" id="formFiltrosReportes">
                
                <!-- 1. BARRA DE BÚSQUEDA EN TIEMPO REAL (Nombre o Cédula) -->
                <div class="mb-3.5">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-sm pointer-events-none"></i>
                        <input type="text"
                               name="buscar"
                               id="inputBuscarEnVivo"
                               value="{{ $buscar ?? '' }}"
                               autocomplete="off"
                               placeholder="Buscar en tiempo real por nombre del evaluado o número de cédula..."
                               class="w-full pl-10 pr-28 py-2.5 rounded-xl border border-slate-200 bg-slate-50/80 focus:bg-white focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-xs text-slate-800 placeholder-slate-400 transition-all font-medium">
                        
                        <div class="absolute right-2 top-2 flex items-center gap-1.5">
                            <button type="button"
                                    id="btnLimpiarBusqueda"
                                    class="{{ empty($buscar) ? 'hidden' : '' }} text-slate-400 hover:text-slate-600 px-1.5 py-1 text-xs transition-colors"
                                    title="Limpiar búsqueda">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-200/80 text-[10px] font-mono font-bold text-slate-600 select-none">
                                <span id="contadorVisibles">{{ $totalAnio }}</span>/<span id="contadorTotal">{{ $totalAnio }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2. SELECTORES DE FILTROS (Año, Mes, Sede, Área) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    
                    <!-- FILTRO AÑO -->
                    <div>
                        <label for="selectAnio" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-regular fa-calendar text-brand-blue mr-1"></i> Año
                        </label>
                        <select name="anio"
                                id="selectAnio"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:border-brand-blue focus:outline-none cursor-pointer">
                            @foreach($aniosDisponibles as $anioItem)
                                <option value="{{ $anioItem }}" @selected((int) $anioSeleccionado === (int) $anioItem)>
                                    Año {{ $anioItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- FILTRO MES -->
                    <div>
                        <label for="selectMes" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-regular fa-calendar-days text-brand-blue mr-1"></i> Mes
                        </label>
                        <select name="mes"
                                id="selectMes"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:border-brand-blue focus:outline-none cursor-pointer">
                            <option value="">Todos los meses</option>
                            @foreach($mesesDisponibles as $numMes => $nombreMes)
                                <option value="{{ $numMes }}" @selected((int) $mesSeleccionado === (int) $numMes)>
                                    {{ $nombreMes }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- FILTRO SEDE -->
                    <div>
                        <label for="selectSede" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-location-dot text-brand-blue mr-1"></i> Sede
                        </label>
                        <select name="sede_id"
                                id="selectSede"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:border-brand-blue focus:outline-none cursor-pointer">
                            <option value="">Todas las sedes</option>
                            @foreach($sedesDisponibles as $sedeItem)
                                <option value="{{ $sedeItem->id }}" @selected((int) $sedeIdSeleccionada === (int) $sedeItem->id)>
                                    {{ $sedeItem->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- FILTRO ÁREA -->
                    <div>
                        <label for="selectArea" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            <i class="fa-solid fa-building-user text-brand-blue mr-1"></i> Área
                        </label>
                        <select name="area"
                                id="selectArea"
                                onchange="this.form.submit()"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:border-brand-blue focus:outline-none cursor-pointer">
                            <option value="">Todas las áreas</option>
                            @foreach($areasDisponibles as $areaItem)
                                <option value="{{ $areaItem }}" @selected($areaSeleccionada === $areaItem)>
                                    {{ $areaItem === 'Sst' ? 'SST' : $areaItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                @if($hayFiltrosActivos)
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-600">
                            <span class="text-[11px] font-bold uppercase text-slate-400">Filtros activos:</span>
                            @if(!empty($buscar))
                                <span class="inline-flex items-center gap-1 bg-brand-light text-brand-dark px-2.5 py-0.5 rounded-full font-semibold text-[11px]">
                                    <i class="fa-solid fa-magnifying-glass text-[9px]"></i> "{{ $buscar }}"
                                </span>
                            @endif
                            @if($mesSeleccionado)
                                <span class="inline-flex items-center gap-1 bg-brand-light text-brand-dark px-2.5 py-0.5 rounded-full font-semibold text-[11px]">
                                    Mes: {{ $mesesDisponibles[$mesSeleccionado] ?? $mesSeleccionado }}
                                </span>
                            @endif
                            @if($sedeIdSeleccionada)
                                @php
                                    $sedeActual = $sedesDisponibles->firstWhere('id', $sedeIdSeleccionada);
                                @endphp
                                <span class="inline-flex items-center gap-1 bg-brand-light text-brand-dark px-2.5 py-0.5 rounded-full font-semibold text-[11px]">
                                    Sede: {{ $sedeActual?->nombre ?? 'ID ' . $sedeIdSeleccionada }}
                                </span>
                            @endif
                            @if($areaSeleccionada)
                                <span class="inline-flex items-center gap-1 bg-brand-light text-brand-dark px-2.5 py-0.5 rounded-full font-semibold text-[11px]">
                                    Área: {{ $areaSeleccionada }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ $rutaForm }}?anio={{ $anioSeleccionado }}"
                           class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors inline-flex items-center gap-1">
                            <i class="fa-solid fa-filter-circle-xmark"></i>
                            <span>Limpiar filtros</span>
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tarjetas de Resumen con el Resultado Filtrado -->
        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-5 gap-2.5">
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Registros</span>
                <span class="font-heading text-lg font-extrabold text-slate-900">{{ $totalAnio }}</span>
            </div>
            <div class="rounded-xl bg-emerald-50/70 border border-emerald-200/70 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Cumple</span>
                <span class="font-heading text-lg font-extrabold text-emerald-800">{{ $totalCumple }}</span>
            </div>
            <div class="rounded-xl bg-sky-50/70 border border-sky-200/70 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 block">Con Recomendaciones</span>
                <span class="font-heading text-lg font-extrabold text-sky-800">{{ $totalRecomendaciones }}</span>
            </div>
            <div class="rounded-xl bg-rose-50/70 border border-rose-200/70 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">No Cumple Perfil</span>
                <span class="font-heading text-lg font-extrabold text-rose-800">{{ $totalNoCumple }}</span>
            </div>
            <div class="col-span-2 sm:col-span-1 rounded-xl bg-amber-50/70 border border-amber-200/70 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Sin Concepto Aún</span>
                <span class="font-heading text-lg font-extrabold text-amber-800">{{ $totalPendientesCert }}</span>
            </div>
        </div>
    </div>

    <!-- Mensaje cuando la búsqueda en tiempo real no arroja coincidencias -->
    <div id="mensajeSinResultadosEnVivo" class="hidden rounded-2xl border-2 border-dashed border-slate-200 bg-white p-10 text-center shadow-2xs">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 mb-2.5">
            <i class="fa-solid fa-magnifying-glass text-xl"></i>
        </div>
        <h3 class="font-heading font-bold text-sm text-slate-800">No se encontraron evaluaciones con ese nombre o cédula</h3>
        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
            Revisa el texto ingresado en el buscador o límpialo para volver a ver los evaluados del listado.
        </p>
    </div>

    <!-- Vista Previa Exacta del Archivo Excel a Descargar -->
    @if($evaluaciones->isEmpty())
        <div class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-2xs">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                <i class="fa-solid fa-filter text-2xl"></i>
            </div>
            <h3 class="font-heading font-bold text-base text-slate-800">No se encontraron evaluaciones con los filtros aplicados</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                Intenta seleccionando otro mes, sede o área, o limpia los filtros para ver todos los registros del año {{ $anioSeleccionado }}.
            </p>
            @if($hayFiltrosActivos)
                <div class="mt-4">
                    <a href="{{ $rutaForm }}?anio={{ $anioSeleccionado }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                        <span>Restablecer filtros del año {{ $anioSeleccionado }}</span>
                    </a>
                </div>
            @endif
        </div>
    @else

        <!-- 1. VISTA MÓVIL (< md): TARJETAS ADAPTATIVAS CON BÚSQUEDA EN TIEMPO REAL -->
        <div class="block md:hidden space-y-3" id="contenedorTarjetasMovil">
            @foreach($evaluaciones as $index => $ev)
                @php
                    $num = $index + 1;
                    $fechaApl = ($ev->fecha_hora_aceptacion ?? $ev->created_at)?->format('d/m/Y') ?? '—';
                    $concepto = $ev->conceptoAptitudinalActual();
                    $marcaCumple = $concepto === 'CUMPLE_SIN_INDICE';
                    $marcaRecom = $concepto === 'CUMPLE_CON_RECOMENDACIONES';
                    $marcaNoCumple = $concepto === 'NO_CUMPLE';
                    $obsTexto = $ev->observacionReporteExcel();
                @endphp
                <div class="tarjeta-evaluado rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs space-y-2.5 transition-all"
                     data-nombre="{{ mb_strtolower($ev->nombre_completo, 'UTF-8') }}"
                     data-cedula="{{ $ev->identificacion }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="inline-block rounded bg-emerald-100 text-emerald-900 font-extrabold text-[10px] px-2 py-0.5 mr-1">
                                N° {{ $num }}
                            </span>
                            <span class="font-bold text-slate-900 text-xs uppercase">{{ $ev->nombre_completo }}</span>
                            <div class="text-[11px] font-mono text-slate-500 mt-0.5">
                                Doc: {{ $ev->identificacion }} · Fecha: {{ $fechaApl }}
                            </div>
                        </div>
                    </div>

                    <div class="text-[11px] bg-slate-50 rounded-xl p-2.5 border border-slate-100 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-400 font-bold uppercase text-[10px] block">Área / Cargo:</span>
                                <span class="font-bold text-slate-800 uppercase">{{ $ev->cargo_aspira ?: '—' }}</span>
                            </div>
                            @if($ev->sede)
                                <div class="text-right">
                                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Sede:</span>
                                    <span class="font-medium text-slate-700 text-[11px]">{{ $ev->sede->nombre }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-1.5 pt-1.5 border-t border-slate-200/70 text-center">
                            <div class="rounded-lg p-1.5 border {{ $marcaCumple ? 'bg-emerald-50 border-emerald-300 text-emerald-800 font-extrabold' : 'bg-white border-slate-200 text-slate-400' }}">
                                <span class="block text-[9px] uppercase">CUMPLE</span>
                                <span class="text-xs">{{ $marcaCumple ? 'X' : '—' }}</span>
                            </div>
                            <div class="rounded-lg p-1.5 border {{ $marcaRecom ? 'bg-sky-50 border-sky-300 text-sky-800 font-extrabold' : 'bg-white border-slate-200 text-slate-400' }}">
                                <span class="block text-[9px] uppercase">RECOMEND.</span>
                                <span class="text-xs">{{ $marcaRecom ? 'X' : '—' }}</span>
                            </div>
                            <div class="rounded-lg p-1.5 border {{ $marcaNoCumple ? 'bg-rose-50 border-rose-300 text-rose-800 font-extrabold' : 'bg-white border-slate-200 text-slate-400' }}">
                                <span class="block text-[9px] uppercase">NO CUMPLE</span>
                                <span class="text-xs">{{ $marcaNoCumple ? 'X' : '—' }}</span>
                            </div>
                        </div>
                        <div class="pt-1.5 border-t border-slate-200/70">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Observación:</span>
                            <span class="text-slate-700 text-[11px]">{{ $obsTexto !== '' ? $obsTexto : '—' }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200/70 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Expediente Digital:</span>
                            @php
                                $rutaPaquete = request()->routeIs('admin.*')
                                    ? route('admin.psicologia.reportes.exportar_resultados', $ev->id)
                                    : route('psicologia.reportes.exportar_resultados', $ev->id);
                            @endphp
                            <a href="{{ $rutaPaquete }}"
                               title="Descargar paquete de resultados (Consentimiento + Valanti + PAC-A + Certificado)"
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-2xs transition-colors">
                                <i class="fa-solid fa-file-zipper text-xs"></i>
                                <span>Exportar Resultados</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. VISTA ESCRITORIO (>= md): TABLA CON BÚSQUEDA EN TIEMPO REAL -->
        <div class="hidden md:block rounded-2xl border border-slate-300 bg-white shadow-2xs overflow-hidden" id="contenedorTablaEscritorio">
            <div class="px-4 py-2.5 bg-slate-900 text-white flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 font-bold">
                    <i class="fa-solid fa-table-cells text-emerald-400"></i>
                    <span>Vista previa del consolidado oficial de pruebas psicotécnicas ({{ $anioSeleccionado }})</span>
                </div>
                <span class="text-[11px] text-slate-300">
                    <span id="badgeContadorTabla">{{ $totalAnio }}</span> {{ $totalAnio === 1 ? 'registro' : 'registros' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-center text-xs" id="tablaReportesPsicologia">
                    <thead>
                        <tr style="background-color: #A8D08D;" class="text-black font-bold text-[11px] uppercase">
                            <th class="border border-slate-700 px-2 py-2.5 w-12">N°</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[210px]">NOMBRE COMPLETO</th>
                            <th class="border border-slate-700 px-3 py-2.5 w-36">NÚMERO DE DOCUMENTO</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[190px]">CARGO PARA DESEMPEÑAR</th>
                            <th class="border border-slate-700 px-2.5 py-2.5 w-28">FECHA DE APLICACIÓN</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-20 text-[10px]">CUMPLE</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-28 text-[10px]">CUMPLE SUGIEREN RECOMENDACIONES</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-24 text-[10px]">NO CUMPLE CON EL PERFIL</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[260px]">OBSERVACION</th>
                            <th class="border border-slate-700 px-3 py-2.5 w-32">SEGUIMIENTO</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[170px] text-center bg-slate-800 text-white">EXPORTAR</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 text-slate-900">
                        @foreach($evaluaciones as $index => $ev)
                            @php
                                $num = $index + 1;
                                $fechaApl = ($ev->fecha_hora_aceptacion ?? $ev->created_at)?->format('d/m/Y') ?? '';
                                $concepto = $ev->conceptoAptitudinalActual();
                                $marcaCumple = $concepto === 'CUMPLE_SIN_INDICE' ? 'X' : '';
                                $marcaRecom = $concepto === 'CUMPLE_CON_RECOMENDACIONES' ? 'X' : '';
                                $marcaNoCumple = $concepto === 'NO_CUMPLE' ? 'X' : '';
                                $obsTexto = $ev->observacionReporteExcel();
                                $rutaPaquete = request()->routeIs('admin.*')
                                    ? route('admin.psicologia.reportes.exportar_resultados', $ev->id)
                                    : route('psicologia.reportes.exportar_resultados', $ev->id);
                            @endphp
                            <tr class="fila-evaluado hover:bg-emerald-50/30 transition-colors"
                                data-nombre="{{ mb_strtolower($ev->nombre_completo, 'UTF-8') }}"
                                data-cedula="{{ $ev->identificacion }}">
                                <td class="border border-slate-300 px-2 py-2 font-semibold">{{ $num }}</td>
                                <td class="border border-slate-300 px-3 py-2 font-semibold uppercase">{{ $ev->nombre_completo }}</td>
                                <td class="border border-slate-300 px-3 py-2 font-mono">{{ $ev->identificacion }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <span class="font-semibold uppercase block">{{ $ev->cargo_aspira }}</span>
                                    @if($ev->sede)
                                        <span class="inline-flex items-center gap-1 text-[10px] text-slate-500 font-normal mt-0.5">
                                            <i class="fa-solid fa-location-dot text-[9px] text-brand-blue"></i>
                                            {{ $ev->sede->nombre }}
                                        </span>
                                    @endif
                                </td>
                                <td class="border border-slate-300 px-2.5 py-2 font-mono">{{ $fechaApl }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-emerald-700">{{ $marcaCumple }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-sky-700">{{ $marcaRecom }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-rose-700">{{ $marcaNoCumple }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-[11px] leading-snug">{{ $obsTexto }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-400"></td>
                                <td class="border border-slate-300 px-3 py-2 text-center">
                                    <a href="{{ $rutaPaquete }}"
                                       title="Descargar paquete de resultados: Consentimiento, Valanti, PAC-A y Certificado"
                                       class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold shadow-2xs transition-colors whitespace-nowrap">
                                        <i class="fa-solid fa-file-zipper text-xs"></i>
                                        <span>Exportar Resultados</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- SCRIPT DE FILTRADO EN TIEMPO REAL -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputBuscar = document.getElementById('inputBuscarEnVivo');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
    const contadorVisibles = document.getElementById('contadorVisibles');
    const badgeContadorTabla = document.getElementById('badgeContadorTabla');
    const mensajeSinResultados = document.getElementById('mensajeSinResultadosEnVivo');
    const contenedorTabla = document.getElementById('contenedorTablaEscritorio');
    const contenedorTarjetas = document.getElementById('contenedorTarjetasMovil');
    const filas = document.querySelectorAll('.fila-evaluado');
    const tarjetas = document.querySelectorAll('.tarjeta-evaluado');

    if (!inputBuscar) return;

    function normalizar(texto) {
        return (texto || '')
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function filtrarEnTiempoReal() {
        const query = normalizar(inputBuscar.value);
        let visibles = 0;

        if (btnLimpiar) {
            btnLimpiar.classList.toggle('hidden', query === '');
        }

        filas.forEach(function (fila) {
            const nombre = normalizar(fila.getAttribute('data-nombre'));
            const cedula = normalizar(fila.getAttribute('data-cedula'));
            const coincide = query === '' || nombre.includes(query) || cedula.includes(query);
            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        tarjetas.forEach(function (tarjeta) {
            const nombre = normalizar(tarjeta.getAttribute('data-nombre'));
            const cedula = normalizar(tarjeta.getAttribute('data-cedula'));
            const coincide = query === '' || nombre.includes(query) || cedula.includes(query);
            tarjeta.style.display = coincide ? '' : 'none';
        });

        if (contadorVisibles) {
            contadorVisibles.textContent = visibles;
        }

        if (badgeContadorTabla) {
            badgeContadorTabla.textContent = visibles;
        }

        const totalRegistros = filas.length || tarjetas.length;
        if (mensajeSinResultados) {
            const sinCoincidencias = (visibles === 0 && totalRegistros > 0);
            mensajeSinResultados.classList.toggle('hidden', !sinCoincidencias);

            if (contenedorTabla) {
                contenedorTabla.classList.toggle('hidden', sinCoincidencias);
            }
            if (contenedorTarjetas) {
                contenedorTarjetas.classList.toggle('hidden', sinCoincidencias);
            }
        }
    }

    inputBuscar.addEventListener('input', filtrarEnTiempoReal);
    inputBuscar.addEventListener('keyup', filtrarEnTiempoReal);

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            inputBuscar.value = '';
            filtrarEnTiempoReal();
            inputBuscar.focus();
        });
    }

    // Si ya viene con valor inicial de búsqueda en backend, ejecutar filtro
    if (inputBuscar.value.trim() !== '') {
        filtrarEnTiempoReal();
    }
});
</script>
@endsection
