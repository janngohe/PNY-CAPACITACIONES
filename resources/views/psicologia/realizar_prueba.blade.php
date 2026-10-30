@extends('layouts.psicologia')

@section('title', 'Realizar Prueba · Código QR de Evaluación')
@section('page_title', 'Realizar Prueba')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- CABECERA DE LA SECCIÓN -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-line/70 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-brand-light text-brand-blue text-[11px] font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-qrcode"></i>
                <span>Evaluación Presencial o Remota</span>
            </div>
            <h2 class="text-xl font-heading font-extrabold text-brand-dark">Código QR para Evaluación Psicológica</h2>
            <p class="text-xs text-muted mt-0.5">Pídele a la persona a evaluar que apunte con la cámara de su celular al código QR para abrir el formulario en 3 pasos.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Formulario para regenerar código -->
            <form method="POST" action="{{ route('psicologia.pruebas.nuevo_codigo') }}" class="m-0">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-brand-light text-slate-700 hover:text-brand-blue border border-line font-heading font-bold text-xs transition flex items-center gap-2 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-rotate text-xs"></i>
                    <span>Generar Nuevo Código</span>
                </button>
            </form>

            <!-- Botón para ver en pantalla completa -->
            <button type="button"
                    onclick="abrirModalQRGigante()"
                    class="px-4 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs transition flex items-center gap-2 cursor-pointer shadow-2xs">
                <i class="fa-solid fa-expand text-xs"></i>
                <span>Proyectar / Pantalla Completa</span>
            </button>
        </div>
    </div>

    <!-- GRID PRINCIPAL: PANEL DEL QR Y CONFIGURACIÓN NGROK/MÓVIL -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: TARJETA DEL CÓDIGO QR (7 COLUMNAS) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-line/80 shadow-xs flex flex-col items-center text-center relative overflow-hidden">
            <!-- Borde decorativo superior -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue"></div>

            <!-- Badge de Sesión Activa -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-50 border border-line text-xs font-semibold text-slate-700 mb-6 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sesión Activa:</span>
                <strong class="font-mono text-brand-blue font-bold text-sm tracking-wider" id="labelCodigoSesion">{{ $codigoSesion }}</strong>
            </div>

            <!-- MARCO DEL CÓDIGO QR -->
            <div class="relative p-4 sm:p-6 bg-white rounded-3xl border-2 border-line/90 shadow-md flex flex-col items-center justify-center">
                <!-- Contenedor del Canvas QR generado por JS -->
                <div id="qr-container" class="flex items-center justify-center min-w-[240px] min-h-[240px] bg-white rounded-2xl overflow-hidden p-2">
                    <div class="text-xs text-muted flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin text-brand-blue text-base"></i>
                        <span>Generando código QR...</span>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-bold text-brand-blue uppercase tracking-wider">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                    <span>Escanear con la cámara del móvil</span>
                </div>
            </div>

            <!-- URL QUE CODIFICA EL QR CON BOTÓN COPIAR -->
            <div class="mt-6 w-full max-w-md">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-muted mb-1.5 text-left">
                    Enlace directo de la evaluación:
                </label>
                <div class="flex items-center gap-2 p-1.5 bg-slate-50 border border-line rounded-xl">
                    <input type="text"
                           id="inputUrlActiva"
                           readonly
                           class="flex-1 bg-transparent px-2 text-xs font-mono text-slate-700 truncate focus:outline-none select-all"
                           value="{{ $urlEvaluacionNgrok ?? $urlEvaluacionActual }}">
                    <button type="button"
                            onclick="copiarUrlAlPortapapeles()"
                            class="px-3 py-1.5 rounded-lg bg-white hover:bg-brand-light text-slate-700 hover:text-brand-blue border border-line text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-2xs cursor-pointer">
                        <i class="fa-regular fa-copy text-xs"></i>
                        <span id="btnCopiarTexto">Copiar</span>
                    </button>
                    <a id="linkProbarUrl"
                       href="{{ $urlEvaluacionNgrok ?? $urlEvaluacionActual }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-brand-light text-slate-700 hover:text-brand-blue border border-line text-xs font-bold transition shrink-0 shadow-2xs"
                       title="Abrir formulario en nueva pestaña para probar">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
                </div>
                <p id="msgUrlCopiada" class="text-[11px] font-semibold text-emerald-600 mt-1.5 hidden text-left flex items-center gap-1">
                    <i class="fa-solid fa-check"></i> Enlace copiado al portapapeles.
                </p>
            </div>

            <!-- BOTONES DE ACCIÓN SECUNDARIOS -->
            <div class="mt-6 pt-5 border-t border-line/70 w-full flex flex-wrap items-center justify-center gap-3">
                <button type="button"
                        onclick="descargarImagenQR()"
                        class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 border border-line text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fa-solid fa-download text-xs text-brand-blue"></i>
                    <span>Descargar Imagen QR</span>
                </button>
                <a href="{{ $urlEvaluacionNgrok ?? $urlEvaluacionActual }}"
                   target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-brand-cyan border border-sky-200 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-eye text-xs"></i>
                    <span>Vista Previa Móvil</span>
                </a>
            </div>

        </div>

        <!-- COLUMNA DERECHA: CONECTIVIDAD NGROK Y ESTRUCTURA DE 3 PASOS (5 COLUMNAS) -->
        <div class="lg:col-span-5 space-y-5">

            <!-- TARJETA: MODO DE CONECTIVIDAD / NGROK PARA CELULARES -->
            <div class="bg-white rounded-3xl p-6 border border-line/80 shadow-xs">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white flex items-center justify-center text-lg shadow-2xs">
                        <i class="fa-solid fa-tower-broadcast"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-brand-dark">Conectividad Móvil (ngrok)</h3>
                        <p class="text-[11px] text-muted">Selecciona la red para generar el QR</p>
                    </div>
                </div>

                <!-- SELECTOR DE RED (NGROK / LOCAL / ACTUAL) -->
                <div class="space-y-2 mb-4">
                    <!-- Opción 1: Túnel Ngrok (Recomendado para Celulares) -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition select-none bg-brand-light/60 border-brand-blue/40" id="labelModoNgrok">
                        <input type="radio" name="modo_red" value="ngrok" checked onchange="cambiarModoRed('ngrok')" class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-heading font-bold text-xs text-brand-dark">Túnel Ngrok (Internet / Móvil)</span>
                                <span id="badgeEstadoNgrok" class="px-2 py-0.5 rounded text-[10px] font-bold {{ $urlNgrokBase ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $urlNgrokBase ? 'Detectado' : 'Manual' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-muted mt-0.5">Permite escanear desde cualquier celular conectado por datos o WiFi.</p>
                        </div>
                    </label>

                    <!-- Opción 2: IP Local WiFi -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-line hover:border-slate-300 cursor-pointer transition select-none bg-white" id="labelModoLocal">
                        <input type="radio" name="modo_red" value="local" onchange="cambiarModoRed('local')" class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1 min-w-0">
                            <span class="font-heading font-bold text-xs text-brand-dark">Red WiFi Local (IP)</span>
                            <p class="text-[11px] text-muted mt-0.5">Válido si el celular está conectado a la misma red WiFi (IP: {{ $localIp }}).</p>
                        </div>
                    </label>

                    <!-- Opción 3: Dominio / Host Actual -->
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-line hover:border-slate-300 cursor-pointer transition select-none bg-white" id="labelModoActual">
                        <input type="radio" name="modo_red" value="actual" onchange="cambiarModoRed('actual')" class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1 min-w-0">
                            <span class="font-heading font-bold text-xs text-brand-dark">Dominio Actual del Navegador</span>
                            <p class="text-[11px] text-muted mt-0.5">Usa exactamente la URL actual de tu navegador.</p>
                        </div>
                    </label>
                </div>

                <!-- Input para personalizar / cambiar la URL de Ngrok -->
                <div id="contenedorConfigNgrok" class="pt-3 border-t border-line/70">
                    <label for="inputCustomNgrok" class="block text-[11px] font-bold text-slate-700 mb-1">
                        URL pública de Ngrok:
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="url"
                               id="inputCustomNgrok"
                               placeholder="https://xxxx-xx-xx.ngrok-free.app"
                               value="{{ $urlNgrokBase ?? '' }}"
                               class="flex-1 px-3 py-1.5 text-xs font-mono rounded-xl border border-line focus:outline-none focus:border-brand-blue text-slate-700 placeholder:text-slate-400">
                        <button type="button"
                                onclick="guardarUrlNgrokManual()"
                                class="px-3 py-1.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold transition shrink-0 cursor-pointer">
                            Aplicar
                        </button>
                    </div>
                    <p class="text-[10px] text-muted mt-1">
                        Si inicias ngrok en tu consola (`ngrok http 8000` o Laragon), pega aquí la URL HTTPS para actualizar el QR.
                    </p>
                </div>
            </div>

            <!-- TARJETA: ESTRUCTURA DEL FORMULARIO DE 3 PASOS -->
            <div class="bg-white rounded-3xl p-6 border border-line/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-brand-light text-brand-blue flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <h3 class="font-heading font-extrabold text-sm text-brand-dark">Formulario en 3 Pasos</h3>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                        Flujo del Evaluado
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <!-- Paso 1 -->
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-line/60">
                        <div class="w-7 h-7 rounded-lg bg-brand-blue text-white flex items-center justify-center font-bold text-xs shrink-0">
                            1
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800">Consentimiento Informado</p>
                            <p class="text-[10.5px] text-muted">Aceptación y firma previa antes de la aplicación.</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-line/60">
                        <div class="w-7 h-7 rounded-lg bg-brand-blue text-white flex items-center justify-center font-bold text-xs shrink-0">
                            2
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800">Primera Prueba</p>
                            <p class="text-[10.5px] text-muted">Cuestionario psicotécnico inicial.</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-line/60">
                        <div class="w-7 h-7 rounded-lg bg-brand-blue text-white flex items-center justify-center font-bold text-xs shrink-0">
                            3
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800">Segunda Prueba</p>
                            <p class="text-[10.5px] text-muted">Cuestionario complementario de evaluación.</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-line/60 text-[11px] text-muted flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-brand-blue"></i>
                    <span>La persona completará cada paso consecutivamente en su teléfono.</span>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- MODAL GIGANTE DE PROYECCIÓN / PANTALLA COMPLETA DEL QR -->
<div id="modalQRGigante" class="fixed inset-0 z-50 hidden bg-brand-dark/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-6">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-2xl flex flex-col items-center text-center relative border border-line animate-fade-in">
        <!-- Botón Cerrar -->
        <button type="button"
                onclick="cerrarModalQRGigante()"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-lg cursor-pointer transition">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Cabecera del Modal -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-light text-brand-blue text-xs font-bold mb-3">
            <i class="fa-solid fa-qrcode"></i>
            <span>C.I. Piscícola New York · Evaluación Psicológica</span>
        </div>
        <h3 class="font-heading font-extrabold text-xl text-brand-dark">Escanea para Iniciar la Evaluación</h3>
        <p class="text-xs text-muted mt-1 max-w-sm">Apunta con la cámara de tu smartphone hacia el código para abrir el formulario.</p>

        <!-- Contenedor QR Gigante -->
        <div class="my-6 p-6 bg-white rounded-3xl border-2 border-line shadow-inner flex items-center justify-center">
            <div id="modalQrElement" class="flex items-center justify-center min-w-[280px] min-h-[280px]"></div>
        </div>

        <div class="inline-flex items-center gap-2 text-xs font-mono text-slate-600 bg-slate-50 px-4 py-2 rounded-xl border border-line mb-4">
            <span>Sesión:</span>
            <strong class="text-brand-blue font-bold text-sm">{{ $codigoSesion }}</strong>
        </div>

        <button type="button"
                onclick="cerrarModalQRGigante()"
                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-heading font-bold transition cursor-pointer">
            Cerrar Proyección
        </button>
    </div>
</div>
@endsection

@push('scripts')
<!-- Librería QRCode.js -->
<script src="{{ asset('qrcode.min.js') }}"></script>
<script>
    if (typeof QRCode === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
    }
</script>

<script>
    const CODIGO_SESION = @json($codigoSesion);
    const URL_ACTUAL_BASE = @json($urlEvaluacionActual);
    const URL_LOCAL_BASE = @json($urlEvaluacionLocal);
    let URL_NGROK_BASE = @json($urlEvaluacionNgrok);
    let ngrokServerUrl = @json($urlNgrokBase);

    // Recuperar URL personalizada de Ngrok desde localStorage si existe
    const ngrokGuardado = localStorage.getItem('pny_psicologia_ngrok_url');
    if (ngrokGuardado) {
        ngrokServerUrl = ngrokGuardado.replace(/\/+$/, '');
        URL_NGROK_BASE = ngrokServerUrl + '/evaluacion-psicologica/' + CODIGO_SESION;
        const inputNgrok = document.getElementById('inputCustomNgrok');
        if (inputNgrok) inputNgrok.value = ngrokServerUrl;
    }

    // Modo de red activo: Si hay Ngrok se preselecciona 'ngrok', de lo contrario 'actual'
    let modoRedActivo = URL_NGROK_BASE ? 'ngrok' : 'actual';
    let qrInstanciaPrincipal = null;
    let qrInstanciaModal = null;

    function obtenerUrlEvaluacionActiva() {
        if (modoRedActivo === 'ngrok' && URL_NGROK_BASE) {
            return URL_NGROK_BASE;
        }
        if (modoRedActivo === 'local' && URL_LOCAL_BASE) {
            return URL_LOCAL_BASE;
        }
        return URL_ACTUAL_BASE;
    }

    function renderizarQRs() {
        const urlActiva = obtenerUrlEvaluacionActiva();

        // Actualizar input de texto visible
        const inputUrl = document.getElementById('inputUrlActiva');
        if (inputUrl) inputUrl.value = urlActiva;

        // Actualizar link de prueba
        const linkProbar = document.getElementById('linkProbarUrl');
        if (linkProbar) linkProbar.href = urlActiva;

        // Generar QR principal
        const container = document.getElementById('qr-container');
        if (container) {
            container.innerHTML = '';
            qrInstanciaPrincipal = new QRCode(container, {
                text: urlActiva,
                width: 240,
                height: 240,
                colorDark: "#002d5e", // Azul corporativo Piscícola New York
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        // Generar QR en Modal Gigante
        const modalContainer = document.getElementById('modalQrElement');
        if (modalContainer) {
            modalContainer.innerHTML = '';
            qrInstanciaModal = new QRCode(modalContainer, {
                text: urlActiva,
                width: 280,
                height: 280,
                colorDark: "#002d5e",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    function cambiarModoRed(modo) {
        modoRedActivo = modo;

        // Estilos visuales de los radios
        const modoCards = {
            'ngrok': document.getElementById('labelModoNgrok'),
            'local': document.getElementById('labelModoLocal'),
            'actual': document.getElementById('labelModoActual')
        };

        Object.keys(modoCards).forEach(key => {
            const card = modoCards[key];
            if (!card) return;
            if (key === modo) {
                card.classList.add('bg-brand-light/60', 'border-brand-blue/40');
                card.classList.remove('bg-white', 'border-line');
            } else {
                card.classList.remove('bg-brand-light/60', 'border-brand-blue/40');
                card.classList.add('bg-white', 'border-line');
            }
        });

        renderizarQRs();
    }

    function guardarUrlNgrokManual() {
        const inputNgrok = document.getElementById('inputCustomNgrok');
        if (!inputNgrok) return;

        let url = inputNgrok.value.trim().replace(/\/+$/, '');
        if (!url) {
            alert('Por favor ingresa una URL válida de Ngrok (ej. https://xxxx.ngrok-free.app)');
            return;
        }

        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            url = 'https://' + url;
            inputNgrok.value = url;
        }

        localStorage.setItem('pny_psicologia_ngrok_url', url);
        ngrokServerUrl = url;
        URL_NGROK_BASE = url + '/evaluacion-psicologica/' + CODIGO_SESION;

        const badge = document.getElementById('badgeEstadoNgrok');
        if (badge) {
            badge.textContent = 'Guardado';
            badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800';
        }

        cambiarModoRed('ngrok');
    }

    function copiarUrlAlPortapapeles() {
        const input = document.getElementById('inputUrlActiva');
        if (!input) return;

        navigator.clipboard.writeText(input.value).then(() => {
            const msg = document.getElementById('msgUrlCopiada');
            const btn = document.getElementById('btnCopiarTexto');
            if (btn) btn.textContent = 'Copiado!';
            if (msg) msg.classList.remove('hidden');

            setTimeout(() => {
                if (btn) btn.textContent = 'Copiar';
                if (msg) msg.classList.add('hidden');
            }, 3000);
        }).catch(() => {
            input.select();
            document.execCommand('copy');
            alert('Enlace copiado al portapapeles');
        });
    }

    function descargarImagenQR() {
        const container = document.getElementById('qr-container');
        const img = container ? container.querySelector('img') : null;
        const canvas = container ? container.querySelector('canvas') : null;

        let src = null;
        if (img && img.src) {
            src = img.src;
        } else if (canvas) {
            src = canvas.toDataURL('image/png');
        }

        if (src) {
            const a = document.createElement('a');
            a.href = src;
            a.download = `QR-Evaluacion-${CODIGO_SESION}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            alert('El código QR aún se está generando, intenta de nuevo en un segundo.');
        }
    }

    function abrirModalQRGigante() {
        const modal = document.getElementById('modalQRGigante');
        if (modal) {
            modal.classList.remove('hidden');
            renderizarQRs();
        }
    }

    function cerrarModalQRGigante() {
        const modal = document.getElementById('modalQRGigante');
        if (modal) modal.classList.add('hidden');
    }

    // Inicializar al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        // Preseleccionar radio button según modoRedActivo
        const radio = document.querySelector(`input[name="modo_red"][value="${modoRedActivo}"]`);
        if (radio) radio.checked = true;
        cambiarModoRed(modoRedActivo);
    });
</script>
@endpush
