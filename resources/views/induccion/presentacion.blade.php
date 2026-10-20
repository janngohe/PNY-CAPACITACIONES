<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $sesion->induccion->titulo }} · Sesión en Vivo · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- QRCode.js para generación instantánea sin dependencias externas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-950 text-slate-100 overflow-hidden select-none">

    <!-- Barra Superior de Control de la Presentación -->
    <header class="bg-slate-900/95 border-b border-slate-800 px-4 py-2.5 flex items-center justify-between gap-4 z-30 shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('induccion.index') }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors" title="Volver al panel">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                        {{ $sesion->induccion->tipo }}
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-400">
                        Código: <strong class="text-cyan-400 text-sm">{{ $sesion->codigo_sesion }}</strong>
                    </span>
                </div>
                <h1 class="text-sm sm:text-base font-bold text-white truncate max-w-md sm:max-w-xl">
                    {{ $sesion->induccion->titulo }}
                </h1>
            </div>
        </div>

        <!-- Acciones Centrales / Tabs si hay Video y PDF -->
        <div class="hidden md:flex items-center gap-2 bg-slate-950/80 p-1 rounded-2xl border border-slate-800">
            @if($sesion->induccion->embed_video_url)
                <button type="button" id="tabVideoBtn" onclick="cambiarVista('video')"
                        class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-cyan-500 text-slate-950 shadow-sm">
                    <i class="fa-brands fa-youtube"></i>
                    <span>Video en Vivo</span>
                </button>
            @endif

            @if($sesion->induccion->archivo_presentacion)
                <button type="button" id="tabPdfBtn" onclick="cambiarVista('pdf')"
                        class="px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Diapositivas PDF</span>
                </button>
            @endif
        </div>

        <!-- Botones de Acción Derecha -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- Contador de Asistentes en Tiempo Real -->
            <button type="button" onclick="toggleDrawerAsistentes()"
                    class="px-3.5 py-2 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 hover:bg-cyan-500/20 text-xs font-bold flex items-center gap-2 transition-all cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Asistentes:</span>
                <span id="contadorAsistentesBadge" class="px-2 py-0.5 rounded-lg bg-cyan-500 text-slate-950 font-black text-xs">
                    {{ $sesion->asistencias->count() }}
                </span>
            </button>

            <!-- Botón Código QR Grande -->
            <button type="button" onclick="abrirModalQR()"
                    class="px-3.5 py-2 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold flex items-center gap-2 border border-slate-700 transition-colors cursor-pointer"
                    title="Mostrar Código QR gigante en pantalla">
                <i class="fa-solid fa-qrcode text-cyan-400 text-sm"></i>
                <span class="hidden sm:inline">Mostrar QR</span>
            </button>

            <!-- Botón Pantalla Completa -->
            <button type="button" onclick="togglePantallaCompleta()"
                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors"
                    title="Pantalla Completa">
                <i id="fullscreenIcon" class="fa-solid fa-expand"></i>
            </button>

            <!-- Botón Finalizar Sesión -->
            @if($sesion->estaActiva())
                <button type="button" onclick="confirmarFinalizarSesion()"
                        class="px-3 py-2 rounded-2xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-xs font-bold transition-all cursor-pointer">
                    <i class="fa-solid fa-flag-checkered mr-1"></i> Finalizar
                </button>
            @else
                <a href="{{ route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]) }}"
                   class="px-3 py-2 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold flex items-center gap-1.5 transition-all shadow-md">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Descargar Acta</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Contenedor Principal de la Presentación -->
    <main class="flex-1 relative bg-slate-950 overflow-hidden flex flex-col items-center justify-center">

        <!-- Vista 1: Video en iFrame -->
        @if($sesion->induccion->embed_video_url)
            <div id="vistaVideo" class="w-full h-full flex items-center justify-center p-2 sm:p-4">
                <div class="w-full h-full max-w-6xl max-h-[82vh] aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                    <iframe id="iframeVideo"
                            src="{{ $sesion->induccion->embed_video_url }}"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen></iframe>
                </div>
            </div>
        @endif

        <!-- Vista 2: Diapositivas PDF -->
        @if($sesion->induccion->archivo_presentacion)
            <div id="vistaPdf" class="w-full h-full flex items-center justify-center p-2 sm:p-4 {{ $sesion->induccion->embed_video_url ? 'hidden' : '' }}">
                <div class="w-full h-full max-w-6xl max-h-[85vh] bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                    <iframe src="{{ asset('storage/' . $sesion->induccion->archivo_presentacion) }}#toolbar=1&navpanes=0"
                            class="w-full h-full rounded-2xl"
                            frameborder="0"></iframe>
                </div>
            </div>
        @endif

        <!-- Si no tiene ni video ni PDF cargado -->
        @if(!$sesion->induccion->embed_video_url && !$sesion->induccion->archivo_presentacion)
            <div class="text-center p-8 max-w-md">
                <div class="w-20 h-20 rounded-3xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-3xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">{{ $sesion->induccion->titulo }}</h2>
                <p class="text-xs text-slate-400 mb-6">
                    Esta inducción se está llevando a cabo de forma presencial. Puedes proyectar el código QR para que los asistentes firmen su asistencia en sus teléfonos.
                </p>
                <button type="button" onclick="abrirModalQR()"
                        class="px-6 py-3 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm tracking-wide shadow-lg shadow-cyan-500/20 transition-all inline-flex items-center gap-2">
                    <i class="fa-solid fa-qrcode"></i> Proyectar Código QR
                </button>
            </div>
        @endif

        <!-- Banner Flotante Inferior de Escaneo QR (visible durante toda la presentación) -->
        <div class="absolute bottom-4 left-4 z-20 flex items-center gap-3 bg-slate-900/90 backdrop-blur-md p-3 rounded-2xl border border-slate-800 shadow-2xl group hover:border-cyan-500/50 transition-all">
            <div id="miniQRElement" class="w-16 h-16 bg-white p-1 rounded-xl shadow cursor-pointer shrink-0" onclick="abrirModalQR()" title="Clic para agrandar"></div>
            <div class="text-xs pr-2">
                <div class="font-bold text-white flex items-center gap-1.5">
                    <i class="fa-solid fa-mobile-screen-button text-cyan-400"></i> Escanea para Asistencia
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    O ingresa a: <strong class="text-cyan-400 font-mono">{{ url('/asistencia/' . $sesion->codigo_sesion) }}</strong>
                </div>
                <div class="text-[11px] text-slate-300 font-mono font-bold mt-0.5">
                    Código: <span class="text-white bg-slate-800 px-1.5 py-0.5 rounded">{{ $sesion->codigo_sesion }}</span>
                </div>
            </div>
        </div>

    </main>

    <!-- Modal con Código QR Gigante para Proyección en Sala / TV -->
    <div id="modalQR" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-8 shadow-2xl text-center relative animate-fade-in">
            <button type="button" onclick="cerrarModalQR()"
                    class="absolute top-6 right-6 text-slate-400 hover:text-white text-xl p-2 rounded-xl bg-slate-800 hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-3">
                <i class="fa-solid fa-qrcode"></i> Registro de Asistencia
            </span>

            <h2 class="text-xl sm:text-2xl font-black text-white mb-1">
                {{ $sesion->induccion->titulo }}
            </h2>
            <p class="text-xs text-slate-400 mb-6">
                Escanea con la cámara de tu celular, digita tu cédula y estampa tu firma digital.
            </p>

            <!-- Contenedor del QR Gigante -->
            <div class="inline-block p-4 bg-white rounded-3xl shadow-2xl mb-6 mx-auto">
                <div id="maxiQRElement" class="w-64 h-64 sm:w-72 sm:h-72 flex items-center justify-center"></div>
            </div>

            <!-- Alternativa sin cámara (Código Manual) -->
            <div class="bg-slate-950/80 p-4 rounded-2xl border border-slate-800 text-xs max-w-md mx-auto space-y-1.5">
                <div class="text-slate-400">
                    ¿Tu cámara no funciona? Entra desde tu navegador móvil a:
                </div>
                <div class="text-cyan-400 font-mono font-bold text-sm select-all">
                    {{ url('/asistencia/' . $sesion->codigo_sesion) }}
                </div>
                <div class="text-slate-400 pt-1">
                    O entra a <span class="text-white font-mono">{{ url('/asistencia') }}</span> e ingresa el código:
                    <strong class="text-white bg-slate-800 px-2 py-0.5 rounded font-mono text-sm">{{ $sesion->codigo_sesion }}</strong>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" onclick="cerrarModalQR()"
                        class="px-6 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer">
                    Cerrar y Continuar Presentación
                </button>
            </div>
        </div>
    </div>

    <!-- Drawer Lateral: Lista de Asistentes en Tiempo Real -->
    <div id="drawerAsistentes" class="fixed inset-y-0 right-0 z-40 w-full sm:w-96 bg-slate-900 border-l border-slate-800 shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-users-viewfinder text-cyan-400"></i>
                <h3 class="text-sm font-bold text-white">Asistentes en Vivo</h3>
                <span id="drawerTotalBadge" class="text-xs font-bold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                    {{ $sesion->asistencias->count() }}
                </span>
            </div>
            <button type="button" onclick="toggleDrawerAsistentes()" class="text-slate-400 hover:text-white p-1 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-3 bg-slate-950/60 border-b border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
            <span>Actualización en tiempo real</span>
            <span class="inline-flex items-center gap-1.5 text-emerald-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Conectado
            </span>
        </div>

        <div id="listaAsistentesContainer" class="flex-1 overflow-y-auto p-4 space-y-2.5">
            <!-- Cargado dinámicamente vía AJAX polling -->
            <div class="text-center py-8 text-xs text-slate-500">
                Cargando asistentes...
            </div>
        </div>

        <div class="p-4 border-t border-slate-800 bg-slate-950/80">
            <a href="{{ route('induccion.acta.pdf', ['codigo' => $sesion->codigo_sesion]) }}"
               class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold flex items-center justify-center gap-2 transition-colors">
                <i class="fa-solid fa-download"></i>
                <span>Descargar Acta Preliminar</span>
            </a>
        </div>
    </div>

    <!-- Scripts de Interacción, Generación de QR y Polling de Asistencias -->
    <script>
        const URL_ASISTENCIA = @json($urlAsistencia);
        const CODIGO_SESION = @json($sesion->codigo_sesion);
        const STREAM_URL = `/induccion/sesion/${CODIGO_SESION}/asistencias-stream`;

        let totalAsistentesAnterior = {{ $sesion->asistencias->count() }};
        let intervalStream = null;

        // Generar Códigos QR al cargar la pantalla
        document.addEventListener('DOMContentLoaded', () => {
            // QR Mini
            new QRCode(document.getElementById("miniQRElement"), {
                text: URL_ASISTENCIA,
                width: 64,
                height: 64,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });

            // QR Gigante
            new QRCode(document.getElementById("maxiQRElement"), {
                text: URL_ASISTENCIA,
                width: 280,
                height: 280,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Iniciar Polling de Asistentes cada 3.5 segundos
            cargarAsistencias();
            intervalStream = setInterval(cargarAsistencias, 3500);
        });

        // Alternar vistas Video / Diapositivas
        function cambiarVista(tipo) {
            const vistaVideo = document.getElementById('vistaVideo');
            const vistaPdf = document.getElementById('vistaPdf');
            const btnVideo = document.getElementById('tabVideoBtn');
            const btnPdf = document.getElementById('tabPdfBtn');

            if (tipo === 'video' && vistaVideo) {
                vistaVideo.classList.remove('hidden');
                if (vistaPdf) vistaPdf.classList.add('hidden');
                if (btnVideo) btnVideo.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-cyan-500 text-slate-950 shadow-sm";
                if (btnPdf) btnPdf.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white";
            } else if (tipo === 'pdf' && vistaPdf) {
                vistaPdf.classList.remove('hidden');
                if (vistaVideo) vistaVideo.classList.add('hidden');
                if (btnPdf) btnPdf.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 bg-cyan-500 text-slate-950 shadow-sm";
                if (btnVideo) btnVideo.className = "px-4 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 text-slate-400 hover:text-white";
            }
        }

        // Modales y Drawers
        function abrirModalQR() {
            document.getElementById('modalQR').classList.remove('hidden');
        }

        function cerrarModalQR() {
            document.getElementById('modalQR').classList.add('hidden');
        }

        function toggleDrawerAsistentes() {
            const drawer = document.getElementById('drawerAsistentes');
            drawer.classList.toggle('translate-x-full');
        }

        // Pantalla Completa
        function togglePantallaCompleta() {
            const icon = document.getElementById('fullscreenIcon');
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
                icon.className = "fa-solid fa-compress";
            } else {
                document.exitFullscreen().catch(err => console.log(err));
                icon.className = "fa-solid fa-expand";
            }
        }

        // Consultar Asistencias vía Polling
        async function cargarAsistencias() {
            try {
                const response = await fetch(STREAM_URL);
                if (!response.ok) return;

                const data = await response.json();

                // Actualizar badges
                document.getElementById('contadorAsistentesBadge').textContent = data.total;
                document.getElementById('drawerTotalBadge').textContent = data.total;

                // Si entraron nuevos asistentes, animar el badge
                if (data.total > totalAsistentesAnterior) {
                    const badge = document.getElementById('contadorAsistentesBadge');
                    badge.classList.add('scale-125', 'bg-emerald-400');
                    setTimeout(() => badge.classList.remove('scale-125', 'bg-emerald-400'), 1000);
                    totalAsistentesAnterior = data.total;
                }

                // Renderizar lista en el drawer
                const container = document.getElementById('listaAsistentesContainer');
                if (data.asistencias.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12 text-xs text-slate-500">
                            <i class="fa-solid fa-user-clock text-2xl text-slate-600 mb-2"></i>
                            <div>Aún no se han registrado asistentes.</div>
                            <div class="text-[11px] text-slate-600 mt-1">Proyecta el código QR para comenzar.</div>
                        </div>
                    `;
                } else {
                    container.innerHTML = data.asistencias.map(a => `
                        <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700/60 flex items-start justify-between gap-2 animate-fade-in">
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-white truncate">${a.nombre}</div>
                                <div class="text-[11px] text-cyan-400 font-mono mt-0.5">CC: ${a.identificacion}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    ${a.es_externo ? '<span class="text-amber-400 font-bold">[EXTERNO] ' + (a.empresa || 'Contratista') + '</span>' : (a.cargo || 'Colaborador')}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-mono text-slate-400">${a.hora}</span>
                                <div class="mt-1">
                                    <span class="inline-flex items-center text-[10px] font-bold text-emerald-400">
                                        <i class="fa-solid fa-signature mr-1"></i> Firmado
                                    </span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error("Error al consultar stream de asistencias:", error);
            }
        }

        // Finalizar Sesión
        async function confirmarFinalizarSesion() {
            if (!confirm("¿Deseas finalizar esta sesión de inducción? Una vez cerrada, no se admitirán más registros de asistencia y podrás descargar el acta oficial.")) {
                return;
            }

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
                    alert("Sesión finalizada con éxito.");
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
                alert("Ocurrió un error al intentar finalizar la sesión.");
            }
        }
    </script>

</body>
</html>
