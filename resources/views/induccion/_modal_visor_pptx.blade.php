<!-- Modal Visor Interactivo de Diapositivas PowerPoint (PPTX Nativo) -->
<div id="modalVisorDiapositivas"
     class="fixed inset-0 hidden bg-slate-950/92 backdrop-blur-xl flex items-center justify-center p-2 sm:p-4 select-none animate-fade-in"
     style="z-index: 999999 !important;"
     tabindex="-1"
     aria-modal="true"
     role="dialog">

    <div class="bg-slate-900 border border-slate-700/80 rounded-3xl w-full max-w-6xl h-[92vh] max-h-[92vh] flex flex-col overflow-hidden shadow-[0_30px_90px_rgba(0,0,0,0.9)] ring-1 ring-white/10 relative">

        <!-- 1. HEADER DEL VISOR -->
        <div class="px-4 sm:px-6 py-3.5 bg-slate-950/95 border-b border-slate-800/90 flex items-center justify-between gap-3 shrink-0">
            <!-- Izquierda: Ícono, Título y Metadatos -->
            <div class="flex items-center gap-3.5 min-w-0">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-amber-600 via-orange-500 to-amber-500 text-white shadow-lg shadow-orange-500/25 flex items-center justify-center text-base shrink-0 ring-1 ring-white/20">
                    <i class="fa-solid fa-file-powerpoint"></i>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-orange-500/15 text-orange-400 text-[10px] font-extrabold uppercase tracking-wider border border-orange-500/30">
                            Presentación PPTX
                        </span>
                        <span id="mvp-induccion-nombre" class="text-xs text-slate-400 truncate hidden md:inline"></span>
                    </div>
                    <h3 id="mvp-titulo" class="text-sm sm:text-base font-bold text-white truncate max-w-xl leading-tight mt-0.5 font-heading">
                        Diapositivas de Inducción
                    </h3>
                </div>
            </div>

            <!-- Derecha: Contador y Acciones -->
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                <!-- Contador de Diapositivas Estilo Pill -->
                <div class="px-3.5 py-1.5 rounded-xl bg-slate-800/90 border border-cyan-500/30 font-mono text-xs text-slate-300 shadow-inner flex items-center gap-1.5">
                    <span class="text-[10px] text-slate-400 font-sans uppercase font-bold hidden sm:inline">Diapositiva</span>
                    <span id="mvp-actual" class="font-black text-cyan-400 text-sm">1</span>
                    <span class="text-slate-500">/</span>
                    <span id="mvp-total" class="font-bold text-white">0</span>
                </div>

                <!-- Botón Pantalla Completa -->
                <button type="button"
                        onclick="mvpToggleFullscreen()"
                        id="mvp-btn-fullscreen"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[36px] rounded-xl bg-slate-800/80 hover:bg-cyan-700/80 active:scale-95 text-slate-300 hover:text-white border border-slate-700 hover:border-cyan-500/50 transition-all cursor-pointer text-xs font-bold shadow-xs"
                        title="Pantalla Completa (F)">
                    <i class="fa-solid fa-expand text-xs"></i>
                    <span class="hidden md:inline">Pantalla Completa</span>
                </button>

                <!-- Botón Abrir en Pestaña Dedicada -->
                <a id="mvp-btn-tab"
                   href="#"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[36px] rounded-xl bg-slate-800/80 hover:bg-slate-700 active:scale-95 text-slate-300 hover:text-white border border-slate-700 transition-all text-xs font-bold"
                   title="Abrir en pestaña independiente para proyectar en sala">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs text-cyan-400"></i>
                    <span class="hidden lg:inline">Pestaña Completa</span>
                </a>

                <!-- Botón Descargar PPTX -->
                <a id="mvp-btn-download"
                   href="#"
                   download
                   class="inline-flex items-center justify-center w-9 h-9 sm:w-auto sm:px-3.5 sm:py-1.5 min-h-[36px] rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 active:scale-95 text-white border border-orange-400/40 transition-all text-xs font-bold shadow-md shadow-orange-600/20"
                   title="Descargar archivo PowerPoint original (.pptx)">
                    <i class="fa-solid fa-download text-xs"></i>
                    <span class="hidden sm:inline sm:ml-1.5">Descargar PPTX</span>
                </a>

                <!-- Botón Cerrar Visor -->
                <button type="button"
                        onclick="cerrarVisorPptxModal()"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-800/80 hover:bg-rose-600 active:scale-95 text-slate-400 hover:text-white border border-slate-700 hover:border-rose-500 transition-all cursor-pointer ml-1"
                        title="Cerrar visor (Escape)">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- 2. ESCENARIO CENTRAL / STAGE -->
        <div id="mvp-stage-container"
             class="flex-1 relative overflow-hidden flex items-center justify-center p-3 sm:p-5 select-none"
             style="background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);">

            <!-- Estado: Cargando Diapositivas -->
            <div id="mvp-loading" class="text-center p-8 z-10 flex flex-col items-center">
                <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mb-3 text-cyan-400 text-2xl shadow-lg">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
                <div class="text-sm font-bold text-white tracking-wide">Cargando diapositivas de la inducción...</div>
                <div class="text-xs text-slate-400 mt-1">Renderizando contenido gráfico con precisión nativa</div>
            </div>

            <!-- Estado: Error -->
            <div id="mvp-error" class="hidden text-center p-6 sm:p-8 z-10 max-w-md bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl mx-auto mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h4 class="text-sm font-bold text-white mb-1.5">No se pudieron cargar las diapositivas</h4>
                <p id="mvp-error-msg" class="text-xs text-slate-400 mb-4 leading-relaxed">
                    Ocurrió un problema al procesar la presentación. Puedes descargar el archivo PowerPoint para visualizarlo en tu equipo.
                </p>
                <div class="flex items-center justify-center gap-2">
                    <button type="button" onclick="mvpReintentar()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 cursor-pointer">
                        <i class="fa-solid fa-rotate-right mr-1.5 text-cyan-400"></i> Reintentar
                    </button>
                    <a id="mvp-error-download" href="#" download class="px-4 py-2 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold cursor-pointer">
                        <i class="fa-solid fa-download mr-1.5"></i> Descargar PPTX
                    </a>
                </div>
            </div>

            <!-- Contenedor dinámico de Diapositivas -->
            <div id="mvp-slides-holder" class="w-full h-full flex items-center justify-center relative overflow-hidden">
                <!-- Las diapositivas se insertan aquí vía JavaScript -->
            </div>

            <!-- Flecha Flotante Anterior -->
            <button type="button"
                    onclick="mvpCambiarSlide(-1)"
                    id="mvp-btn-float-prev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/85 hover:bg-cyan-600 active:scale-90 text-white border border-white/15 hover:border-cyan-400 backdrop-blur-md flex items-center justify-center shadow-2xl transition-all cursor-pointer z-30 disabled:opacity-10 disabled:pointer-events-none"
                    title="Diapositiva anterior (Flecha Izquierda [←])">
                <i class="fa-solid fa-chevron-left text-base"></i>
            </button>

            <!-- Flecha Flotante Siguiente -->
            <button type="button"
                    onclick="mvpCambiarSlide(1)"
                    id="mvp-btn-float-next"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-slate-900/85 hover:bg-cyan-600 active:scale-90 text-white border border-white/15 hover:border-cyan-400 backdrop-blur-md flex items-center justify-center shadow-2xl transition-all cursor-pointer z-30 disabled:opacity-10 disabled:pointer-events-none"
                    title="Siguiente diapositiva (Flecha Derecha [→] o Espacio)">
                <i class="fa-solid fa-chevron-right text-base"></i>
            </button>
        </div>

        <!-- 3. BARRA INFERIOR / CONTROLES -->
        <div class="px-4 sm:px-6 py-3 bg-slate-950/95 border-t border-slate-800/90 flex flex-col gap-2 shrink-0">
            <!-- Barra de Progreso de Diapositivas -->
            <div class="w-full bg-slate-800/80 h-1 rounded-full overflow-hidden">
                <div id="mvp-progress-bar"
                     class="h-full bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-500 rounded-full transition-all duration-300"
                     style="width: 0%;"></div>
            </div>

            <div class="flex items-center justify-between gap-3 text-xs">
                <!-- Botón Anterior -->
                <div class="flex items-center gap-2">
                    <button type="button"
                            onclick="mvpCambiarSlide(-1)"
                            id="mvp-btn-prev"
                            class="px-4 py-2 min-h-[38px] rounded-xl bg-slate-800/90 hover:bg-slate-700 active:scale-95 text-white font-bold transition flex items-center gap-2 border border-slate-700 cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Anterior</span>
                        <kbd class="hidden sm:inline-block px-1.5 py-0.5 rounded bg-slate-900 text-[10px] text-slate-400 border border-slate-700">←</kbd>
                    </button>
                </div>

                <!-- Dots / Mini Selector con Scroll Horizontal -->
                <div id="mvp-dots-container"
                     class="flex items-center gap-1.5 overflow-x-auto max-w-[40%] sm:max-w-[55%] py-1 no-scrollbar">
                    <!-- Se generan dinámicamente -->
                </div>

                <!-- Botón Siguiente -->
                <div class="flex items-center gap-2">
                    <button type="button"
                            onclick="mvpCambiarSlide(1)"
                            id="mvp-btn-next"
                            class="px-4 py-2 min-h-[38px] rounded-xl bg-cyan-600 hover:bg-cyan-500 active:scale-95 text-white font-bold transition flex items-center gap-2 shadow-lg shadow-cyan-600/20 border border-cyan-400/40 cursor-pointer disabled:opacity-20 disabled:pointer-events-none">
                        <span>Siguiente</span>
                        <kbd class="hidden sm:inline-block px-1.5 py-0.5 rounded bg-cyan-800 text-[10px] text-cyan-200 border border-cyan-500/50">→</kbd>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Estilos específicos del visor de diapositivas en modal */
    .mvp-slide-wrapper {
        position: relative;
        transform-origin: 0 0;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        overflow: hidden;
        background: #ffffff;
    }

    .mvp-slide-pane {
        display: none;
        width: 100%;
        height: 100%;
        align-items: center;
        justify-content: center;
    }

    .mvp-slide-pane.active {
        display: flex !important;
        animation: mvpFadeIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes mvpFadeIn {
        from {
            opacity: 0.7;
            transform: scale(0.99);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    let mvpCurrentInduccionId = null;
    let mvpCurrentArchivo = null;
    let mvpCurrentIndex = 0;
    let mvpTotalSlides = 0;
    let mvpSlideWidth = 1920;
    let mvpSlideHeight = 1080;
    let mvpSlidesData = [];

    window.abrirVisorPptxModal = function (induccionId, archivoRelativo, tituloRecurso) {
        mvpCurrentInduccionId = induccionId;
        mvpCurrentArchivo = archivoRelativo ? decodeURIComponent(archivoRelativo) : null;
        mvpCurrentIndex = 0;
        mvpSlidesData = [];

        const modal = document.getElementById('modalVisorDiapositivas');
        if (!modal) return;

        // IMPORTANTE: Asegurar que el modal quede directamente en document.body para máxima prioridad de z-index
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        modal.style.zIndex = '999999';

        const loader = document.getElementById('mvp-loading');
        const errorBox = document.getElementById('mvp-error');
        const slidesHolder = document.getElementById('mvp-slides-holder');
        const dotsContainer = document.getElementById('mvp-dots-container');

        // Reset UI
        modal.classList.remove('hidden');
        loader.classList.remove('hidden');
        errorBox.classList.add('hidden');
        slidesHolder.innerHTML = '';
        dotsContainer.innerHTML = '';
        document.getElementById('mvp-actual').textContent = '1';
        document.getElementById('mvp-total').textContent = '...';

        if (tituloRecurso) {
            document.getElementById('mvp-titulo').textContent = decodeURIComponent(tituloRecurso);
        }

        // Configurar enlaces de descarga y pestaña completa
        const tabBtn = document.getElementById('mvp-btn-tab');
        const downloadBtn = document.getElementById('mvp-btn-download');
        const errorDownloadBtn = document.getElementById('mvp-error-download');

        const params = mvpCurrentArchivo ? `?archivo=${encodeURIComponent(mvpCurrentArchivo)}` : '';
        tabBtn.href = `/induccion/${induccionId}/visor-diapositivas${params}`;

        if (mvpCurrentArchivo) {
            const cleanPath = mvpCurrentArchivo.replace(/^storage\//, '');
            const downloadUrl = `/storage/${cleanPath}`;
            downloadBtn.href = downloadUrl;
            downloadBtn.style.display = '';
            errorDownloadBtn.href = downloadUrl;
        } else {
            downloadBtn.style.display = 'none';
        }

        // Petición AJAX para obtener las diapositivas
        const urlFetch = `/induccion/${induccionId}/slides-json${params}`;

        fetch(urlFetch, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => {
            if (!res.ok) {
                // Fallback a ruta admin si es necesario
                if (res.status === 404 || res.status === 403) {
                    return fetch(`/admin/inducciones/${induccionId}/slides-json${params}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(r => r.json());
                }
                throw new Error('No se pudo cargar la presentación');
            }
            return res.json();
        })
        .then(data => {
            if (!data || !data.success || !data.slides || data.slides.length === 0) {
                throw new Error(data?.message || 'No hay diapositivas disponibles');
            }

            mvpSlidesData = data.slides;
            mvpTotalSlides = data.slides.length;
            mvpSlideWidth = parseFloat(data.width) || 1920;
            mvpSlideHeight = parseFloat(data.height) || 1080;

            if (data.titulo && !tituloRecurso) {
                document.getElementById('mvp-titulo').textContent = data.titulo;
            }

            if (data.archivo_url) {
                downloadBtn.href = data.archivo_url;
                downloadBtn.style.display = '';
                errorDownloadBtn.href = data.archivo_url;
            }

            // Construir Paneles de Diapositivas
            let slidesHtml = '';
            let dotsHtml = '';

            data.slides.forEach((slideHtml, idx) => {
                slidesHtml += `
                    <div class="mvp-slide-pane ${idx === 0 ? 'active' : ''}" data-mvp-index="${idx}">
                        <div class="mvp-slide-wrapper" data-width="${mvpSlideWidth}" data-height="${mvpSlideHeight}">
                            ${slideHtml}
                        </div>
                    </div>
                `;

                dotsHtml += `
                    <button type="button"
                            onclick="mvpIrASlide(${idx})"
                            class="mvp-dot rounded-full transition-all cursor-pointer shrink-0 ${idx === 0 ? 'bg-cyan-400 w-5 h-2 shadow-sm shadow-cyan-400/50' : 'bg-slate-700 hover:bg-slate-500 w-2 h-2'}"
                            title="Diapositiva ${idx + 1}"
                            data-mvp-dot="${idx}"></button>
                `;
            });

            slidesHolder.innerHTML = slidesHtml;
            dotsContainer.innerHTML = dotsHtml;

            loader.classList.add('hidden');
            mvpActualizarControles();
            setTimeout(mvpEscalarSlideActivo, 50);
            setTimeout(mvpEscalarSlideActivo, 200);
        })
        .catch(err => {
            console.error('Error al cargar diapositivas:', err);
            loader.classList.add('hidden');
            errorBox.classList.remove('hidden');
            document.getElementById('mvp-error-msg').textContent = err.message || 'Error de conexión al cargar la presentación.';
        });
    };

    window.cerrarVisorPptxModal = function () {
        const modal = document.getElementById('modalVisorDiapositivas');
        if (modal) {
            modal.classList.add('hidden');
        }
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {});
        }
    };

    window.mvpReintentar = function () {
        if (mvpCurrentInduccionId) {
            window.abrirVisorPptxModal(mvpCurrentInduccionId, mvpCurrentArchivo);
        }
    };

    window.mvpActualizarControles = function () {
        document.getElementById('mvp-actual').textContent = mvpCurrentIndex + 1;
        document.getElementById('mvp-total').textContent = mvpTotalSlides;

        const btnPrev = document.getElementById('mvp-btn-prev');
        const btnNext = document.getElementById('mvp-btn-next');
        const btnFloatPrev = document.getElementById('mvp-btn-float-prev');
        const btnFloatNext = document.getElementById('mvp-btn-float-next');
        const progressBar = document.getElementById('mvp-progress-bar');

        const esPrimero = (mvpCurrentIndex === 0);
        const esUltimo = (mvpCurrentIndex >= mvpTotalSlides - 1);

        if (btnPrev) btnPrev.disabled = esPrimero;
        if (btnFloatPrev) btnFloatPrev.disabled = esPrimero;
        if (btnNext) btnNext.disabled = esUltimo;
        if (btnFloatNext) btnFloatNext.disabled = esUltimo;

        // Barra de progreso
        if (progressBar && mvpTotalSlides > 0) {
            const pct = Math.round(((mvpCurrentIndex + 1) / mvpTotalSlides) * 100);
            progressBar.style.width = `${pct}%`;
        }

        // Actualizar dots
        document.querySelectorAll('.mvp-dot').forEach((dot, idx) => {
            if (idx === mvpCurrentIndex) {
                dot.className = 'mvp-dot rounded-full transition-all cursor-pointer shrink-0 bg-cyan-400 w-5 h-2 shadow-sm shadow-cyan-400/50';
                dot.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                dot.className = 'mvp-dot rounded-full transition-all cursor-pointer shrink-0 bg-slate-700 hover:bg-slate-500 w-2 h-2';
            }
        });

        mvpEscalarSlideActivo();
    };

    window.mvpCambiarSlide = function (delta) {
        const nuevo = mvpCurrentIndex + delta;
        if (nuevo < 0 || nuevo >= mvpTotalSlides) return;
        window.mvpIrASlide(nuevo);
    };

    window.mvpIrASlide = function (index) {
        if (index < 0 || index >= mvpTotalSlides) return;
        const panes = document.querySelectorAll('.mvp-slide-pane');
        panes.forEach((p, idx) => {
            if (idx === index) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });
        mvpCurrentIndex = index;
        mvpActualizarControles();
    };

    window.mvpEscalarSlideActivo = function () {
        const activePane = document.querySelector('.mvp-slide-pane.active');
        if (!activePane) return;

        const wrapper = activePane.querySelector('.mvp-slide-wrapper');
        if (!wrapper) return;

        const container = document.getElementById('mvp-stage-container');
        if (!container) return;

        const origW = parseFloat(wrapper.dataset.width || mvpSlideWidth || 1920);
        const origH = parseFloat(wrapper.dataset.height || mvpSlideHeight || 1080);

        const padX = 32;
        const padY = 32;
        const availW = Math.max(100, container.clientWidth - padX);
        const availH = Math.max(100, container.clientHeight - padY);

        const scale = Math.min(availW / origW, availH / origH);

        const canvas = wrapper.querySelector('.pptx-canvas');
        if (canvas) {
            canvas.style.transform = `scale(${scale})`;
            canvas.style.transformOrigin = '0 0';
            wrapper.style.width = `${origW * scale}px`;
            wrapper.style.height = `${origH * scale}px`;
        }
    };

    window.mvpToggleFullscreen = function () {
        const modal = document.getElementById('modalVisorDiapositivas');
        if (!document.fullscreenElement) {
            if (modal.requestFullscreen) {
                modal.requestFullscreen().catch(() => {});
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            }
        }
    };

    document.addEventListener('fullscreenchange', () => {
        const btn = document.getElementById('mvp-btn-fullscreen');
        if (btn) {
            if (document.fullscreenElement) {
                btn.innerHTML = '<i class="fa-solid fa-compress text-xs"></i><span class="hidden md:inline">Salir</span>';
            } else {
                btn.innerHTML = '<i class="fa-solid fa-expand text-xs"></i><span class="hidden md:inline">Pantalla Completa</span>';
            }
        }
        setTimeout(mvpEscalarSlideActivo, 100);
    });

    window.addEventListener('resize', () => {
        const modal = document.getElementById('modalVisorDiapositivas');
        if (modal && !modal.classList.contains('hidden')) {
            mvpEscalarSlideActivo();
        }
    });

    // Atajos de Teclado cuando el visor está visible
    window.addEventListener('keydown', (e) => {
        const modal = document.getElementById('modalVisorDiapositivas');
        if (!modal || modal.classList.contains('hidden')) return;

        if (e.key === 'ArrowRight' || e.key === 'PageDown' || e.key === ' ') {
            e.preventDefault();
            mvpCambiarSlide(1);
        } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
            e.preventDefault();
            mvpCambiarSlide(-1);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            cerrarVisorPptxModal();
        } else if (e.key === 'f' || e.key === 'F') {
            mvpToggleFullscreen();
        } else if (e.key === 'Home') {
            mvpIrASlide(0);
        } else if (e.key === 'End') {
            mvpIrASlide(mvpTotalSlides - 1);
        }
    });
</script>
