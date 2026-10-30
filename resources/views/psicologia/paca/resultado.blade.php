@extends('layouts.psicologia')

@section('title', 'Resultados PAC-A · ' . $aplicacion->candidato->nombre_completo)
@section('page_title', 'Resultados PAC-A')
@section('page_subtitle', $aplicacion->candidato->nombre_completo)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto pb-16">

    <!-- Encabezado con Botones de Acción -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-circle-check"></i> Prueba Calificada
                </span>
                <span class="text-xs text-slate-400">Sesión #{{ $aplicacion->id }}</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-1">
                Resultados de Evaluación PAC-A
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Evaluado: <strong>{{ $aplicacion->candidato->nombre_completo }}</strong> (CC {{ $aplicacion->candidato->numero_identificacion }})
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 print:hidden flex-wrap">
            @if($aplicacion->consentimiento)
                <button type="button"
                   onclick="abrirModalPdf('{{ route('psicologia.consentimientos.pdf', [$aplicacion->consentimiento, 'inline' => 1]) }}', '{{ route('psicologia.consentimientos.pdf', $aplicacion->consentimiento) }}', 'Consentimiento - {{ addslashes($aplicacion->candidato->nombre_completo) }}')"
                   title="Previsualizar y Descargar Consentimiento (GH-FO-025)"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3.5 py-2 text-xs font-bold text-rose-700 transition-colors shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-xs text-rose-600"></i>
                    Ver Consentimiento
                </button>
            @endif
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition-colors shadow-2xs">
                <i class="fa-solid fa-print text-xs"></i>
                Imprimir
            </button>
            <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-2xs transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Volver al Historial
            </a>
        </div>
    </div>

    <!-- Tarjeta de Ficha Técnica de la Aplicación -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-2xs">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-id-card-clip text-brand-blue"></i> Datos de la Aplicación y Custodia
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Fecha de Aplicación:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $aplicacion->fecha_aplicacion->format('d/m/Y') }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Edad del Evaluado:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $aplicacion->edad_evaluado }} años</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Profesional Responsable:</span>
                <span class="font-bold text-slate-800 truncate block">{{ $aplicacion->responsable?->nombre_completo ?? 'Psicología PNY' }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Consentimiento Vinculado:</span>
                <span class="font-bold text-emerald-700">GH-FO-025 Ver 01 (Firmado)</span>
            </div>
        </div>
    </div>

    <!-- ALERTA SI HAY AMBIGÜEDAD DOCUMENTAL -->
    @if($resultado->tieneAmbiguedad())
        <div class="rounded-2xl border border-amber-300 bg-amber-50/80 p-5 shadow-2xs text-xs text-amber-900 space-y-2">
            <div class="flex items-center gap-2 font-bold text-sm text-amber-800">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base"></i>
                Puntaje en Rango con Discrepancia Documental / Validación Profesional Pendiente
            </div>
            <p class="leading-relaxed">
                El sistema calculó los puntajes numéricos exactos conforme a las fórmulas oficiales. Sin embargo, uno o ambos indicadores cayeron en un rango donde las condiciones de la fórmula de Excel y la tabla de referencia de la hoja INFORMACION presentan discrepancias o superposición (por ejemplo, puntaje 8.5 en tendencia accidentógena o 18.0 - 18.5 en patrón tipo A).
            </p>
            @if($resultado->observaciones_profesional)
                <div class="p-2.5 rounded-lg bg-white/70 border border-amber-200 font-mono text-[11px] text-amber-800">
                    {{ $resultado->observaciones_profesional }}
                </div>
            @endif
            <p class="font-semibold text-amber-800">
                Se conservan los puntajes calculados y se requiere confirmación del criterio por la persona profesional responsable.
            </p>
        </div>
    @endif

    <!-- LAS DOS TARJETAS PRINCIPALES DE INDICADORES -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- INDICADOR 1: TENDENCIA ACCIDENTÓGENA -->
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Indicador I</span>
                    <span class="text-[11px] font-bold text-slate-400">12 Preguntas Evaluadas</span>
                </div>

                <div class="mt-4 flex items-baseline justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Tendencia Accidentógena</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Susceptibilidad y conductas de riesgo laboral</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $resultado->puntaje_tendencia_accidentogena }}</span>
                        <span class="text-xs text-slate-400 font-medium">/ 24 pts máx</span>
                    </div>
                </div>

                <!-- Caja de Interpretación -->
                <div class="mt-5 p-4 rounded-xl border {{ $resultado->estado_interpretacion_accidentogena === 'AMBIGUA_VALIDACION_PENDIENTE' ? 'border-amber-200 bg-amber-50/50' : 'border-slate-200 bg-slate-50' }}">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">
                        Interpretación Institucional:
                    </span>
                    <p class="text-xs font-bold text-slate-800 leading-relaxed">
                        "{{ $resultado->interpretacion_accidentogena }}"
                    </p>
                </div>

                <!-- Rangos de Referencia Documentados -->
                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Puntaje &ge; 11:</span>
                        <span class="font-medium text-slate-700">Presencia de rasgos</span>
                    </div>
                    <div class="flex justify-between">
                        <span>De 9 a 10.5:</span>
                        <span class="font-medium text-slate-700">Sugerir indagar / descartar</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Puntaje &le; 8:</span>
                        <span class="font-medium text-slate-700">No se evidencian rasgos</span>
                    </div>
                </div>
            </div>

            <!-- Desglose de preguntas desplegable -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <details class="text-xs">
                    <summary class="font-bold text-brand-blue cursor-pointer hover:underline flex items-center justify-between">
                        <span>Ver desglose de las 12 preguntas que componen este indicador</span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </summary>
                    <div class="mt-3 space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($preguntasAccidentogena as $num)
                            @php
                                $r = $respuestasPorId[$num] ?? null;
                            @endphp
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200/60 flex items-center justify-between gap-2 text-[11px]">
                                <span class="font-medium text-slate-700">
                                    <strong>#{{ $num }}:</strong> {{ $afirmaciones[$num] }}
                                </span>
                                <span class="shrink-0 font-bold px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-900">
                                    {{ $r ? $r->textoOpcion() : '—' }} ({{ $r ? $r->puntaje : 0 }} pts)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>

        <!-- INDICADOR 2: PATRÓN DE COMPORTAMIENTO TIPO A -->
        <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Indicador II</span>
                    <span class="text-[11px] font-bold text-slate-400">20 Preguntas Evaluadas</span>
                </div>

                <div class="mt-4 flex items-baseline justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Patrón de Comportamiento Tipo A</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Urgencia temporal, competitividad y hostilidad</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $resultado->puntaje_patron_tipo_a }}</span>
                        <span class="text-xs text-slate-400 font-medium">/ 40 pts máx</span>
                    </div>
                </div>

                <!-- Caja de Interpretación -->
                <div class="mt-5 p-4 rounded-xl border {{ $resultado->estado_interpretacion_tipo_a === 'AMBIGUA_VALIDACION_PENDIENTE' ? 'border-amber-200 bg-amber-50/50' : 'border-slate-200 bg-slate-50' }}">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">
                        Interpretación Institucional:
                    </span>
                    <p class="text-xs font-bold text-slate-800 leading-relaxed">
                        "{{ $resultado->interpretacion_tipo_a }}"
                    </p>
                </div>

                <!-- Rangos de Referencia Documentados -->
                <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Puntaje &ge; 20:</span>
                        <span class="font-medium text-slate-700">Presencia de rasgos</span>
                    </div>
                    <div class="flex justify-between">
                        <span>De 18 a 19.5:</span>
                        <span class="font-medium text-slate-700">Sugerir indagar / descartar</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Puntaje &lt; 18:</span>
                        <span class="font-medium text-slate-700">No se evidencian rasgos</span>
                    </div>
                </div>
            </div>

            <!-- Desglose de preguntas desplegable -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <details class="text-xs">
                    <summary class="font-bold text-brand-blue cursor-pointer hover:underline flex items-center justify-between">
                        <span>Ver desglose de las 20 preguntas que componen este indicador</span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </summary>
                    <div class="mt-3 space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($preguntasTipoA as $num)
                            @php
                                $r = $respuestasPorId[$num] ?? null;
                            @endphp
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-200/60 flex items-center justify-between gap-2 text-[11px]">
                                <span class="font-medium text-slate-700">
                                    <strong>#{{ $num }}:</strong> {{ $afirmaciones[$num] }}
                                </span>
                                <span class="shrink-0 font-bold px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-900">
                                    {{ $r ? $r->textoOpcion() : '—' }} ({{ $r ? $r->puntaje : 0 }} pts)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </details>
            </div>
        </div>

    </div>

    <!-- NOTA INSTITUCIONAL DE CONFIDENCIALIDAD Y RESPONSABILIDAD ÉTICA -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs text-xs text-slate-600 space-y-2">
        <h3 class="font-bold text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-scale-balanced text-brand-blue"></i>
            Nota de Responsabilidad Profesional y Confidencialidad
        </h3>
        <p class="leading-relaxed">
            Los resultados psicotécnicos presentados corresponden exclusivamente a los indicadores autorizados en el instrumento institucional PAC-A de C.I. Piscícola New York S.A. No constituyen diagnósticos clínicos ni determinan de forma automática la decisión de contratación. La interpretación final y el informe consolidado deben ser emitidos y firmados por el profesional de psicología asignado al proceso de selección.
        </p>
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between text-[11px] text-slate-400">
            <span>Registro generado electrónicamente · C.I. Piscícola New York S.A.</span>
            <span>Fecha de finalización: {{ $aplicacion->hora_finalizacion ? $aplicacion->hora_finalizacion->format('d/m/Y H:i') : $aplicacion->updated_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

</div>
@endsection
