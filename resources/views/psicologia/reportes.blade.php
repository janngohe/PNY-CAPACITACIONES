@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.psicologia')

@section('title', 'Reportes · Psicología')
@section('page_title', 'Reportes y Consolidado Excel')

@section('content')
<div class="space-y-5">

    <!-- Encabezado y Filtro por Año + Botón de Descarga Excel -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 shadow-2xs">
                    <i class="fa-solid fa-file-excel text-xl"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 border border-emerald-200/60">
                        <i class="fa-solid fa-table"></i>
                        CONSOLIDADO ANUAL DE PRUEBAS PSICOTÉCNICAS
                    </span>
                    <h1 class="mt-1 font-heading text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        Reporte Excel de Pruebas Aplicadas ({{ $anioSeleccionado }})
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Filtra únicamente por año y descarga el archivo Excel (<code>.xlsx</code>) con los 10 campos oficiales del consolidado institucional.
                    </p>
                </div>
            </div>

            <!-- Selector de Año (en vivo) + Botón Descargar Excel -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                <form method="GET" action="{{ request()->routeIs('admin.*') ? route('admin.psicologia.reportes') : route('psicologia.reportes') }}" class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <label for="selectAnioReporte" class="text-xs font-bold text-slate-600 flex items-center gap-1.5 shrink-0">
                        <i class="fa-regular fa-calendar text-brand-blue"></i>
                        <span>Año:</span>
                    </label>
                    <select name="anio"
                            id="selectAnioReporte"
                            onchange="this.form.submit()"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-extrabold text-slate-900 focus:border-brand-blue focus:outline-none cursor-pointer">
                        @foreach($aniosDisponibles as $anioItem)
                            <option value="{{ $anioItem }}" @selected((int) $anioSeleccionado === (int) $anioItem)>
                                {{ $anioItem }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.reportes.excel', ['anio' => $anioSeleccionado]) : route('psicologia.reportes.excel', ['anio' => $anioSeleccionado]) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-xs font-bold shadow-sm transition-colors">
                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                    <span>Descargar Excel {{ $anioSeleccionado }} (.xlsx)</span>
                </a>
            </div>
        </div>

        <!-- Tarjetas de Resumen del Año Seleccionado -->
        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-5 gap-2.5">
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 px-3.5 py-2.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Año {{ $anioSeleccionado }}</span>
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

    <!-- Vista Previa Exacta del Archivo Excel a Descargar -->
    @if($evaluaciones->isEmpty())
        <div class="rounded-2xl border border-slate-200/80 bg-white p-12 text-center shadow-2xs">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                <i class="fa-solid fa-folder-open text-2xl"></i>
            </div>
            <h3 class="font-heading font-bold text-base text-slate-800">No hay registros de pruebas en el año {{ $anioSeleccionado }}</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                Selecciona otro año en el filtro superior o descarga la plantilla vacía con los encabezados oficiales.
            </p>
        </div>
    @else

        <!-- 1. VISTA MÓVIL (< md): TARJETAS ADAPTATIVAS -->
        <div class="block md:hidden space-y-3">
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
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-2xs space-y-2.5">
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

                    <div class="text-[11px] bg-slate-50 rounded-xl p-2.5 border border-slate-100 space-y-1">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px]">Cargo para desempeñar:</span>
                            <span class="font-bold text-slate-800 uppercase block">{{ $ev->cargo_aspira ?: '—' }}</span>
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
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. VISTA ESCRITORIO (>= md): TABLA IDÉNTICA AL EXCEL -->
        <div class="hidden md:block rounded-2xl border border-slate-300 bg-white shadow-2xs overflow-hidden">
            <div class="px-4 py-2.5 bg-slate-900 text-white flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 font-bold">
                    <i class="fa-solid fa-table-cells text-emerald-400"></i>
                    <span>Vista previa exacta de la hoja "{{ $anioSeleccionado }}" (PRUEBAS APLICADAS {{ $anioSeleccionado }}.xlsx)</span>
                </div>
                <span class="text-[11px] text-slate-300">{{ $totalAnio }} {{ $totalAnio === 1 ? 'registro' : 'registros' }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-center text-xs">
                    <thead>
                        <tr style="background-color: #A8D08D;" class="text-black font-bold text-[11px] uppercase">
                            <th class="border border-slate-700 px-2 py-2.5 w-12">N°</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[210px]">NOMBRE COMPLETO</th>
                            <th class="border border-slate-700 px-3 py-2.5 w-36">NÚMERO DE DOCUMENTO</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[180px]">CARGO PARA DESEMPEÑAR</th>
                            <th class="border border-slate-700 px-2.5 py-2.5 w-28">FECHA DE APLICACIÓN</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-20 text-[10px]">CUMPLE</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-28 text-[10px]">CUMPLE SUGIEREN RECOMENDACIONES</th>
                            <th class="border border-slate-700 px-2 py-2.5 w-24 text-[10px]">NO CUMPLE CON EL PERFIL</th>
                            <th class="border border-slate-700 px-3 py-2.5 min-w-[260px]">OBSERVACION</th>
                            <th class="border border-slate-700 px-3 py-2.5 w-32">SEGUIMIENTO</th>
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
                            @endphp
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="border border-slate-300 px-2 py-2 font-semibold">{{ $num }}</td>
                                <td class="border border-slate-300 px-3 py-2 font-semibold uppercase">{{ $ev->nombre_completo }}</td>
                                <td class="border border-slate-300 px-3 py-2 font-mono">{{ $ev->identificacion }}</td>
                                <td class="border border-slate-300 px-3 py-2 uppercase">{{ $ev->cargo_aspira }}</td>
                                <td class="border border-slate-300 px-2.5 py-2 font-mono">{{ $fechaApl }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-emerald-700">{{ $marcaCumple }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-sky-700">{{ $marcaRecom }}</td>
                                <td class="border border-slate-300 px-2 py-2 font-extrabold text-sm text-rose-700">{{ $marcaNoCumple }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-[11px] leading-snug">{{ $obsTexto }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-400"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
