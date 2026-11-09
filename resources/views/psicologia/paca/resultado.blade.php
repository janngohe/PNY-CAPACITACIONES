@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.psicologia')

@section('title', 'Resultados PAC-A · ' . $aplicacion->nombre_completo)
@section('page_title', 'Resultados PAC-A')
@section('page_subtitle', $aplicacion->nombre_completo)

@section('content')
<div class="space-y-8 w-full max-w-7xl mx-auto pb-16">

    @php
        $urlPdfConsentimientoInline = (request()->routeIs('admin.*') ? route('admin.psicologia.consentimientos.pdf', $aplicacion) : route('psicologia.consentimientos.pdf', $aplicacion)) . '?inline=1';
        $urlPdfConsentimientoDescarga = request()->routeIs('admin.*') ? route('admin.psicologia.consentimientos.pdf', $aplicacion) : route('psicologia.consentimientos.pdf', $aplicacion);

        $urlPdfPacaInline = (request()->routeIs('admin.*') ? route('admin.psicologia.paca.pdf', $aplicacion) : route('psicologia.paca.pdf', $aplicacion)) . '?inline=1';
        $urlPdfPacaDescarga = request()->routeIs('admin.*') ? route('admin.psicologia.paca.pdf', $aplicacion) : route('psicologia.paca.pdf', $aplicacion);
    @endphp

    <!-- Encabezado con Botones de Acción (En móvil: opciones en columna; en PC: diseño horizontal ajustado) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="space-y-2 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                        <i class="fa-solid fa-circle-check"></i> Prueba Calificada
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Sesión #{{ $aplicacion->id }} ({{ $aplicacion->codigo_sesion }})</span>
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-snug">
                    Resultados de Evaluación PAC-A
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 font-medium flex items-center gap-1.5 flex-wrap pt-0.5">
                    <span class="text-slate-400 font-normal">Evaluado:</span>
                    <strong class="text-slate-900 font-bold">{{ $aplicacion->nombre_completo }}</strong>
                    <span class="text-slate-400 font-normal">&bull;</span>
                    <span class="text-slate-500 font-mono text-xs">C.C. {{ $aplicacion->identificacion }}</span>
                </p>
            </div>

            <!-- Botones de Acción: En móvil en columna (flex-col w-full), en PC en fila adaptada (sm:flex-row sm:w-auto sm:flex-wrap) -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:flex-wrap gap-2 w-full lg:w-auto print:hidden">
                <!-- Exportar PDF PAC-A -->
                <a href="{{ $urlPdfPacaDescarga }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 text-xs font-bold text-white transition-all shadow-2xs">
                    <i class="fa-solid fa-file-arrow-down text-xs"></i>
                    <span>Exportar</span>
                </a>

                <!-- Previsualizar PDF PAC-A -->
                <button type="button"
                   onclick="abrirModalPdf('{{ $urlPdfPacaInline }}', '{{ $urlPdfPacaDescarga }}', '{{ addslashes($aplicacion->nombre_completo) }}', 'Informe PAC-A · {{ addslashes($aplicacion->nombre_completo) }}')"
                   title="Previsualizar Informe de Resultados PAC-A"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 text-xs font-bold text-indigo-700 transition-colors shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-eye text-xs text-indigo-600"></i>
                    <span>Ver</span>
                </button>

                @if($aplicacion->valantiFinalizada())
                    <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.valanti.resultado', $aplicacion) : route('psicologia.valanti.resultado', $aplicacion) }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl border border-sky-200 bg-sky-50 hover:bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-700 transition-colors shadow-2xs">
                        <i class="fa-solid fa-scale-balanced text-xs"></i>
                        <span>Prueba 1 (VALANTI)</span>
                    </a>
                @endif

                <button type="button"
                   onclick="abrirModalPdf('{{ $urlPdfConsentimientoInline }}', '{{ $urlPdfConsentimientoDescarga }}', '{{ addslashes($aplicacion->nombre_completo) }}', 'Consentimiento Informado (GH-FO-025) · {{ addslashes($aplicacion->nombre_completo) }}')"
                   title="Previsualizar y Descargar Consentimiento (GH-FO-025)"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 text-xs font-bold text-rose-700 transition-colors shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-xs text-rose-600"></i>
                    <span>Consentimiento</span>
                </button>

                <a href="{{ request()->routeIs('admin.*') ? route('admin.psicologia.historial') : route('psicologia.pruebas.historial') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 rounded-xl bg-brand-blue hover:bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Volver al Historial</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-300 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-xs font-bold text-rose-800 flex items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-950 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Tarjeta de Ficha Técnica de la Aplicación -->
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-2xs">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-id-card-clip text-brand-blue"></i> Datos de la Aplicación y Custodia
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Fecha de Aplicación:</span>
                <span class="font-bold text-slate-800 text-sm">{{ ($aplicacion->fecha_hora_aceptacion ?? $aplicacion->created_at)->format('d/m/Y') }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Edad del Evaluado:</span>
                <span class="font-bold text-slate-800 text-sm">{{ $aplicacion->edad }} años</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Cargo al que aspira:</span>
                <span class="font-bold text-slate-800 truncate block">{{ $aplicacion->cargo_aspira }}</span>
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
                        <span>Puntaje &le; 8.5:</span>
                        <span class="font-medium text-slate-700">No se evidencian rasgos</span>
                    </div>
                </div>
            </div>

            <!-- Botón de acción rápida hacia el desglose -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <button type="button"
                        onclick="seleccionarFiltroDesglose('accidentogena')"
                        class="w-full py-2.5 px-3.5 rounded-xl border border-rose-200 bg-rose-50/70 hover:bg-rose-100 text-rose-800 text-xs font-bold transition-all flex items-center justify-between cursor-pointer shadow-2xs group">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-rose-600"></i>
                        <span>Ver desglose de las 12 preguntas</span>
                    </span>
                    <i class="fa-solid fa-arrow-down text-rose-500 text-xs transition-transform group-hover:translate-y-0.5"></i>
                </button>
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

            <!-- Botón de acción rápida hacia el desglose -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <button type="button"
                        onclick="seleccionarFiltroDesglose('tipo_a')"
                        class="w-full py-2.5 px-3.5 rounded-xl border border-indigo-200 bg-indigo-50/70 hover:bg-indigo-100 text-indigo-800 text-xs font-bold transition-all flex items-center justify-between cursor-pointer shadow-2xs group">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-indigo-600"></i>
                        <span>Ver desglose de las 20 preguntas</span>
                    </span>
                    <i class="fa-solid fa-arrow-down text-indigo-500 text-xs transition-transform group-hover:translate-y-0.5"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- TARJETA RESUMEN DE COBERTURA TOTAL DE LA BATERÍA (40 PREGUNTAS) -->
    <div class="rounded-2xl bg-white border border-slate-200/90 p-4 sm:p-5 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-base shrink-0 shadow-xs border border-brand-blue/20">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-extrabold text-sm text-slate-900">Totalidad del Cuestionario PAC-A (40 de 40 Preguntas)</h3>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">100% Completas</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    12 ítems componen Tendencia Accidentógena, 20 ítems Patrón Tipo A y 8 ítems complementan el perfil de consistencia y control.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            <button type="button"
                    onclick="seleccionarFiltroDesglose('accidentogena')"
                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span>12 Accidentógena</span>
            </button>
            <button type="button"
                    onclick="seleccionarFiltroDesglose('tipo_a')"
                    class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span>20 Tipo A</span>
            </button>
            <button type="button"
                    onclick="seleccionarFiltroDesglose('control')"
                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <span>8 Control</span>
            </button>
        </div>
    </div>

    <!-- SECCIÓN MAESTRA: DESGLOSE COMPLETO DE LAS 40 PREGUNTAS (UNA POR UNA O LISTA COMPLETA) -->
    <form id="formAjustarPaca" action="{{ request()->routeIs('admin.*') ? route('admin.psicologia.paca.actualizar_respuestas', $aplicacion) : route('psicologia.paca.actualizar_respuestas', $aplicacion) }}" method="POST">
        @csrf
        <div id="seccionDesglosePaca" class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-7 shadow-2xs space-y-6 scroll-mt-20">
            
            <!-- Cabecera de la sección -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-md bg-brand-light text-brand-blue font-bold text-[11px] tracking-wide uppercase border border-brand-blue/20">
                            <i class="fa-solid fa-list-check mr-1"></i> Auditoría Detallada
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 font-bold text-[11px] tracking-wide border border-amber-200 flex items-center gap-1">
                            <i class="fa-solid fa-user-pen text-amber-600"></i> Modo Ajuste Profesional
                        </span>
                        <span class="text-xs text-slate-400 font-semibold hidden sm:inline">40 afirmaciones</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        Desglose Completo de las 40 Preguntas PAC-A
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Revise cada afirmación y la respuesta del candidato. Como profesional de Psicología, puede hacer clic en cualquier opción para ajustar la respuesta y recalcular el resultado.
                    </p>
                </div>

            <!-- Selector de Modos de Vista (Visor 1 por 1 vs Lista Completa) -->
            <div class="flex items-center gap-1.5 shrink-0 self-start md:self-auto bg-slate-100 p-1.5 rounded-2xl border border-slate-200/80">
                <button type="button"
                        id="btnModoVisor"
                        onclick="cambiarModoVista('visor')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-900 shadow-xs cursor-pointer">
                    <i class="fa-solid fa-sliders text-brand-blue"></i>
                    <span>Visor 1 por 1</span>
                </button>
                <button type="button"
                        id="btnModoLista"
                        onclick="cambiarModoVista('lista')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer">
                    <i class="fa-solid fa-table-list text-slate-400"></i>
                    <span>Ver Todas (Lista)</span>
                </button>
            </div>
        </div>

        <!-- Barra de Filtros por Categoría y Buscador en Tiempo Real -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Pestañas de Filtro -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-thin">
                <button type="button"
                        id="tab-filtro-todas"
                        onclick="seleccionarFiltroDesglose('todas')"
                        class="tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-brand-blue text-white shadow-2xs border border-brand-blue">
                    Todas (40)
                </button>
                <button type="button"
                        id="tab-filtro-accidentogena"
                        onclick="seleccionarFiltroDesglose('accidentogena')"
                        class="tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-1"></span>
                    Tendencia Accidentógena (12)
                </button>
                <button type="button"
                        id="tab-filtro-tipo_a"
                        onclick="seleccionarFiltroDesglose('tipo_a')"
                        class="tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-1"></span>
                    Patrón Tipo A (20)
                </button>
                <button type="button"
                        id="tab-filtro-control"
                        onclick="seleccionarFiltroDesglose('control')"
                        class="tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200">
                    <span class="inline-block w-2 h-2 rounded-full bg-slate-400 mr-1"></span>
                    Complementarias / Control (8)
                </button>
            </div>

            <!-- Buscador en tiempo real -->
            <div class="relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       id="inputBuscarDesglose"
                       oninput="filtrarTextoDesglose(this.value)"
                       placeholder="Buscar afirmación o palabra..."
                       class="w-full pl-8 pr-8 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all">
                <button type="button"
                        id="btnLimpiarBusqueda"
                        onclick="limpiarBusquedaDesglose()"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden text-xs cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- CONTENEDOR 1: MODO VISOR PASO A PASO (1 a 1) -->
        <div id="contenedorModoVisor" class="space-y-5">
            <!-- Píldoras de Preguntas (1 a 40) en cuadrícula responsiva -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold px-0.5">
                    <span>Haga clic en cualquier píldora para inspeccionar su respuesta:</span>
                    <span id="labelVisorCategoriaActiva" class="font-bold text-brand-blue">Mostrando: Todas las 40 Preguntas</span>
                </div>

                <div class="grid grid-cols-8 sm:grid-cols-10 md:grid-cols-20 gap-1.5">
                    @for($num = 1; $num <= 40; $num++)
                        @php
                            $cat = \App\Constants\PacaPreguntas::categoriaDePregunta($num);
                            $r = $respuestasPorId[$num] ?? null;
                            $opc = $r ? $r->opcion_seleccionada : '-';
                        @endphp
                        <button type="button"
                                id="btn-pill-paca-{{ $num }}"
                                data-categoria="{{ $cat['clave'] }}"
                                onclick="irAPreguntaPaca({{ $num }})"
                                title="#{{ $num }}: {{ $cat['nombre'] }} · Marcó: {{ $opc }}"
                                class="pill-pregunta-paca h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex flex-col items-center justify-center relative {{ $num === 1 ? 'bg-brand-blue text-white border-brand-blue shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                            <span>{{ $num }}</span>
                            <span class="w-1.5 h-1.5 rounded-full {{ $cat['dot_color'] }} absolute bottom-1 {{ $num === 1 ? 'bg-white' : '' }}"></span>
                        </button>
                    @endfor
                </div>

                <!-- Leyenda de colores de las píldoras -->
                <div class="flex items-center gap-4 pt-1 text-[11px] text-slate-400 justify-end">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tendencia Accidentógena
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Patrón Tipo A
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Control / Complementaria
                    </span>
                </div>
            </div>

            <!-- Visor de la Pregunta Activa (Slides 1..40) -->
            <div class="relative">
                @for($num = 1; $num <= 40; $num++)
                    @php
                        $cat = \App\Constants\PacaPreguntas::categoriaDePregunta($num);
                        $r = $respuestasPorId[$num] ?? null;
                        $opcionMarcada = $r ? $r->opcion_seleccionada : null;
                        $ptsObtenidos = $r ? (float)$r->puntaje : 0.0;
                        $textoAfirmacion = $afirmaciones[$num] ?? '';
                    @endphp

                    <div id="slide-paca-{{ $num }}"
                         data-categoria="{{ $cat['clave'] }}"
                         data-texto="{{ mb_strtolower($textoAfirmacion . ' ' . $num . ' ' . ($r ? $r->textoOpcion() : '')) }}"
                         class="slide-paca {{ $num === 1 ? '' : 'hidden' }} rounded-2xl bg-slate-50/70 border border-slate-200/90 p-5 sm:p-7 space-y-6 transition-all">

                        <!-- Cabecera de la Pregunta -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200/80">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="px-3 py-1 rounded-xl bg-brand-dark text-white font-mono text-xs sm:text-sm font-black shadow-2xs">
                                    Pregunta #{{ $num }} de 40
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $cat['badge_class'] }}">
                                    <i class="{{ $cat['icono'] }}"></i>
                                    <span>{{ $cat['indicador'] }}: {{ $cat['nombre'] }}</span>
                                </span>
                            </div>

                            <div class="flex items-center gap-2 self-start sm:self-auto">
                                <span class="text-xs text-slate-500 font-medium">Puntos obtenidos:</span>
                                <span class="px-3 py-1 rounded-xl bg-white border border-slate-200 font-mono text-xs font-extrabold text-slate-900 shadow-2xs">
                                    @if($cat['puntua'])
                                        {{ number_format($ptsObtenidos, 1) }} / {{ number_format($cat['max_pts'], 1) }} pts
                                    @else
                                        0.0 pts (Ítem de control)
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Afirmación en Texto Destacado -->
                        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-2xs space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Afirmación evaluada en el cuestionario:</span>
                            <blockquote class="text-base sm:text-lg font-bold text-slate-800 leading-relaxed italic">
                                “{{ $textoAfirmacion }}”
                            </blockquote>
                        </div>

                        <!-- Las 4 Opciones de Respuesta con la marcada destacada (Modo interactivo de ajuste) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-600 flex-wrap gap-1">
                                <span>Opciones de respuesta institucional:</span>
                                <span class="text-[11px] text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200 font-semibold">
                                    <i class="fa-solid fa-hand-pointer text-[10px] mr-1"></i>Haga clic para cambiar respuesta
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                                @foreach($opciones as $cod => $infoOpc)
                                    @php
                                        $esSeleccionada = ($opcionMarcada === $cod);
                                        $ptsOpcion = $cat['puntua'] ? $infoOpc['puntaje'] : 0.0;
                                    @endphp
                                    <label id="card-opcion-paca-{{ $num }}-{{ $cod }}"
                                           onclick="seleccionarOpcionPaca({{ $num }}, '{{ $cod }}')"
                                           class="cursor-pointer relative block rounded-2xl p-3.5 sm:p-4 border transition-all card-opc-paca-{{ $num }} min-w-0 {{ $esSeleccionada ? 'bg-blue-50/90 border-2 border-brand-blue shadow-xs ring-4 ring-blue-50/50' : 'bg-white border-slate-200 opacity-60 hover:opacity-100 hover:border-slate-300' }}">
                                        
                                        <input type="radio"
                                               name="respuestas[{{ $num }}]"
                                               value="{{ $cod }}"
                                               id="radio-paca-{{ $num }}-{{ $cod }}"
                                               data-pregunta="{{ $num }}"
                                               data-original="{{ $opcionMarcada }}"
                                               {{ $esSeleccionada ? 'checked' : '' }}
                                               class="sr-only">

                                        <span id="badge-marcada-{{ $num }}-{{ $cod }}"
                                              class="absolute -top-2.5 right-2 sm:right-3 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-2xs {{ $esSeleccionada ? 'bg-brand-blue text-white' : 'hidden' }}">
                                            <i class="fa-solid fa-check text-[8px]"></i> Marcada
                                        </span>

                                        <div class="flex items-center justify-between gap-2 min-w-0">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span id="cod-box-{{ $num }}-{{ $cod }}"
                                                      class="w-7 h-7 rounded-lg font-mono font-black text-xs shrink-0 flex items-center justify-center {{ $esSeleccionada ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $cod }}
                                                </span>
                                                <span id="cod-nombre-{{ $num }}-{{ $cod }}"
                                                      class="font-bold text-xs sm:text-sm truncate {{ $esSeleccionada ? 'text-slate-900' : 'text-slate-600' }}"
                                                      title="{{ $infoOpc['nombre'] }}">
                                                    {{ $infoOpc['nombre'] }}
                                                </span>
                                            </div>
                                            <span id="cod-pts-{{ $num }}-{{ $cod }}"
                                                  class="font-mono text-xs font-bold shrink-0 {{ $esSeleccionada ? 'text-brand-blue' : 'text-slate-400' }}">
                                                {{ number_format($ptsOpcion, 1) }} pts
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Explicación de aportación al factor -->
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 text-xs text-slate-600 flex items-start gap-2.5">
                            <i class="fa-solid fa-circle-info text-slate-400 mt-0.5 shrink-0"></i>
                            <div>
                                @if($cat['clave'] === 'accidentogena')
                                    <span>Esta afirmación aporta al <strong>Indicador I: Tendencia Accidentógena</strong>. Una respuesta de mayor frecuencia refleja conductas o actitudes de vulnerabilidad ante riesgos y normas en el trabajo.</span>
                                @elseif($cat['clave'] === 'tipo_a')
                                    <span>Esta afirmación aporta al <strong>Indicador II: Patrón de Comportamiento Tipo A</strong>. Una respuesta de mayor frecuencia indica premura temporal, competitividad intensa y autoexigencia constante.</span>
                                @else
                                    <span>Esta afirmación actúa como <strong>Ítem de Control / Complementario</strong>. Permite validar la consistencia global del evaluado y no suma puntos de riesgo a los indicadores de alerta.</span>
                                @endif
                            </div>
                        </div>

                    </div>
                @endfor
            </div>

            <!-- Botones de Navegación Anterior / Siguiente -->
            <div class="flex items-center justify-between pt-2">
                <button type="button"
                        id="btnAnteriorPaca"
                        onclick="cambiarPreguntaPaca(-1)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                    <span>Pregunta Anterior</span>
                </button>

                <div class="text-xs text-slate-500 font-semibold text-center">
                    <span id="contadorPacaVisor" class="font-mono font-extrabold text-sm text-slate-800">1</span> de 40 preguntas
                    <span class="hidden sm:inline text-slate-400 font-normal"> · (Use las flechas &larr; &rarr; del teclado)</span>
                </div>

                <button type="button"
                        id="btnSiguientePaca"
                        onclick="cambiarPreguntaPaca(1)"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                    <span>Siguiente Pregunta</span>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- CONTENEDOR 2: MODO LISTA COMPLETA (40 Preguntas en Tarjetas Ejecutivas) -->
        <div id="contenedorModoLista" class="hidden space-y-4">
            <div class="flex items-center justify-between text-xs text-slate-500 font-medium pb-1">
                <span id="labelContadorLista">Mostrando las 40 preguntas del cuestionario:</span>
                <span class="text-[11px] text-slate-400">Orden del 1 al 40</span>
            </div>

            <div id="gridPreguntasLista" class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @for($num = 1; $num <= 40; $num++)
                    @php
                        $cat = \App\Constants\PacaPreguntas::categoriaDePregunta($num);
                        $r = $respuestasPorId[$num] ?? null;
                        $opcionMarcada = $r ? $r->opcion_seleccionada : '-';
                        $ptsObtenidos = $r ? (float)$r->puntaje : 0.0;
                        $textoAfirmacion = $afirmaciones[$num] ?? '';
                        $textoOpcion = $r ? $r->textoOpcion() : 'Sin respuesta';
                    @endphp
                    <div class="item-tarjeta-paca rounded-2xl bg-white border border-slate-200/90 p-4 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between gap-3"
                         data-numero="{{ $num }}"
                         data-categoria="{{ $cat['clave'] }}"
                         data-texto="{{ mb_strtolower($textoAfirmacion . ' ' . $num . ' ' . $textoOpcion . ' ' . $opcionMarcada) }}">

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="w-6 h-6 rounded-lg bg-brand-dark text-white font-mono text-[11px] font-black flex items-center justify-center shrink-0">
                                        {{ $num }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $cat['badge_class'] }}">
                                        {{ $cat['nombre'] }}
                                    </span>
                                </div>
                                <span class="font-mono text-xs font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200 shrink-0">
                                    {{ $cat['puntua'] ? number_format($ptsObtenidos, 1) . ' pts' : 'Control' }}
                                </span>
                            </div>

                            <p class="text-xs font-medium text-slate-800 leading-snug">
                                {{ $textoAfirmacion }}
                            </p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2 text-[11px] flex-wrap">
                            <span class="text-slate-500 font-medium">Respuesta:</span>
                            <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
                                @foreach($opciones as $cod => $infoOpc)
                                    <button type="button"
                                            id="btn-lista-paca-{{ $num }}-{{ $cod }}"
                                            onclick="seleccionarOpcionPaca({{ $num }}, '{{ $cod }}')"
                                            class="btn-opc-lista-{{ $num }} px-2.5 py-1 rounded-lg text-xs font-mono font-black transition-all cursor-pointer {{ $opcionMarcada === $cod ? 'bg-brand-blue text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}"
                                            title="{{ $infoOpc['nombre'] }} ({{ $cod }})">
                                        {{ $cod }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Alerta si ninguna coincide con la búsqueda -->
            <div id="alertaSinResultadosBusqueda" class="hidden text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-300 p-6 space-y-2">
                <i class="fa-solid fa-magnifying-glass text-2xl text-slate-400"></i>
                <p class="text-xs font-bold text-slate-700">No se encontraron preguntas que coincidan con la búsqueda o filtro.</p>
                <button type="button" onclick="limpiarBusquedaDesglose()" class="text-xs text-brand-blue hover:underline font-semibold cursor-pointer">
                    Restablecer filtros y ver las 40 preguntas
                </button>
            </div>
        </div>
        </div>

        <!-- Barra Flotante de Confirmación de Ajustes en PAC-A -->
        <div id="barraFlotantePaca" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 hidden max-w-xl w-[92%] bg-slate-900/95 backdrop-blur-md text-white p-3.5 sm:p-4 rounded-2xl shadow-2xl border border-slate-700/80 flex items-center justify-between gap-3 transition-all duration-300">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold shrink-0">
                    <i class="fa-solid fa-pen-nib text-sm"></i>
                </div>
                <div class="min-w-0">
                    <span class="text-xs sm:text-sm font-extrabold text-white block truncate" id="txtConteoModificadasPaca">
                        1 respuesta ajustada
                    </span>
                    <span class="text-[10px] sm:text-[11px] text-slate-400 block truncate">
                        Recalculará los puntajes de riesgo del evaluado
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        onclick="descartarModificacionesPaca()"
                        class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all cursor-pointer">
                    Descartar
                </button>
                <button type="submit"
                        id="btnGuardarPacaAjuste"
                        class="px-3.5 sm:px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-md transition-all cursor-pointer active:scale-95">
                    <i class="fa-solid fa-calculator text-xs"></i>
                    <span>Guardar y Recalcular</span>
                </button>
            </div>
        </div>
    </form>

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
            <span>Fecha de finalización: {{ ($resultado->finalizada_at ?? $resultado->updated_at)->format('d/m/Y H:i') }}</span>
        </div>
    </div>

</div>

<!-- MODAL DE PREVISUALIZACIÓN DE PDF INTERACTIVO -->
<div id="modalVisorPdf" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-4xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="min-w-0 pr-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Formato Oficial GH-FO-025</span>
                <h3 id="tituloModalPdf" class="text-sm font-bold text-slate-900 truncate">Consentimiento Informado</h3>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a id="btnDescargarPdfModal"
                   href="#"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 text-xs font-bold transition-colors shadow-2xs">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span class="hidden sm:inline">Descargar PDF</span>
                </a>

                <a id="btnPestanaModal"
                   href="#"
                   target="_blank"
                   title="Abrir en pestaña completa"
                   class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>

                <button type="button"
                        onclick="cerrarModalPdf()"
                        class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-500 border border-slate-200 text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <div class="relative flex-1 bg-slate-100 overflow-hidden flex flex-col">
            <div id="spinnerCargaPdf" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10 transition-opacity">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-brand-blue mb-3"></i>
                <span class="text-xs font-semibold text-slate-600">Cargando documento PDF institucional...</span>
            </div>

            <iframe id="iframeVisorPdf"
                    src="about:blank"
                    class="w-full h-full border-0"
                    onload="document.getElementById('spinnerCargaPdf').classList.add('hidden')">
            </iframe>
        </div>

    </div>
</div>

<script>
    function abrirModalPdf(urlInline, urlDescarga, nombrePersona, tituloCustom) {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');
        const spinner = document.getElementById('spinnerCargaPdf');
        const titulo = document.getElementById('tituloModalPdf');
        const btnDescargar = document.getElementById('btnDescargarPdfModal');
        const btnPestana = document.getElementById('btnPestanaModal');

        titulo.textContent = tituloCustom || ('Consentimiento Informado · ' + nombrePersona);
        btnDescargar.href = urlDescarga;
        btnPestana.href = urlInline;

        spinner.classList.remove('hidden');
        iframe.src = urlInline;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalPdf() {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');

        modal.classList.add('hidden');
        iframe.src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
    }

    /* ============================================================
     * LÓGICA INTERACTIVA DEL DESGLOSE DE LAS 40 PREGUNTAS (PAC-A)
     * ============================================================ */
    let preguntaPacaActual = 1;
    let filtroPacaActual = 'todas';
    let modoPacaActual = 'visor';
    let queryBusquedaPaca = '';

    function irAPreguntaPaca(num) {
        if (num < 1 || num > 40) return;
        preguntaPacaActual = num;

        // Ocultar todos los slides y mostrar el seleccionado
        document.querySelectorAll('.slide-paca').forEach(el => el.classList.add('hidden'));
        const slide = document.getElementById('slide-paca-' + num);
        if (slide) slide.classList.remove('hidden');

        // Actualizar píldoras interactivas
        document.querySelectorAll('.pill-pregunta-paca').forEach(btn => {
            btn.className = 'pill-pregunta-paca h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex flex-col items-center justify-center relative bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100';
            const dot = btn.querySelector('span:last-child');
            if (dot) dot.classList.remove('bg-white');
        });

        const pillActiva = document.getElementById('btn-pill-paca-' + num);
        if (pillActiva) {
            pillActiva.className = 'pill-pregunta-paca h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex flex-col items-center justify-center relative bg-brand-blue text-white border-brand-blue shadow-xs';
            const dot = pillActiva.querySelector('span:last-child');
            if (dot) dot.classList.add('bg-white');
        }

        // Actualizar contador y estado de botones
        const contador = document.getElementById('contadorPacaVisor');
        if (contador) contador.textContent = num;

        const btnAnt = document.getElementById('btnAnteriorPaca');
        const btnSig = document.getElementById('btnSiguientePaca');
        if (btnAnt) btnAnt.disabled = (num <= 1);
        if (btnSig) btnSig.disabled = (num >= 40);
    }

    function cambiarPreguntaPaca(delta) {
        irAPreguntaPaca(preguntaPacaActual + delta);
    }

    function cambiarModoVista(modo) {
        modoPacaActual = modo;
        const visor = document.getElementById('contenedorModoVisor');
        const lista = document.getElementById('contenedorModoLista');
        const btnVisor = document.getElementById('btnModoVisor');
        const btnLista = document.getElementById('btnModoLista');

        if (modo === 'visor') {
            visor.classList.remove('hidden');
            lista.classList.add('hidden');
            btnVisor.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-900 shadow-xs cursor-pointer';
            btnLista.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer';
        } else {
            visor.classList.add('hidden');
            lista.classList.remove('hidden');
            btnLista.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 bg-white text-slate-900 shadow-xs cursor-pointer';
            btnVisor.className = 'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 text-slate-600 hover:text-slate-900 cursor-pointer';
            aplicarFiltrosLista();
        }
    }

    function seleccionarFiltroDesglose(categoria) {
        filtroPacaActual = categoria;

        // Actualizar pestañas activas
        document.querySelectorAll('.tab-filtro-btn').forEach(btn => {
            btn.className = 'tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200';
        });

        const tabActivo = document.getElementById('tab-filtro-' + categoria);
        if (tabActivo) {
            tabActivo.className = 'tab-filtro-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer bg-brand-blue text-white shadow-2xs border border-brand-blue';
        }

        // Si estamos en visor, saltar a la 1ª pregunta de esa categoría
        if (categoria === 'accidentogena') {
            irAPreguntaPaca(3);
        } else if (categoria === 'tipo_a') {
            irAPreguntaPaca(1);
        } else if (categoria === 'control') {
            irAPreguntaPaca(5);
        }

        // Actualizar indicador de categoría en visor
        const labelVisor = document.getElementById('labelVisorCategoriaActiva');
        if (labelVisor) {
            const titulos = {
                'todas': 'Mostrando: Todas las 40 Preguntas',
                'accidentogena': 'Filtrado: 12 Preguntas de Tendencia Accidentógena',
                'tipo_a': 'Filtrado: 20 Preguntas de Patrón Tipo A',
                'control': 'Filtrado: 8 Preguntas Complementarias / Control'
            };
            labelVisor.textContent = titulos[categoria] || 'Mostrando preguntas';
        }

        // Resaltar u opacar píldoras según la categoría
        document.querySelectorAll('.pill-pregunta-paca').forEach(pill => {
            const cat = pill.getAttribute('data-categoria');
            if (categoria === 'todas' || cat === categoria) {
                pill.style.opacity = '1';
                pill.classList.remove('grayscale');
            } else {
                pill.style.opacity = '0.35';
                pill.classList.add('grayscale');
            }
        });

        aplicarFiltrosLista();

        // Desplazamiento suave hacia la sección de desglose
        const seccion = document.getElementById('seccionDesglosePaca');
        if (seccion) {
            seccion.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function filtrarTextoDesglose(val) {
        queryBusquedaPaca = (val || '').toLowerCase().trim();
        const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
        if (btnLimpiar) {
            btnLimpiar.classList.toggle('hidden', queryBusquedaPaca.length === 0);
        }

        // Si el usuario escribe una búsqueda, conmutar a lista para que vea todas las coincidencias
        if (queryBusquedaPaca.length > 0 && modoPacaActual !== 'lista') {
            cambiarModoVista('lista');
        }

        aplicarFiltrosLista();
    }

    function limpiarBusquedaDesglose() {
        const input = document.getElementById('inputBuscarDesglose');
        if (input) input.value = '';
        queryBusquedaPaca = '';
        const btnLimpiar = document.getElementById('btnLimpiarBusqueda');
        if (btnLimpiar) btnLimpiar.classList.add('hidden');
        seleccionarFiltroDesglose('todas');
    }

    function aplicarFiltrosLista() {
        let visibles = 0;
        const items = document.querySelectorAll('.item-tarjeta-paca');

        items.forEach(card => {
            const cat = card.getAttribute('data-categoria');
            const texto = card.getAttribute('data-texto') || '';
            const coincideCat = (filtroPacaActual === 'todas' || cat === filtroPacaActual);
            const coincideQuery = (queryBusquedaPaca === '' || texto.includes(queryBusquedaPaca));

            if (coincideCat && coincideQuery) {
                card.classList.remove('hidden');
                visibles++;
            } else {
                card.classList.add('hidden');
            }
        });

        const labelContador = document.getElementById('labelContadorLista');
        if (labelContador) {
            labelContador.textContent = `Mostrando ${visibles} de 40 preguntas:`;
        }

        const alertaSin = document.getElementById('alertaSinResultadosBusqueda');
        if (alertaSin) {
            alertaSin.classList.toggle('hidden', visibles > 0);
        }
    }

    // Atajos de teclado para el visor interactivo (Flechas izquierda / derecha)
    document.addEventListener('keydown', function(e) {
        if (modoPacaActual === 'visor' && !['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) {
            if (e.key === 'ArrowRight') cambiarPreguntaPaca(1);
            if (e.key === 'ArrowLeft') cambiarPreguntaPaca(-1);
        }
    });

    // =========================================================================
    // LÓGICA DE AJUSTE PROFESIONAL DE RESPUESTAS PAC-A
    // =========================================================================
    function seleccionarOpcionPaca(numPregunta, nuevoCodigo) {
        // 1. Marcar radio input
        const radio = document.getElementById(`radio-paca-${numPregunta}-${nuevoCodigo}`);
        if (radio) {
            radio.checked = true;
        }

        // 2. Actualizar tarjetas del Visor
        document.querySelectorAll(`.card-opc-paca-${numPregunta}`).forEach(card => {
            card.classList.remove('bg-blue-50/90', 'border-2', 'border-brand-blue', 'shadow-xs', 'ring-4', 'ring-blue-50/50');
            card.classList.add('bg-white', 'border-slate-200', 'opacity-60');
        });

        const cardActiva = document.getElementById(`card-opcion-paca-${numPregunta}-${nuevoCodigo}`);
        if (cardActiva) {
            cardActiva.classList.remove('bg-white', 'border-slate-200', 'opacity-60');
            cardActiva.classList.add('bg-blue-50/90', 'border-2', 'border-brand-blue', 'shadow-xs', 'ring-4', 'ring-blue-50/50');
        }

        // 3. Badges de selección en el Visor
        document.querySelectorAll(`[id^="badge-marcada-${numPregunta}-"]`).forEach(b => {
            b.classList.add('hidden');
        });
        const badgeActivo = document.getElementById(`badge-marcada-${numPregunta}-${nuevoCodigo}`);
        if (badgeActivo) {
            const esOriginal = (radio && radio.getAttribute('data-original') === nuevoCodigo);
            badgeActivo.classList.remove('hidden');
            if (esOriginal) {
                badgeActivo.className = 'absolute -top-2.5 right-2 sm:right-3 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-2xs bg-brand-blue text-white';
                badgeActivo.innerHTML = '<i class="fa-solid fa-check text-[8px]"></i> Marcada';
            } else {
                badgeActivo.className = 'absolute -top-2.5 right-2 sm:right-3 px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1 shadow-2xs bg-amber-500 text-white';
                badgeActivo.innerHTML = '<i class="fa-solid fa-user-pen text-[8px]"></i> Modificada';
            }
        }

        // 4. Actualizar botones en modo Lista
        document.querySelectorAll(`.btn-opc-lista-${numPregunta}`).forEach(btn => {
            btn.className = 'btn-opc-lista-' + numPregunta + ' px-2.5 py-1 rounded-lg text-xs font-mono font-black transition-all cursor-pointer text-slate-600 hover:text-slate-900 hover:bg-white';
        });
        const btnListaActivo = document.getElementById(`btn-lista-paca-${numPregunta}-${nuevoCodigo}`);
        if (btnListaActivo) {
            const esOriginal = (radio && radio.getAttribute('data-original') === nuevoCodigo);
            btnListaActivo.className = 'btn-opc-lista-' + numPregunta + ' px-2.5 py-1 rounded-lg text-xs font-mono font-black transition-all cursor-pointer text-white shadow-xs ' + (esOriginal ? 'bg-brand-blue' : 'bg-amber-500');
        }

        // 5. Actualizar barra flotante
        actualizarBarraModificacionesPaca();
    }

    function actualizarBarraModificacionesPaca() {
        let modificadas = 0;
        const radios = document.querySelectorAll('#formAjustarPaca input[type="radio"]:checked');
        radios.forEach(r => {
            const orig = r.getAttribute('data-original');
            if (orig && r.value !== orig) {
                modificadas++;
            }
        });

        const barra = document.getElementById('barraFlotantePaca');
        const txt = document.getElementById('txtConteoModificadasPaca');
        if (barra && txt) {
            if (modificadas > 0) {
                txt.textContent = `${modificadas} ${modificadas === 1 ? 'respuesta ajustada' : 'respuestas ajustadas'}`;
                barra.classList.remove('hidden');
            } else {
                barra.classList.add('hidden');
            }
        }
    }

    function descartarModificacionesPaca() {
        const radios = document.querySelectorAll('#formAjustarPaca input[type="radio"]');
        radios.forEach(r => {
            const orig = r.getAttribute('data-original');
            if (orig && r.value === orig) {
                seleccionarOpcionPaca(r.getAttribute('data-pregunta'), orig);
            }
        });
        const barra = document.getElementById('barraFlotantePaca');
        if (barra) barra.classList.add('hidden');
    }

    // Iniciar con la primera pregunta activa
    document.addEventListener('DOMContentLoaded', function() {
        irAPreguntaPaca(1);
    });
</script>
@endsection
