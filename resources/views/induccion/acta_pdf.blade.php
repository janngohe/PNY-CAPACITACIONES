<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Asistencia - {{ $sesion->codigo_sesion }}</title>
    <style>
        @page {
            margin: 28px 30px;
            size: letter portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.35;
        }

        /* Encabezado Institucional */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #003d80;
            margin-bottom: 12px;
        }
        .header-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .logo-col {
            width: 18%;
            text-align: center;
            background: #ffffff;
        }
        .logo-col img {
            max-height: 48px;
            max-width: 110px;
        }
        .title-col {
            width: 58%;
            text-align: center;
        }
        .title-col h2 {
            margin: 0;
            font-size: 12px;
            color: #003d80;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-col h3 {
            margin: 3px 0 0;
            font-size: 10px;
            color: #475569;
            font-weight: normal;
        }
        .info-col {
            width: 24%;
            font-size: 8px;
            color: #334155;
            background: #f8fafc;
        }

        /* Ficha Técnica de la Sesión */
        .ficha-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .ficha-table td {
            padding: 5px 8px;
            font-size: 9px;
            border: 1px solid #e2e8f0;
        }
        .ficha-label {
            font-weight: bold;
            color: #003d80;
            width: 18%;
            text-transform: uppercase;
            font-size: 8px;
            background: #f1f5f9;
        }
        .ficha-val {
            width: 32%;
            color: #0f172a;
        }

        /* Tabla de Asistentes */
        .asistentes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .asistentes-table th {
            background: #003d80;
            color: #ffffff;
            font-size: 8px;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #002d60;
            font-weight: bold;
            text-align: left;
        }
        .asistentes-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .asistentes-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .num-col {
            width: 4%;
            text-align: center;
            font-weight: bold;
        }
        .cc-col {
            width: 14%;
            font-family: DejaVu Sans Mono, monospace;
        }
        .nombre-col {
            width: 26%;
            font-weight: bold;
            color: #0f172a;
        }
        .cargo-col {
            width: 18%;
        }
        .area-col {
            width: 15%;
        }
        .hora-col {
            width: 8%;
            text-align: center;
            font-size: 7.5px;
            color: #64748b;
        }
        .firma-col {
            width: 15%;
            text-align: center;
            padding: 2px !important;
        }
        .firma-img {
            max-height: 28px;
            max-width: 95px;
            display: block;
            margin: 0 auto;
        }

        /* Firmas de Cierre y Responsables */
        .cierre-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .cierre-table td {
            width: 50%;
            text-align: center;
            padding: 0 30px;
            vertical-align: top;
        }
        .linea-firma {
            border-top: 1.5px solid #0f172a;
            padding-top: 5px;
            margin-top: 35px;
        }
        .linea-firma strong {
            display: block;
            font-size: 10px;
            color: #003d80;
        }
        .linea-firma span {
            display: block;
            font-size: 8.5px;
            color: #64748b;
        }

        /* Pie de Página */
        .footer-note {
            margin-top: 16px;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Encabezado Institucional -->
    <table class="header-table">
        <tr>
            <td class="logo-col" rowspan="3">
                @php
                    $logoPath = public_path('images/Logo.png');
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo PNY">
                @else
                    <strong style="color: #003d80;">PISCÍCOLA NEW YORK</strong>
                @endif
            </td>
            <td class="title-col" rowspan="3">
                <h2>C.I. Piscícola New York S.A.</h2>
                <h3>ACTA OFICIAL DE ASISTENCIA A INDUCCIÓN Y CAPACITACIÓN</h3>
                <small style="color: #0284c7; font-weight: bold; text-transform: uppercase;">Sistema Integrado de Gestión (SST / Calidad / BPM)</small>
            </td>
            <td class="info-col"><strong>Código:</strong> SST-FOR-018</td>
        </tr>
        <tr>
            <td class="info-col"><strong>Versión:</strong> 04</td>
        </tr>
        <tr>
            <td class="info-col"><strong>Sesión:</strong> {{ $sesion->codigo_sesion }}</td>
        </tr>
    </table>

    <!-- Ficha Técnica de la Sesión -->
    <table class="ficha-table">
        <tr>
            <td class="ficha-label">Tema / Inducción:</td>
            <td class="ficha-val" colspan="3">
                <strong>{{ $sesion->induccion->titulo }}</strong>
            </td>
        </tr>
        <tr>
            <td class="ficha-label">Facilitador(a):</td>
            <td class="ficha-val">{{ $sesion->facilitador->nombre_completo ?? 'N/A' }}</td>
            <td class="ficha-label">Sede / Ubicación:</td>
            <td class="ficha-val">{{ $sesion->sede->nombre ?? 'Sede Principal' }} ({{ $sesion->sede->ciudad ?? 'Rivera' }})</td>
        </tr>
        <tr>
            <td class="ficha-label">Fecha Inicio:</td>
            <td class="ficha-val">{{ $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y h:i A') : 'N/A' }}</td>
            <td class="ficha-label">Fecha Cierre:</td>
            <td class="ficha-val">{{ $sesion->fecha_cierre ? $sesion->fecha_cierre->format('d/m/Y h:i A') : 'En Curso / No cerrada' }}</td>
        </tr>
        <tr>
            <td class="ficha-label">Participantes:</td>
            <td class="ficha-val" colspan="3">
                Total Registrados: <strong>{{ $sesion->asistencias->count() }}</strong> &middot;
                Colaboradores PNY: <strong>{{ $sesion->asistencias->where('es_externo', false)->count() }}</strong> &middot;
                Externos / Contratistas: <strong>{{ $sesion->asistencias->where('es_externo', true)->count() }}</strong>
            </td>
        </tr>
    </table>

    <!-- Tabla de Asistentes con Firmas Digitales -->
    <table class="asistentes-table">
        <thead>
            <tr>
                <th class="num-col">#</th>
                <th class="cc-col">Cédula (CC)</th>
                <th class="nombre-col">Nombres y Apellidos</th>
                <th class="cargo-col">Cargo / Ocupación</th>
                <th class="area-col">Área / Empresa</th>
                <th class="hora-col">Hora</th>
                <th class="firma-col">Firma Digital</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sesion->asistencias as $idx => $asist)
                <tr>
                    <td class="num-col">{{ $idx + 1 }}</td>
                    <td class="cc-col">{{ $asist->identificacion }}</td>
                    <td class="nombre-col">
                        {{ $asist->nombre_completo }}
                        @if($asist->es_externo)
                            <span style="font-size: 7px; color: #b45309; font-weight: bold;">[EXT]</span>
                        @endif
                    </td>
                    <td class="cargo-col">{{ $asist->cargo ?? 'Operario' }}</td>
                    <td class="area-col">{{ $asist->area ?? ($asist->empresa_externa ?? 'N/A') }}</td>
                    <td class="hora-col">{{ $asist->fecha_registro ? $asist->fecha_registro->format('h:i A') : '' }}</td>
                    <td class="firma-col">
                        @if($asist->firma_digital)
                            @php
                                $firmaPath = null;
                                if (str_starts_with($asist->firma_digital, 'data:image')) {
                                    $firmaPath = $asist->firma_digital;
                                } else {
                                    $diskPath = storage_path('app/public/' . $asist->firma_digital);
                                    if (file_exists($diskPath)) {
                                        $firmaPath = $diskPath;
                                    }
                                }
                            @endphp

                            @if($firmaPath)
                                <img src="{{ $firmaPath }}" class="firma-img" alt="Firma">
                            @else
                                <span style="font-size: 7.5px; color: #0284c7;">Firmado Digital</span>
                            @endif
                        @else
                            <span style="font-size: 7.5px; color: #94a3b8;">Sin Firma</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">
                        No se registraron asistencias en esta sesión.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bloque de Firmas de Cierre -->
    <table class="cierre-table">
        <tr>
            <td>
                <div class="linea-firma">
                    <strong>{{ $sesion->facilitador->nombre_completo ?? 'Facilitador Responsable' }}</strong>
                    <span>{{ $sesion->facilitador->cargoOficial() ?? 'Facilitador de Inducción' }}</span>
                    <span>C.I. Piscícola New York S.A.</span>
                </div>
            </td>
            <td>
                <div class="linea-firma">
                    <strong>Coordinación SST / Talento Humano</strong>
                    <span>Validación y Registro de Inducción</span>
                    <span>C.I. Piscícola New York S.A.</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Nota al pie -->
    <div class="footer-note">
        Este documento constituye constancia y evidencia de asistencia con firma táctil digital capturada en el módulo de inducción.
        Generado automáticamente por el Sistema de Capacitaciones el {{ date('d/m/Y \a \l\a\s h:i A') }}.
    </div>

</body>
</html>
