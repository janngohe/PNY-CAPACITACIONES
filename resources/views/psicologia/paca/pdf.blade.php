<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Resultados PAC-A - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 18mm 16mm 18mm 16mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 9.5pt;
            line-height: 1.45;
        }

        /* ENCABEZADO INSTITUCIONAL */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .logo-col {
            width: 25%;
            text-align: center;
        }
        .logo-col img {
            max-width: 130px;
            max-height: 52px;
        }
        .titulo-col {
            width: 50%;
            text-align: center;
        }
        .titulo-col h2 {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .titulo-col h1 {
            margin: 3px 0 0 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .meta-col {
            width: 25%;
            font-size: 8pt;
            line-height: 1.3;
            padding: 0 !important;
        }
        .tabla-meta {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-meta td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2.5px 5px;
        }
        .tabla-meta tr:last-child td {
            border-bottom: none;
        }

        /* FICHA DE DATOS DEL CANDIDATO */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9pt;
        }
        .tabla-datos td {
            border: 1px solid #000;
            padding: 5px 8px;
            background-color: #fafafa;
        }

        /* RESULTADOS: INDICADOR 1 E INDICADOR 2 */
        .tabla-resultados {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .tabla-resultados th {
            background-color: #f1f5f9;
            color: #000;
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
        }
        .tabla-resultados td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
            font-size: 9pt;
        }
        .ind-puntaje {
            font-size: 10pt;
            margin-bottom: 4px;
        }
        .ind-sub {
            font-size: 8pt;
            color: #444;
            margin-bottom: 8px;
        }
        .ind-interpretacion {
            font-size: 8.5pt;
            line-height: 1.4;
            background-color: #fafafa;
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: justify;
        }

        /* DESGLOSE DE LAS 40 AFIRMACIONES */
        .titulo-seccion {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 5px 0;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #000;
        }
        .tabla-respuestas {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.2pt;
            line-height: 1.3;
        }
        .tabla-respuestas th {
            background-color: #f1f5f9;
            color: #000;
            border: 1px solid #000;
            padding: 3.5px 5px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .tabla-respuestas td {
            border: 1px solid #ccc;
            padding: 3px 5px;
            vertical-align: middle;
        }
        .tabla-respuestas tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* SECCIÓN DE FIRMAS */
        .tabla-firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .tabla-firmas td {
            vertical-align: bottom;
            text-align: center;
            padding: 10px 20px;
        }
        .linea-firma {
            border-top: 1.5px solid #000;
            margin: 0 15px 6px 15px;
        }
        .img-firma {
            max-height: 48px;
            max-width: 160px;
            margin-bottom: 3px;
        }
        .texto-firma {
            font-size: 8.5pt;
            color: #000;
            line-height: 1.35;
        }
        .pie-pagina {
            margin-top: 10px;
            font-size: 7pt;
            color: #555;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <!-- 1. ENCABEZADO INSTITUCIONAL OFICIAL -->
    <table class="tabla-encabezado">
        <tr>
            <td class="logo-col">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo PNY">
                @else
                    <strong>C.I. PISCÍCOLA NEW YORK S.A.</strong>
                @endif
            </td>
            <td class="titulo-col">
                <h2>GESTIÓN HUMANA · PSICOLOGÍA ORGANIZACIONAL</h2>
                <h1>INFORME DE EVALUACIÓN PAC-A</h1>
            </td>
            <td class="meta-col">
                <table class="tabla-meta">
                    <tr>
                        <td><strong>CÓDIGO:</strong> GH-FO-PAC</td>
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

    <!-- 2. DATOS DEL CANDIDATO EVALUADO -->
    <table class="tabla-datos">
        <tr>
            <td style="width: 40%;"><strong>Candidato(a):</strong> {{ $evaluacion->nombre_completo }}</td>
            <td style="width: 25%;"><strong>Cédula:</strong> {{ $evaluacion->identificacion }} ({{ $evaluacion->lugar_expedicion }})</td>
            <td style="width: 23%;"><strong>Área / Cargo:</strong> {{ $evaluacion->cargo_aspira }}</td>
            <td style="width: 12%;"><strong>Edad:</strong> {{ $evaluacion->edad }} años</td>
        </tr>
    </table>

    <!-- 3. RESULTADOS: SOLO INDICADOR 1 E INDICADOR 2 -->
    <table class="tabla-resultados">
        <thead>
            <tr>
                <th style="width: 50%;">INDICADOR I: TENDENCIA ACCIDENTÓGENA</th>
                <th style="width: 50%;">INDICADOR II: PATRÓN DE COMPORTAMIENTO TIPO A</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- INDICADOR 1 -->
                <td>
                    <div class="ind-puntaje">
                        Puntaje Obtenido: <strong>{{ number_format($resultado->puntaje_tendencia_accidentogena, 1) }} / 24 pts máx</strong>
                    </div>
                    <div class="ind-sub">Total 12 Afirmaciones Evaluadas</div>
                    <div class="ind-interpretacion">
                        <strong>Interpretación Institucional:</strong><br>
                        "{{ $resultado->interpretacion_accidentogena }}"
                    </div>
                </td>

                <!-- INDICADOR 2 -->
                <td>
                    <div class="ind-puntaje">
                        Puntaje Obtenido: <strong>{{ number_format($resultado->puntaje_patron_tipo_a, 1) }} / 40 pts máx</strong>
                    </div>
                    <div class="ind-sub">Total 20 Afirmaciones Evaluadas</div>
                    <div class="ind-interpretacion">
                        <strong>Interpretación Institucional:</strong><br>
                        "{{ $resultado->interpretacion_tipo_a }}"
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 4. DESGLOSE DETALLADO DE RESPUESTAS (SIN COLUMNA DE PUNTOS) -->
    <div class="titulo-seccion">Desglose de Respuestas (40 Afirmaciones)</div>

    <table class="tabla-respuestas">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">Nº</th>
                <th style="width: 58%;">Afirmación Evaluada</th>
                <th style="width: 22%;">Dimensión Evaluada</th>
                <th style="width: 15%; text-align: center;">Respuesta Elegida</th>
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
                            Tendencia Accidentógena
                        @elseif($esTipoA)
                            Patrón Tipo A
                        @else
                            Control
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $infoOpcion['nombre'] }}</strong> ({{ $opcion }})
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 5. FIRMA Y CONSTANCIA PROFESIONAL -->
    <table class="tabla-firmas">
        <tr>
            <td style="width: 50%;">
                <div style="font-size: 8pt; color: #444; margin-bottom: 6px;">Firma registrada en Consentimiento GH-FO-025:</div>
                <div class="linea-firma"></div>
                <div class="texto-firma">
                    <strong>{{ $evaluacion->nombre_completo }}</strong><br>
                    C.C. {{ $evaluacion->identificacion }} de {{ $evaluacion->lugar_expedicion }}<br>
                    Candidato(a) Evaluado(a)
                </div>
            </td>
            <td style="width: 50%;">
                @if(!empty($firmaPsicologaBase64))
                    <img src="{{ $firmaPsicologaBase64 }}" class="img-firma" alt="Firma Profesional">
                @else
                    <div style="height: 48px;"></div>
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
        Documento confidencial emitido por el Sistema de Selección y Talento Humano · C.I. Piscícola New York S.A.S. · Fecha de emisión: {{ now()->format('d/m/Y h:i A') }}
    </div>

</body>
</html>
