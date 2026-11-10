<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta Oficial de Asistencia - {{ $sesion->codigo_sesion }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: letter portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 9pt;
            line-height: 1.4;
        }

        /* 1. Encabezado Institucional (Idéntico a Consentimiento GH-FO-025) */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .logo-col {
            width: 22%;
            text-align: center;
        }
        .logo-col img {
            max-width: 130px;
            max-height: 55px;
        }
        .titulo-col {
            width: 54%;
            text-align: center;
        }
        .titulo-col h2 {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .titulo-col h1 {
            margin: 4px 0 0 0;
            font-size: 10.5pt;
            font-weight: bold;
        }
        .meta-col {
            width: 24%;
            font-size: 8pt;
            line-height: 1.3;
        }
        .meta-col strong {
            display: inline-block;
            width: 65px;
        }

        /* 2. Ficha Técnica / Información de la Inducción */
        .seccion-titulo {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            color: #000;
        }
        .tabla-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        .tabla-info td {
            border: 1px solid #000;
            padding: 4px 7px;
            vertical-align: middle;
        }
        .info-label {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            width: 20%;
        }
        .info-val {
            width: 30%;
        }

        /* 3. Tabla de Participantes */
        .tabla-participantes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
            page-break-inside: auto;
        }
        .tabla-participantes thead {
            display: table-header-group;
        }
        .tabla-participantes tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .tabla-participantes th {
            border: 1px solid #000;
            background-color: #f2f2f2;
            padding: 5px 4px;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
        }
        .tabla-participantes td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .col-num {
            width: 5%;
            text-align: center;
            font-weight: bold;
        }
        .col-nombre {
            width: 33%;
        }
        .col-cedula {
            width: 18%;
            text-align: center;
            font-family: 'Helvetica', monospace;
        }
        .col-area {
            width: 22%;
        }
        .col-firma {
            width: 22%;
            text-align: center;
            padding: 2px 4px !important;
        }
        .firma-img {
            max-height: 32px;
            max-width: 110px;
            display: block;
            margin: 0 auto;
        }

        /* Pie de página */
        .nota-pie {
            font-size: 7pt;
            color: #555;
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #999;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    @php
        $logoBase64 = null;
        $rutaLogo = public_path('images/Logo.png');
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
        }
        $fechaDocumento = $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y') : now()->format('d/m/Y');
    @endphp

    <!-- 1. ENCABEZADO INSTITUCIONAL -->
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
                <h2>SISTEMA INTEGRADO DE GESTIÓN </h2>
                <h1>ACTA DE ASISTENCIA A INDUCCIÓN</h1>
            </td>
            <td class="meta-col" style="padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 8pt;">
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>CÓDIGO:</strong> SST-FOR-018</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>VERSIÓN:</strong> 04</td>
                    </tr>
                    <tr>
                        <td style="border: none; border-bottom: 1px solid #000; padding: 2px 5px;"><strong>FECHA:</strong> {{ $fechaDocumento }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 5px;"><strong>SESIÓN:</strong> {{ $sesion->codigo_sesion }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 2. INFORMACIÓN DE LA INDUCCIÓN -->
    <div class="seccion-titulo">1. INFORMACIÓN DE LA INDUCCIÓN Y SESIÓN</div>
    <table class="tabla-info">
        <tr>
            <td class="info-label" style="width: 20%;">Tema / Inducción:</td>
            <td class="info-val" colspan="3" style="font-weight: bold; font-size: 9.5pt;">
                {{ $sesion->induccion->titulo }}
            </td>
        </tr>
        <tr>
            <td class="info-label" style="width: 20%;">Facilitador(a):</td>
            <td class="info-val" style="width: 36%;">
                {{ $sesion->facilitador->nombre_completo ?? 'N/A' }}
                @if($sesion->facilitador?->cargo)
                    <span style="font-size: 7.5pt; color: #444;">({{ $sesion->facilitador->cargo }})</span>
                @endif
            </td>
            <td class="info-label" style="width: 18%;">Sede:</td>
            <td class="info-val" style="width: 26%;">
                {{ $sesion->sede->nombre ?? 'Sede Principal' }}
                @if($sesion->sede?->ciudad)
                    - {{ $sesion->sede->ciudad }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Fecha y Hora:</td>
            <td class="info-val">
                Inicio: {{ $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}
                @if($sesion->fecha_cierre)
                    <br>Cierre: {{ $sesion->fecha_cierre->format('d/m/Y h:i A') }}
                @endif
            </td>
            <td class="info-label">Participantes:</td>
            <td class="info-val">
                Total: <strong>{{ $sesion->asistencias->count() }}</strong>
                (PNY: {{ $sesion->asistencias->where('es_externo', false)->count() }} &middot; Ext: {{ $sesion->asistencias->where('es_externo', true)->count() }})
            </td>
        </tr>
    </table>

    <!-- 3. PARTICIPANTES Y FIRMAS DIGITALES -->
    <div class="seccion-titulo">2. REGISTRO OFICIAL DE PARTICIPANTES Y FIRMAS</div>
    <table class="tabla-participantes">
        <thead>
            <tr>
                <th class="col-num">#</th>
                <th class="col-nombre">Nombre Completo</th>
                <th class="col-cedula">Número de Cédula</th>
                <th class="col-area">Área / Empresa</th>
                <th class="col-firma">Firma Digital</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesion->asistencias as $idx => $asist)
                @php
                    $firmaImg = null;
                    if (!empty($asist->firma_digital)) {
                        if (str_starts_with($asist->firma_digital, 'data:image')) {
                            $firmaImg = $asist->firma_digital;
                        } else {
                            $diskPath = storage_path('app/public/' . $asist->firma_digital);
                            if (file_exists($diskPath)) {
                                $mime = mime_content_type($diskPath) ?: 'image/png';
                                $firmaImg = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($diskPath));
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="col-num">{{ $idx + 1 }}</td>
                    <td class="col-nombre">
                        <strong>{{ $asist->nombre_completo }}</strong>
                        @if($asist->cargo)
                            <div style="font-size: 7.5pt; color: #444;">{{ $asist->cargo }}</div>
                        @endif
                    </td>
                    <td class="col-cedula">
                        {{ $asist->identificacion }}
                    </td>
                    <td class="col-area">
                        {{ $asist->area ?: ($asist->empresa_externa ?: 'N/A') }}
                        @if($asist->es_externo)
                            <span style="font-size: 7.5pt; color: #b45309; font-weight: bold;">(Contratista)</span>
                        @endif
                    </td>
                    <td class="col-firma">
                        @if($firmaImg)
                            <img src="{{ $firmaImg }}" class="firma-img" alt="Firma">
                            <div style="font-size: 6.5pt; color: #555; margin-top: 1px;">
                                [Firmado {{ $asist->fecha_registro ? $asist->fecha_registro->format('d/m/Y h:i A') : '' }}]
                            </div>
                        @else
                            <span style="font-size: 7.5pt; color: #888;">Sin Firma</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 18px; color: #666;">
                        No se registraron participantes en esta sesión.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Nota al pie -->
    <div class="nota-pie">
        Este documento constituye constancia y evidencia formal de asistencia con firma táctil digital capturada en el Sistema Integrado de Gestión.
        Generado automáticamente por el Sistema de Inducciones y Capacitaciones el {{ date('d/m/Y \a \l\a\s h:i A') }}.
    </div>

</body>
</html>