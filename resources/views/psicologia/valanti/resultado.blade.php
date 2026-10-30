@extends('layouts.psicologia')

@section('title', 'Resultados Cuestionario VALANTI · ' . $aplicacion->nombre_completo)
@section('page_title', 'Cuestionario VALANTI')
@section('page_subtitle', $aplicacion->nombre_completo)

@section('content')
<div class="space-y-8 max-w-5xl mx-auto pb-16">

    @php
        $urlPdfConsentimientoInline = route('psicologia.consentimientos.pdf', $aplicacion) . '?inline=1';
        $urlPdfConsentimientoDescarga = route('psicologia.consentimientos.pdf', $aplicacion);

        $urlPdfValantiInline = route('psicologia.valanti.pdf', $aplicacion) . '?inline=1';
        $urlPdfValantiDescarga = route('psicologia.valanti.pdf', $aplicacion);

        $colorNivel = function (string $nivel): string {
            return match ($nivel) {
                'Muy Alto' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Promedio Alto' => 'bg-sky-50 text-sky-700 border-sky-200',
                'Promedio' => 'bg-slate-100 text-slate-700 border-slate-200',
                'Promedio Bajo' => 'bg-amber-50 text-amber-700 border-amber-200',
                default => 'bg-rose-50 text-rose-700 border-rose-200',
            };
        };

        // Datos ordenados en sentido horario comenzando a las 12:00:
        // 1. Verdad, 2. Rectitud, 3. Paz, 4. Amor, 5. No violencia
        $serieCandidatoPE = [
            (int) $resultado->pe_verdad,
            (int) $resultado->pe_rectitud,
            (int) $resultado->pe_paz,
            (int) $resultado->pe_amor,
            (int) $resultado->pe_no_violencia,
        ];

        // Perfil Ideal Estático: Verdad=50, Rectitud=65, Paz=50, Amor=65, No violencia=65
        $seriePerfilEmpresa = [50, 65, 50, 65, 65];

        // Interpretación Narrativa Oficial de los 5 Valores
        $narrativa = \App\Constants\ValantiPreguntas::generarNarrativaCompleta($aplicacion->nombre_completo, [
            'VERDAD' => (int) $resultado->pe_verdad,
            'RECTITUD' => (int) $resultado->pe_rectitud,
            'PAZ' => (int) $resultado->pe_paz,
            'AMOR' => (int) $resultado->pe_amor,
            'NO_VIOLENCIA' => (int) $resultado->pe_no_violencia,
        ]);
    @endphp

    <!-- Encabezado con Botones de Acción y Exportación PDF Automática -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-circle-check"></i> Prueba 2 Calificada · PDF Generado Automáticamente
                </span>
                <span class="text-xs text-slate-400">Sesión #{{ $aplicacion->id }} ({{ $aplicacion->codigo_sesion }})</span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-1">
                Resultados Cuestionario VALANTI
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Evaluado: <strong>{{ $aplicacion->nombre_completo }}</strong> (CC {{ $aplicacion->identificacion }}) · Baremos Nacionales 1997 (n=730)
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0 print:hidden flex-wrap">
            <!-- Exportar PDF del Gráfico y Reporte VALANTI (Ya generado automáticamente) -->
            <a href="{{ $urlPdfValantiDescarga }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white transition-all shadow-2xs">
                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                Exportar PDF Gráfico
            </a>

            <button type="button"
               onclick="abrirModalPdf('{{ $urlPdfValantiInline }}', '{{ $urlPdfValantiDescarga }}', 'Cuestionario VALANTI · {{ addslashes($aplicacion->nombre_completo) }}', 'Reporte Cuestionario VALANTI')"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-bold text-slate-700 transition-colors shadow-2xs cursor-pointer">
                <i class="fa-solid fa-eye text-xs text-brand-blue"></i>
                Ver PDF Gráfico
            </button>

            @if($aplicacion->pacaFinalizada())
                <a href="{{ route('psicologia.paca.resultado', $aplicacion) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 px-3.5 py-2 text-xs font-bold text-indigo-700 transition-colors shadow-2xs">
                    <i class="fa-solid fa-chart-simple text-xs"></i>
                    Ver Prueba 1 (PAC-A)
                </a>
            @endif

            <button type="button"
               onclick="abrirModalPdf('{{ $urlPdfConsentimientoInline }}', '{{ $urlPdfConsentimientoDescarga }}', 'Consentimiento Informado · {{ addslashes($aplicacion->nombre_completo) }}', 'Formato Oficial GH-FO-025')"
               class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3.5 py-2 text-xs font-bold text-rose-700 transition-colors shadow-2xs cursor-pointer">
                <i class="fa-solid fa-file-pdf text-xs text-rose-600"></i>
                Consentimiento
            </button>

            <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-brand-blue hover:bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-2xs transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Historial
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
                <span class="text-slate-400 block font-medium">Valor Predominante:</span>
                @php
                    $pred = $resultado->dimensionPredominante();
                @endphp
                <span class="font-bold text-brand-blue text-sm">{{ $pred['nombre'] }} (PE {{ $pred['pe'] }})</span>
            </div>
        </div>
    </div>

    <!-- COMPONENTE DE REPORTE VISUAL: GRÁFICO RADIAL DE 5 EJES (CUESTIONARIO VALANTI) -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-2xs">
        <!-- Barra superior de título y botón directo de exportación PDF del gráfico -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                    Cuestionario VALANTI
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Comparación gráfica del candidato frente al perfil ideal en los 5 ejes axiológicos (Escala 0 a 70)
                </p>
            </div>

            <div class="flex items-center gap-2 print:hidden">
                <button type="button"
                        onclick="abrirModalPdf('{{ $urlPdfValantiInline }}', '{{ $urlPdfValantiDescarga }}', 'Cuestionario VALANTI · {{ addslashes($aplicacion->nombre_completo) }}', 'Reporte Cuestionario VALANTI')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 text-xs font-bold transition-colors cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass-chart text-brand-blue"></i>
                    Previsualizar PDF
                </button>
                <a href="{{ $urlPdfValantiDescarga }}"
                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 text-xs font-bold transition-colors shadow-2xs">
                    <i class="fa-solid fa-file-pdf"></i>
                    Descargar PDF del Gráfico
                </a>
            </div>
        </div>

        <!-- Leyenda Enmarcada con Borde Fino (Solo "Perfil ideal" y Nombre con Puntaje estándar) -->
        <div class="text-center mb-4">
            <div class="inline-flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8 px-5 py-2.5 rounded-xl border border-slate-400 bg-white shadow-2xs text-xs">
                <!-- Serie Perfil Ideal: Línea punteada azul con rombo -->
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center w-9 relative">
                        <span class="w-full border-t-2 border-dashed border-[#001c9b]"></span>
                        <span class="w-2.5 h-2.5 bg-[#001c9b] rotate-45 absolute left-1/2 -translate-x-1/2"></span>
                    </span>
                    <span class="font-bold text-slate-900 text-xs">
                        Perfil ideal
                    </span>
                </div>

                <!-- Serie Candidato: Línea continua negra con cuadrado -->
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center w-9 relative">
                        <span class="w-full border-t-2 border-solid border-black"></span>
                        <span class="w-2.5 h-2.5 bg-black absolute left-1/2 -translate-x-1/2"></span>
                    </span>
                    <span class="font-bold text-slate-900 text-xs">
                        {{ $aplicacion->nombre_completo }} (Puntaje estándar)
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenedor del Canvas estilo Estrella de 5 Ejes (Idéntico a la imagen oficial VALANTI) -->
        <div class="w-full max-w-[680px] mx-auto flex items-center justify-center">
            <canvas id="radarValantiCanvas" width="760" height="490" class="w-full h-auto max-w-[660px]"></canvas>
        </div>
    </div>

    <!-- TARJETA DE INTERPRETACIÓN CUALITATIVA NARRATIVA DE CADA VALOR -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-2xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-brand-blue block">Significado e Interpretación</span>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                    Análisis Descriptivo de Resultados (Cuestionario VALANTI)
                </h2>
            </div>
            <i class="fa-solid fa-book-open-reader text-brand-blue text-xl"></i>
        </div>

        <!-- Párrafo Resumen Principal (Área más importante vs menos importante) -->
        <div class="p-4 rounded-2xl bg-brand-light/60 border border-brand-blue/25 text-xs sm:text-sm font-bold text-slate-900 leading-relaxed">
            {{ $narrativa['resumen'] }}
        </div>

        <!-- Los 5 Párrafos Descriptivos (Verdad, Rectitud, Paz, Amor, No Violencia) -->
        <div class="space-y-3 text-xs sm:text-sm text-slate-700 leading-relaxed">
            @foreach($baremos as $dimKey => $info)
                @php
                    $clave = $info['clave'];
                    $pe = $resultado->{"pe_{$clave}"};
                    $nivel = $resultado->{"nivel_{$clave}"};
                @endphp
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">
                                {{ $info['nombre'] }} ({{ $info['area'] }})
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $colorNivel($nivel) }}">
                                PE {{ $pe }} · {{ $nivel }}
                            </span>
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            {{ $narrativa['parrafos'][$dimKey] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TABLA PRINCIPAL DE RESULTADOS ESTADÍSTICOS Y DISTANCIA ORGANIZACIONAL -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Tabla Estadística y Comparación con Norma Nacional (Baremos 1997, n=730)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Fórmula: <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">PE = round(50 + 10 * ((PD - Media) / D.S.))</code> · <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">Distancia = PE - Perfil_Ideal</code>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-xs font-extrabold text-emerald-700">
                <i class="fa-solid fa-calculator"></i>
                Sumatoria PD: {{ $resultado->pd_total }} / 90 pts
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Dimensión / Valor</th>
                        <th class="py-3.5 px-4 text-center">Puntaje Directo (PD)</th>
                        <th class="py-3.5 px-4 text-center">Norma (Media / D.S.)</th>
                        <th class="py-3.5 px-4 text-center">Puntaje Estándar (PE)</th>
                        <th class="py-3.5 px-4 text-center">Interpretación Cualitativa</th>
                        <th class="py-3.5 px-4 text-center">Perfil Ideal</th>
                        <th class="py-3.5 px-4 text-center">Distancia (PE - Ideal)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($baremos as $dimKey => $info)
                        @php
                            $clave = $info['clave'];
                            $pd = $resultado->{"pd_{$clave}"};
                            $pe = $resultado->{"pe_{$clave}"};
                            $nivel = $resultado->{"nivel_{$clave}"};
                            $perfilIdealFijo = (int) $info['perfil_esperado_default'];
                            $distancia = $pe - $perfilIdealFijo;
                            $anchoBarra = max(5, min(100, round(($pe / 70) * 100)));
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-extrabold text-slate-900 text-sm block">{{ $info['nombre'] }}</span>
                                <span class="text-[11px] text-slate-400">{{ $info['area'] }}</span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-10 h-8 rounded-xl bg-slate-100 font-mono font-black text-slate-800 text-sm border border-slate-200">
                                    {{ $pd }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center font-mono text-[11px] text-slate-500">
                                <div>M: <strong>{{ number_format($info['media'], 2) }}</strong></div>
                                <div>DS: <strong>{{ number_format($info['desviacion'], 2) }}</strong></div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="font-mono font-black text-base text-slate-900 block">{{ $pe }}</span>
                                <div class="w-24 mx-auto bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                    <div class="bg-brand-blue h-full rounded-full" style="width: {{ $anchoBarra }}%;"></div>
                                </div>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $colorNivel($nivel) }}">
                                    {{ $nivel }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-12 h-8 rounded-xl bg-blue-50/80 border border-blue-200 font-mono text-xs font-black text-[#001c9b]">
                                    {{ $perfilIdealFijo }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-center">
                                @php
                                    $claseDist = $distancia > 0
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                        : ($distancia < 0 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200');
                                    $signo = $distancia > 0 ? '+' : '';
                                @endphp
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-xl font-mono font-black text-xs border {{ $claseDist }}">
                                    {{ $signo }}{{ $distancia }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/90 border-t border-slate-200 text-xs font-bold text-slate-700">
                        <td class="py-3.5 px-4">TOTALES CONTROL</td>
                        <td class="py-3.5 px-4 text-center font-mono font-black text-emerald-700">{{ $resultado->pd_total }} / 90</td>
                        <td colspan="5" class="py-3.5 px-4 text-[11px] text-slate-500 font-normal">
                            Rangos PE: <strong>&gt;60</strong> Muy Alto · <strong>55–60</strong> Promedio Alto · <strong>45–54</strong> Promedio · <strong>40–44</strong> Promedio Bajo · <strong>&lt;40</strong> Bajo
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- DESGLOSE DE LAS 30 RESPUESTAS DEL CANDIDATO -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs">
        <details class="text-xs">
            <summary class="font-bold text-brand-blue cursor-pointer hover:underline flex items-center justify-between text-sm">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    Ver desglose detallado de las 30 respuestas del candidato (Parte 1 y Parte 2)
                </span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </summary>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[500px] overflow-y-auto pr-1">
                @foreach($preguntas as $num => $item)
                    @php
                        $r = $respuestasPorNumero[$num] ?? null;
                        $ptsA = $r ? $r->puntos_a : 0;
                        $ptsB = $r ? $r->puntos_b : 0;
                    @endphp
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 space-y-1.5 text-[11px]">
                        <div class="flex items-center justify-between border-b border-slate-200/70 pb-1">
                            <span class="font-extrabold text-slate-800">
                                Pregunta #{{ $num }} ({{ $item['parte'] === 1 ? 'Valores' : 'Antivalores' }})
                            </span>
                            <span class="font-mono font-black px-2 py-0.5 rounded bg-white border border-slate-200 text-brand-blue">
                                {{ $ptsA }} - {{ $ptsB }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 {{ $ptsA >= 2 ? 'font-bold text-slate-900' : 'text-slate-500' }}">
                            <span><strong>A ({{ $baremos[$item['dim_a']]['nombre'] }}):</strong> {{ $item['texto_a'] }}</span>
                            <span class="font-mono shrink-0">{{ $ptsA }} pts</span>
                        </div>
                        <div class="flex items-center justify-between gap-2 {{ $ptsB >= 2 ? 'font-bold text-slate-900' : 'text-slate-500' }}">
                            <span><strong>B ({{ $baremos[$item['dim_b']]['nombre'] }}):</strong> {{ $item['texto_b'] }}</span>
                            <span class="font-mono shrink-0">{{ $ptsB }} pts</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </details>
    </div>

</div>

<!-- MODAL DE PREVISUALIZACIÓN DE PDF INTERACTIVO -->
<div id="modalVisorPdf" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-4xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 bg-slate-50 shrink-0">
            <div class="min-w-0 pr-4">
                <span id="subtituloModalPdf" class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Documento Oficial</span>
                <h3 id="tituloModalPdf" class="text-sm font-bold text-slate-900 truncate">Visor PDF</h3>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="btnDescargarPdfModal" href="#" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs font-bold transition-colors shadow-2xs">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span class="hidden sm:inline">Descargar PDF</span>
                </a>
                <a id="btnPestanaModal" href="#" target="_blank" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                </a>
                <button type="button" onclick="cerrarModalPdf()" class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-white hover:bg-rose-50 hover:text-rose-600 text-slate-500 border border-slate-200 text-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>
        <div class="relative flex-1 bg-slate-100 overflow-hidden flex flex-col">
            <div id="spinnerCargaPdf" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-brand-blue mb-3"></i>
                <span class="text-xs font-semibold text-slate-600">Cargando documento PDF institucional...</span>
            </div>
            <iframe id="iframeVisorPdf" src="about:blank" class="w-full h-full border-0" onload="document.getElementById('spinnerCargaPdf').classList.add('hidden')"></iframe>
        </div>
    </div>
</div>

<script>
    /**
     * Renderizador Canvas 2D de alta precisión idéntico al gráfico oficial de VALANTI:
     * - 5 ejes radiales negros (sin telaraña exterior) con marcas perpendiculares en 10, 20, 30, 40, 50, 60, 70
     * - Etiquetas grandes en negrita (Verdad, Rectitud, Paz, Amor, No violencia)
     * - Escala 0 a 70 en el eje superior
     * - Línea azul punteada con rombos (Perfil ideal: 50, 65, 50, 65, 65)
     * - Línea negra continua con cuadrados (Puntaje estándar del candidato)
     */
    const DATOS_CANDIDATO_PE = @json($serieCandidatoPE);
    const DATOS_PERFIL_IDEAL = [50, 65, 50, 65, 65];

    function dibujarGraficoValanti() {
        const canvas = document.getElementById('radarValantiCanvas');
        if (!canvas) return;

        const dpr = window.devicePixelRatio || 1;
        const logicalWidth = 760;
        const logicalHeight = 490;

        canvas.width = logicalWidth * dpr;
        canvas.height = logicalHeight * dpr;

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);

        const cx = 380;
        const cy = 260;
        const maxRadius = 185;
        const maxVal = 70;

        // Limpiar fondo blanco
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, logicalWidth, logicalHeight);

        const ejes = [
            { nombre: 'Verdad',       angulo: -90, pe: DATOS_CANDIDATO_PE[0], ideal: DATOS_PERFIL_IDEAL[0] },
            { nombre: 'Rectitud',     angulo: -18, pe: DATOS_CANDIDATO_PE[1], ideal: DATOS_PERFIL_IDEAL[1] },
            { nombre: 'Paz',          angulo: 54,  pe: DATOS_CANDIDATO_PE[2], ideal: DATOS_PERFIL_IDEAL[2] },
            { nombre: 'Amor',         angulo: 126, pe: DATOS_CANDIDATO_PE[3], ideal: DATOS_PERFIL_IDEAL[3] },
            { nombre: 'No violencia', angulo: 198, pe: DATOS_CANDIDATO_PE[4], ideal: DATOS_PERFIL_IDEAL[4] },
        ];

        function puntoPolar(valor, anguloGrados) {
            const valClamped = Math.max(0, Math.min(maxVal, valor));
            const r = (valClamped / maxVal) * maxRadius;
            const rad = (anguloGrados * Math.PI) / 180;
            return [cx + r * Math.cos(rad), cy + r * Math.sin(rad)];
        }

        // 1. Dibujar los 5 ejes radiales y sus marcas de graduación perpendiculares (10..70)
        ctx.strokeStyle = '#111827';
        ctx.lineWidth = 1.5;

        ejes.forEach((eje) => {
            const [vx, vy] = puntoPolar(70, eje.angulo);
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.lineTo(vx, vy);
            ctx.stroke();

            // Marcas perpendiculares (ticks)
            const perpRad = ((eje.angulo + 90) * Math.PI) / 180;
            const tickHalf = 5;
            const dx = tickHalf * Math.cos(perpRad);
            const dy = tickHalf * Math.sin(perpRad);

            for (let step = 10; step <= 70; step += 10) {
                const [tx, ty] = puntoPolar(step, eje.angulo);
                ctx.beginPath();
                ctx.moveTo(tx - dx, ty - dy);
                ctx.lineTo(tx + dx, ty + dy);
                ctx.stroke();
            }
        });

        // 2. Números de escala (0, 10, 20, 30, 40, 50, 60, 70) a la izquierda del eje superior (Verdad)
        ctx.fillStyle = '#000000';
        ctx.font = '13px Arial, sans-serif';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (let step = 0; step <= 70; step += 10) {
            const [, ty] = puntoPolar(step, -90);
            const offsetY = step === 0 ? 8 : 0;
            ctx.fillText(String(step), cx - 12, ty + offsetY);
        }

        // 3. Títulos de los 5 ejes en negrita grande y legible
        ctx.fillStyle = '#000000';
        ctx.font = 'bold 22px Arial, sans-serif';

        ejes.forEach((eje, idx) => {
            const [tipX, tipY] = puntoPolar(70, eje.angulo);
            if (idx === 0) {
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';
                ctx.fillText(eje.nombre, cx, tipY - 12);
            } else if (idx === 1) {
                ctx.textAlign = 'left';
                ctx.textBaseline = 'middle';
                ctx.fillText(eje.nombre, tipX + 14, tipY);
            } else if (idx === 2) {
                ctx.textAlign = 'left';
                ctx.textBaseline = 'top';
                ctx.fillText(eje.nombre, tipX + 10, tipY + 4);
            } else if (idx === 3) {
                ctx.textAlign = 'right';
                ctx.textBaseline = 'top';
                ctx.fillText(eje.nombre, tipX - 10, tipY + 4);
            } else {
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';
                ctx.fillText(eje.nombre, tipX - 14, tipY);
            }
        });

        // 4. Serie Perfil Ideal: Línea azul punteada (#001c9b) + Rombos azules
        const coordsIdeal = ejes.map((e) => puntoPolar(e.ideal, e.angulo));
        ctx.save();
        ctx.strokeStyle = '#001c9b';
        ctx.lineWidth = 2.2;
        ctx.setLineDash([6, 4]);
        ctx.beginPath();
        coordsIdeal.forEach(([x, y], i) => {
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.closePath();
        ctx.stroke();
        ctx.restore();

        // Marcadores Rombo azules
        ctx.fillStyle = '#001c9b';
        coordsIdeal.forEach(([ix, iy]) => {
            const d = 6.5;
            ctx.beginPath();
            ctx.moveTo(ix, iy - d);
            ctx.lineTo(ix + d, iy);
            ctx.lineTo(ix, iy + d);
            ctx.lineTo(ix - d, iy);
            ctx.closePath();
            ctx.fill();
        });

        // 5. Serie Candidato (Puntaje estándar): Línea negra continua + Cuadrados negros
        const coordsCand = ejes.map((e) => puntoPolar(e.pe, e.angulo));
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2.2;
        ctx.beginPath();
        coordsCand.forEach(([x, y], i) => {
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.closePath();
        ctx.stroke();

        // Marcadores Cuadrados negros
        ctx.fillStyle = '#000000';
        coordsCand.forEach(([kx, ky]) => {
            const s = 5.5;
            ctx.fillRect(kx - s, ky - s, s * 2, s * 2);
        });
    }

    document.addEventListener('DOMContentLoaded', dibujarGraficoValanti);

    function abrirModalPdf(urlInline, urlDescarga, tituloTxt, subtituloTxt) {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');
        const spinner = document.getElementById('spinnerCargaPdf');
        const titulo = document.getElementById('tituloModalPdf');
        const subtitulo = document.getElementById('subtituloModalPdf');
        const btnDescargar = document.getElementById('btnDescargarPdfModal');
        const btnPestana = document.getElementById('btnPestanaModal');

        titulo.textContent = tituloTxt;
        if (subtitulo && subtituloTxt) subtitulo.textContent = subtituloTxt;
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
</script>
@endsection
