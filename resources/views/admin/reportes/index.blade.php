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
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filtrar por Área</label>
                                    <select name="area_id" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                        <option value="">Todas las áreas</option>
                                        @foreach ($areas as $a)
                                            <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if (in_array($clave, ['progreso', 'resultados', 'certificados']))
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Capacitación</label>
                                    <select name="capacitacion_id" class="w-full px-2.5 py-1.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark focus:bg-white focus:outline-none focus:border-brand-blue">
                                        <option value="">Todas</option>
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
    function descargarReporte(tipo, formato) {
        const form = document.getElementById('form-reporte-' + tipo);
        const params = new URLSearchParams(new FormData(form)).toString();
        const url = `/admin/reportes/${tipo}/${formato}` + (params ? '?' + params : '');
        window.location.href = url;
    }
</script>
@endsection
