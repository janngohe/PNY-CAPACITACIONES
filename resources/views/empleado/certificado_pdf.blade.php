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
        .doc { font-size: 11px; color: #475569; margin: 0 0 10px; }
        .doc strong { color: #0f2a4a; }
        .curso { width: 84%; margin: 0 auto; border: 1px solid #cbd5e1; border-radius: 14px; background: #f8fafc; padding: 12px 24px; }
        .curso small { font-size: 9px; letter-spacing: 1.5px; font-weight: bold; color: #0284c7; text-transform: uppercase; }
        .curso h3 { margin: 5px 0 6px; font-size: 18px; color: #003d80; text-transform: uppercase; }
        .curso-texto { font-size: 10px; color: #334155; line-height: 1.45; margin: 0 0 6px; }
        .curso-constancia { font-size: 9px; color: #64748b; font-style: italic; margin: 0 0 8px; }
        .pill { display: inline-block; border-radius: 20px; padding: 3px 14px; font-size: 9px; font-weight: bold; margin: 0 4px; }
        .pill-date { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .firmas { width: 80%; margin: 12px auto 0; border-collapse: collapse; }
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
    $plantilla = $certificado->plantillaCertificado ?? \App\Models\PlantillaCertificado::where('estado', true)->first();
    $f1n = $plantilla->firma_1_nombre ?? 'Ing. Carlos Mendoza';
    $f1c = $plantilla->firma_1_cargo ?? 'Jefe de Área / Gestión de Operaciones';
    $f1rPath = ($plantilla && $plantilla->firma_1_ruta) ? public_path('storage/' . $plantilla->firma_1_ruta) : null;
    $f1rSrc = ($f1rPath && file_exists($f1rPath)) ? 'data:image/' . pathinfo($f1rPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($f1rPath)) : null;

    if (!$f1rSrc && $certificado->usuario?->area_id) {
        $jefeArea = \App\Models\Usuario::where('area_id', $certificado->usuario->area_id)
            ->where('rol', 'JEFE_AREA')
            ->whereNotNull('firma_digital')
            ->first();
        if ($jefeArea && $jefeArea->tieneFirmaRegistrada()) {
            $f1n = $jefeArea->nombre_completo;
            $f1c = $jefeArea->cargoOficial();
            $f1rPath = public_path('storage/' . $jefeArea->firma_digital);
            $f1rSrc = ($f1rPath && file_exists($f1rPath)) ? 'data:image/' . pathinfo($f1rPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($f1rPath)) : null;
        }
    }

    $f2n = $plantilla->firma_2_nombre ?? 'Dra. Elena Ramos';
    $f2c = $plantilla->firma_2_cargo ?? 'Gerente General';
    $f2rPath = ($plantilla && $plantilla->firma_2_ruta) ? public_path('storage/' . $plantilla->firma_2_ruta) : null;
    $f2rSrc = ($f2rPath && file_exists($f2rPath)) ? 'data:image/' . pathinfo($f2rPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($f2rPath)) : null;

    $plantillaBgPath = ($plantilla && $plantilla->ruta_plantilla) ? public_path('storage/' . $plantilla->ruta_plantilla) : null;
    $plantillaBgSrc = ($plantillaBgPath && file_exists($plantillaBgPath)) ? 'data:image/' . pathinfo($plantillaBgPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($plantillaBgPath)) : null;

    $org = $plantilla->nombre_organizacion ?? 'C.I. Piscícola New York S.A.S.';
    $textoCert = $plantilla->texto_certificado ?? ('La Dirección General de ' . $org . ' hace constar formalmente que:');
    $duracionHoras = $certificado->capacitacion?->duracion_estimada ?? 4;
    $fechaRealizada = $certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : date('d/m/Y');
@endphp
<div class="page">
    @if($plantillaBgSrc)
        <img src="{{ $plantillaBgSrc }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; opacity: 0.12;" alt="">
    @endif

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:100px;">
                    @if($logoSrc)<div class="logo-box"><img src="{{ $logoSrc }}" alt="Logo"></div>@endif
                </td>
                <td class="brand">
                    <h2>{{ $org }}</h2>
                    <p>Sistema Institucional de Gestión del Talento Humano y Capacitación Continuada</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="wave"></div>

    @if(!$plantillaBgSrc && $logoSrc)
        <img class="watermark" src="{{ $logoSrc }}" alt="">
    @endif

    <div class="body">
        <h1>Certificado de Capacitación y Aprobación</h1>
        <p class="lead">{{ $textoCert }}</p>

        <div class="nombre">{{ strtoupper($certificado->nombre_empleado) }}</div>
        <p class="doc">
            Con documento de identificación N.º <strong>{{ $certificado->identificacion }}</strong>,
            adscrito al área de <strong>{{ $certificado->area_nombre ?? 'Producción Piscícola' }}</strong>,
        </p>

        <div class="curso">
            <small>Ha cumplido satisfactoriamente la capacitación formativa en:</small>
            <h3>{{ strtoupper($certificado->nombre_capacitacion) }}</h3>
            <p class="curso-texto">
                Con una intensidad horaria de <strong>{{ $duracionHoras }} {{ $duracionHoras == 1 ? 'hora' : 'horas' }}</strong>, realizada el día <strong>{{ $fechaRealizada }}</strong>, con el propósito de fortalecer sus conocimientos y competencias en el área correspondiente.
            </p>
            <p class="curso-constancia">
                Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
            </p>
            <span class="pill pill-date">Rivera - Huila, {{ $fechaRealizada }}</span>
        </div>

        {{-- OCULTO TEMPORALMENTE: Firmas para implementación futura --}}
        @if(false)
        <table class="firmas">
            <tr>
                <td>
                    @if($f1rSrc)
                        <img src="{{ $f1rSrc }}" style="max-height: 45px; max-width: 170px; display: block; margin: 0 auto 3px;" alt="Firma Jefe Inmediato">
                    @else
                        <div class="firma-nombre">{{ Str::limit($f1n, 22) }}</div>
                        <div class="firma-id">Digital Sign: #PNY-JEFE-{{ substr(md5($f1n), 0, 5) }}</div>
                    @endif
                    <div class="linea"><b>{{ $f1n }}</b><span>{{ $f1c }} (Jefe Inmediato)</span></div>
                </td>
                <td>
                    @if($f2rSrc)
                        <img src="{{ $f2rSrc }}" style="max-height: 45px; max-width: 170px; display: block; margin: 0 auto 3px;" alt="Firma Gerente">
                    @else
                        <div class="firma-nombre" style="color:#0056b3;">{{ Str::limit($f2n, 22) }}</div>
                        <div class="firma-id">Digital Sign: #PNY-GER-{{ substr(md5($f2n), 0, 5) }}</div>
                    @endif
                    <div class="linea"><b>{{ $f2n }}</b><span>{{ $f2c }} (Gerencia General)</span></div>
                </td>
            </tr>
        </table>
        @endif
    </div>

    <div class="footer">Documento Oficial emitido por el Sistema de Capacitación de {{ $org }}</div>
</div>
</body>
</html>
