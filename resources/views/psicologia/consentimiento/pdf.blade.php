<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización Pruebas Psicotécnicas GH-FO-025 - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 11pt;
            line-height: 1.6;
        }
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .logo-col {
            width: 25%;
            text-align: center;
        }
        .logo-col img {
            max-width: 140px;
            max-height: 60px;
        }
        .titulo-col {
            width: 50%;
            text-align: center;
        }
        .titulo-col h2 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .titulo-col h1 {
            margin: 4px 0 0 0;
            font-size: 11.5pt;
            font-weight: bold;
        }
        .meta-col {
            width: 25%;
            font-size: 8pt;
            line-height: 1.3;
        }
        .meta-col strong {
            display: inline-block;
            width: 65px;
        }
        .datos-generales {
            margin-bottom: 25px;
            font-size: 11pt;
        }
        .campo-linea {
            margin-bottom: 10px;
        }
        .campo-label {
            font-weight: bold;
        }
        .campo-valor {
            border-bottom: 1px solid #000;
            padding: 0 10px 2px 10px;
            display: inline-block;
            min-width: 240px;
        }
        .parrafo-cuerpo {
            text-align: justify;
            font-size: 11.5pt;
            line-height: 1.8;
            margin: 35px 0;
        }
        .texto-autoriza {
            margin-top: 40px;
            font-weight: bold;
            font-size: 11.5pt;
        }
        .seccion-firma {
            margin-top: 35px;
            margin-bottom: 50px;
        }
        .linea-firma {
            width: 320px;
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
        }
        .firma-texto {
            font-size: 11pt;
        }
        .tabla-control {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 40px;
        }
        .tabla-control th, .tabla-control td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: center;
        }
        .tabla-control th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .control-titulo {
            font-size: 8.5pt;
            font-weight: bold;
            margin-bottom: 4px;
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
                <h2>GESTIÓN HUMANA</h2>
                <h1>AUTORIZACIÓN PARA LA REALIZACIÓN DE PRUEBAS PSICOTÉCNICAS</h1>
            </td>
            <td class="meta-col" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 4px;"><strong>CÓDIGO:</strong> GH-FO-025</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 4px;"><strong>VERSIÓN:</strong> 01</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 4px;"><strong>FECHA:</strong> {{ ($evaluacion->fecha_hora_aceptacion ?? $evaluacion->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 4px;"><strong>PÁGINA:</strong> 1 de 1</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- DATOS GENERALES -->
    <div class="datos-generales">
        <div class="campo-linea">
            <span class="campo-label">Cargo al que aspira:</span>
            <span class="campo-valor">{{ $evaluacion->cargo_aspira }}</span>
        </div>
        <div class="campo-linea">
            <span class="campo-label">Edad:</span>
            <span class="campo-valor">{{ $evaluacion->edad }} años (Fecha Nac.: {{ $evaluacion->fecha_nacimiento?->format('d/m/Y') }})</span>
        </div>
        <div class="campo-linea">
            <span class="campo-label">Fecha y Hora de Aceptación:</span>
            <span class="campo-valor">{{ ($evaluacion->fecha_hora_aceptacion ?? $evaluacion->created_at)->format('d/m/Y h:i A') }}</span>
        </div>
    </div>

    <!-- CUERPO DE LA AUTORIZACIÓN -->
    <div class="parrafo-cuerpo">
        Yo, <strong>{{ $evaluacion->nombre_completo }}</strong>, identificado(a) con cédula de ciudadanía No. <strong>{{ $evaluacion->identificacion }}</strong>, expedida en <strong>{{ $evaluacion->lugar_expedicion }}</strong>, autorizo realizar las pruebas psicotécnicas como proceso de selección y contratación de personal en la empresa <strong>C.I. PISCÍCOLA NEW YORK S.A.</strong>
    </div>

    <div class="parrafo-cuerpo" style="margin-top: 15px;">
        El resultado de la aplicación de los instrumentos de evaluación psicotécnicos es de total confidencialidad.
    </div>

    <!-- SECCIÓN DE AUTORIZACIÓN Y FIRMA -->
    <div class="texto-autoriza">
        AUTORIZA:
    </div>

    <div class="seccion-firma">
        @if(!empty($firmaBase64))
            <div style="margin-bottom: -8px;">
                <img src="{{ $firmaBase64 }}" style="max-height: 65px; max-width: 260px;" alt="Firma Digital">
            </div>
        @endif
        <div class="linea-firma"></div>
        <div class="firma-texto">
            <strong>Firma:</strong>
            <span style="font-size: 8.5pt; color: #0369a1;">
                [Firmado Digitalmente el {{ ($evaluacion->fecha_hora_aceptacion ?? $evaluacion->created_at)->format('d/m/Y h:i A') }}]
            </span>
            <br>
            <span style="display: inline-block; margin-top: 6px;">
                <strong>Cédula:</strong> {{ $evaluacion->identificacion }}
            </span>
        </div>
    </div>

    <!-- CONTROL DE CAMBIOS -->
    <div style="position: absolute; bottom: 0; left: 0; right: 0;">
        <div class="control-titulo">CONTROL DE CAMBIOS</div>
        <table class="tabla-control">
            <thead>
                <tr>
                    <th style="width: 8%;">PAG</th>
                    <th style="width: 32%;">DESCRIPCION DEL CAMBIO</th>
                    <th style="width: 10%;">VERSION</th>
                    <th style="width: 14%;">VIGENCIA</th>
                    <th style="width: 12%;">ELABORO</th>
                    <th style="width: 12%;">REVISO</th>
                    <th style="width: 12%;">APROBO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1 de 1</td>
                    <td>Emisión del documento</td>
                    <td>01</td>
                    <td>10/11/2023</td>
                    <td>Psicóloga</td>
                    <td>Jefe Gestión Humana</td>
                    <td>Jefe Gestión Humana</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>

