<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Resultados PAC-A - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 12mm 14mm 12mm 14mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0f172a;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .logo-col {
            width: 22%;
            text-align: center;
        }
        .logo-col img {
            max-width: 110px;
            max-height: 40px;
        }
        .titulo-col {
            width: 54%;
            text-align: center;
        }
        .titulo-col h2 {
            margin: 0;
            font-size: 8.5pt;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
        }
        .titulo-col h1 {
            margin: 2px 0 0 0;
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }
        .titulo-col p {
            margin: 1px 0 0 0;
            font-size: 7pt;
            color: #64748b;
        }
        .meta-col {
            width: 24%;
            font-size: 7pt;
            line-height: 1.25;
            padding: 0 !important;
        }
        .tabla-meta {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-meta td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px 4px;
        }
        .tabla-meta tr:last-child td {
            border-bottom: none;
        }

        /* FICHA DEL CANDIDATO */
        .tabla-ficha {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        .tabla-ficha td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            background-color: #f8fafc;
        }

        /* SECCIÓN DE LOS 2 INDICADORES PRINCIPALES */
        .tabla-indicadores {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 10px;
        }
        .caja-indicador {
            width: 50%;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            vertical-align: top;
            background-color: #ffffff;
        }
        .caja-indicador.acc {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }
        .caja-indicador.tipo-a {
            border-color: #6366f1;
            background-color: #eef2ff;
        }
        .ind-encabezado {
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .ind-num {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .ind-titulo {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            margin: 1px 0;
        }
        .ind-puntaje-caja {
            margin: 6px 0;
            text-align: center;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            border-radius: 4px;
        }
        .ind-puntaje-val {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
        }
        .ind-puntaje-max {
            font-size: 8.5pt;
            color: #64748b;
        }
        .ind-interpretacion {
            font-size: 7.5pt;
            line-height: 1.35;
            background-color: #ffffff;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 6px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: #1e293b;
            text-align: justify;
        }

        /* TABLA DE BAREMOS Y RANGOS */
        .tabla-baremos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 7.5pt;
        }
        .tabla-baremos th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 4px 6px;
            text-align: left;
            font-size: 7pt;
            text-transform: uppercase;
        }
        .tabla-baremos td {
            border: 1px solid #cbd5e1;
            padding: 3px 6px;
        }

        /* DESGLOSE DE LAS 40 PREGUNTAS */
        .seccion-titulo {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin: 8px 0 4px 0;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #0f172a;
        }
        .tabla-preguntas {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.8pt;
            line-height: 1.25;
        }
        .tabla-preguntas th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 3px 4px;
            text-align: left;
            font-size: 6.5pt;
            text-transform: uppercase;
        }
        .tabla-preguntas td {
            border: 1px solid #e2e8f0;
            padding: 2.5px 4px;
            vertical-align: middle;
        }
        .tabla-preguntas tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge-dim {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-acc {
            background-color: #ffe4e6;
            color: #be123c;
        }
        .badge-tipo-a {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        .badge-control {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* FIRMAS Y PIE */
        .seccion-firmas {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .seccion-firmas td {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
            padding: 8px 15px;
        }
        .linea-firma {
            border-top: 1px solid #000;
            margin: 0 10px 4px 10px;
        }
        .img-firma {
            max-height: 48px;
            max-width: 160px;
            margin-bottom: 2px;
        }
        .texto-firma {
            font-size: 7.5pt;
            color: #334155;
            line-height: 1.3;
        }
        .pie-pagina {
            margin-top: 8px;
            font-size: 6.5pt;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <!-- 1. ENCABEZADO INSTITUCIONAL GH-FO -->
    <table class="tabla-encabezado">
        <tr>
            <td class="logo-col">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo PNY">
                @else
                    <strong style="font-size: 8pt; color: #1e3a8a;">C.I. PISCÍCOLA NEW YORK S.A.</strong>
                @endif
            </td>
            <td class="titulo-col">
                <h2>GESTIÓN HUMANA · PSICOLOGÍA ORGANIZACIONAL</h2>
                <h1>INFORME DE EVALUACIÓN PAC-A</h1>
                <p>Cuestionario de Tendencia Accidentógena y Patrón de Comportamiento Tipo A</p>
            </td>
            <td class="meta-col">
                <table class="tabla-meta">
                    <tr>
                        <td><strong>FORMATO:</strong> GH-FO-PAC</td>
                    </tr>
                    <tr>
                        <td><strong>VERSIÓN:</strong> 01</td>
                    </tr>
                    <tr>
                        <td><strong>FECHA:</strong> {{ ($resultado->finalizada_at ?? $evaluacion->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>SESIÓN:</strong> {{ $evaluacion->codigo_sesion }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 2. DATOS DEL EVALUADO -->
    <table class="tabla-ficha">
        <tr>
            <td style="width: 38%;"><strong>Candidato(a):</strong> {{ $evaluacion->nombre_completo }}</td>
            <td style="width: 24%;"><strong>Cédula:</strong> {{ $evaluacion->identificacion }} ({{ $evaluacion->lugar_expedicion }})</td>
            <td style="width: 26%;"><strong>Área / Cargo:</strong> {{ $evaluacion->cargo_aspira }}</td>
            <td style="width: 12%;"><strong>Edad:</strong> {{ $evaluacion->edad }} años</td>
        </tr>
    </table>

    <!-- 3. RESULTADOS DE LOS DOS INDICADORES PRINCIPALES -->
    <table class="tabla-indicadores">
        <tr>
            <!-- INDICADOR 1: TENDENCIA ACCIDENTÓGENA -->
            <td class="caja-indicador acc">
                <div class="ind-encabezado">
                    <span class="ind-num">Indicador I · 12 Ítems</span>
                    <div class="ind-titulo">Tendencia Accidentógena</div>
                </div>

                <div class="ind-puntaje-caja">
                    <span class="ind-puntaje-val">{{ number_format($resultado->puntaje_tendencia_accidentogena, 1) }}</span>
                    <span class="ind-puntaje-max">/ 24 pts máx</span>
                </div>

                <div class="ind-interpretacion">
                    <strong>Interpretación:</strong><br>
                    "{{ $resultado->interpretacion_accidentogena }}"
                </div>
            </td>

            <!-- INDICADOR 2: PATRÓN DE COMPORTAMIENTO TIPO A -->
            <td class="caja-indicador tipo-a">
                <div class="ind-encabezado">
                    <span class="ind-num">Indicador II · 20 Ítems</span>
                    <div class="ind-titulo">Patrón de Comportamiento Tipo A</div>
                </div>

                <div class="ind-puntaje-caja">
                    <span class="ind-puntaje-val">{{ number_format($resultado->puntaje_patron_tipo_a, 1) }}</span>
                    <span class="ind-puntaje-max">/ 40 pts máx</span>
                </div>

                <div class="ind-interpretacion">
                    <strong>Interpretación:</strong><br>
                    "{{ $resultado->interpretacion_tipo_a }}"
                </div>
            </td>
        </tr>
    </table>

    <!-- 4. TABLA COMPARATIVA DE BAREMOS Y RANGOS NORMATIVOS -->
    <table class="tabla-baremos">
        <tr>
            <th style="width: 50%;">Baremo: Indicador I (Tendencia Accidentógena)</th>
            <th style="width: 50%;">Baremo: Indicador II (Patrón Tipo A)</th>
        </tr>
        <tr>
            <td>
                <strong>&le; 8.5 pts:</strong> No se evidencian rasgos accidentógenos (Favorable)<br>
                <strong>9.0 a 10.5 pts:</strong> Se sugiere indagar / descartar tendencia accidentógena<br>
                <strong>&ge; 11.0 pts:</strong> Presencia de rasgos que sugieren tendencia accidentógena
            </td>
            <td>
                <strong>&lt; 18.0 pts:</strong> No se evidencian rasgos del Patrón Tipo A (Tipo B / Estable)<br>
                <strong>18.0 a 19.5 pts:</strong> Se sugiere indagar / descartar tendencia al Patrón Tipo A<br>
                <strong>&ge; 20.0 pts:</strong> Presencia de rasgos del Patrón Tipo A (Urgencia / Competitividad)
            </td>
        </tr>
    </table>

    <!-- 5. DESGLOSE TABULADO DE LAS 40 AFIRMACIONES -->
    <div class="seccion-titulo">Desglose Detallado de Respuestas (40 Afirmaciones)</div>

    <table class="tabla-preguntas">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">Nº</th>
                <th style="width: 53%;">Afirmación Evaluada</th>
                <th style="width: 17%;">Dimensión Evaluada</th>
                <th style="width: 16%;">Opción Elegida</th>
                <th style="width: 10%; text-align: center;">Puntos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($afirmaciones as $num => $texto)
                @php
                    $resp = $respuestasPorId->get($num);
                    $opcion = $resp ? strtoupper($resp->opcion_seleccionada) : 'N';
                    $infoOpcion = $opciones[$opcion] ?? ['nombre' => $opcion, 'puntaje' => 0.0];
                    $esAcc = in_array($num, $preguntasAccidentogena, true);
                    $esTipoA = in_array($num, $preguntasTipoA, true);
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $num }}</td>
                    <td>{{ $texto }}</td>
                    <td>
                        @if($esAcc)
                            <span class="badge-dim badge-acc">Accidentógena</span>
                        @elseif($esTipoA)
                            <span class="badge-dim badge-tipo-a">Patrón Tipo A</span>
                        @else
                            <span class="badge-dim badge-control">Control</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $infoOpcion['nombre'] }}</strong> ({{ $opcion }})
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($esAcc || $esTipoA)
                            {{ number_format($infoOpcion['puntaje'], 1) }}
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 6. VALIDACIÓN Y FIRMA PROFESIONAL -->
    <table class="seccion-firmas">
        <tr>
            <td>
                <!-- Candidato Evaluado -->
                <div style="font-size: 7.5pt; color: #64748b; margin-bottom: 4px;">Firma registrada en Consentimiento GH-FO-025:</div>
                <div class="linea-firma"></div>
                <div class="texto-firma">
                    <strong>{{ $evaluacion->nombre_completo }}</strong><br>
                    C.C. {{ $evaluacion->identificacion }} de {{ $evaluacion->lugar_expedicion }}<br>
                    Candidato(a) Evaluado(a)
                </div>
            </td>
            <td>
                <!-- Psicóloga Oficial Responsable -->
                @if(!empty($firmaPsicologaBase64))
                    <img src="{{ $firmaPsicologaBase64 }}" class="img-firma" alt="Firma Profesional">
                @else
                    <div style="height: 38px;"></div>
                @endif
                <div class="linea-firma"></div>
                <div class="texto-firma">
                    <strong>{{ $psicologo->nombre_completo ?? 'Especialista en Psicología y Selección' }}</strong><br>
                    Psicología y Selección de Personal<br>
                    C.I. Piscícola New York S.A.
                </div>
            </td>
        </tr>
    </table>

    <div class="pie-pagina">
        Documento confidencial emitido por el Sistema Integral de Selección y Capacitaciones · C.I. Piscícola New York S.A.S. · Fecha de emisión: {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>
</html>

