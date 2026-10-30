<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consentimiento Informado - {{ $candidato->numero_identificacion }}</title>
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
            font-size: 12pt;
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
        .meta-fila {
            border-bottom: 1px solid #000;
            padding: 2px 0;
        }
        .meta-fila:last-child {
            border-bottom: none;
        }

        .datos-generales {
            margin-bottom: 25px;
            font-size: 11pt;
        }
        .campo-linea {
            margin-bottom: 12px;
        }
        .campo-label {
            font-weight: bold;
        }
        .campo-valor {
            border-bottom: 1px solid #000;
            padding: 0 10px 2px 10px;
            display: inline-block;
            min-width: 250px;
        }

        .parrafo-cuerpo {
            text-align: justify;
            font-size: 11.5pt;
            line-height: 1.8;
            margin: 35px 0;
        }
        .texto-autoriza {
            margin-top: 45px;
            font-weight: bold;
            font-size: 11.5pt;
        }
        .seccion-firma {
            margin-top: 50px;
            margin-bottom: 60px;
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
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo PNY">
                @else
                    <strong>C.I. PISCÍCOLA NEW YORK</strong>
                @endif
            </td>
            <td class="titulo-col">
                <h2>GESTIÓN HUMANA</h2>
                <h1>CONSENTIMIENTO INFORMADO</h1>
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
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 4px;"><strong>FECHA:</strong> 10/11/2023</td>
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
            <span class="campo-label">Ciudad/Municipio:</span>
            <span class="campo-valor">{{ $consentimiento->ciudad_municipio }}</span>
        </div>
        <div class="campo-linea">
            <span class="campo-label">Fecha:</span>
            <span class="campo-valor">{{ $consentimiento->fecha_consentimiento->format('d/m/Y') }}</span>
        </div>
    </div>

    <!-- CUERPO DEL CONSENTIMIENTO -->
    <div class="parrafo-cuerpo">
        Yo <strong>{{ $candidato->nombre_completo }}</strong>, identificado con cedula de ciudadanía No. <strong>{{ $candidato->numero_identificacion }}</strong> expedida en <strong>{{ $candidato->lugar_expedicion }}</strong>, <strong>autorizo</strong> realizar las pruebas psicotécnicas como proceso de selección y contratación de personal en la empresa <strong>C.I. PISCÍCOLA NEW YORK S.A.</strong> El resultado de la aplicación de los instrumentos de evaluación psicotécnicos es de total confidencialidad.
    </div>

    <!-- SECCIÓN DE AUTORIZACIÓN Y FIRMA FÍSICA -->
    <div class="texto-autoriza">
        AUTORIZA,
    </div>

    <div class="seccion-firma">
        @if($consentimiento->firma_digital_ruta && file_exists(storage_path('app/public/' . $consentimiento->firma_digital_ruta)))
            <div style="margin-bottom: -10px;">
                <img src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $consentimiento->firma_digital_ruta))) }}"
                     style="max-height: 55px; max-width: 250px;" alt="Firma Digital">
            </div>
        @endif
        <div class="linea-firma"></div>
        <div class="firma-texto">
            @if($consentimiento->estado === 'FIRMADO_DIGITAL')
                <strong>Firma:</strong> <span style="font-size: 8.5pt; color: #0369a1;">[Firmado Digitalmente el {{ $consentimiento->fecha_firma_digital ? $consentimiento->fecha_firma_digital->format('d/m/Y h:i A') : $consentimiento->fecha_consentimiento->format('d/m/Y') }}]</span><br>
            @else
                <strong>Firma:</strong> ________________________________<br>
            @endif
            <span style="display: inline-block; margin-top: 6px;">
                <strong>Cedula:</strong> {{ $candidato->numero_identificacion }}
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
                    <td>psicóloga</td>
                    <td>Jefe Gestión Humana</td>
                    <td>Jefe Gestión Humana</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
