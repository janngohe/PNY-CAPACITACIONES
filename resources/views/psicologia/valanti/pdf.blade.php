<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cuestionario VALANTI - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #0f172a;
            font-size: 9pt;
            line-height: 1.4;
        }
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 4px 8px;
            vertical-align: middle;
        }
        .logo-col {
            width: 24%;
            text-align: center;
        }
        .logo-col img {
            max-width: 120px;
            max-height: 44px;
        }
        .titulo-col {
            width: 52%;
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
            font-size: 11.5pt;
            font-weight: bold;
        }
        .meta-col {
            width: 24%;
            font-size: 7.5pt;
            line-height: 1.3;
        }
        .tabla-ficha {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .tabla-ficha td {
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            background-color: #f8fafc;
        }
        .caja-grafico {
            border: 1px solid #cbd5e1;
            padding: 10px 10px 6px 10px;
            text-align: center;
            margin-bottom: 10px;
        }
        .titulo-grafico {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 6px 0;
            color: #000000;
        }
        .leyenda-box {
            display: inline-block;
            border: 1px solid #475569;
            padding: 5px 14px;
            margin-bottom: 4px;
            font-size: 8.5pt;
            background-color: #ffffff;
        }
        .caja-narrativa {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 10px 12px;
            margin-bottom: 10px;
            font-size: 8.5pt;
            line-height: 1.45;
            text-align: justify;
        }
        .caja-narrativa p {
            margin: 0 0 5px 0;
        }
        .caja-narrativa p:last-child {
            margin-bottom: 0;
        }
        .tabla-resultados {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        .tabla-resultados th {
            background-color: #0f172a;
            color: #ffffff;
            border: 1px solid #0f172a;
            padding: 5px 5px;
            font-size: 7pt;
            text-transform: uppercase;
            text-align: center;
        }
        .tabla-resultados td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            text-align: center;
        }
        .tabla-resultados td.col-dim {
            text-align: left;
            font-weight: bold;
        }
        .fila-total td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 1.5px solid #0f172a;
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
            <td class="meta-col" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt;">
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>PRUEBA:</strong> VALANTI</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>BAREMO:</strong> Nacional 1997 (n=730)</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>FECHA:</strong> {{ ($resultado->finalizada_at ?? $evaluacion->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 5px;"><strong>SESIÓN:</strong> {{ $evaluacion->codigo_sesion }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- DATOS DEL CANDIDATO EVALUADO -->
    <table class="tabla-ficha">
        <tr>
            <td style="width: 38%;"><strong>Candidato(a):</strong> {{ $evaluacion->nombre_completo }}</td>
            <td style="width: 24%;"><strong>Cédula:</strong> {{ $evaluacion->identificacion }}</td>
            <td style="width: 24%;"><strong>Cargo:</strong> {{ $evaluacion->cargo_aspira }}</td>
            <td style="width: 14%;"><strong>Edad:</strong> {{ $evaluacion->edad }} años</td>
        </tr>
    </table>

    <!-- GRÁFICO RADIAL DE 5 EJES -->
    <div class="caja-grafico">
        <div class="titulo-grafico">Cuestionario VALANTI</div>

        <div class="leyenda-box">
            <span style="color: #001c9b; font-weight: bold;">- - &#9670; - - Perfil ideal</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span style="color: #000000; font-weight: bold;">&#8212;&#9632;&#8212; {{ $evaluacion->nombre_completo }} (Puntaje estándar)</span>
        </div>

        <div style="margin-top: 2px;">
            <img src="{{ $radarPngBase64 }}" style="width: 470px; height: 307px;" alt="Gráfico Cuestionario VALANTI">
        </div>
    </div>

    <!-- INTERPRETACIÓN CUALITATIVA NARRATIVA -->
    <div class="caja-narrativa">
        <p style="font-weight: bold; color: #0f172a;">{{ $narrativa['resumen'] }}</p>
        @foreach($narrativa['parrafos'] as $parrafo)
            <p>{{ $parrafo }}</p>
        @endforeach
    </div>

    <!-- TABLA ESTADÍSTICA DE LAS 5 DIMENSIONES -->
    <table class="tabla-resultados">
        <thead>
            <tr>
                <th style="text-align: left; width: 22%;">Dimensión / Valor</th>
                <th style="width: 12%;">Puntaje Directo (PD)</th>
                <th style="width: 18%;">Norma Nacional (Media / D.S.)</th>
                <th style="width: 13%;">Puntaje Estándar (PE)</th>
                <th style="width: 15%;">Nivel</th>
                <th style="width: 10%;">Perfil Ideal</th>
                <th style="width: 10%;">Distancia</th>
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
                @endphp
                <tr>
                    <td class="col-dim">{{ $info['nombre'] }} ({{ $info['area'] }})</td>
                    <td style="font-weight: bold;">{{ $pd }}</td>
                    <td>{{ number_format($info['media'], 2) }} / {{ number_format($info['desviacion'], 2) }}</td>
                    <td style="font-weight: bold;">{{ $pe }}</td>
                    <td style="font-weight: bold;">{{ $nivel }}</td>
                    <td style="color: #001c9b; font-weight: bold;">{{ $ideal }}</td>
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
                <td colspan="5" style="text-align: left; font-size: 7.5pt; font-weight: normal; color: #475569;">
                    PE = round(50 + 10 * ((PD - Media) / D.S.)) &nbsp;|&nbsp; Distancia = PE - Perfil Ideal
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
