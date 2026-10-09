<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado {{ $certificado->codigo }}</title>
    <style>
        @page { margin: 0; size: letter landscape; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: DejaVu Sans, Helvetica, Arial, sans-serif; color: #0f172a; }
        .page { width: 100%; height: 100%; position: relative; background: #ffffff; }
        .header { background: #003d80; color: #ffffff; height: 105px; padding: 18px 40px; position: relative; }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo-box { background: #ffffff; border-radius: 12px; padding: 6px 10px; width: 90px; text-align: center; }
        .logo-box img { height: 56px; }
        .brand { padding-left: 16px; }
        .brand h2 { margin: 0; font-size: 17px; letter-spacing: 1px; text-transform: uppercase; }
        .brand h2 span { color: #67e8f9; }
        .brand p { margin: 3px 0 0; font-size: 9px; color: #cbd5e1; }
        .code-box { text-align: right; }
        .code-box small { display: block; font-size: 8px; letter-spacing: 1.5px; color: #7dd3fc; text-transform: uppercase; }
        .code-box strong { font-size: 13px; letter-spacing: 1px; }
        .wave { background: #0056b3; height: 10px; border-bottom: 4px solid #38bdf8; }
        .watermark { position: absolute; top: 175px; left: 50%; margin-left: -190px; width: 380px; opacity: 0.05; }
        .body { text-align: center; padding: 18px 60px 0; position: relative; }
        .badge { display: inline-block; background: #fffbeb; color: #92400e; border: 1px solid #fcd34d; border-radius: 20px; padding: 4px 16px; font-size: 10px; font-weight: bold; }
        h1 { margin: 12px 0 6px; font-size: 25px; color: #0f2a4a; text-transform: uppercase; letter-spacing: 0.5px; }
        .lead { font-size: 11px; color: #64748b; margin: 0 0 8px; }
        .nombre { font-size: 29px; font-weight: bold; color: #0f2a4a; border-bottom: 3px solid #0056b3; display: inline-block; padding: 0 22px 4px; margin: 4px 0 8px; }
        .doc { font-size: 11px; color: #475569; margin: 0 0 12px; }
        .doc strong { color: #0f2a4a; }
        .curso { width: 78%; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 14px; background: #f8fafc; padding: 12px 20px; }
        .curso small { font-size: 9px; letter-spacing: 1.5px; font-weight: bold; color: #0ea5e9; text-transform: uppercase; }
        .curso h3 { margin: 6px 0 8px; font-size: 20px; color: #0056b3; }
        .pill { display: inline-block; border-radius: 20px; padding: 3px 12px; font-size: 9px; font-weight: bold; margin: 0 4px; }
        .pill-ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .pill-date { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .firmas { width: 80%; margin: 14px auto 0; border-collapse: collapse; }
        .firmas td { width: 50%; text-align: center; vertical-align: bottom; }
        .firma-nombre { font-family: DejaVu Serif, serif; font-style: italic; font-size: 17px; font-weight: bold; height: 34px; }
        .firma-id { font-size: 7px; color: #94a3b8; }
        .linea { width: 210px; margin: 3px auto 0; border-top: 2px solid #475569; padding-top: 4px; }
        .linea b { font-size: 11px; color: #0f2a4a; }
        .linea span { display: block; font-size: 9px; color: #64748b; }
        .footer { position: absolute; bottom: 0; left: 0; width: 100%; background: #0f172a; color: #94a3b8; text-align: center; font-size: 8px; padding: 9px 0; }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/Logo.png');
    $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    $plantilla = $certificado->plantillaCertificado;
    $f1n = $plantilla->firma_1_nombre ?? 'Ing. Carlos Mendoza';
    $f1c = $plantilla->firma_1_cargo ?? 'Director de Gestión Humana y Bioseguridad';
    $f2n = $plantilla->firma_2_nombre ?? 'Dra. Elena Ramos';
    $f2c = $plantilla->firma_2_cargo ?? 'Gerente de Calidad y Procesos';
@endphp
<div class="page">
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:100px;">
                    @if($logoSrc)<div class="logo-box"><img src="{{ $logoSrc }}" alt="Logo"></div>@endif
                </td>
                <td class="brand">
                    <h2>C.I. Piscícola <span>New York</span> S.A.S.</h2>
                    <p>Sistema Institucional de Gestión del Talento Humano y Capacitación Continuada</p>
                </td>
                <td class="code-box">
                    <small>Código de autenticidad</small>
                    <strong>{{ $certificado->codigo }}</strong>
                </td>
            </tr>
        </table>
    </div>
    <div class="wave"></div>

    @if($logoSrc)<img class="watermark" src="{{ $logoSrc }}" alt="">@endif

    <div class="body">
        <span class="badge">Acreditación Institucional Oficial</span>
        <h1>Certificado de Capacitación y Aprobación</h1>
        <p class="lead">La Dirección General de C.I. Piscícola New York S.A.S. hace constar formalmente que:</p>

        <div class="nombre">{{ $certificado->nombre_empleado }}</div>
        <p class="doc">
            Con documento de identificación Nº <strong>{{ $certificado->identificacion }}</strong>,
            adscrito al área de <strong>{{ $certificado->area_nombre ?? 'Producción Piscícola' }}</strong>.
        </p>

        <div class="curso">
            <small>Ha cumplido satisfactoriamente el programa formativo</small>
            <h3>{{ $certificado->nombre_capacitacion }}</h3>
            <span class="pill pill-ok">Calificación: {{ rtrim(rtrim(number_format((float) $certificado->porcentaje, 2), '0'), '.') }}% (Aprobado)</span>
            <span class="pill pill-date">Fecha de emisión: {{ $certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : date('d/m/Y') }}</span>
        </div>

        <table class="firmas">
            <tr>
                <td>
                    <div class="firma-nombre">C. Mendoza</div>
                    <div class="firma-id">Digital Sign: #PNY-AUTH-8821</div>
                    <div class="linea"><b>{{ $f1n }}</b><span>{{ $f1c }}</span></div>
                </td>
                <td>
                    <div class="firma-nombre" style="color:#0056b3;">E. Ramos H.</div>
                    <div class="firma-id">Digital Sign: #PNY-QUAL-9943</div>
                    <div class="linea"><b>{{ $f2n }}</b><span>{{ $f2c }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">Documento de Acreditación Interna emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.</div>
</div>
</body>
</html>
