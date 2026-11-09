@extends('layouts.admin')

@section('title', 'Generación de Reportes')
@section('page_title', 'Reportes y Estadísticas')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Auditoría y Analítica',
        'titulo' => 'Centro de Reportes y Exportación',
        'descripcion' => 'Genera y descarga informes oficiales en formato Excel/CSV o PDF con membrete corporativo para auditorías de calidad (ICA, HACCP, BAP), gestión del talento y control de cumplimiento.',
        'icono' => 'fa-file-arrow-down',
    ])

    <!-- REPORTE OFICIAL DE PSICOLOGÍA Y SELECCIÓN (EXCEL PRUEBAS APLICADAS) -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-7 text-white shadow-md relative overflow-hidden border border-emerald-500/30">
        <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-emerald-500/10 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-extrabold uppercase tracking-wider">
                        <i class="fa-solid fa-file-excel mr-1"></i> Formato Oficial Excel
                    </span>
                    <span class="text-xs text-slate-300 font-semibold">Módulo de Psicología y Selección</span>
                </div>
                <h2 class="text-lg sm:text-xl font-heading font-extrabold text-white leading-tight">
                    Reporte Anual de Pruebas Psicotécnicas Aplicadas (PAC-A / VALANTI)
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Descarga el consolidado anual de aspirantes evaluados, baremos de riesgo accidentógeno, patrón de conducta tipo A, escalas de valores VALANTI y concepto aptitudinal oficial (GH-FO-028) con exactamente la misma estructura que genera el área de psicología.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <form method="GET" action="{{ route('admin.psicologia.reportes.excel') }}" class="flex items-center gap-2">
                    <select name="anio" class="px-3 py-2 rounded-xl bg-white/10 text-white border border-white/20 text-xs font-bold focus:bg-slate-800 focus:outline-none">
                        @php
                            $anioActual = (int) date('Y');
                        @endphp
                        @for($y = $anioActual; $y >= 2024; $y--)
                            <option value="{{ $y }}" class="text-slate-900">{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-heading font-extrabold text-xs transition shadow-xs flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-download text-xs"></i>
                        <span>Descargar Excel</span>
                    </button>
                </form>

                <a href="{{ route('admin.psicologia.reportes') }}"
                   class="px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white font-heading font-bold text-xs transition border border-white/20 text-center flex items-center justify-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-eye text-xs"></i>
                    <span>Ver y Filtrar en Línea</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($tipos as $clave => $info)
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-6 flex flex-col justify-between gap-5 hover:shadow-md transition-all">
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="w-12 h-12 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-xl shrink-0 shadow-2xs">
                            <i class="fa-solid {{ $info['icono'] }}"></i>
                        </span>
                        <div>
                            <span class="text-[10px] font-bold text-brand-blue uppercase tracking-wider">Reporte Oficial</span>
                            <h3 class="font-heading font-extrabold text-base text-brand-dark leading-tight">{{ $info['titulo'] }}</h3>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $info['desc'] }}</p>

                    <!-- FILTROS ESPECÍFICOS DEL REPORTE -->
                    <form id="form-reporte-{{ $clave }}" method="GET" action="{{ route('admin.reportes.descargar', ['tipo' => $clave, 'formato' => 'csv']) }}" class="space-y-3 pt-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @if (in_array($clave, ['progreso', 'resultados', 'certificados', 'usuarios']))
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filtrar por Sede</label>
                                    <select name="sede_id" onchange="sincronizarAreasReporte('{{ $clave }}')" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                        <option value="">Todas las sedes</option>
                                        @foreach ($sedes as $s)
                                            <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filtrar por Área</label>
                                    <select name="area_id" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                        <option value="">Todas las áreas</option>
                                        @foreach ($areas as $a)
                                            <option value="{{ $a->id }}" data-sede-id="{{ $a->sede_id }}">{{ $a->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if (in_array($clave, ['progreso', 'resultados', 'certificados']))
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Capacitación</label>
                                    <select name="capacitacion_id" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                        <option value="">Todas las capacitaciones</option>
                                        @foreach ($capacitaciones as $c)
                                            <option value="{{ $c->id }}">{{ $c->titulo }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if (in_array($clave, ['resultados', 'certificados']))
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Desde</label>
                                    <input type="date" name="desde" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Hasta</label>
                                    <input type="date" name="hasta" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- BOTONES DE DESCARGA (CSV y PDF) -->
                <div class="flex items-center gap-2 pt-3 border-t border-line/60">
                    <button type="button" onclick="descargarReporte('{{ $clave }}', 'csv')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-xs transition-colors cursor-pointer shadow-xs">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Excel / CSV</span>
                    </button>
                    <button type="button" onclick="descargarReporte('{{ $clave }}', 'pdf')"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-brand-dark text-white font-heading font-bold text-xs transition-colors cursor-pointer shadow-xs">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Documento PDF</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function sincronizarAreasReporte(tipo) {
        const form = document.getElementById('form-reporte-' + tipo);
        if (!form) return;
        const selectSede = form.querySelector('select[name="sede_id"]');
        const selectArea = form.querySelector('select[name="area_id"]');
        if (!selectSede || !selectArea) return;

        const sedeId = selectSede.value;
        const opciones = selectArea.querySelectorAll('option[data-sede-id]');
        let opcionValida = false;

        opciones.forEach(opt => {
            const optSedeId = opt.getAttribute('data-sede-id');
            const coincide = !sedeId || String(optSedeId) === String(sedeId);
            opt.hidden = !coincide;
            opt.disabled = !coincide;
            if (coincide && opt.value === selectArea.value) {
                opcionValida = true;
            }
        });

        if (selectArea.value && !opcionValida) {
            selectArea.value = '';
        }
    }

    function descargarReporte(tipo, formato) {
        const form = document.getElementById('form-reporte-' + tipo);
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `/admin/reportes/${tipo}/${formato}` + (params ? '?' + params : '');
        window.location.href = url;
    }
</script>
@endsection
