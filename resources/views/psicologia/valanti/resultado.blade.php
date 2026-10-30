@extends('layouts.psicologia')

@section('title', 'Resultados Cuestionario VALANTI · ' . $aplicacion->nombre_completo)
@section('page_title', 'Cuestionario VALANTI')
@section('page_subtitle', $aplicacion->nombre_completo)

@section('content')
<div class="space-y-6 w-full max-w-none pb-16">

    @php
        $urlPdfConsentimientoInline = route('psicologia.consentimientos.pdf', $aplicacion) . '?inline=1';
        $urlPdfConsentimientoDescarga = route('psicologia.consentimientos.pdf', $aplicacion);

        $urlPdfValantiInline = route('psicologia.valanti.pdf', $aplicacion) . '?inline=1';
        $urlPdfValantiDescarga = route('psicologia.valanti.pdf', $aplicacion);

        $colorNivel = function (string $nivel): string {
            return match ($nivel) {
                'Muy alto', 'Muy Alto', 'Alto' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
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
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-6 shadow-2xs">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">
                Resultados Cuestionario VALANTI
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Evaluado: <strong class="text-slate-800">{{ $aplicacion->nombre_completo }}</strong> (CC {{ $aplicacion->identificacion }}) · Baremos Nacionales 1997 (n=730)
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
    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-6 shadow-2xs">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3.5 flex items-center gap-2">
            <i class="fa-solid fa-id-card-clip text-brand-blue"></i> Datos de la Aplicación y Custodia
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Fecha de Aplicación:</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ ($aplicacion->fecha_hora_aceptacion ?? $aplicacion->created_at)->format('d/m/Y') }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Edad del Evaluado:</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $aplicacion->edad }} años</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Cargo al que aspira:</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 truncate block">{{ $aplicacion->cargo_aspira }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block font-medium">Valor Predominante:</span>
                @php
                    $pred = $resultado->dimensionPredominante();
                @endphp
                <span class="font-bold text-brand-blue text-sm mt-0.5 block">{{ $pred['nombre'] }} (PE {{ $pred['pe'] }})</span>
            </div>
        </div>
    </div>

    <!-- GRILLA AMPLIA EN PC: GRÁFICO RADIAL + INTERPRETACIÓN NARRATIVA -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA (7/12 en PC): GRÁFICO RADIAL DE 5 EJES -->
        <div class="xl:col-span-7 rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-7 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                        Cuestionario VALANTI
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Comparación gráfica del candidato frente al perfil ideal en los 5 ejes axiológicos (Escala 0 a 70)
                    </p>
                </div>

                <div class="flex items-center gap-2 print:hidden flex-wrap">
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

            <!-- Leyenda Enmarcada con Borde Fino -->
            <div class="text-center mb-4">
                <div class="inline-flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-8 px-4 sm:px-5 py-2.5 rounded-xl border border-slate-400 bg-white shadow-2xs text-xs max-w-full">
                    <!-- Serie Perfil Ideal: Línea punteada azul con rombo -->
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center w-9 relative shrink-0">
                            <span class="w-full border-t-2 border-dashed border-[#001c9b]"></span>
                            <span class="w-2.5 h-2.5 bg-[#001c9b] rotate-45 absolute left-1/2 -translate-x-1/2"></span>
                        </span>
                        <span class="font-bold text-slate-900 text-xs">
                            Perfil ideal
                        </span>
                    </div>

                    <!-- Serie Candidato: Línea continua negra con cuadrado -->
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="inline-flex items-center w-9 relative shrink-0">
                            <span class="w-full border-t-2 border-solid border-black"></span>
                            <span class="w-2.5 h-2.5 bg-black absolute left-1/2 -translate-x-1/2"></span>
                        </span>
                        <span class="font-bold text-slate-900 text-xs truncate">
                            {{ $aplicacion->nombre_completo }} (Puntaje estándar)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenedor del Canvas estilo Estrella de 5 Ejes -->
            <div class="w-full mx-auto flex items-center justify-center overflow-hidden">
                <canvas id="radarValantiCanvas" width="760" height="490" class="w-full h-auto max-w-[720px]"></canvas>
            </div>
        </div>

        <!-- COLUMNA DERECHA (5/12 en PC): INTERPRETACIÓN CUALITATIVA NARRATIVA DE CADA VALOR -->
        <div class="xl:col-span-5 rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-7 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-blue block">Significado e Interpretación</span>
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                        Análisis Descriptivo de Resultados
                    </h2>
                </div>
                <i class="fa-solid fa-book-open-reader text-brand-blue text-xl"></i>
            </div>

            <!-- Párrafo Resumen Principal (Área más importante vs menos importante) -->
            <div class="p-3.5 sm:p-4 rounded-2xl bg-brand-light/60 border border-brand-blue/25 text-xs sm:text-sm font-bold text-slate-900 leading-relaxed">
                {{ $narrativa['resumen'] }}
            </div>

            <!-- Los 5 Párrafos Descriptivos (Verdad, Rectitud, Paz, Amor, No Violencia) -->
            <div class="space-y-2.5 text-xs sm:text-sm text-slate-700 leading-relaxed">
                @foreach($baremos as $dimKey => $info)
                    @php
                        $clave = $info['clave'];
                        $pe = $resultado->{"pe_{$clave}"};
                        $nivel = $resultado->{"nivel_{$clave}"};
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <span class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">
                                {{ $info['nombre'] }} ({{ $info['area'] }})
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $colorNivel($nivel) }}">
                                PE {{ $pe }} · {{ $nivel }}
                            </span>
                        </div>
                        <p class="text-slate-700 text-xs leading-relaxed">
                            {{ $narrativa['parrafos'][$dimKey] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- TABLA PRINCIPAL DE RESULTADOS ESTADÍSTICOS Y DISTANCIA ORGANIZACIONAL -->
    <div class="rounded-2xl border border-slate-200/90 bg-white shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">
                    Tabla Estadística y Comparación con Norma Nacional (Baremos 1997, n=730)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Fórmula: <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">PE = round(50 + 10 * ((PD - Media) / D.S.))</code> · <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700">Distancia = PE - Perfil_Ideal</code>
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 border border-emerald-200 px-3 py-1.5 text-xs font-extrabold text-emerald-700 self-start sm:self-auto">
                <i class="fa-solid fa-calculator"></i>
                Sumatoria PD: {{ $resultado->pd_total }} / 90 pts
            </span>
        </div>

        <!-- VISTA EN MÓVIL: TARJETAS POR COLUMNA / DIMENSIÓN SIN DESPLAZAMIENTO HORIZONTAL -->
        <div class="md:hidden p-3.5 space-y-3 overflow-x-hidden">
            @foreach($baremos as $dimKey => $info)
                @php
                    $clave = $info['clave'];
                    $pd = $resultado->{"pd_{$clave}"};
                    $pe = $resultado->{"pe_{$clave}"};
                    $nivel = $resultado->{"nivel_{$clave}"};
                    $perfilIdealFijo = (int) $info['perfil_esperado_default'];
                    $distancia = $pe - $perfilIdealFijo;
                    $anchoBarra = max(5, min(100, round(($pe / 70) * 100)));
                    $asteriscos = \App\Constants\ValantiPreguntas::asteriscosPuntajeEstandar($pe);
                    $claseDist = $distancia > 0
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : ($distancia < 0 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200');
                    $signo = $distancia > 0 ? '+' : '';
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-3.5 space-y-3">
                    <!-- Cabecera de la Dimensión + Interpretación -->
                    <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-200/80">
                        <div>
                            <span class="font-extrabold text-slate-900 text-sm block">{{ $info['nombre'] }}</span>
                            <span class="text-[11px] text-slate-500">{{ $info['area'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $colorNivel($nivel) }}">
                                {{ $nivel }}
                            </span>
                            <span class="block font-mono font-black text-rose-600 text-xs tracking-widest mt-0.5">{{ $asteriscos }}</span>
                        </div>
                    </div>

                    <!-- Datos organizados en columnas verticales sin scroll lateral -->
                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex flex-col justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Puntaje Directo (PD)</span>
                            <span class="font-mono font-black text-base text-slate-900 mt-1">{{ $pd }} pts</span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex flex-col justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Norma (Media / D.S.)</span>
                            <span class="font-mono text-[11px] text-slate-700 mt-1">
                                M: <strong>{{ number_format($info['media'], 2) }}</strong> · DS: <strong>{{ number_format($info['desviacion'], 2) }}</strong>
                            </span>
                        </div>

                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex flex-col justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Puntaje Estándar (PE)</span>
                            <div class="mt-1">
                                <span class="font-mono font-black text-base text-slate-900 block">{{ $pe }}</span>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                    <div class="bg-brand-blue h-full rounded-full" style="width: {{ $anchoBarra }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex flex-col justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ideal vs Distancia</span>
                            <div class="flex items-center justify-between gap-1.5 mt-1">
                                <span class="px-2 py-0.5 rounded-lg bg-blue-50 border border-blue-200 font-mono text-xs font-black text-[#001c9b]">
                                    Ideal: {{ $perfilIdealFijo }}
                                </span>
                                <span class="px-2 py-0.5 rounded-lg font-mono font-black text-xs border {{ $claseDist }}">
                                    {{ $signo }}{{ $distancia }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Totales Control en Móvil -->
            <div class="rounded-2xl bg-slate-100 border border-slate-200 p-3.5 space-y-1.5 text-xs">
                <div class="flex items-center justify-between font-bold text-slate-800">
                    <span>TOTALES CONTROL (PD):</span>
                    <span class="font-mono font-black text-emerald-700 text-sm">{{ $resultado->pd_total }} / 90 pts</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Rangos PE (Excel VALANTI): <strong>&ge;74</strong> Muy alto · <strong>64–73</strong> Alto · <strong>54–63</strong> Promedio Alto · <strong>44–53</strong> Promedio · <strong>34–43</strong> Promedio Bajo · <strong>24–33</strong> Bajo · <strong>&lt;24</strong> Muy bajo
                </p>
            </div>
        </div>

        <!-- VISTA EN PC / TABLET: TABLA COMPLETA -->
        <div class="hidden md:block">
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
                            $asteriscos = \App\Constants\ValantiPreguntas::asteriscosPuntajeEstandar($pe);
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
                                <span class="block font-mono font-black text-rose-600 text-xs tracking-widest mt-1">{{ $asteriscos }}</span>
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
                            Rangos PE (Excel VALANTI): <strong>&ge;74</strong> Muy alto · <strong>64–73</strong> Alto · <strong>54–63</strong> Promedio Alto · <strong>44–53</strong> Promedio · <strong>34–43</strong> Promedio Bajo · <strong>24–33</strong> Bajo · <strong>&lt;24</strong> Muy bajo
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- DESGLOSE INTERACTIVO PASO A PASO DE LAS 30 RESPUESTAS DEL CANDIDATO (UNA POR UNA) -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-6 shadow-2xs">
        <details id="detailsDesgloseValanti" class="group text-xs">
            <summary class="font-bold text-brand-blue cursor-pointer select-none flex items-center justify-between gap-3 text-sm sm:text-base py-1">
                <span class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <span>Ver desglose detallado de las 30 respuestas del candidato (Una por una)</span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 group-open:text-brand-blue">
                    <span class="hidden sm:inline group-open:hidden">Desplegar visor</span>
                    <span class="hidden sm:group-open:inline">Ocultar visor</span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200 group-open:rotate-180"></i>
                </span>
            </summary>

            <div class="mt-5 pt-4 border-t border-slate-100 space-y-4">

            <!-- Selector rápido por columnas (1 al 30) sin desplazamiento lateral en móvil -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 font-semibold px-0.5 flex-wrap gap-1">
                        <span>Seleccione o avance pregunta por pregunta (1 a 30):</span>
                        <span id="indicadorParteActiva" class="font-bold text-brand-blue">Parte 1 · Valores (Ítems 1–9)</span>
                    </div>

                    <div class="grid grid-cols-6 sm:grid-cols-10 md:grid-cols-15 gap-1.5">
                        @foreach($preguntas as $num => $item)
                            <button type="button"
                                    id="btn-pill-pregunta-{{ $num }}"
                                    onclick="irAPreguntaDesglose({{ $num }})"
                                    class="pill-pregunta-desglose h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex items-center justify-center {{ $num === 1 ? 'bg-brand-blue text-white border-brand-blue shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                {{ $num }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Contenedor de las 30 Tarjetas (Solo 1 visible a la vez) -->
                <div class="relative">
                    @foreach($preguntas as $num => $item)
                        @php
                            $r = $respuestasPorNumero[$num] ?? null;
                            $ptsA = $r ? (int) $r->puntos_a : 0;
                            $ptsB = $r ? (int) $r->puntos_b : 0;
                            $dimAInfo = $baremos[$item['dim_a']];
                            $dimBInfo = $baremos[$item['dim_b']];
                        @endphp

                        <div id="slide-pregunta-{{ $num }}"
                             data-parte="{{ $item['parte'] }}"
                             class="slide-desglose-valanti {{ $num === 1 ? '' : 'hidden' }} rounded-2xl bg-slate-50/80 border border-slate-200/90 p-4 sm:p-6 space-y-4 transition-all">

                            <!-- Cabecera de la Pregunta Activa -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200/80">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="px-3 py-1 rounded-xl bg-brand-dark text-white font-mono text-xs sm:text-sm font-black">
                                        Pregunta #{{ $num }} de 30
                                    </span>
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-bold border {{ $item['parte'] === 1 ? 'bg-blue-50 text-brand-blue border-blue-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        {{ $item['parte'] === 1 ? 'Parte 1 · Selección de Valores por Importancia' : 'Parte 2 · Juicio de Inaceptabilidad (Antivalores)' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-auto">
                                    <span class="text-xs font-semibold text-slate-500">Distribución marcada:</span>
                                    <span class="px-3 py-1 rounded-xl bg-white border border-brand-blue/30 font-mono text-sm font-black text-brand-blue shadow-2xs">
                                        {{ $ptsA }} - {{ $ptsB }}
                                    </span>
                                </div>
                            </div>

                            <!-- Comparativa de Opción A y Opción B (Lado a lado en PC, apilado en móvil) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-5">

                                <!-- Tarjeta Opción A -->
                                <div class="p-4 sm:p-5 rounded-2xl border-2 transition-all flex flex-col justify-between gap-3 {{ $ptsA >= 2 ? 'bg-white border-brand-blue shadow-xs' : 'bg-white/70 border-slate-200 opacity-85' }}">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-slate-700">
                                                <span class="w-6 h-6 rounded-lg bg-brand-dark text-white flex items-center justify-center font-mono text-xs">A</span>
                                                <span>Opción A</span>
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Valor: {{ $dimAInfo['nombre'] }}
                                            </span>
                                        </div>
                                        <p class="text-sm sm:text-base font-bold text-slate-900 leading-snug pt-1">
                                            “{{ $item['texto_a'] }}”
                                        </p>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[11px] font-semibold text-slate-500">
                                            Puntos asignados a {{ $dimAInfo['nombre'] }}:
                                        </span>
                                        <span class="px-3 py-1 rounded-xl font-mono text-xs sm:text-sm font-black {{ $ptsA >= 2 ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $ptsA }} {{ $ptsA === 1 ? 'punto' : 'puntos' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Tarjeta Opción B -->
                                <div class="p-4 sm:p-5 rounded-2xl border-2 transition-all flex flex-col justify-between gap-3 {{ $ptsB >= 2 ? 'bg-white border-brand-blue shadow-xs' : 'bg-white/70 border-slate-200 opacity-85' }}">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold uppercase tracking-wider text-slate-700">
                                                <span class="w-6 h-6 rounded-lg bg-sky-700 text-white flex items-center justify-center font-mono text-xs">B</span>
                                                <span>Opción B</span>
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Valor: {{ $dimBInfo['nombre'] }}
                                            </span>
                                        </div>
                                        <p class="text-sm sm:text-base font-bold text-slate-900 leading-snug pt-1">
                                            “{{ $item['texto_b'] }}”
                                        </p>
                                    </div>

                                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[11px] font-semibold text-slate-500">
                                            Puntos asignados a {{ $dimBInfo['nombre'] }}:
                                        </span>
                                        <span class="px-3 py-1 rounded-xl font-mono text-xs sm:text-sm font-black {{ $ptsB >= 2 ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ $ptsB }} {{ $ptsB === 1 ? 'punto' : 'puntos' }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Controles de Navegación (Anterior / Contador / Siguiente) adaptados a Móvil y PC -->
                <div class="flex items-center justify-between gap-2 sm:gap-4 pt-1">
                    <button type="button"
                            id="btnPreguntaAnterior"
                            onclick="cambiarPreguntaDesglose(-1)"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 disabled:opacity-40 disabled:pointer-events-none px-3.5 sm:px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition-all shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Anterior</span>
                    </button>

                    <div class="text-center">
                        <span id="contadorPreguntaDesglose" class="font-mono font-extrabold text-xs sm:text-sm text-slate-800">
                            Pregunta 1 de 30
                        </span>
                        <span class="hidden sm:block text-[11px] text-slate-400">
                            También puede usar las flechas ⬅ ➡ del teclado
                        </span>
                    </div>

                    <button type="button"
                            id="btnPreguntaSiguiente"
                            onclick="cambiarPreguntaDesglose(1)"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-blue hover:bg-brand-dark disabled:opacity-40 disabled:pointer-events-none px-3.5 sm:px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition-all shadow-2xs cursor-pointer">
                        <span>Siguiente</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </div>
        </details>
    </div>

</div>

<!-- MODAL DE PREVISUALIZACIÓN DE PDF INTERACTIVO -->
<div id="modalVisorPdf" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 transition-all duration-200" aria-modal="true" role="dialog">
    <div class="bg-white rounded-3xl max-w-5xl w-full h-[88vh] flex flex-col overflow-hidden shadow-2xl border border-slate-200">
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

    /**
     * Visor paso a paso (1 por 1) de las 30 respuestas del cuestionario VALANTI
     */
    let preguntaDesgloseActual = 1;
    const TOTAL_PREGUNTAS_VALANTI = 30;

    function irAPreguntaDesglose(numero) {
        if (numero < 1 || numero > TOTAL_PREGUNTAS_VALANTI) return;
        preguntaDesgloseActual = numero;

        // Ocultar todas las tarjetas y mostrar únicamente la seleccionada
        document.querySelectorAll('.slide-desglose-valanti').forEach((el) => {
            el.classList.add('hidden');
        });
        const slideActivo = document.getElementById('slide-pregunta-' + numero);
        if (slideActivo) {
            slideActivo.classList.remove('hidden');
        }

        // Actualizar estado activo de los botones numerados (1..30)
        document.querySelectorAll('.pill-pregunta-desglose').forEach((btn) => {
            btn.className = 'pill-pregunta-desglose h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex items-center justify-center bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100';
        });
        const pillActiva = document.getElementById('btn-pill-pregunta-' + numero);
        if (pillActiva) {
            pillActiva.className = 'pill-pregunta-desglose h-8 sm:h-9 rounded-xl font-mono text-xs font-extrabold border transition-all cursor-pointer flex items-center justify-center bg-brand-blue text-white border-brand-blue shadow-xs';
        }

        // Actualizar textos indicadores y estado de botones Anterior / Siguiente
        const contador = document.getElementById('contadorPreguntaDesglose');
        if (contador) {
            contador.textContent = 'Pregunta ' + numero + ' de ' + TOTAL_PREGUNTAS_VALANTI;
        }

        const indicadorParte = document.getElementById('indicadorParteActiva');
        if (indicadorParte) {
            indicadorParte.textContent = numero <= 9
                ? 'Parte 1 · Valores (Ítems 1–9)'
                : 'Parte 2 · Antivalores (Ítems 10–30)';
        }

        const btnAnt = document.getElementById('btnPreguntaAnterior');
        const btnSig = document.getElementById('btnPreguntaSiguiente');
        if (btnAnt) btnAnt.disabled = (numero <= 1);
        if (btnSig) btnSig.disabled = (numero >= TOTAL_PREGUNTAS_VALANTI);
    }

    function cambiarPreguntaDesglose(delta) {
        irAPreguntaDesglose(preguntaDesgloseActual + delta);
    }

    document.addEventListener('DOMContentLoaded', () => {
        dibujarGraficoValanti();
        irAPreguntaDesglose(1);
    });

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

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cerrarModalPdf();
            return;
        }
        const details = document.getElementById('detailsDesgloseValanti');
        if (details && details.open) {
            if (e.key === 'ArrowRight') {
                cambiarPreguntaDesglose(1);
            } else if (e.key === 'ArrowLeft') {
                cambiarPreguntaDesglose(-1);
            }
        }
    });
</script>
@endsection
