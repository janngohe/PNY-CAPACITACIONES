<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuestionario VALANTI - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 18mm 16mm 18mm 16mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000000;
            font-size: 9pt;
            line-height: 1.4;
        }

        /* ENCABEZADO INSTITUCIONAL IDÉNTICO AL CONSENTIMIENTO */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .tabla-encabezado td {
            border: 1px solid #000000;
            padding: 4px 8px;
            vertical-align: middle;
        }
        .logo-col {
            width: 25%;
            text-align: center;
        }
        .logo-col img {
            max-width: 125px;
            max-height: 48px;
        }
        .titulo-col {
            width: 50%;
            text-align: center;
        }
        .titulo-col h2 {
            margin: 0;
            font-size: 9pt;
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
            font-size: 7.5pt;
            line-height: 1.3;
            padding: 0 !important;
        }
        .tabla-meta {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-meta td {
            border: none;
            border-bottom: 1px solid #000000;
            padding: 2.5px 5px;
        }
        .tabla-meta tr:last-child td {
            border-bottom: none;
        }

        /* FICHA DE DATOS DEL EVALUADO */
        .tabla-ficha {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .tabla-ficha td {
            border: 1px solid #000000;
            padding: 4px 8px;
            background-color: #fafafa;
        }

        /* CAJA DEL GRÁFICO RADIAL */
        .caja-grafico {
            border: 1px solid #000000;
            padding: 8px 10px 6px 10px;
            text-align: center;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .titulo-grafico {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            color: #000000;
            text-transform: uppercase;
        }
        .leyenda-box {
            display: inline-block;
            border: 1px solid #000000;
            padding: 4px 12px;
            margin-bottom: 4px;
            font-size: 8pt;
            background-color: #ffffff;
        }

        /* CAJA DE INTERPRETACIÓN NARRATIVA */
        .caja-narrativa {
            border: 1px solid #000000;
            background-color: #fafafa;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 8pt;
            line-height: 1.4;
            text-align: justify;
            page-break-inside: avoid;
        }
        .caja-narrativa p {
            margin: 0 0 4px 0;
        }
        .caja-narrativa p:last-child {
            margin-bottom: 0;
        }

        /* TABLA DE RESULTADOS OFICIALES (SIN BAREMO) */
        .tabla-resultados {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            page-break-inside: avoid;
        }
        .tabla-resultados th {
            background-color: #f2f2f2;
            color: #000000;
            border: 1px solid #000000;
            padding: 5px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .tabla-resultados td {
            border: 1px solid #000000;
            padding: 4px 6px;
            text-align: center;
        }
        .tabla-resultados td.col-dim {
            text-align: left;
            font-weight: bold;
        }
        .fila-total td {
            background-color: #fafafa;
            font-weight: bold;
            border-top: 1.5px solid #000000;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO INSTITUCIONAL -->
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
                <h1>CUESTIONARIO VALANTI</h1>
            </td>
            <td class="meta-col">
                <table class="tabla-meta" style="font-size: 7.5pt;">
                    <tr>
                        <td><strong>CÓDIGO:</strong> GH-FO-VAL</td>
                    </tr>
                    <tr>
                        <td><strong>VERSIÓN:</strong> 01</td>
                    </tr>
                    <tr>
                        <td><strong>FECHA:</strong> {{ ($resultado->finalizada_at ?? $evaluacion->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>PÁGINA:</strong> 1 de 1</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL CANDIDATO EVALUADO -->
    <table class="tabla-ficha">
        <tr>
            <td style="width: 32%;"><strong>Evaluado(a):</strong> {{ $evaluacion->nombre_completo }}</td>
            <td style="width: 20%;"><strong>Cédula:</strong> {{ $evaluacion->identificacion }}</td>
            <td style="width: 22%;"><strong>Cargo / Área:</strong> {{ $evaluacion->cargo_aspira }}</td>
            <td style="width: 14%;"><strong>Sede:</strong> {{ $evaluacion->sede?->nombre ?? 'Principal' }}</td>
            <td style="width: 12%;"><strong>Edad:</strong> {{ $evaluacion->edad }} años</td>
        </tr>
    </table>

    <!-- GRÁFICO RADIAL DE 5 EJES -->
    <div class="caja-grafico">
        <div class="titulo-grafico">Gráfico de Valores Psicotécnicos (VALANTI)</div>

        <div class="leyenda-box">
            <span style="color: #0026c3; font-weight: bold;">
                <span style="display: inline-block; width: 10px; height: 3px; background-color: #0026c3; vertical-align: middle;"></span>
                <span style="display: inline-block; width: 6px; height: 6px; background-color: #0026c3; vertical-align: middle; margin: 0 1px;"></span>
                <span style="display: inline-block; width: 10px; height: 3px; background-color: #0026c3; vertical-align: middle; margin-right: 3px;"></span>
                Perfil ideal
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span style="color: #000000; font-weight: bold;">
                <span style="display: inline-block; width: 10px; height: 3px; background-color: #000000; vertical-align: middle;"></span>
                <span style="display: inline-block; width: 6px; height: 6px; background-color: #000000; vertical-align: middle; margin: 0 1px;"></span>
                <span style="display: inline-block; width: 10px; height: 3px; background-color: #000000; vertical-align: middle; margin-right: 3px;"></span>
                {{ $evaluacion->nombre_completo }} (Puntaje estándar)
            </span>
        </div>

        <div style="margin-top: 2px;">
            <img src="{{ $radarPngBase64 }}" style="width: 440px; height: 287px;" alt="Gráfico Cuestionario VALANTI">
        </div>
    </div>

    <!-- INTERPRETACIÓN CUALITATIVA NARRATIVA -->
    <div class="caja-narrativa">
        <p style="font-weight: bold; color: #000000;">{{ $narrativa['resumen'] }}</p>
        @foreach($narrativa['parrafos'] as $parrafo)
            <p>{{ $parrafo }}</p>
        @endforeach
    </div>

    <!-- TABLA DE RESULTADOS DE LAS 5 DIMENSIONES (SIN INFORMACIÓN DE BAREMO) -->
    <table class="tabla-resultados">
        <thead>
            <tr>
                <th style="text-align: left; width: 28%;">Dimensión / Valor</th>
                <th style="width: 14%;">Puntaje Directo (PD)</th>
                <th style="width: 14%;">Puntaje Estándar (PE)</th>
                <th style="width: 22%;">Nivel de Presencia</th>
                <th style="width: 11%;">Perfil Ideal</th>
                <th style="width: 11%;">Distancia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($baremos as $dimKey => $info)
                @php
                    $clave = $info['clave'];
                    $pd = $resultado->{"pd_{$clave}"};
                    $pe = $resultado->{"pe_{$clave}"};
                    $nivel = $resultado->{"nivel_{$clave}"};
                    $ideal = (int) $info['perfil_esperado_default'];
                    $distancia = $pe - $ideal;
                    $signo = $distancia > 0 ? '+' : '';
                    $asteriscos = \App\Constants\ValantiPreguntas::asteriscosPuntajeEstandar($pe);
                @endphp
                <tr>
                    <td class="col-dim">{{ $info['nombre'] }} ({{ $info['area'] }})</td>
                    <td style="font-weight: bold;">{{ $pd }}</td>
                    <td style="font-weight: bold;">{{ $pe }}</td>
                    <td style="font-weight: bold;">
                        {{ $nivel }}
                        <span style="color: #dc2626; font-family: monospace; font-size: 8pt; letter-spacing: 1px; margin-left: 2px;">{{ $asteriscos }}</span>
                    </td>
                    <td style="font-weight: bold; color: #0026c3;">{{ $ideal }}</td>
                    <td style="font-weight: bold; color: {{ $distancia >= 0 ? '#047857' : '#b45309' }};">
                        {{ $signo }}{{ $distancia }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fila-total">
                <td style="text-align: left;">SUMATORIA CONTROL</td>
                <td>{{ $resultado->pd_total }} / 90 pts</td>
                <td colspan="4" style="text-align: right; font-size: 7.5pt; font-weight: normal; color: #444444;">
                    Distancia = Puntaje Estándar (PE) &minus; Perfil Ideal Empresa
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
