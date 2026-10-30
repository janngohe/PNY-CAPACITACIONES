@extends('layouts.psicologia')

@section('title', 'Código QR para Evaluados')
@section('page_title', 'Código QR para Auto-registro de Evaluados')
@section('page_subtitle', 'Suministra este código a los aspirantes para que registren sus datos, firmen el consentimiento y apliquen PAC-A')

@section('content')
<div class="space-y-6">

    <!-- Tarjeta Principal con el Código QR -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Columna Izquierda: Visor del Código QR y Acciones Rápidas -->
        <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col items-center text-center">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-brand-blue border border-blue-200 text-xs font-bold uppercase tracking-wider mb-4">
                <i class="fa-solid fa-qrcode"></i> Escaneo Móvil
            </div>

            <!-- Contenedor del Código QR Nítido -->
            <div class="p-3 bg-white border-2 border-slate-200 rounded-3xl shadow-sm hover:border-brand-blue transition-colors mb-4 cursor-pointer"
                 onclick="abrirModalQRProyeccion()"
                 title="Haz clic para proyectar en pantalla completa">
                <div id="contenedorQrVisual" class="w-64 h-64 flex items-center justify-center"></div>
            </div>

            <p class="text-xs text-slate-500 mb-4 max-w-xs leading-relaxed">
                Apunta con la cámara de cualquier teléfono celular al código para abrir inmediatamente el portal seguro de evaluación.
            </p>

            <!-- Acciones Principales -->
            <div class="w-full space-y-2.5">
                <button type="button"
                        onclick="abrirModalQRProyeccion()"
                        class="w-full py-3 px-4 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-expand"></i>
                    <span>Proyectar en Pantalla Completa</span>
                </button>

                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            onclick="copiarEnlace()"
                            class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-copy text-brand-blue"></i>
                        <span id="btnCopiarTxt">Copiar Enlace</span>
                    </button>

                    <a href="{{ route('psicologia.qr.imprimir') }}"
                       target="_blank"
                       id="btnImprimirCartel"
                       class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-1.5 text-center">
                        <i class="fa-solid fa-print text-emerald-600"></i>
                        <span>Imprimir Cartel</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Configuración de Enlace, Instrucciones y Estadísticas -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Selector de Enlace / Dirección de Red -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900 font-serif flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-brand-blue"></i>
                    <span>Configuración de Dirección del QR</span>
                </h3>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Selecciona según dónde se encuentren los evaluados para garantizar que su teléfono pueda cargar el formulario:
                </p>

                <div class="space-y-2.5 text-xs">
                    <!-- Opción 1: Servidor Actual -->
                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors"
                           onclick="cambiarModoUrl('actual')">
                        <input type="radio" name="modoUrl" id="radioModoActual" value="actual" checked class="text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1">
                            <strong class="text-slate-800 font-bold block">Dominio Actual del Servidor</strong>
                            <code class="text-slate-500 font-mono text-[11px]">{{ $urlBaseActual }}</code>
                        </div>
                    </label>

                    <!-- Opción 2: Red Local Wi-Fi -->
                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors"
                           onclick="cambiarModoUrl('local')">
                        <input type="radio" name="modoUrl" id="radioModoLocal" value="local" class="text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1">
                            <strong class="text-slate-800 font-bold block">Red Local Wi-Fi (Para celulares conectados a la misma red)</strong>
                            <code class="text-brand-blue font-mono text-[11px] font-bold">{{ $urlRedLocal }}</code>
                        </div>
                    </label>

                    <!-- Opción 3: Túnel Público Ngrok -->
                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-amber-200 bg-amber-50/40 hover:bg-amber-50 cursor-pointer transition-colors"
                           onclick="cambiarModoUrl('ngrok')">
                        <input type="radio" name="modoUrl" id="radioModoNgrok" value="ngrok" class="text-brand-blue focus:ring-brand-blue">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <strong class="text-slate-900 font-bold flex items-center gap-1.5">
                                    <i class="fa-solid fa-bolt text-amber-500"></i>
                                    <span>Túnel Público Ngrok (Recomendado para celulares con datos móviles)</span>
                                </strong>
                                <button type="button" onclick="configurarNgrokManual(event)"
                                        class="text-[10px] font-bold text-brand-blue hover:text-brand-dark flex items-center gap-1 hover:underline">
                                    <i class="fa-solid fa-gear"></i> Ajustar URL
                                </button>
                            </div>
                            <div class="mt-1 flex items-center gap-2">
                                <span id="badgeNgrokEstado" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urlNgrok ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $urlNgrok ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span id="txtNgrokEstado">{{ $urlNgrok ? 'Túnel Activo' : 'Manual / Inactivo' }}</span>
                                </span>
                                <code class="text-slate-800 font-mono text-[11px] font-bold" id="txtNgrokUrlPreview">
                                    {{ $urlNgrok ?: 'https://xxxx.ngrok-free.dev/evaluacion' }}
                                </code>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Campo de Enlace Actual -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                        URL Codificada en el QR:
                    </label>
                    <input type="text"
                           id="inputUrlActiva"
                           readonly
                           value="{{ $urlBaseActual }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-800 select-all focus:outline-none">
                </div>
            </div>

            <!-- Flujo Paso a Paso para la Psicóloga -->
            <div class="bg-blue-50/60 border border-blue-200/80 rounded-3xl p-5 shadow-xs text-xs space-y-3">
                <div class="font-extrabold text-brand-blue flex items-center gap-2">
                    <i class="fa-solid fa-route"></i>
                    <span>¿Cómo funciona el proceso de auto-registro?</span>
                </div>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-700 leading-relaxed font-medium">
                    <li><strong>Proyecta o imprime el QR:</strong> Colócalo en la sala de aspirantes o muéstraselo en pantalla.</li>
                    <li><strong>El aspirante escanea e ingresa sus datos:</strong> Nombre, cédula, lugar de expedición y municipio.</li>
                    <li><strong>Consentimiento automático (GH-FO-025):</strong> El sistema completa el documento y el aspirante lo firma digitalmente en su pantalla táctil.</li>
                    <li><strong>Aplicación PAC-A:</strong> Responde las 40 preguntas desde su celular con guardado de borrador y envía la prueba.</li>
                    <li><strong>Calificación inmediata:</strong> La prueba queda calificada al instante en tu panel con sus dos indicadores calculados.</li>
                </ol>
            </div>

            <!-- Últimos Aspirantes Registrados vía QR -->
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-users-viewfinder text-brand-blue"></i>
                        <span>Últimos Evaluados vía QR (Total: {{ $totalEvaluadosQr }})</span>
                    </h4>
                    <a href="{{ route('psicologia.pruebas.historial') }}" class="text-[11px] font-bold text-brand-blue hover:underline">
                        Ver Historial
                    </a>
                </div>

                @if($ultimosEvaluados->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center italic">
                        Aún no hay aspirantes registrados mediante QR hoy.
                    </p>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($ultimosEvaluados as $eval)
                            @php
                                $appPaca = $eval->aplicaciones->where('prueba_codigo', 'PAC-A')->first();
                            @endphp
                            <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <strong class="text-slate-900 block font-bold">{{ $eval->nombre_completo }}</strong>
                                    <span class="text-[11px] text-slate-400">C.C. {{ $eval->numero_identificacion }} · {{ $eval->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-right flex items-center gap-2 flex-wrap justify-end">
                                    @php $cons = $eval->ultimoConsentimiento(); @endphp
                                    @if($cons)
                                        <button type="button"
                                           onclick="abrirModalPdf('{{ route('psicologia.consentimientos.pdf', [$cons, 'inline' => 1]) }}', '{{ route('psicologia.consentimientos.pdf', $cons) }}', 'Consentimiento - {{ addslashes($eval->nombre_completo) }}')"
                                           title="Previsualizar Consentimiento PDF con Firma"
                                           class="px-2 py-1 rounded-lg bg-rose-50 text-rose-700 font-bold text-[11px] hover:bg-rose-100 border border-rose-200/80 inline-flex items-center gap-1 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-file-pdf text-[10px] text-rose-600"></i>
                                            Consentimiento
                                        </button>
                                    @endif
                                    @if($appPaca && $appPaca->estaFinalizada())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            PAC-A Calificada
                                        </span>
                                        <a href="{{ route('psicologia.paca.resultado', $appPaca) }}"
                                           class="px-2.5 py-1 rounded-lg bg-brand-light text-brand-blue font-bold text-[11px] hover:bg-blue-100">
                                            Resultado
                                        </a>
                                    @elseif($appPaca)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            PAC-A en curso ({{ round($appPaca->progreso_porcentaje) }}%)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            Consentimiento Firmado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

<!-- MODAL: Proyección en Pantalla Completa para Sala -->
<div id="modalQRProyeccion" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 text-center relative shadow-2xl animate-in fade-in duration-200">
        <button type="button" onclick="cerrarModalQRProyeccion()"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-2 text-lg cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-brand-blue flex items-center justify-center mx-auto mb-3">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
        </div>

        <span class="text-[10px] font-bold uppercase tracking-widest text-brand-blue block">C.I. Piscícola New York S.A.</span>
        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 font-serif mt-0.5">
            Auto-registro y Prueba PAC-A
        </h3>
        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
            Apunta la cámara de tu celular a este código para iniciar tu registro y evaluación.
        </p>

        <!-- QR Gigante -->
        <div class="my-6 p-4 bg-white border-2 border-slate-200 rounded-3xl inline-block shadow-md">
            <div id="contenedorQrGigante" class="w-64 h-64 sm:w-72 sm:h-72 flex items-center justify-center"></div>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs font-mono font-bold text-brand-blue select-all break-all" id="txtUrlModal">
            {{ $urlBaseActual }}
        </div>

        <button type="button" onclick="cerrarModalQRProyeccion()"
                class="mt-5 px-6 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider cursor-pointer">
            Cerrar Proyección
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('qrcode.min.js') }}"></script>
<script>
    if (typeof QRCode === 'undefined') {
        document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
    }

    const URL_ACTUAL = "{{ $urlBaseActual }}";
    const URL_LOCAL = "{{ $urlRedLocal }}";
    let URL_NGROK_SERVER = @json($urlNgrok);
    let URL_NGROK_CUSTOM = localStorage.getItem('pny_psicologia_ngrok_url') || null;

    let URL_NGROK = URL_NGROK_CUSTOM || URL_NGROK_SERVER;

    let urlSeleccionada = URL_NGROK || URL_ACTUAL;
    let qrVisualInstance = null;
    let qrGiganteInstance = null;

    function actualizarPreviewNgrok() {
        const preview = document.getElementById('txtNgrokUrlPreview');
        const badge = document.getElementById('badgeNgrokEstado');
        const txtBadge = document.getElementById('txtNgrokEstado');

        if (URL_NGROK) {
            if (preview) preview.textContent = URL_NGROK;
            if (badge) {
                badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
            }
            if (txtBadge) txtBadge.textContent = 'Túnel Configurado';
        } else {
            if (preview) preview.textContent = 'https://xxxx.ngrok-free.dev/evaluacion';
            if (badge) {
                badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700';
            }
            if (txtBadge) txtBadge.textContent = 'Manual / Inactivo';
        }
    }

    function renderizarQrs(url) {
        urlSeleccionada = url;
        document.getElementById('inputUrlActiva').value = url;
        document.getElementById('txtUrlModal').textContent = url;

        // Actualizar botón de impresión con la URL seleccionada
        const btnImp = document.getElementById('btnImprimirCartel');
        if (btnImp) {
            btnImp.href = `{{ route('psicologia.qr.imprimir') }}?url=${encodeURIComponent(url)}`;
        }

        // QR Visual en el panel
        const cVisual = document.getElementById('contenedorQrVisual');
        cVisual.innerHTML = '';
        qrVisualInstance = new QRCode(cVisual, {
            text: url,
            width: 256,
            height: 256,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // QR Gigante para proyector
        const cGigante = document.getElementById('contenedorQrGigante');
        cGigante.innerHTML = '';
        qrGiganteInstance = new QRCode(cGigante, {
            text: url,
            width: 288,
            height: 288,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function cambiarModoUrl(modo) {
        if (modo === 'local') {
            document.getElementById('radioModoLocal').checked = true;
            renderizarQrs(URL_LOCAL);
        } else if (modo === 'ngrok') {
            document.getElementById('radioModoNgrok').checked = true;
            if (!URL_NGROK) {
                configurarNgrokManual();
            } else {
                renderizarQrs(URL_NGROK);
            }
        } else {
            document.getElementById('radioModoActual').checked = true;
            renderizarQrs(URL_ACTUAL);
        }
    }

    function configurarNgrokManual(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }

        const actual = URL_NGROK || 'https://xxxx.ngrok-free.dev/evaluacion';
        const nueva = prompt("Ingresa la URL pública de tu túnel Ngrok (ej. https://xxxx.ngrok-free.dev):", actual);

        if (nueva !== null) {
            let limpia = nueva.trim().replace(/\/+$/, '');
            if (limpia) {
                if (!limpia.endsWith('/evaluacion')) {
                    limpia = limpia + '/evaluacion';
                }
                URL_NGROK = limpia;
                localStorage.setItem('pny_psicologia_ngrok_url', limpia);
                actualizarPreviewNgrok();
                cambiarModoUrl('ngrok');
            } else {
                URL_NGROK = URL_NGROK_SERVER;
                localStorage.removeItem('pny_psicologia_ngrok_url');
                actualizarPreviewNgrok();
                cambiarModoUrl(URL_NGROK ? 'ngrok' : 'actual');
            }
        }
    }

    function copiarEnlace() {
        navigator.clipboard.writeText(urlSeleccionada).then(() => {
            const btn = document.getElementById('btnCopiarTxt');
            btn.textContent = '¡Copiado!';
            setTimeout(() => { btn.textContent = 'Copiar Enlace'; }, 2000);
        });
    }

    function abrirModalQRProyeccion() {
        document.getElementById('modalQRProyeccion').classList.remove('hidden');
    }

    function cerrarModalQRProyeccion() {
        document.getElementById('modalQRProyeccion').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', () => {
        actualizarPreviewNgrok();
        if (URL_NGROK) {
            cambiarModoUrl('ngrok');
        } else {
            renderizarQrs(URL_ACTUAL);
        }
    });
</script>
@endpush
