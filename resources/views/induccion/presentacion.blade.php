<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $sesion->induccion->titulo }} · {{ $sesion->estaActiva() ? 'Presentación en Vivo' : 'Resumen de Asistencia' }} · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800;900&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Librerías locales para funcionamiento 100% Offline / Local y en Internet -->
    <script src="{{ asset('qrcode.min.js') }}"></script>
    <script>
        if (typeof QRCode === 'undefined') {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
        }
    </script>
    <script src="{{ asset('pdf.min.js') }}"></script>
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('pdf.worker.min.js') }}";
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-blue: #0056b3;
            --brand-deep: #003d80;
            --brand-dark: #002d5e;
            --brand-light: #e6f1fc;
            --brand-sky: #0ea5e9;
            --brand-cyan: #0284c7;
            --ocean-wave: #0369a1;
            --paper: #f8fafc;
            --line: #d8e3ef;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f1f5f9;
            color: #1e293b;
            font-family: 'DM Sans', -apple-system, sans-serif;
        }

        .layout-container {
            display: flex;
            flex-direction: row;
            height: calc(100vh - 56px);
            width: 100vw;
            overflow: hidden;
            background-color: #f1f5f9;
        }

        .layout-container.hidden, #seccionSesionActiva.hidden {
            display: none !important;
        }

        .slider-column {
            flex: 1 1 0%;
            display: flex;
            flex-direction: column;
            height: 100%;
            background-color: #eef2f6;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-column {
            width: 380px;
            min-width: 340px;
            max-width: 420px;
            height: 100%;
            background-color: #ffffff;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 20;
            box-shadow: -4px 0 15px -3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .sidebar-column.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            border-left: none !important;
            overflow: hidden !important;
        }

        .slide-pane {
            display: none !important;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .slide-pane.active-slide {
            display: flex !important;
            opacity: 1 !important;
            align-items: center;
            justify-content: center;
        }

        .pptx-slide-wrapper {
            position: relative;
            transform-origin: 0 0;
            box-shadow: 0 20px 35px -10px rgba(0, 61, 128, 0.15);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        .pptx-slide-wrapper .pptx-canvas {
            transform-origin: 0 0;
        }

        .pdf-canvas-wrapper {
            overflow: auto;
            max-width: 100%;
            max-height: 82vh;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            box-shadow: 0 15px 30px -8px rgba(0, 61, 128, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Fullscreen */
        body.is-fullscreen #topHeaderBar {
            display: none !important;
        }
        body.is-fullscreen .layout-container {
            height: 100vh !important;
        }
        body.is-fullscreen .sidebar-column {
            display: none !important;
        }
        body.is-fullscreen #floatingFullscreenHUD {
            display: flex !important;
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 overflow-hidden select-none bg-slate-100">

    <!-- 1. BARRA SUPERIOR DE CONTROL -->
    <header id="topHeaderBar" class="bg-white border-b border-slate-200 px-4 py-2 flex items-center justify-between gap-3 z-30 shrink-0 h-[56px] shadow-xs">
        <!-- Izquierda: Regresar, Código y Título -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('induccion.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-brand-blue transition-colors border border-slate-200" title="Volver al panel de inducciones">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-brand-blue border border-blue-200">
                        Inducción Presencial
                    </span>
                    <span class="text-xs font-mono text-slate-500">
                        Código: <strong class="text-brand-deep bg-blue-50/80 px-1.5 py-0.5 rounded border border-blue-200 select-all cursor-pointer font-bold" onclick="copiarCodigo()">{{ $sesion->codigo_sesion }}</strong>
                    </span>
                    <span class="text-slate-300 hidden sm:inline">|</span>
                    <span class="text-xs text-slate-500 truncate hidden sm:inline">
                        <i class="fa-solid fa-location-dot text-brand-blue mr-1"></i>{{ $sesion->sede->nombre ?? 'Sede Principal' }}
                    </span>
                </div>
                <h1 class="text-xs sm:text-sm font-bold text-slate-900 truncate max-w-sm sm:max-w-md lg:max-w-xl font-heading">
                    {{ $sesion->induccion->titulo }}
                </h1>
            </div>
        </div>

        <!-- Centro: Controles del Slider (SOLO SI LA SESIÓN ESTÁ ACTIVA) -->
        <div id="controlesSliderHeader" class="{{ $sesion->estaActiva() ? 'flex' : 'hidden' }} items-center gap-1.5 bg-slate-100 px-3 py-1 rounded-xl border border-slate-200 shrink-0">
            <button type="button" onclick="slideAnterior()"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-brand-blue hover:bg-white transition-all cursor-pointer"
                    title="Diapositiva Anterior (Tecla Flecha Izquierda)">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <button type="button" onclick="abrirModalIndice()"
                    class="px-2 py-0.5 rounded-lg text-xs font-mono font-bold text-brand-deep hover:bg-white transition-colors flex items-center gap-1.5 cursor-pointer"
                    title="Ver cuadrícula de diapositivas">
                <i class="fa-solid fa-layer-group text-[10px] text-brand-blue"></i>
                <span><span id="badgeSlideActualTop">1</span> / <span id="badgeSlideTotalTop">{{ count($slides) }}</span></span>
            </button>

            <button type="button" onclick="slideSiguiente()"
                    class="p-1.5 rounded-lg text-slate-600 hover:text-brand-blue hover:bg-white transition-all cursor-pointer"
                    title="Diapositiva Siguiente (Tecla Flecha Derecha o Espacio)">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>

        <!-- Derecha: Acciones según Estado de la Sesión -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Si la sesión sigue activa -->
            <div id="accionesSesionActivaHeader" class="{{ $sesion->estaActiva() ? 'flex' : 'hidden' }} items-center gap-2">
                <button type="button" onclick="toggleSidebarAsistencias()" id="btnToggleSidebar"
                        class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center gap-1.5 border border-slate-300 transition-all cursor-pointer shadow-2xs"
                        title="Ocultar o Mostrar el Panel Lateral">
                    <i class="fa-solid fa-table-columns text-brand-blue text-xs"></i>
                    <span class="hidden md:inline" id="textoToggleSidebar">Ocultar Panel</span>
                </button>

                <button type="button" onclick="togglePantallaCompleta()" id="btnFullscreenTop"
                        class="px-3 py-1.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all cursor-pointer"
                        title="Proyectar a Pantalla Completa (Tecla F o F11)">
                    <i id="fullscreenIconTop" class="fa-solid fa-expand text-xs"></i>
                    <span class="hidden sm:inline">Pantalla Completa</span>
                </button>

                <!-- Botón Finalizar Sesión con Modal -->
                <button type="button" onclick="abrirModalConfirmarFinalizar()"
                        class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border border-rose-200 text-xs font-bold transition-all cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-flag-checkered mr-1"></i> <span>Finalizar</span>
                </button>
            </div>

            <!-- Si la sesión ya fue finalizada -->
            <div id="accionesSesionFinalizadaHeader" class="{{ !$sesion->estaActiva() ? 'flex' : 'hidden' }} items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Sesión Finalizada
                </span>

                <a href="{{ route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]) }}"
                   class="px-3.5 py-1.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Descargar Acta (PDF)</span>
                </a>
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- ESCENARIO A: SESIÓN EN CURSO (SLIDER DE DIAPOSITIVAS + ASISTENCIA EN VIVO) -->
    <!-- ========================================================================= -->
    <div id="seccionSesionActiva" class="layout-container {{ $sesion->estaActiva() ? '' : 'hidden' }}">

        <!-- COLUMNA IZQUIERDA: SLIDER PRINCIPAL -->
        <section class="slider-column" id="sliderColumn">

            <!-- Barra de Progreso Superior -->
            <div class="w-full bg-slate-200 h-1 shrink-0">
                <div id="slideProgressBar" class="h-full bg-gradient-to-r from-cyan-500 via-brand-sky to-brand-blue transition-all duration-300"
                     style="width: {{ count($slides) > 0 ? (100 / count($slides)) : 100 }}%;"></div>
            </div>

            <!-- Escenario de la Diapositiva (Stage) -->
            <div id="slideStage" class="flex-1 w-full h-full relative flex items-center justify-center p-3 sm:p-6 overflow-y-auto">

                <!-- Diapositivas Renderizadas -->
                @foreach($slides as $index => $slide)
                    <div id="slidePane-{{ $index }}"
                         class="slide-pane {{ $index === 0 ? 'active-slide' : '' }}"
                         data-slide-index="{{ $index }}"
                         data-slide-type="{{ $slide['tipo'] }}"
                         data-slide-title="{{ $slide['titulo'] ?? '' }}">

                        {{-- CASO 1: PORTADA INSTITUCIONAL --}}
                        @if($slide['tipo'] === 'portada')
                            <div class="w-full max-w-4xl bg-white border border-slate-200 rounded-3xl p-5 sm:p-8 md:p-10 shadow-xl relative text-center flex flex-col items-center justify-center max-h-[86vh] overflow-y-auto my-auto custom-scrollbar">
                                <div class="flex items-center gap-2 mb-3 bg-slate-50 px-4 py-1.5 rounded-xl border border-slate-200 shrink-0">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-6 sm:h-7 w-auto object-contain">
                                    <span class="text-xs font-bold tracking-wider text-brand-dark uppercase">C.I. Piscícola New York S.A.</span>
                                </div>

                                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-brand-blue border border-blue-200 mb-2.5 inline-flex items-center gap-1.5 shrink-0">
                                    <i class="fa-solid fa-chalkboard-user"></i> Sesión de Inducción
                                </span>

                                <h2 class="text-xl sm:text-3xl md:text-4xl font-black text-slate-900 mb-3 font-heading max-w-3xl leading-tight shrink-0">
                                    {{ $sesion->induccion->titulo }}
                                </h2>

                                @if($sesion->induccion->descripcion)
                                    <div class="w-full max-w-2xl my-2 shrink-0">
                                        <div class="text-xs sm:text-sm text-slate-700 leading-relaxed max-h-48 sm:max-h-60 overflow-y-auto px-4 py-3 bg-slate-50/90 rounded-2xl border border-slate-200 text-left sm:text-center whitespace-pre-line shadow-2xs custom-scrollbar">
                                            {{ $sesion->induccion->descripcion }}
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 w-full max-w-2xl mb-5 text-left text-xs shrink-0">
                                    <div class="bg-slate-50 p-2.5 sm:p-3 rounded-xl border border-slate-200">
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Facilitador</div>
                                        <div class="font-bold text-slate-900 mt-0.5 truncate">{{ $sesion->facilitador->nombre_completo ?? 'Facilitador' }}</div>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 sm:p-3 rounded-xl border border-slate-200">
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Sede</div>
                                        <div class="font-bold text-slate-900 mt-0.5 truncate">{{ $sesion->sede->nombre ?? 'Principal' }}</div>
                                    </div>
                                    <div class="bg-slate-50 p-2.5 sm:p-3 rounded-xl border border-slate-200 col-span-2 sm:col-span-1">
                                        <div class="text-[10px] text-slate-500 font-bold uppercase">Código Sesión</div>
                                        <div class="font-mono font-black text-brand-blue text-sm mt-0.5">{{ $sesion->codigo_sesion }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <button type="button" onclick="slideSiguiente()"
                                            class="px-8 py-3 rounded-2xl bg-brand-blue hover:bg-brand-deep text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                        <span>Comenzar Explicación</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                        {{-- CASO 2: DIAPOSITIVA POWERPOINT (.PPTX CONVERTIDO EN EL SERVIDOR) --}}
                        @elseif($slide['tipo'] === 'pptx')
                            <div class="w-full h-full flex items-center justify-center overflow-hidden">
                                <div class="pptx-slide-wrapper"
                                     data-width="{{ $slide['width'] ?? 1920 }}"
                                     data-height="{{ $slide['height'] ?? 1080 }}">
                                    {!! $slide['html'] !!}
                                </div>
                            </div>

                        {{-- CASO 3: DOCUMENTO PDF (CON HERRAMIENTAS DE ZOOM Y PÁGINAS) --}}
                        @elseif($slide['tipo'] === 'pdf')
                            <div class="w-full h-full flex flex-col items-center justify-center p-2 pdf-container-slide"
                                 data-pdf-url="{{ $slide['archivo_url'] }}"
                                 data-pdf-title="{{ $slide['titulo'] }}">

                                <!-- Barra Superior de Herramientas del PDF (Zoom y Navegación) -->
                                <div class="mb-2 bg-white px-4 py-1.5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3 text-xs z-10 shrink-0">
                                    <div class="flex items-center gap-1.5 pr-3 border-r border-slate-200">
                                        <button type="button" onclick="pdfPaginaAnterior(this)" class="p-1 rounded text-slate-600 hover:text-brand-blue hover:bg-slate-100 cursor-pointer" title="Página anterior">
                                            <i class="fa-solid fa-chevron-left text-[11px]"></i>
                                        </button>
                                        <span class="font-mono text-slate-700 font-bold text-xs">
                                            Pág. <span class="pdf-current-page text-brand-blue font-black">1</span> / <span class="pdf-total-pages">1</span>
                                        </span>
                                        <button type="button" onclick="pdfPaginaSiguiente(this)" class="p-1 rounded text-slate-600 hover:text-brand-blue hover:bg-slate-100 cursor-pointer" title="Página siguiente">
                                            <i class="fa-solid fa-chevron-right text-[11px]"></i>
                                        </button>
                                    </div>

                                    <!-- Controles de Zoom para acercar letras pequeñas -->
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[11px] text-slate-500 font-bold hidden sm:inline">Zoom:</span>
                                        <button type="button" onclick="cambiarZoomPdf(-0.25)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue border border-slate-200 transition-colors cursor-pointer flex items-center gap-1" title="Alejar Documento">
                                            <i class="fa-solid fa-magnifying-glass-minus text-xs"></i>
                                        </button>

                                        <span class="pdf-zoom-label font-mono font-bold text-brand-deep text-xs px-1.5">100%</span>

                                        <button type="button" onclick="cambiarZoomPdf(0.25)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-brand-blue border border-slate-200 transition-colors cursor-pointer flex items-center gap-1" title="Acercar Documento (Letras más grandes)">
                                            <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                                            <span class="hidden md:inline text-[10px]">Acercar</span>
                                        </button>

                                        <button type="button" onclick="resetearZoomPdf()" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold border border-slate-200 transition-colors cursor-pointer ml-1" title="Restablecer tamaño original">
                                            Ajustar
                                        </button>
                                    </div>
                                </div>

                                <!-- Lienzo con scroll cuando se hace Zoom -->
                                <div class="pdf-canvas-wrapper w-full h-full">
                                    <div class="pdf-loader text-center p-8">
                                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-brand-blue mb-2"></i>
                                        <div class="text-xs font-bold text-slate-700">Cargando diapositivas del documento PDF...</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">Renderizando con pdf.js</div>
                                    </div>
                                    <canvas class="pdf-render-canvas hidden"></canvas>
                                </div>
                            </div>

                        {{-- CASO 4: VIDEO DE INDUCCIÓN --}}
                        @elseif($slide['tipo'] === 'video')
                            <div class="w-full h-full flex items-center justify-center p-2">
                                <div class="w-full h-full max-w-5xl max-h-[80vh] aspect-video bg-black rounded-3xl overflow-hidden shadow-xl border border-slate-200">
                                    <iframe src="{{ $slide['embed_url'] }}"
                                            class="w-full h-full"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen></iframe>
                                </div>
                            </div>

                        {{-- CASO 5: IMAGEN --}}
                        @elseif($slide['tipo'] === 'imagen')
                            <div class="w-full h-full flex items-center justify-center p-2">
                                <div class="max-w-5xl max-h-[80vh] rounded-3xl overflow-hidden shadow-xl border border-slate-200 bg-white flex items-center justify-center">
                                    <img src="{{ $slide['url'] }}" alt="{{ $slide['titulo'] }}" class="max-w-full max-h-[80vh] object-contain">
                                </div>
                            </div>

                        {{-- CASO 6: DIAPOSITIVAS ESTRUCTURADAS (TEMARIO / NORMAS / TEXTO) --}}
                        @elseif(in_array($slide['tipo'], ['temario', 'normas', 'texto']))
                            <div class="w-full max-w-3xl bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl max-h-[85vh] flex flex-col">
                                <div class="flex items-center gap-3 mb-4 shrink-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-lg border border-blue-200">
                                        <i class="fa-solid {{ $slide['tipo'] === 'temario' ? 'fa-bullseye' : ($slide['tipo'] === 'normas' ? 'fa-shield-halved' : 'fa-file-lines') }}"></i>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-brand-blue uppercase tracking-wider">{{ $slide['subtitulo'] ?? 'Información' }}</span>
                                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">{{ $slide['titulo'] }}</h3>
                                    </div>
                                </div>
                                <div class="text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 p-5 sm:p-6 rounded-2xl border border-slate-200 mb-4 overflow-y-auto whitespace-pre-line flex-1">
                                    {{ $slide['contenido'] ?? '' }}
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100 shrink-0">
                                    <span>C.I. Piscícola New York</span>
                                    <span>Sesión: {{ $sesion->codigo_sesion }}</span>
                                </div>
                            </div>

                        {{-- CASO 7: CIERRE Y REPASO DE ASISTENCIA --}}
                        @elseif($slide['tipo'] === 'cierre')
                            <div class="w-full max-w-3xl bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-signature"></i> Finalización de Inducción
                                </span>
                                <h2 class="text-xl sm:text-3xl font-black text-slate-900 mb-2 font-heading">
                                    Verificación de Asistencias y Firmas
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto mb-6 leading-relaxed">
                                    Por favor asegúrate de haber escaneado el código QR o ingresado el código manual en tu celular para que tu firma quede asentada en el acta oficial.
                                </p>
                                <div class="inline-flex items-center gap-3 bg-blue-50/80 px-4 py-2.5 rounded-2xl border border-blue-200 mb-6">
                                    <span class="text-xs text-slate-600">Código de sesión:</span>
                                    <strong class="font-mono text-brand-deep text-lg font-black">{{ $sesion->codigo_sesion }}</strong>
                                </div>
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" onclick="slideAnterior()"
                                            class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs cursor-pointer border border-slate-200">
                                        <i class="fa-solid fa-arrow-left mr-1"></i> Repasar Diapositivas
                                    </button>
                                    @if($sesion->estaActiva())
                                        <button type="button" onclick="abrirModalConfirmarFinalizar()"
                                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider shadow-md cursor-pointer">
                                            <i class="fa-solid fa-flag-checkered mr-1"></i> Finalizar Inducción
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach

                <!-- Flechas Laterales Flotantes en el Slider -->
                <button type="button" onclick="slideAnterior()"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-2xl bg-white/95 hover:bg-brand-blue text-slate-700 hover:text-white border border-slate-200 flex items-center justify-center transition-all cursor-pointer z-10 shadow-lg hover:scale-105 active:scale-95"
                        title="Diapositiva Anterior (Flecha Izquierda)">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>

                <button type="button" onclick="slideSiguiente()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-11 h-11 rounded-2xl bg-white/95 hover:bg-brand-blue text-slate-700 hover:text-white border border-slate-200 flex items-center justify-center transition-all cursor-pointer z-10 shadow-lg hover:scale-105 active:scale-95"
                        title="Diapositiva Siguiente (Flecha Derecha o Espacio)">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>
            </div>

            <!-- Barra Inferior de Navegación del Slider -->
            <footer class="bg-white border-t border-slate-200 px-4 py-2 flex items-center justify-between text-xs z-10 shrink-0 h-11 shadow-2xs">
                <div class="flex items-center gap-2 text-slate-500 text-[11px] truncate">
                    <span>Tema:</span>
                    <strong id="slideTituloFooter" class="text-slate-800 truncate max-w-xs sm:max-w-md font-bold">
                        {{ $slides[0]['titulo'] ?? 'Inicio' }}
                    </strong>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <span class="text-slate-400 hidden sm:inline text-[11px]">
                        Atajos: <span class="text-slate-600 font-mono font-bold">← / →</span> diapositivas · <span class="text-slate-600 font-mono font-bold">F</span> pantalla completa
                    </span>

                    <div class="flex items-center gap-1 font-mono font-bold text-brand-deep bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        <span id="badgeSlideActualFooter">1</span> / {{ count($slides) }}
                    </div>
                </div>
            </footer>

            <!-- HUD Flotante Exclusivo en Modo Pantalla Completa (Auto-hide) -->
            <div id="floatingFullscreenHUD" class="hidden absolute bottom-4 left-4 z-40 bg-white/95 backdrop-blur-md p-3 rounded-2xl border border-slate-200 shadow-2xl flex items-center gap-3">
                <div id="hudQRElement" class="w-14 h-14 bg-white p-1 rounded-xl shrink-0 cursor-pointer border border-slate-200" onclick="abrirModalQR()" title="Clic para ampliar QR"></div>
                <div class="text-[11px]">
                    <div class="font-bold text-slate-900 flex items-center gap-1">
                        <i class="fa-solid fa-qrcode text-brand-blue"></i> Escanea Asistencia
                    </div>
                    <div class="font-mono text-brand-deep font-bold mt-0.5">
                        Código: {{ $sesion->codigo_sesion }}
                    </div>
                    <div class="text-[10px] text-slate-500">
                        Asistentes: <strong id="hudAsistentesBadge" class="text-emerald-600 font-bold">0</strong>
                    </div>
                </div>
                <button type="button" onclick="togglePantallaCompleta()" class="p-2 text-slate-500 hover:text-slate-900 cursor-pointer ml-1" title="Salir de pantalla completa">
                    <i class="fa-solid fa-compress text-xs"></i>
                </button>
            </div>

        </section>

        <!-- COLUMNA DERECHA: PANEL FIJO DE ASISTENCIA Y QR EN VIVO -->
        <aside class="sidebar-column" id="sidebarColumn">

            <!-- Bloque 1: Código QR y Código Manual de Acceso -->
            <div class="p-4 bg-slate-50 border-b border-slate-200 text-center relative shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-brand-blue uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode"></i> Escaneo de Asistencia
                    </span>
                    <button type="button" onclick="abrirModalQR()" class="text-[11px] font-bold text-brand-blue hover:text-brand-deep cursor-pointer" title="Agrandar QR para proyector">
                        <i class="fa-solid fa-maximize mr-0.5"></i> Ampliar
                    </button>
                </div>

                <!-- Lienzo QR Nítido -->
                <div class="inline-block p-2 bg-white rounded-2xl shadow-md border border-slate-200 mx-auto cursor-pointer hover:border-brand-blue transition-colors" onclick="abrirModalQR()" title="Clic para proyectar QR gigante">
                    <div id="sidebarQRElement" class="w-36 h-36 flex items-center justify-center"></div>
                </div>

                <!-- Código Manual Destacado -->
                <div class="mt-2.5 bg-white p-2.5 rounded-xl border border-slate-200 text-left shadow-2xs">
                    <div class="flex items-center justify-between text-[10px] text-slate-500">
                        <span>¿Cámara no funciona?</span>
                        <span class="text-brand-blue font-bold">Código manual:</span>
                    </div>
                    <div class="flex items-center justify-between gap-1.5 mt-1">
                        <div class="text-[11px] text-slate-700 font-mono truncate select-all font-semibold" id="textoUrlManualSidebar">
                            {{ $urlManualActual }}
                        </div>
                        <strong class="font-mono text-xs font-black text-brand-deep bg-blue-50 px-2 py-0.5 rounded border border-blue-200 select-all shrink-0 cursor-pointer" onclick="copiarCodigo()" title="Copiar código">
                            {{ $sesion->codigo_sesion }}
                        </strong>
                    </div>
                </div>

                <!-- Selector de Modo de Red (Ngrok vs WiFi Local vs Web) -->
                <div class="mt-2.5 flex items-center justify-center gap-1 text-[10px] flex-wrap">
                    @if(!empty($urlAsistenciaNgrok))
                        <button type="button" id="btnModoNgrokSidebar" onclick="seleccionarModoRed('ngrok')"
                                class="px-2.5 py-1 rounded-lg font-bold transition-all bg-emerald-600 text-white cursor-pointer shadow-2xs flex items-center gap-1"
                                title="Túnel público por Ngrok (ideal para escanear con celulares usando datos móviles o cualquier red)">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-200 animate-pulse"></span>
                            <i class="fa-solid fa-bolt"></i> Ngrok (Celular)
                        </button>
                    @endif
                    <button type="button" id="btnModoLocalSidebar" onclick="seleccionarModoRed('local')"
                            class="px-2.5 py-1 rounded-lg font-bold transition-all text-slate-600 hover:text-slate-900 bg-white border border-slate-200 cursor-pointer shadow-2xs flex items-center gap-1"
                            title="Para celulares conectados a la misma red WiFi">
                        <i class="fa-solid fa-wifi"></i> WiFi Sala
                    </button>
                    <button type="button" id="btnModoWebSidebar" onclick="seleccionarModoRed('actual')"
                            class="px-2.5 py-1 rounded-lg font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 cursor-pointer transition-all flex items-center gap-1"
                            title="Dominio local pnycapacitaciones.test">
                        <i class="fa-solid fa-globe"></i> Web
                    </button>
                    <button type="button" onclick="configurarUrlPersonalizada()"
                            class="p-1 rounded-lg text-slate-400 hover:text-brand-blue hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Personalizar URL pública de asistencia">
                        <i class="fa-solid fa-gear text-[10px]"></i>
                    </button>
                </div>
            </div>

            <!-- Bloque 2: Lista de Asistentes en Tiempo Real -->
            <div class="p-3 bg-white border-b border-slate-200 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <h3 class="text-xs font-bold text-slate-900">Participantes en Vivo</h3>
                </div>
                <span id="badgeTotalAsistentesSidebar" class="font-mono font-bold text-xs px-2 py-0.5 rounded-full bg-blue-50 text-brand-blue border border-blue-200">
                    {{ $sesion->asistencias->count() }}
                </span>
            </div>

            <!-- Buscador de Asistentes -->
            <div class="p-2 border-b border-slate-200 shrink-0 bg-slate-50/50">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                    <input type="text" id="inputFiltroAsistentes" onkeyup="filtrarAsistentes()" placeholder="Buscar por nombre o cédula..."
                           class="w-full pl-7 pr-2 py-1 rounded-lg bg-white border border-slate-200 text-[11px] text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-blue">
                </div>
            </div>

            <!-- Listado con scroll de asistentes que van firmando -->
            <div id="contenedorListaAsistentes" class="flex-1 overflow-y-auto p-2.5 space-y-2 bg-slate-50/40">
                <div class="text-center py-8 text-xs text-slate-400">
                    <i class="fa-solid fa-user-clock text-2xl text-slate-300 mb-2"></i>
                    <div>Esperando participantes...</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">El código QR está disponible para escanear.</div>
                </div>
            </div>

            <!-- Bloque 3: Acciones de Descarga de Acta -->
            <div class="p-3 bg-white border-t border-slate-200 shrink-0 flex items-center gap-2">
                <button type="button" onclick="abrirModalConfirmarFinalizar()"
                        class="flex-1 py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-colors border border-rose-200 text-center cursor-pointer">
                    <i class="fa-solid fa-flag-checkered text-rose-600 group-hover:text-white"></i>
                    <span>Finalizar Inducción</span>
                </button>
            </div>

        </aside>

    </div>

    <!-- ========================================================================= -->
    <!-- ESCENARIO B: SESIÓN FINALIZADA (SE OCULTA LA PRESENTACIÓN Y SOLO MUESTRA  -->
    <!--              LOS PARTICIPANTES QUE ASISTIERON CON SUS FIRMAS Y ACTA)      -->
    <!-- ========================================================================= -->
    <div id="seccionSesionFinalizada" class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-100 {{ !$sesion->estaActiva() ? '' : 'hidden' }} select-text">
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Banner Institucional de Sesión Concluida -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Inducción Finalizada y Consolidada
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-500">
                            Código: <strong class="text-brand-blue">{{ $sesion->codigo_sesion }}</strong>
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-3xl font-black text-slate-900 font-heading leading-tight">
                        {{ $sesion->induccion->titulo }}
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                        La presentación ha concluido y el registro de asistentes se encuentra cerrado. A continuación se detallan los participantes que asistieron junto con sus firmas digitales.
                    </p>

                    <!-- Fechas y Facilitador -->
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 mt-4 pt-3 border-t border-slate-100">
                        <div>
                            <span class="text-slate-400 font-semibold">Facilitador:</span>
                            <strong class="text-slate-800">{{ $sesion->facilitador->nombre_completo ?? 'Facilitador' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold">Sede:</span>
                            <strong class="text-slate-800">{{ $sesion->sede->nombre ?? 'Principal' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold">Fecha:</span>
                            <strong class="text-slate-800">{{ $sesion->fecha_inicio ? $sesion->fecha_inicio->format('d/m/Y') : date('d/m/Y') }}</strong>
                        </div>
                        @if($sesion->fecha_cierre)
                            <div>
                                <span class="text-slate-400 font-semibold">Cierre:</span>
                                <strong class="text-slate-800">{{ $sesion->fecha_cierre->format('h:i A') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones Principales de Descarga -->
                <div class="flex flex-col sm:flex-row md:flex-col gap-2.5 shrink-0">
                    <a href="{{ route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]) }}"
                       class="px-6 py-3 rounded-2xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-file-pdf text-sm"></i>
                        <span>Descargar Acta Oficial (PDF)</span>
                    </a>
                    <a href="{{ route('induccion.index') }}"
                       class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-colors border border-slate-200 text-center">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Volver al Catálogo</span>
                    </a>
                </div>
            </div>

            <!-- Fila de Tarjetas / Métricas de Asistencia -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Asistentes</div>
                    <div class="text-2xl sm:text-3xl font-black text-brand-deep mt-1 font-mono" id="metricaTotalFinal">
                        {{ $sesion->asistencias->count() }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Participantes confirmados</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Colaboradores PNY</div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-800 mt-1 font-mono">
                        {{ $sesion->asistencias->where('es_externo', false)->count() }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Personal en nómina</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Personal Externo</div>
                    <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1 font-mono">
                        {{ $sesion->asistencias->where('es_externo', true)->count() }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">Contratistas y visitantes</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Firmas Digitales</div>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1 font-mono">
                        100%
                    </div>
                    <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">Firmas capturadas</div>
                </div>
            </div>

            <!-- Tabla de Participantes que Asistieron a la Inducción -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-heading flex items-center gap-2">
                            <i class="fa-solid fa-users text-brand-blue"></i>
                            <span>Listado de Participantes que Asistieron</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Registro oficial con identificación, cargo, hora y firma digital</p>
                    </div>

                    <!-- Buscador rápido -->
                    <div class="relative w-full sm:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="filtroTablaFinalizados" onkeyup="filtrarTablaFinalizados()" placeholder="Buscar participante..."
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-blue shadow-2xs">
                    </div>
                </div>

                <!-- Tabla de Asistencias -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4 w-12 text-center">#</th>
                                <th class="py-3 px-4">Participante</th>
                                <th class="py-3 px-4">Documento</th>
                                <th class="py-3 px-4">Cargo / Área</th>
                                <th class="py-3 px-4 text-center">Hora de Registro</th>
                                <th class="py-3 px-4 text-center">Firma Digital</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTablaFinalizados" class="divide-y divide-slate-100">
                            @forelse($sesion->asistencias as $idx => $asistencia)
                                <tr class="hover:bg-blue-50/30 transition-colors fila-asistente">
                                    <td class="py-3 px-4 font-mono font-bold text-slate-400 text-center text-xs">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2.5">
                                            @php
                                                $ini = collect(explode(' ', $asistencia->nombre_completo))->map(fn($n) => $n[0] ?? '')->take(2)->join('');
                                            @endphp
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-brand-blue to-ocean-wave text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ strtoupper($ini) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 text-xs truncate participante-nombre">{{ $asistencia->nombre_completo }}</div>
                                                @if($asistencia->es_externo)
                                                    <span class="inline-flex text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200 mt-0.5">
                                                        [EXTERNO] {{ $asistencia->empresa_externa ?? 'Contratista' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-brand-blue text-xs participante-documento">
                                        {{ $asistencia->identificacion }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-slate-800 text-xs">{{ $asistencia->cargo ?? 'Colaborador' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $asistencia->area ?? 'General' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-slate-500 text-xs">
                                        {{ $asistencia->fecha_registro ? $asistencia->fecha_registro->format('h:i A') : '' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($asistencia->firma_url)
                                            <button type="button" onclick="verFirmaModal('{{ $asistencia->firma_url }}', '{{ addslashes($asistencia->nombre_completo) }}')"
                                                    class="inline-block p-1 bg-white rounded-lg border border-slate-200 hover:border-brand-blue shadow-2xs transition-all cursor-pointer group"
                                                    title="Clic para ver firma ampliada">
                                                <img src="{{ $asistencia->firma_url }}" alt="Firma" class="h-8 max-w-[90px] object-contain mx-auto group-hover:scale-105 transition-transform">
                                            </button>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Sin firma</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px]"></i> Confirmado
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                        <i class="fa-solid fa-user-xmark text-3xl text-slate-300 mb-2"></i>
                                        <div>No se registraron participantes durante esta sesión de inducción.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div>
                        Mostrando <span class="font-bold text-slate-800">{{ $sesion->asistencias->count() }}</span> participante(s) en total
                    </div>
                    <div>
                         Acta de asistencia a inducción disponible para descarga.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CONFIRMACIÓN PARA FINALIZAR LA INDUCCIÓN                         -->
    <!-- ========================================================================= -->
    <div id="modalConfirmarFinalizar" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl text-center relative animate-fade-in">
            <button type="button" onclick="cerrarModalConfirmarFinalizar()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Ícono de Bandera / Finalización -->
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 border border-rose-200 shadow-xs">
                <i class="fa-solid fa-flag-checkered"></i>
            </div>

            <h3 class="text-xl font-black text-slate-900 mb-2 font-heading">
                ¿Deseas finalizar esta inducción?
            </h3>
            <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                Al finalizar la sesión, se cerrará el registro de asistencia, se ocultará la presentación de diapositivas y se consolidará el acta oficial con todas las firmas capturadas.
            </p>

            <!-- Resumen Informativo -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs text-left mb-6 space-y-1.5">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Código de sesión:</span>
                    <strong class="font-mono text-brand-deep">{{ $sesion->codigo_sesion }}</strong>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Asistentes registrados:</span>
                    <strong id="modalFinalizarConteo" class="text-emerald-700 font-bold">{{ $sesion->asistencias->count() }} participantes</strong>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>Sede:</span>
                    <span class="text-slate-800 font-medium">{{ $sesion->sede->nombre ?? 'Principal' }}</span>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="cerrarModalConfirmarFinalizar()"
                        class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer border border-slate-200">
                    Continuar Explicando
                </button>
                <button type="button" id="btnEjecutarFinalizar" onclick="ejecutarFinalizacionSesion()"
                        class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md hover:shadow-lg flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-flag-checkered"></i>
                    <span>Sí, Finalizar Sesión</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: MENSAJE DE ÉXITO AL FINALIZAR LA INDUCCIÓN                       -->
    <!-- ========================================================================= -->
    <div id="modalExitoFinalizacion" onclick="if (event.target === this) irAlHomeInduccion();" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-emerald-200 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl text-center relative animate-fade-in">
            <button type="button" onclick="irAlHomeInduccion()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer" title="Cerrar y volver al inicio">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Ícono Verde de Verificación -->
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-4 border border-emerald-200 shadow-xs">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 mb-2">
                Sesión Finalizada
            </span>

            <h3 class="text-2xl font-black text-slate-900 mb-2 font-heading">
                ¡Inducción Finalizada con Éxito!
            </h3>
            <p class="text-xs sm:text-sm text-slate-600 mb-5 leading-relaxed">
                La sesión ha sido cerrada exitosamente. Las firmas digitales han quedado consolidadas y el acta oficial de asistencia se encuentra lista. Puedes descargar el acta en PDF o continuar al panel de inducciones.
            </p>

            <!-- Resumen de Métricas -->
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200 text-xs text-left mb-6 flex items-center justify-around text-center">
                <div>
                    <div class="text-[10px] text-slate-500 uppercase font-bold">Total Asistentes</div>
                    <div id="modalExitoConteo" class="text-2xl font-black text-emerald-700 font-mono mt-0.5">
                        {{ $sesion->asistencias->count() }}
                    </div>
                </div>
                <div class="h-8 w-px bg-emerald-200"></div>
                <div>
                    <div class="text-[10px] text-slate-500 uppercase font-bold">Código Oficial</div>
                    <div class="text-sm font-black text-brand-deep font-mono mt-1">
                        {{ $sesion->codigo_sesion }}
                    </div>
                </div>
                <div class="h-8 w-px bg-emerald-200"></div>
                <div>
                    <div class="text-[10px] text-slate-500 uppercase font-bold">Sede</div>
                    <div class="text-xs font-bold text-slate-800 mt-1 truncate max-w-[120px]">
                        {{ $sesion->sede->nombre ?? 'Sede Principal' }}
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5">
                <button type="button" onclick="descargarActaYSalir()"
                   class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-brand-blue hover:bg-brand-deep text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Descargar Acta (PDF) y Salir</span>
                </button>
                <button type="button" onclick="irAlHomeInduccion()"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition-colors border border-slate-200 cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-house"></i>
                    <span>Continuar al Inicio</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: PROYECCIÓN DE QR GIGANTE PARA SALA COMPLETA                      -->
    <!-- ========================================================================= -->
    <div id="modalQRGigante" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center relative shadow-2xl">
            <button type="button" onclick="cerrarModalQR()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-brand-blue border border-blue-200 mb-2">
                <i class="fa-solid fa-qrcode"></i> Registro de Asistencia
            </span>

            <h3 class="text-xl font-black text-slate-900 mb-1 font-heading">{{ $sesion->induccion->titulo }}</h3>
            <p class="text-xs text-slate-500 mb-4">Apunta con la cámara de tu celular para ingresar y firmar tu asistencia.</p>

            <div class="inline-block p-4 bg-white rounded-3xl shadow-xl border-4 border-blue-100 mb-4 mx-auto">
                <div id="modalMaxiQRElement" class="w-64 h-64 flex items-center justify-center"></div>
            </div>

            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-xs text-left max-w-sm mx-auto space-y-1.5">
                <div class="text-slate-500 text-[11px]">¿Sin cámara? Entra a:</div>
                <div class="text-brand-blue font-mono font-bold text-xs truncate select-all" id="textoUrlModalQR">
                    {{ $urlManualActual }}
                </div>
                <div class="text-slate-500 text-[11px] pt-1">
                    Código de sesión: <strong class="text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded font-mono text-sm ml-1 select-all font-bold">{{ $sesion->codigo_sesion }}</strong>
                </div>
            </div>

            <div class="mt-4">
                <button type="button" onclick="cerrarModalQR()"
                        class="px-6 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider cursor-pointer border border-slate-200">
                    Cerrar y Continuar
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: ÍNDICE DE DIAPOSITIVAS (SALTAR A CUALQUIERA)                     -->
    <!-- ========================================================================= -->
    <div id="modalIndiceDiapositivas" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-brand-blue"></i>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Diapositivas de la Inducción</h3>
                </div>
                <button type="button" onclick="cerrarModalIndice()" class="text-slate-400 hover:text-slate-700 p-1 text-base cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-3 grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                @foreach($slides as $idx => $s)
                    <button type="button" onclick="irASlide({{ $idx }}); cerrarModalIndice();"
                            class="p-3 rounded-xl bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 text-left transition-all group cursor-pointer flex flex-col justify-between h-24">
                        <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                            <span class="group-hover:text-brand-blue font-bold">#{{ $idx + 1 }}</span>
                            <span class="uppercase px-1 rounded bg-white text-slate-600 text-[9px] border border-slate-200">{{ $s['tipo'] }}</span>
                        </div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-brand-blue line-clamp-2 mt-1">
                            {{ $s['titulo'] ?? ('Diapositiva ' . ($idx + 1)) }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: ZOOM DE FIRMA DIGITAL CAPTURADA                                  -->
    <!-- ========================================================================= -->
    <div id="modalZoomFirma" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-sm w-full p-6 text-center relative shadow-2xl animate-fade-in">
            <button type="button" onclick="cerrarModalFirma()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-base cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h4 class="text-sm font-bold text-slate-900 mb-1" id="modalFirmaNombre">Firma Digital</h4>
            <p class="text-[11px] text-slate-500 mb-3">Firma digital estampada en el celular</p>
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-center min-h-[140px]">
                <img id="modalFirmaImg" src="" alt="Firma" class="max-h-36 max-w-full object-contain">
            </div>
            <button type="button" onclick="cerrarModalFirma()" class="mt-4 px-5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold cursor-pointer">
                Cerrar
            </button>
        </div>
    </div>

    <!-- NOTIFICACIÓN TOAST CUANDO ENTRA UN ASISTENTE NUEVO -->
    <div id="toastNuevoParticipante" class="fixed top-16 right-4 z-50 transform translate-y-[-150%] opacity-0 transition-all duration-300 pointer-events-none max-w-sm">
        <div class="bg-white border border-emerald-300 text-slate-800 p-3.5 rounded-2xl shadow-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base shrink-0 border border-emerald-200">
                <i class="fa-solid fa-signature"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">¡Nuevo Asistente Confirmado!</div>
                <div id="toastNombreTexto" class="text-xs font-bold text-slate-900 truncate">Juan Pérez</div>
                <div id="toastAreaTexto" class="text-[10px] text-slate-500 truncate">Operaciones</div>
            </div>
        </div>
    </div>

    <!-- NOTIFICACIÓN TOAST INFORMATIVA (COPIAR CÓDIGO, ETC) -->
    <div id="toastInformativo" class="fixed top-16 right-4 z-50 transform translate-y-[-150%] opacity-0 transition-all duration-300 pointer-events-none max-w-sm">
        <div class="bg-white border border-blue-200 text-slate-800 p-3.5 rounded-2xl shadow-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-brand-blue flex items-center justify-center text-base shrink-0 border border-blue-200">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="min-w-0">
                <div class="text-[10px] text-brand-blue font-bold uppercase tracking-wider">Aviso</div>
                <div id="toastInformativoTexto" class="text-xs font-bold text-slate-900 truncate">Información copiada</div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ALERTA O ERROR (REEMPLAZA ALERTAS NATIVAS DEL NAVEGADOR) -->
    <div id="modalAlertaGenerica" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-sm w-full p-6 text-center relative shadow-2xl animate-fade-in">
            <div id="modalAlertaIcono" class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-3 border border-amber-200 shadow-xs">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <h4 id="modalAlertaTitulo" class="text-base font-black text-slate-900 mb-1 font-heading">Atención</h4>
            <p id="modalAlertaMensaje" class="text-xs text-slate-600 mb-5 leading-relaxed">Mensaje de alerta</p>
            <button type="button" onclick="cerrarModalAlerta()" class="w-full py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                Entendido
            </button>
        </div>
    </div>

    <!-- JAVASCRIPT DEL SLIDER, MODALES DE FINALIZACIÓN Y STREAMING EN VIVO -->
    <script>
        const CODIGO_SESION = @json($sesion->codigo_sesion);
        const URL_ASISTENCIA_ACTUAL = @json($urlAsistencia);
        const URL_ASISTENCIA_LOCAL = @json($urlAsistenciaLocal);
        let URL_ASISTENCIA_NGROK = @json($urlAsistenciaNgrok);
        const URL_MANUAL_ACTUAL = @json($urlManualActual);
        const URL_MANUAL_LOCAL = @json($urlManualLocal);
        let URL_MANUAL_NGROK = @json($urlManualNgrok);
        const LOCAL_IP = @json($localIp);
        const TOTAL_SLIDES = {{ count($slides) }};
        const STREAM_URL = `/induccion/sesion/${CODIGO_SESION}/asistencias-stream`;
        const ES_SESION_ACTIVA = {{ $sesion->estaActiva() ? 'true' : 'false' }};

        // Si el usuario guardó una URL pública personalizada en localStorage
        const customUrlGuardada = localStorage.getItem('pny_custom_ngrok_url');
        if (customUrlGuardada) {
            URL_ASISTENCIA_NGROK = customUrlGuardada.replace(/\/+$/, '') + '/asistencia/' + CODIGO_SESION;
            URL_MANUAL_NGROK = customUrlGuardada.replace(/\/+$/, '') + '/asistencia';
        }

        let slideActualIndex = 0;
        let qrActualMode = URL_ASISTENCIA_NGROK ? 'ngrok' : ((window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.hostname.endsWith('.test')) ? 'local' : 'actual');
        let totalAsistentesAnterior = {{ $sesion->asistencias->count() }};
        let intervalStream = null;
        let sidebarQrInstance = null;
        let modalQrInstance = null;
        let hudQrInstance = null;
        let listaAsistentesCache = [];
        let pdfDocs = {};
        let pdfZoomLevels = {};

        function getUrlAsistenciaActiva() {
            if (qrActualMode === 'ngrok' && URL_ASISTENCIA_NGROK) {
                return URL_ASISTENCIA_NGROK;
            }
            if (qrActualMode === 'local' && LOCAL_IP) {
                return URL_ASISTENCIA_LOCAL;
            }
            return URL_ASISTENCIA_ACTUAL;
        }

        function getUrlManualActiva() {
            if (qrActualMode === 'ngrok' && URL_MANUAL_NGROK) {
                return URL_MANUAL_NGROK;
            }
            if (qrActualMode === 'local' && LOCAL_IP) {
                return URL_MANUAL_LOCAL;
            }
            return URL_MANUAL_ACTUAL;
        }

        function actualizarCodigosQR() {
            if (!ES_SESION_ACTIVA) return;

            const urlActiva = getUrlAsistenciaActiva();
            const urlManual = getUrlManualActiva();

            const txtSide = document.getElementById('textoUrlManualSidebar');
            if (txtSide) txtSide.textContent = urlManual;

            const txtModal = document.getElementById('textoUrlModalQR');
            if (txtModal) txtModal.textContent = urlManual;

            const sideElem = document.getElementById("sidebarQRElement");
            if (sideElem) {
                sideElem.innerHTML = '';
                sidebarQrInstance = new QRCode(sideElem, {
                    text: urlActiva,
                    width: 144,
                    height: 144,
                    colorDark: "#003d80",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            const hudElem = document.getElementById("hudQRElement");
            if (hudElem) {
                hudElem.innerHTML = '';
                hudQrInstance = new QRCode(hudElem, {
                    text: urlActiva,
                    width: 56,
                    height: 56,
                    colorDark: "#003d80",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            const modalElem = document.getElementById("modalMaxiQRElement");
            if (modalElem) {
                modalElem.innerHTML = '';
                modalQrInstance = new QRCode(modalElem, {
                    text: urlActiva,
                    width: 256,
                    height: 256,
                    colorDark: "#003d80",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        }

        function seleccionarModoRed(modo) {
            qrActualMode = modo;
            const btnNgrok = document.getElementById('btnModoNgrokSidebar');
            const btnLocal = document.getElementById('btnModoLocalSidebar');
            const btnWeb = document.getElementById('btnModoWebSidebar');

            const activeBlue = "px-2.5 py-1 rounded-lg font-bold transition-all bg-brand-blue text-white cursor-pointer shadow-2xs flex items-center gap-1";
            const activeEmerald = "px-2.5 py-1 rounded-lg font-bold transition-all bg-emerald-600 text-white cursor-pointer shadow-2xs flex items-center gap-1";
            const inactive = "px-2.5 py-1 rounded-lg font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 cursor-pointer transition-all flex items-center gap-1";

            if (btnNgrok) btnNgrok.className = (modo === 'ngrok') ? activeEmerald : inactive;
            if (btnLocal) btnLocal.className = (modo === 'local') ? activeBlue : inactive;
            if (btnWeb) btnWeb.className = (modo === 'actual') ? activeBlue : inactive;

            actualizarCodigosQR();
        }

        function configurarUrlPersonalizada() {
            const actual = URL_MANUAL_NGROK ? URL_MANUAL_NGROK.replace(/\/asistencia$/, '') : '';
            const nueva = prompt("Ingresa la URL pública de tu túnel (ej. https://xxxx.ngrok-free.dev):", actual);
            if (nueva !== null) {
                const limpia = nueva.trim().replace(/\/+$/, '');
                if (limpia) {
                    localStorage.setItem('pny_custom_ngrok_url', limpia);
                    URL_ASISTENCIA_NGROK = limpia + '/asistencia/' + CODIGO_SESION;
                    URL_MANUAL_NGROK = limpia + '/asistencia';
                    let btnNgrok = document.getElementById('btnModoNgrokSidebar');
                    if (btnNgrok) btnNgrok.classList.remove('hidden');
                    seleccionarModoRed('ngrok');
                    mostrarToastInformativo("URL de escaneo actualizada a: " + limpia);
                } else {
                    localStorage.removeItem('pny_custom_ngrok_url');
                    mostrarToastInformativo("Se restableció la URL detectada por defecto.");
                    URL_ASISTENCIA_NGROK = @json($urlAsistenciaNgrok);
                    URL_MANUAL_NGROK = @json($urlManualNgrok);
                    seleccionarModoRed(URL_ASISTENCIA_NGROK ? 'ngrok' : 'actual');
                }
            }
        }

        // ==========================================
        // DETENER VIDEOS AL CAMBIAR DE DIAPOSITIVA
        // ==========================================
        function detenerVideosSlide(slideElem) {
            if (!slideElem) return;

            slideElem.querySelectorAll('video').forEach(video => {
                try {
                    video.pause();
                } catch (e) {}
            });

            slideElem.querySelectorAll('iframe').forEach(iframe => {
                try {
                    iframe.contentWindow.postMessage(JSON.stringify({
                        event: 'command',
                        func: 'pauseVideo',
                        args: []
                    }), '*');
                } catch (e) {}

                try {
                    iframe.contentWindow.postMessage(JSON.stringify({
                        method: 'pause'
                    }), '*');
                } catch (e) {}

                const currentSrc = iframe.getAttribute('src');
                if (currentSrc) {
                    iframe.setAttribute('src', currentSrc);
                }
            });
        }

        // ==========================================
        // CONTROL DEL SLIDER DE DIAPOSITIVAS
        // ==========================================
        function irASlide(index) {
            if (index < 0 || index >= TOTAL_SLIDES) return;

            const slideAnteriorElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (slideAnteriorElem) {
                detenerVideosSlide(slideAnteriorElem);
                slideAnteriorElem.classList.remove('active-slide');
            }

            slideActualIndex = index;

            const slideNuevoElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (slideNuevoElem) {
                slideNuevoElem.classList.add('active-slide');

                const bTop = document.getElementById('badgeSlideActualTop');
                if (bTop) bTop.textContent = slideActualIndex + 1;

                const bFoot = document.getElementById('badgeSlideActualFooter');
                if (bFoot) bFoot.textContent = slideActualIndex + 1;

                const titulo = slideNuevoElem.dataset.slideTitle || `Diapositiva ${slideActualIndex + 1}`;
                const tFoot = document.getElementById('slideTituloFooter');
                if (tFoot) tFoot.textContent = titulo;

                const progreso = ((slideActualIndex + 1) / TOTAL_SLIDES) * 100;
                const pBar = document.getElementById('slideProgressBar');
                if (pBar) pBar.style.width = `${progreso}%`;

                if (slideNuevoElem.dataset.slideType === 'pdf') {
                    renderizarSlidePdf(slideNuevoElem);
                }

                ajustarEscalaPptx();
            }
        }

        function slideSiguiente() {
            const slideActualElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (slideActualElem && slideActualElem.dataset.slideType === 'pdf') {
                const pdfInfo = pdfDocs[slideActualIndex];
                if (pdfInfo && pdfInfo.currentPage < pdfInfo.totalPages) {
                    pdfInfo.currentPage++;
                    renderizarPaginaPdf(slideActualElem, slideActualIndex, pdfInfo.currentPage);
                    return;
                }
            }

            if (slideActualIndex < TOTAL_SLIDES - 1) {
                irASlide(slideActualIndex + 1);
            }
        }

        function slideAnterior() {
            const slideActualElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (slideActualElem && slideActualElem.dataset.slideType === 'pdf') {
                const pdfInfo = pdfDocs[slideActualIndex];
                if (pdfInfo && pdfInfo.currentPage > 1) {
                    pdfInfo.currentPage--;
                    renderizarPaginaPdf(slideActualElem, slideActualIndex, pdfInfo.currentPage);
                    return;
                }
            }

            if (slideActualIndex > 0) {
                irASlide(slideActualIndex - 1);
            }
        }

        function ajustarEscalaPptx() {
            const stage = document.getElementById('slideStage');
            if (!stage) return;

            const stageWidth = stage.clientWidth - 40;
            const stageHeight = stage.clientHeight - 40;

            document.querySelectorAll('.pptx-slide-wrapper').forEach(wrapper => {
                const origW = parseFloat(wrapper.dataset.width || 1920);
                const origH = parseFloat(wrapper.dataset.height || 1080);
                const scale = Math.min(stageWidth / origW, stageHeight / origH);

                const canvas = wrapper.querySelector('.pptx-canvas');
                if (canvas) {
                    canvas.style.transform = `scale(${scale})`;
                    canvas.style.transformOrigin = '0 0';
                    wrapper.style.width = `${origW * scale}px`;
                    wrapper.style.height = `${origH * scale}px`;
                }
            });
        }
        window.addEventListener('resize', ajustarEscalaPptx);

        // ==========================================
        // RENDERIZADO DE PDF CON ZOOM
        // ==========================================
        async function renderizarSlidePdf(slideElem) {
            const slideIndex = parseInt(slideElem.dataset.slideIndex);
            const pdfContainer = slideElem.querySelector('.pdf-container-slide') || slideElem;
            const pdfUrl = pdfContainer.dataset.pdfUrl;
            const canvas = slideElem.querySelector('.pdf-render-canvas');
            const loader = slideElem.querySelector('.pdf-loader');
            const totalPagesSpan = slideElem.querySelector('.pdf-total-pages');

            if (!pdfUrl || !canvas) return;

            try {
                if (!pdfDocs[slideIndex]) {
                    if (typeof pdfjsLib === 'undefined') {
                        loader.innerHTML = `
                            <iframe src="${pdfUrl}#toolbar=1" class="w-full h-[65vh] rounded-xl"></iframe>
                        `;
                        return;
                    }

                    const loadingTask = pdfjsLib.getDocument(pdfUrl);
                    const pdf = await loadingTask.promise;
                    pdfDocs[slideIndex] = {
                        doc: pdf,
                        currentPage: 1,
                        totalPages: pdf.numPages
                    };
                    pdfZoomLevels[slideIndex] = 1.0;
                }

                const pdfInfo = pdfDocs[slideIndex];
                if (totalPagesSpan) totalPagesSpan.textContent = pdfInfo.totalPages;

                await renderizarPaginaPdf(slideElem, slideIndex, pdfInfo.currentPage);

                canvas.classList.remove('hidden');
                if (loader) loader.classList.add('hidden');
            } catch (err) {
                console.error("Error al cargar PDF:", err);
                if (loader) {
                    loader.innerHTML = `
                        <iframe src="${pdfUrl}#toolbar=1" class="w-full h-[65vh] rounded-xl"></iframe>
                    `;
                }
            }
        }

        async function renderizarPaginaPdf(slideElem, slideIndex, pageNum) {
            const pdfInfo = pdfDocs[slideIndex];
            if (!pdfInfo) return;

            const canvas = slideElem.querySelector('.pdf-render-canvas');
            const currentPageSpan = slideElem.querySelector('.pdf-current-page');
            if (currentPageSpan) currentPageSpan.textContent = pageNum;

            const page = await pdfInfo.doc.getPage(pageNum);
            const stage = document.getElementById('slideStage');
            const maxW = stage.clientWidth - 60;
            const maxH = stage.clientHeight - 90;

            const unscaledViewport = page.getViewport({ scale: 1 });
            const baseScale = Math.min(maxW / unscaledViewport.width, maxH / unscaledViewport.height, 2.0);
            const zoom = pdfZoomLevels[slideIndex] || 1.0;
            const scale = baseScale * zoom;

            const viewport = page.getViewport({ scale: scale });

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            const ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        }

        function pdfPaginaSiguiente(btn) {
            const slideElem = document.getElementById(`slidePane-${slideActualIndex}`);
            const pdfInfo = pdfDocs[slideActualIndex];
            if (slideElem && pdfInfo && pdfInfo.currentPage < pdfInfo.totalPages) {
                pdfInfo.currentPage++;
                renderizarPaginaPdf(slideElem, slideActualIndex, pdfInfo.currentPage);
            }
        }

        function pdfPaginaAnterior(btn) {
            const slideElem = document.getElementById(`slidePane-${slideActualIndex}`);
            const pdfInfo = pdfDocs[slideActualIndex];
            if (slideElem && pdfInfo && pdfInfo.currentPage > 1) {
                pdfInfo.currentPage--;
                renderizarPaginaPdf(slideElem, slideActualIndex, pdfInfo.currentPage);
            }
        }

        async function cambiarZoomPdf(delta) {
            const slideElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (!slideElem || slideElem.dataset.slideType !== 'pdf') return;

            if (!pdfZoomLevels[slideActualIndex]) {
                pdfZoomLevels[slideActualIndex] = 1.0;
            }

            let nuevoZoom = pdfZoomLevels[slideActualIndex] + delta;
            nuevoZoom = Math.min(Math.max(nuevoZoom, 0.5), 3.0);
            pdfZoomLevels[slideActualIndex] = nuevoZoom;

            const zoomLabel = slideElem.querySelector('.pdf-zoom-label');
            if (zoomLabel) {
                zoomLabel.textContent = Math.round(nuevoZoom * 100) + '%';
            }

            const pdfInfo = pdfDocs[slideActualIndex];
            if (pdfInfo) {
                await renderizarPaginaPdf(slideElem, slideActualIndex, pdfInfo.currentPage);
            }
        }

        async function resetearZoomPdf() {
            const slideElem = document.getElementById(`slidePane-${slideActualIndex}`);
            if (!slideElem || slideElem.dataset.slideType !== 'pdf') return;

            pdfZoomLevels[slideActualIndex] = 1.0;
            const zoomLabel = slideElem.querySelector('.pdf-zoom-label');
            if (zoomLabel) {
                zoomLabel.textContent = '100%';
            }

            const pdfInfo = pdfDocs[slideActualIndex];
            if (pdfInfo) {
                await renderizarPaginaPdf(slideElem, slideActualIndex, pdfInfo.currentPage);
            }
        }

        // ==========================================
        // STREAMING EN TIEMPO REAL DE ASISTENCIAS
        // ==========================================
        async function consultarAsistencias() {
            try {
                const response = await fetch(STREAM_URL);
                if (!response.ok) return;

                const data = await response.json();
                listaAsistentesCache = data.asistencias || [];

                const total = data.total || 0;
                const badgeSide = document.getElementById('badgeTotalAsistentesSidebar');
                const badgeHUD = document.getElementById('hudAsistentesBadge');
                const modalFinalizarTotal = document.getElementById('modalFinalizarConteo');

                if (badgeSide) badgeSide.textContent = total;
                if (badgeHUD) badgeHUD.textContent = total;
                if (modalFinalizarTotal) modalFinalizarTotal.textContent = `${total} participantes`;

                if (total > totalAsistentesAnterior) {
                    const ultimo = listaAsistentesCache[0];
                    if (ultimo) {
                        mostrarToastNuevoAsistente(ultimo.nombre, ultimo.area || ultimo.cargo);
                    }
                    totalAsistentesAnterior = total;
                }

                renderizarListaAsistentes(listaAsistentesCache);
            } catch (err) {
                console.error("Error stream asistencias:", err);
            }
        }

        function mostrarToastNuevoAsistente(nombre, area) {
            const toast = document.getElementById('toastNuevoParticipante');
            document.getElementById('toastNombreTexto').textContent = nombre;
            document.getElementById('toastAreaTexto').textContent = area;

            toast.classList.remove('translate-y-[-150%]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-[-150%]', 'opacity-0');
            }, 4000);
        }

        function renderizarListaAsistentes(asistencias) {
            const container = document.getElementById('contenedorListaAsistentes');
            if (!container) return;

            if (asistencias.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-xs text-slate-400">
                        <i class="fa-solid fa-user-clock text-2xl text-slate-300 mb-2"></i>
                        <div>Esperando participantes...</div>
                        <div class="text-[10px] text-slate-400 mt-0.5">El código QR está disponible para escanear.</div>
                    </div>
                `;
                return;
            }

            container.innerHTML = asistencias.map((a) => {
                const iniciales = a.nombre.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                return `
                    <div class="p-2.5 rounded-xl bg-white border border-slate-200 flex items-start justify-between gap-2 transition-all hover:border-blue-300 shadow-2xs">
                        <div class="flex items-start gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-blue to-ocean-wave text-white text-[10px] font-bold flex items-center justify-center shrink-0 shadow-xs">
                                ${iniciales}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-900 truncate">${a.nombre}</div>
                                <div class="text-[10px] text-brand-blue font-mono font-bold">CC: ${a.identificacion}</div>
                                <div class="text-[9px] text-slate-500 truncate">
                                    ${a.es_externo ? '<span class="text-amber-700 font-bold mr-1">[EXTERNO]</span>' : ''}${a.cargo}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[9px] font-mono text-slate-400">${a.hora}</span>
                            <div class="mt-0.5">
                                <span class="inline-flex items-center text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                    <i class="fa-solid fa-check mr-0.5"></i> Firmado
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filtrarAsistentes() {
            const query = (document.getElementById('inputFiltroAsistentes').value || '').toLowerCase().trim();
            if (!query) {
                renderizarListaAsistentes(listaAsistentesCache);
                return;
            }
            const filtrados = listaAsistentesCache.filter(a =>
                a.nombre.toLowerCase().includes(query) ||
                a.identificacion.toLowerCase().includes(query) ||
                (a.cargo && a.cargo.toLowerCase().includes(query)) ||
                (a.area && a.area.toLowerCase().includes(query))
            );
            renderizarListaAsistentes(filtrados);
        }

        // ==========================================
        // FILTRAR TABLA DE SESIÓN FINALIZADA
        // ==========================================
        function filtrarTablaFinalizados() {
            const query = (document.getElementById('filtroTablaFinalizados')?.value || '').toLowerCase().trim();
            const filas = document.querySelectorAll('.fila-asistente');

            filas.forEach(fila => {
                const nombre = (fila.querySelector('.participante-nombre')?.textContent || '').toLowerCase();
                const documento = (fila.querySelector('.participante-documento')?.textContent || '').toLowerCase();

                if (!query || nombre.includes(query) || documento.includes(query)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        // ==========================================
        // PANTALLA COMPLETA Y COLAPSO DE SIDEBAR
        // ==========================================
        function toggleSidebarAsistencias() {
            const sidebar = document.getElementById('sidebarColumn');
            const texto = document.getElementById('textoToggleSidebar');
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                if (texto) texto.textContent = 'Ocultar Panel';
            } else {
                sidebar.classList.add('collapsed');
                if (texto) texto.textContent = 'Mostrar Panel';
            }
            setTimeout(ajustarEscalaPptx, 320);
        }

        function togglePantallaCompleta() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                document.exitFullscreen().catch(err => console.log(err));
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const body = document.body;
            const icon = document.getElementById('fullscreenIconTop');

            if (document.fullscreenElement) {
                body.classList.add('is-fullscreen');
                if (icon) icon.className = "fa-solid fa-compress text-xs";
            } else {
                body.classList.remove('is-fullscreen');
                if (icon) icon.className = "fa-solid fa-expand text-xs";
            }
            setTimeout(ajustarEscalaPptx, 250);
        });

        // ==========================================
        // MODALES DE FINALIZACIÓN Y FLUJO DE ESTADO
        // ==========================================
        function abrirModalConfirmarFinalizar() {
            const conteoElem = document.getElementById('modalFinalizarConteo');
            if (conteoElem) {
                conteoElem.textContent = `${totalAsistentesAnterior} participantes`;
            }
            document.getElementById('modalConfirmarFinalizar').classList.remove('hidden');
        }

        function cerrarModalConfirmarFinalizar() {
            document.getElementById('modalConfirmarFinalizar').classList.add('hidden');
        }

        async function ejecutarFinalizacionSesion() {
            const btn = document.getElementById('btnEjecutarFinalizar');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Finalizando...`;

            try {
                const response = await fetch(`/induccion/sesion/${CODIGO_SESION}/finalizar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // 1. Cerrar modal de confirmación
                    cerrarModalConfirmarFinalizar();

                    // 2. Salir de pantalla completa si estaba activa
                    if (document.fullscreenElement) {
                        try { await document.exitFullscreen(); } catch (e) {}
                    }

                    // 3. OCULTAR INMEDIATAMENTE LA PRESENTACIÓN (SLIDER Y PANEL LATERAL)
                    const seccionActiva = document.getElementById('seccionSesionActiva');
                    if (seccionActiva) {
                        seccionActiva.classList.add('hidden');
                        seccionActiva.style.setProperty('display', 'none', 'important');
                    }

                    // 4. ADAPTAR HEADER
                    const controlesHeader = document.getElementById('controlesSliderHeader');
                    if (controlesHeader) controlesHeader.classList.add('hidden');

                    const accionesActiva = document.getElementById('accionesSesionActivaHeader');
                    if (accionesActiva) accionesActiva.classList.add('hidden');

                    const accionesFinalizada = document.getElementById('accionesSesionFinalizadaHeader');
                    if (accionesFinalizada) accionesFinalizada.classList.remove('hidden');

                    // 5. MOSTRAR LA PANTALLA EXCLUSIVA DE PARTICIPANTES QUE ASISTIERON
                    const seccionFinalizada = document.getElementById('seccionSesionFinalizada');
                    if (seccionFinalizada) {
                        seccionFinalizada.classList.remove('hidden');
                    }

                    // 6. Detener polling de stream
                    if (intervalStream) clearInterval(intervalStream);

                    // 7. Detener videos que estuvieran sonando
                    const slideActualElem = document.getElementById(`slidePane-${slideActualIndex}`);
                    if (slideActualElem) detenerVideosSlide(slideActualElem);

                    // 8. Sincronizar participantes en la tabla final
                    sincronizarParticipantesEnTablaFinal();

                    // 9. Actualizar métricas del modal de éxito
                    const exitoConteo = document.getElementById('modalExitoConteo');
                    if (exitoConteo) exitoConteo.textContent = totalAsistentesAnterior;

                    // 10. Abrir modal de éxito institucional
                    document.getElementById('modalExitoFinalizacion').classList.remove('hidden');
                } else {
                    mostrarModalAlerta(data.message || "No se pudo finalizar la sesión. Inténtalo nuevamente.", "Error al Finalizar", true);
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fa-solid fa-flag-checkered mr-1"></i> Sí, Finalizar Sesión`;
                }
            } catch (err) {
                console.error(err);
                mostrarModalAlerta("Ocurrió un error al intentar finalizar la sesión. Verifica tu conexión con el servidor.", "Error de Conexión", true);
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-flag-checkered mr-1"></i> Sí, Finalizar Sesión`;
            }
        }

        function descargarActaYSalir() {
            const urlActa = "{{ route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]) }}";
            const urlHome = "{{ route('induccion.index', ['ver_sesion' => $sesion->codigo_sesion]) }}";

            const a = document.createElement('a');
            a.href = urlActa;
            a.setAttribute('download', '');
            a.target = '_blank';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);

            setTimeout(() => {
                window.location.href = urlHome;
            }, 600);
        }

        function irAlHomeInduccion() {
            window.location.href = "{{ route('induccion.index', ['ver_sesion' => $sesion->codigo_sesion]) }}";
        }

        function mostrarPantallaParticipantesFinalizados() {
            irAlHomeInduccion();
        }

        function sincronizarParticipantesEnTablaFinal() {
            const metricaTotal = document.getElementById('metricaTotalFinal');
            if (metricaTotal) metricaTotal.textContent = totalAsistentesAnterior;

            if (listaAsistentesCache && listaAsistentesCache.length > 0) {
                const tbody = document.getElementById('cuerpoTablaFinalizados');
                if (tbody) {
                    tbody.innerHTML = listaAsistentesCache.map((a, idx) => {
                        const ini = (a.nombre || '').split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
                        const firmaHtml = a.firma_url 
                            ? `<button type="button" onclick="verFirmaModal('${a.firma_url}', '${(a.nombre || '').replace(/'/g, "\\'")}')" class="inline-block p-1 bg-white rounded-lg border border-slate-200 hover:border-brand-blue shadow-2xs transition-all cursor-pointer group" title="Clic para ver firma ampliada">
                                <img src="${a.firma_url}" alt="Firma" class="h-8 max-w-[90px] object-contain mx-auto group-hover:scale-105 transition-transform">
                               </button>`
                            : `<span class="text-[11px] text-slate-400 italic">Sin firma</span>`;

                        const externoBadge = a.es_externo 
                            ? `<span class="inline-flex text-[9px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200 mt-0.5">
                                [EXTERNO] ${a.empresa || 'Contratista'}
                               </span>`
                            : '';

                        return `
                            <tr class="hover:bg-blue-50/30 transition-colors fila-asistente">
                                <td class="py-3 px-4 font-mono font-bold text-slate-400 text-center text-xs">
                                    ${idx + 1}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-brand-blue to-ocean-wave text-white text-xs font-bold flex items-center justify-center shrink-0 shadow-2xs">
                                            ${ini}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-xs truncate participante-nombre">${a.nombre}</div>
                                            ${externoBadge}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono font-bold text-brand-blue text-xs participante-documento">
                                    ${a.identificacion}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-800 text-xs">${a.cargo || 'Colaborador'}</div>
                                    <div class="text-[11px] text-slate-500">${a.area || 'General'}</div>
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-slate-500 text-xs">
                                    ${a.hora || ''}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    ${firmaHtml}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        <i class="fa-solid fa-check text-[9px]"></i> Confirmado
                                    </span>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            }
        }

        // ==========================================
        // MODAL DE ALERTAS GENÉRICAS
        // ==========================================
        function mostrarModalAlerta(mensaje, titulo = 'Atención', esError = false) {
            const modal = document.getElementById('modalAlertaGenerica');
            const tit = document.getElementById('modalAlertaTitulo');
            const msg = document.getElementById('modalAlertaMensaje');
            const ico = document.getElementById('modalAlertaIcono');

            if (tit) tit.textContent = titulo;
            if (msg) msg.textContent = mensaje;
            if (ico) {
                if (esError) {
                    ico.className = "w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-3 border border-rose-200 shadow-xs";
                    ico.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                } else {
                    ico.className = "w-14 h-14 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center text-2xl mx-auto mb-3 border border-blue-200 shadow-xs";
                    ico.innerHTML = '<i class="fa-solid fa-circle-info"></i>';
                }
            }
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalAlerta() {
            const modal = document.getElementById('modalAlertaGenerica');
            if (modal) modal.classList.add('hidden');
        }

        function mostrarToastInformativo(mensaje) {
            const toast = document.getElementById('toastInformativo');
            if (!toast) return;
            const txt = document.getElementById('toastInformativoTexto');
            if (txt) txt.textContent = mensaje;

            toast.classList.remove('translate-y-[-150%]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-[-150%]', 'opacity-0');
            }, 3000);
        }

        // ==========================================
        // MODAL DE ZOOM DE FIRMA DIGITAL
        // ==========================================
        function verFirmaModal(firmaUrl, nombreParticipante) {
            document.getElementById('modalFirmaImg').src = firmaUrl;
            document.getElementById('modalFirmaNombre').textContent = `Firma de ${nombreParticipante}`;
            document.getElementById('modalZoomFirma').classList.remove('hidden');
        }

        function cerrarModalFirma() {
            document.getElementById('modalZoomFirma').classList.add('hidden');
        }

        // ==========================================
        // MODALES SECUNDARIOS
        // ==========================================
        function abrirModalQR() {
            actualizarCodigosQR();
            document.getElementById('modalQRGigante').classList.remove('hidden');
        }

        function cerrarModalQR() {
            document.getElementById('modalQRGigante').classList.add('hidden');
        }

        function abrirModalIndice() {
            document.getElementById('modalIndiceDiapositivas').classList.remove('hidden');
        }

        function cerrarModalIndice() {
            document.getElementById('modalIndiceDiapositivas').classList.add('hidden');
        }

        function copiarCodigo() {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(CODIGO_SESION).then(() => {
                    mostrarToastInformativo(`Código '${CODIGO_SESION}' copiado al portapapeles.`);
                }).catch(() => {
                    mostrarToastInformativo(`Código de sesión: ${CODIGO_SESION}`);
                });
            } else {
                mostrarToastInformativo(`Código de sesión: ${CODIGO_SESION}`);
            }
        }

        // ==========================================
        // ATAJOS DE TECLADO
        // ==========================================
        document.addEventListener('keydown', (e) => {
            if (['input', 'textarea'].includes(document.activeElement.tagName.toLowerCase())) {
                return;
            }

            switch(e.key) {
                case 'ArrowRight':
                case ' ':
                case 'PageDown':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        slideSiguiente();
                    }
                    break;
                case 'ArrowLeft':
                case 'PageUp':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        slideAnterior();
                    }
                    break;
                case 'Home':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        irASlide(0);
                    }
                    break;
                case 'End':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        irASlide(TOTAL_SLIDES - 1);
                    }
                    break;
                case 'f':
                case 'F':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        togglePantallaCompleta();
                    }
                    break;
                case 'q':
                case 'Q':
                    if (ES_SESION_ACTIVA) {
                        e.preventDefault();
                        const modalQR = document.getElementById('modalQRGigante');
                        if (modalQR.classList.contains('hidden')) {
                            abrirModalQR();
                        } else {
                            cerrarModalQR();
                        }
                    }
                    break;
                case 'Escape':
                    cerrarModalQR();
                    cerrarModalIndice();
                    cerrarModalConfirmarFinalizar();
                    cerrarModalFirma();
                    cerrarModalAlerta();
                    if (!ES_SESION_ACTIVA || !document.getElementById('modalExitoFinalizacion')?.classList.contains('hidden')) {
                        irAlHomeInduccion();
                    }
                    break;
            }
        });

        // ==========================================
        // INICIALIZACIÓN
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('finalizada') === '1') {
                const modalExito = document.getElementById('modalExitoFinalizacion');
                if (modalExito) modalExito.classList.remove('hidden');
            }

            if (ES_SESION_ACTIVA) {
                seleccionarModoRed(qrActualMode);
                actualizarCodigosQR();
                ajustarEscalaPptx();

                const primerSlide = document.getElementById('slidePane-0');
                if (primerSlide && primerSlide.dataset.slideType === 'pdf') {
                    renderizarSlidePdf(primerSlide);
                }

                consultarAsistencias();
                intervalStream = setInterval(consultarAsistencias, 3000);
            }
        });
    </script>

</body>
</html>
