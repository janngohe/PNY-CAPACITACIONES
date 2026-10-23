<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $induccion->titulo }} - Diapositivas | C.I. Piscícola New York</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fuentes Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pptx-slide-wrapper {
            position: relative;
            transform-origin: center center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            transition: transform 0.15s ease-out;
        }

        .slide-pane {
            display: none;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
        }

        .slide-pane.active {
            display: flex !important;
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0.6; }
            to { opacity: 1; }
        }

        /* Ocultar barra de scroll en navegación de miniaturas */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-100 overflow-hidden select-none bg-slate-950">

    @php
        $slides = $pptxData['slides'] ?? [];
        $totalSlides = count($slides);
        $aspectWidth = $pptxData['width'] ?? 1920;
        $aspectHeight = $pptxData['height'] ?? 1080;
    @endphp

    <!-- 1. BARRA SUPERIOR -->
    <header class="bg-slate-900/90 border-b border-slate-800 px-4 py-2.5 flex items-center justify-between gap-3 z-30 shrink-0 h-[60px] backdrop-blur-md">
        <div class="flex items-center gap-3 min-w-0">
            <button type="button" onclick="window.history.back()"
                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer border border-slate-700"
                    title="Regresar">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </button>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-orange-500/20 text-orange-400 border border-orange-500/30">
                        <i class="fa-solid fa-file-powerpoint mr-1"></i> Diapositivas
                    </span>
                    <span class="text-xs text-slate-400 truncate hidden sm:inline">{{ $induccion->titulo }}</span>
                </div>
                <h1 class="text-sm font-bold text-white truncate max-w-xl">{{ $induccion->titulo }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <!-- Contador -->
            <div class="px-3 py-1.5 rounded-xl bg-slate-800/90 border border-slate-700 font-mono text-xs text-slate-300">
                Diapositiva <span id="contadorActual" class="font-black text-cyan-400">1</span> / <span class="font-bold text-white">{{ $totalSlides }}</span>
            </div>

            <!-- Pantalla Completa -->
            <button type="button" onclick="toggleFullscreen()" id="btnFullscreen"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-bold transition-all cursor-pointer">
                <i class="fa-solid fa-expand text-xs"></i>
                <span class="hidden sm:inline">Pantalla Completa</span>
            </button>

            @if($archivoRelativo)
                <a href="{{ asset('storage/' . $archivoRelativo) }}" download
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white border border-orange-500 text-xs font-bold transition-all shadow-sm">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span class="hidden sm:inline">Descargar PPTX</span>
                </a>
            @endif
        </div>
    </header>

    <!-- 2. ESCENARIO DE DIAPOSITIVAS -->
    <main class="flex-1 relative overflow-hidden flex items-center justify-center p-3 sm:p-6 bg-slate-950" id="stageContainer">
        @if($totalSlides > 0)
            @foreach($slides as $sIdx => $slideHtml)
                <div class="slide-pane {{ $sIdx === 0 ? 'active' : '' }}" data-slide-index="{{ $sIdx }}">
                    <div class="pptx-slide-wrapper"
                         data-width="{{ $aspectWidth }}"
                         data-height="{{ $aspectHeight }}">
                        {!! $slideHtml !!}
                    </div>
                </div>
            @endforeach

            <!-- Flecha Anterior Flotante -->
            <button type="button" onclick="cambiarSlide(-1)" id="btnFloatPrev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/80 hover:bg-cyan-600 text-white border border-slate-700 backdrop-blur-md flex items-center justify-center shadow-2xl transition-all cursor-pointer z-20 disabled:opacity-20 disabled:pointer-events-none"
                    title="Diapositiva anterior (Flecha Izquierda [←])">
                <i class="fa-solid fa-chevron-left text-base"></i>
            </button>

            <!-- Flecha Siguiente Flotante -->
            <button type="button" onclick="cambiarSlide(1)" id="btnFloatNext"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/80 hover:bg-cyan-600 text-white border border-slate-700 backdrop-blur-md flex items-center justify-center shadow-2xl transition-all cursor-pointer z-20 disabled:opacity-20 disabled:pointer-events-none"
                    title="Siguiente diapositiva (Flecha Derecha [→] o Espacio)">
                <i class="fa-solid fa-chevron-right text-base"></i>
            </button>
        @else
            <div class="text-center p-8 bg-slate-900 rounded-3xl border border-slate-800 max-w-md">
                <i class="fa-solid fa-circle-exclamation text-4xl text-amber-500 mb-3 block"></i>
                <h3 class="text-lg font-bold text-white mb-1">Sin diapositivas</h3>
                <p class="text-xs text-slate-400">No se pudieron extraer diapositivas de este archivo o el archivo no existe.</p>
            </div>
        @endif
    </main>

    <!-- 3. BARRA INFERIOR DE NAVEGACIÓN -->
    <footer class="bg-slate-900/90 border-t border-slate-800 px-4 py-2.5 shrink-0 flex items-center justify-between gap-3 text-xs backdrop-blur-md">
        <div class="flex items-center gap-2">
            <button type="button" onclick="cambiarSlide(-1)" id="btnBottomPrev"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center gap-2 border border-slate-700 cursor-pointer disabled:opacity-30 disabled:pointer-events-none">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Anterior</span>
            </button>
            <span class="text-[11px] text-slate-500 hidden md:inline">Usa las teclas [←] y [→] de tu teclado</span>
        </div>

        <!-- Indicador de Dots / Diapositivas -->
        <div class="flex items-center gap-1.5 overflow-x-auto max-w-[40%] sm:max-w-[60%] py-1 no-scrollbar" id="dotsContainer">
            @for($d = 0; $d < $totalSlides; $d++)
                <button type="button" onclick="irASlide({{ $d }})"
                        class="dot-indicator rounded-full transition-all cursor-pointer shrink-0 {{ $d === 0 ? 'bg-cyan-400 w-5 h-2' : 'bg-slate-700 hover:bg-slate-500 w-2 h-2' }}"
                        title="Ir a diapositiva {{ $d + 1 }}" data-dot-index="{{ $d }}"></button>
            @endfor
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="cambiarSlide(1)" id="btnBottomNext"
                    class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold transition flex items-center gap-2 shadow-md cursor-pointer disabled:opacity-30 disabled:pointer-events-none">
                <span>Siguiente</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </footer>

    <script>
        const totalSlides = {{ $totalSlides }};
        let currentSlide = 0;

        function actualizarControles() {
            document.getElementById('contadorActual').textContent = currentSlide + 1;

            const btnFloatPrev = document.getElementById('btnFloatPrev');
            const btnFloatNext = document.getElementById('btnFloatNext');
            const btnBottomPrev = document.getElementById('btnBottomPrev');
            const btnBottomNext = document.getElementById('btnBottomNext');

            if (btnFloatPrev) btnFloatPrev.disabled = (currentSlide === 0);
            if (btnFloatNext) btnFloatNext.disabled = (currentSlide >= totalSlides - 1);
            if (btnBottomPrev) btnBottomPrev.disabled = (currentSlide === 0);
            if (btnBottomNext) btnBottomNext.disabled = (currentSlide >= totalSlides - 1);

            // Actualizar Dots
            document.querySelectorAll('.dot-indicator').forEach((dot, idx) => {
                if (idx === currentSlide) {
                    dot.className = 'dot-indicator rounded-full transition-all cursor-pointer shrink-0 bg-cyan-400 w-5 h-2';
                    dot.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    dot.className = 'dot-indicator rounded-full transition-all cursor-pointer shrink-0 bg-slate-700 hover:bg-slate-500 w-2 h-2';
                }
            });

            escalarDiapositivaActiva();
        }

        function cambiarSlide(delta) {
            const nuevo = currentSlide + delta;
            if (nuevo < 0 || nuevo >= totalSlides) return;
            irASlide(nuevo);
        }

        function irASlide(index) {
            if (index < 0 || index >= totalSlides) return;
            const panes = document.querySelectorAll('.slide-pane');
            panes.forEach((p, idx) => {
                if (idx === index) {
                    p.classList.add('active');
                } else {
                    p.classList.remove('active');
                }
            });
            currentSlide = index;
            actualizarControles();
        }

        function escalarDiapositivaActiva() {
            const activePane = document.querySelector('.slide-pane.active');
            if (!activePane) return;

            const wrapper = activePane.querySelector('.pptx-slide-wrapper');
            if (!wrapper) return;

            const container = document.getElementById('stageContainer');
            if (!container) return;

            const origWidth = parseFloat(wrapper.dataset.width) || 1920;
            const origHeight = parseFloat(wrapper.dataset.height) || 1080;

            const availWidth = container.clientWidth - 40;
            const availHeight = container.clientHeight - 40;

            if (availWidth <= 0 || availHeight <= 0) return;

            const scaleX = availWidth / origWidth;
            const scaleY = availHeight / origHeight;
            const scale = Math.min(scaleX, scaleY, 1.0);

            wrapper.style.width = origWidth + 'px';
            wrapper.style.height = origHeight + 'px';
            wrapper.style.transform = `scale(${scale})`;
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const btn = document.getElementById('btnFullscreen');
            if (document.fullscreenElement) {
                btn.innerHTML = '<i class="fa-solid fa-compress text-xs"></i><span class="hidden sm:inline">Salir</span>';
            } else {
                btn.innerHTML = '<i class="fa-solid fa-expand text-xs"></i><span class="hidden sm:inline">Pantalla Completa</span>';
            }
            setTimeout(escalarDiapositivaActiva, 100);
        });

        // Eventos de teclado
        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'PageDown' || e.key === ' ') {
                e.preventDefault();
                cambiarSlide(1);
            } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                e.preventDefault();
                cambiarSlide(-1);
            } else if (e.key === 'f' || e.key === 'F') {
                toggleFullscreen();
            } else if (e.key === 'Home') {
                irASlide(0);
            } else if (e.key === 'End') {
                irASlide(totalSlides - 1);
            }
        });

        window.addEventListener('resize', () => {
            escalarDiapositivaActiva();
        });

        document.addEventListener('DOMContentLoaded', () => {
            actualizarControles();
            setTimeout(escalarDiapositivaActiva, 50);
        });
    </script>
</body>
</html>
