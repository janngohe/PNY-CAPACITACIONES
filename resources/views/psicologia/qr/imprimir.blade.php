<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cartel QR - Auto-registro y Evaluación Psicotécnica · C.I. Piscícola New York</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700;800&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @page {
            size: letter portrait;
            margin: 1.5cm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #ffffff;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
            padding: 20px;
        }
        .borde-institucional {
            border: 3px solid #0284c7;
            border-radius: 24px;
            padding: 30px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
        }
        .logo {
            width: 70px;
            height: 70px;
            object-contain: fit;
        }
        .header-text h1 {
            font-size: 20pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .header-text h2 {
            font-size: 11pt;
            color: #0284c7;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 2px;
        }
        .badge-codigo {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 12px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 9pt;
            font-weight: bold;
            color: #475569;
            margin-top: 6px;
        }
        .titulo-cartel {
            margin: 25px 0 15px;
        }
        .titulo-cartel h3 {
            font-size: 22pt;
            font-weight: 800;
            color: #0369a1;
            line-height: 1.2;
        }
        .titulo-cartel p {
            font-size: 11pt;
            color: #64748b;
            margin-top: 6px;
        }
        .qr-section {
            margin: 20px auto;
            display: inline-block;
            padding: 16px;
            border: 2px dashed #0284c7;
            border-radius: 20px;
            background: #f8fafc;
        }
        .qr-canvas {
            width: 250px;
            height: 250px;
            margin: 0 auto;
        }
        .url-box {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10pt;
            font-weight: bold;
            color: #0369a1;
            margin-top: 10px;
            word-break: break-all;
        }
        .pasos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 25px 0;
            text-align: left;
        }
        .paso-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
        }
        .paso-numero {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #0284c7;
            color: #ffffff;
            font-weight: bold;
            font-size: 10pt;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        .paso-titulo {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .paso-desc {
            font-size: 8pt;
            color: #64748b;
            line-height: 1.3;
        }
        .footer-institucional {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 8pt;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12pt;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimir Este Cartel
        </button>
    </div>

    <div class="borde-institucional">

        <div>
            <!-- Header Oficial -->
            <div class="header">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="logo">
                <div class="header-text">
                    <h2>C.I. Piscícola New York S.A.</h2>
                    <h1>GESTIÓN HUMANA · SELECCIÓN DE PERSONAL</h1>
                    <div class="badge-codigo">FORMATO INSTITUCIONAL GH-FO-025 · EVALUACIÓN PSICOTÉCNICA</div>
                </div>
            </div>

            <!-- Título y Llamado a la Acción -->
            <div class="titulo-cartel">
                <h3>PORTAL DE AUTO-REGISTRO Y EVALUACIÓN</h3>
                <p>Apunta con la cámara de tu teléfono móvil al código QR para comenzar tu proceso</p>
            </div>

            <!-- Código QR Central -->
            <div class="qr-section">
                <div id="qrCanvas" class="qr-canvas"></div>
                <div class="url-box">{{ $urlEvaluacion }}</div>
            </div>
        </div>

        <!-- 4 Pasos del Proceso -->
        <div>
            <div class="pasos-grid">
                <div class="paso-card">
                    <div class="paso-numero">1</div>
                    <div class="paso-titulo">Escanea el QR</div>
                    <div class="paso-desc">Apunta con la cámara de tu celular para abrir el portal sin descargar ninguna app.</div>
                </div>
                <div class="paso-card">
                    <div class="paso-numero">2</div>
                    <div class="paso-titulo">Tus Datos</div>
                    <div class="paso-desc">Escribe tu nombre, cédula, lugar de expedición y municipio de residencia.</div>
                </div>
                <div class="paso-card">
                    <div class="paso-numero">3</div>
                    <div class="paso-titulo">Consentimiento</div>
                    <div class="paso-desc">Lee el formato GH-FO-025 y fírmalo digitalmente con tu dedo en la pantalla.</div>
                </div>
                <div class="paso-card">
                    <div class="paso-numero">4</div>
                    <div class="paso-titulo">Prueba PAC-A</div>
                    <div class="paso-desc">Responde las 40 preguntas sobre hábitos de trabajo y envía tu prueba.</div>
                </div>
            </div>

            <!-- Pie de Página Institucional -->
            <div class="footer-institucional">
                <span>C.I. Piscícola New York S.A. · Departamento de Gestión Humana y Psicología</span>
                <span>Confidencialidad Garantizada · Proceso Oficial de Selección</span>
            </div>
        </div>

    </div>

    <script src="{{ asset('qrcode.min.js') }}"></script>
    <script>
        if (typeof QRCode === 'undefined') {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('qrCanvas');
            new QRCode(container, {
                text: "{{ $urlEvaluacion }}",
                width: 250,
                height: 250,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
</body>
</html>
