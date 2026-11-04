@php
    /** @var \App\Models\Usuario $usuario */
    $usuario = $usuario ?? auth()->user();
    $debeRegistrarFirma = $usuario && $usuario->rol === 'PSICOLOGIA' && !$usuario->tieneFirmaRegistrada();
    $tieneFirma = $usuario && $usuario->tieneFirmaRegistrada();
@endphp

<!-- ========================================================
     MODAL DE REGISTRO / ACTUALIZACIÓN DE FIRMA DIGITAL (PSICOLOGÍA)
     ======================================================== -->
<div id="modal-firma-psicologa"
     class="fixed inset-0 z-50 {{ $debeRegistrarFirma ? 'flex' : 'hidden' }} items-center justify-center p-3 sm:p-5"
     role="dialog"
     aria-modal="true">

    <!-- Backdrop con desenfoque suave -->
    <div id="modal-firma-psicologa-backdrop"
         class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs transition-opacity duration-300 {{ $debeRegistrarFirma ? 'opacity-100' : 'opacity-0' }}"
         onclick="cerrarModalFirmaPsicologa()"></div>

    <!-- Contenedor del Modal -->
    <div id="modal-firma-psicologa-panel"
         class="relative bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all duration-300 {{ $debeRegistrarFirma ? 'scale-100 opacity-100' : 'scale-95 opacity-0' }} z-10 font-sans my-4 max-h-[92vh] flex flex-col">

        <!-- CABECERA INSTITUCIONAL CON GRADIENTE PISCÍCOLA -->
        <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-5 sm:p-6 overflow-hidden shrink-0">
            <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-cyan-400/15 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-2xl bg-white/15 text-cyan-300 flex items-center justify-center text-lg font-bold shadow-xs shrink-0 border border-white/20">
                        <i class="fa-solid fa-signature"></i>
                    </span>
                    <div>
                        <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-cyan-200 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/15 mb-1">
                            {{ $debeRegistrarFirma ? 'Configuración Inicial · Primer Ingreso' : 'Firma Digital Oficial' }}
                        </span>
                        <h3 class="font-heading font-black text-base sm:text-lg text-white leading-tight">
                            {{ $tieneFirma ? 'Actualizar Firma Digital Oficial' : 'Registra tu Firma Digital Oficial' }}
                        </h3>
                    </div>
                </div>

                <button type="button"
                        onclick="cerrarModalFirmaPsicologa()"
                        class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer shrink-0"
                        title="Cerrar ventana">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <p class="text-xs text-slate-200/90 mt-2.5 relative z-10 leading-relaxed">
                Hola, <strong class="text-white">{{ $usuario->nombre_completo ?? 'Especialista' }}</strong>. Tu firma se guardará una sola vez en el sistema para <strong class="text-cyan-300">firmar automáticamente</strong> el Certificado de Aptitud Psicológica (<strong>GH-FO-028</strong>) sin que tengas que firmar cada vez.
            </p>
        </div>

        <!-- CUERPO SCROLLEABLE -->
        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1">

            <!-- VISTA PREVIA SI YA TIENE FIRMA REGISTRADA -->
            @if($tieneFirma)
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-14 w-28 bg-white rounded-xl border border-emerald-200 p-1 flex items-center justify-center shrink-0 shadow-2xs">
                            <img src="{{ $usuario->firmaUrl() }}" alt="Firma Actual" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                Firma Oficial Activa
                            </span>
                            <p class="text-[10px] text-slate-500 mt-0.5 truncate">
                                Actualizada: {{ $usuario->firma_actualizada_el ? $usuario->firma_actualizada_el->format('d/m/Y h:i A') : 'Registrada' }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('psicologia.firma.eliminar') }}" onsubmit="return confirm('¿Seguro que deseas eliminar tu firma digital registrada?');" class="shrink-0 m-0">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800 hover:bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-200 transition cursor-pointer">
                            <i class="fa-solid fa-trash-can mr-1"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            @endif

            <!-- SELECTOR DE PESTAÑAS (DIBUJAR vs SUBIR ARCHIVO) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block font-heading font-bold text-xs text-slate-800">
                        {{ $tieneFirma ? 'Reemplazar con nueva firma:' : 'Selecciona cómo deseas ingresar tu firma:' }}
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl text-xs font-heading font-bold">
                    <button type="button"
                            id="btn-tab-firma-dibujar"
                            onclick="cambiarTabFirmaPsicologa('dibujar')"
                            class="py-2 px-3 rounded-lg bg-white text-brand-blue shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-pen-nib text-xs"></i>
                        <span>Dibujar en Pantalla</span>
                    </button>
                    <button type="button"
                            id="btn-tab-firma-archivo"
                            onclick="cambiarTabFirmaPsicologa('archivo')"
                            class="py-2 px-3 rounded-lg text-slate-600 hover:text-brand-blue transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Subir Imagen</span>
                    </button>
                </div>
            </div>

            <!-- FORMULARIO DE GUARDADO -->
            <form id="form-guardar-firma-psicologa"
                  method="POST"
                  action="{{ route('psicologia.firma.guardar') }}"
                  enctype="multipart/form-data"
                  onsubmit="return validarEnvioFirmaPsicologa()">
                @csrf
                <input type="hidden" name="modo_firma" id="input-psicologa-modo-firma" value="dibujar">
                <input type="hidden" name="firma_canvas" id="input-psicologa-firma-canvas" value="">

                <!-- PANEL 1: DIBUJAR EN PANTALLA -->
                <div id="panel-firma-dibujar" class="space-y-2">
                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span>Dibuja tu trazo con el mouse o con tu dedo en la pantalla:</span>
                        <button type="button"
                                onclick="limpiarCanvasFirmaPsicologa()"
                                class="text-brand-blue hover:text-brand-deep font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Limpiar</span>
                        </button>
                    </div>

                    <div class="relative rounded-2xl border-2 border-dashed border-slate-300 bg-white overflow-hidden shadow-inner h-44">
                        <canvas id="canvas-firma-psicologa" class="w-full h-full block touch-none cursor-crosshair"></canvas>
                        <div id="canvas-firma-placeholder" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-pencil text-slate-300 text-xl mb-1"></i>
                            <span>Firma aquí tu rúbrica</span>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: SUBIR ARCHIVO -->
                <div id="panel-firma-archivo" class="hidden space-y-2">
                    <p class="text-[11px] text-slate-500">
                        Carga una imagen con fondo blanco o transparente de tu firma (formato PNG o JPG):
                    </p>

                    <label for="input-archivo-firma-psicologa"
                           class="relative flex flex-col items-center justify-center p-6 rounded-2xl border-2 border-dashed border-slate-300 hover:border-brand-blue bg-slate-50/60 hover:bg-slate-50 transition-all cursor-pointer min-h-[160px]">
                        <input type="file"
                               name="firma_archivo"
                               id="input-archivo-firma-psicologa"
                               accept="image/png,image/jpeg,image/jpg,image/webp"
                               class="hidden"
                               onchange="previsualizarArchivoFirmaPsicologa(this)">

                        <!-- Estado Vacío -->
                        <div id="box-archivo-empty" class="text-center space-y-2">
                            <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 text-brand-blue flex items-center justify-center mx-auto shadow-2xs">
                                <i class="fa-solid fa-arrow-up-from-bracket text-lg"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Haz clic para buscar tu imagen</span>
                                <span class="text-[10px] text-slate-400">Formatos recomendados: PNG o JPG con fondo transparente</span>
                            </div>
                        </div>

                        <!-- Estado con Previsualización -->
                        <div id="box-archivo-preview" class="hidden flex-col items-center space-y-2">
                            <img id="img-preview-archivo-psicologa" src="" alt="Vista previa de firma" class="max-h-24 max-w-full object-contain rounded-lg border border-slate-200 p-1 bg-white shadow-2xs">
                            <span class="text-[11px] font-bold text-emerald-700 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i>
                                Imagen seleccionada correctamente
                            </span>
                        </div>
                    </label>
                </div>

                <!-- ALERTA DE ERROR EN VIVO -->
                <div id="alerta-error-firma-psicologa" class="hidden mt-3 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs font-bold text-rose-700 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                    <span id="texto-error-firma-psicologa">Por favor ingresa tu firma antes de guardar.</span>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 mt-4">
                    <button type="button"
                            onclick="cerrarModalFirmaPsicologa()"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Firma Oficial</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ========================================================
     SCRIPTS DE CONTROL DEL MODAL Y TRAZO CANVAS
     ======================================================== -->
<script>
    let canvasPsicologa = null;
    let ctxPsicologa = null;
    let dibujandoPsicologa = false;
    let tieneTrazoPsicologa = false;
    let modoFirmaActual = 'dibujar';

    function abrirModalFirmaPsicologa() {
        const m = document.getElementById('modal-firma-psicologa');
        const b = document.getElementById('modal-firma-psicologa-backdrop');
        const p = document.getElementById('modal-firma-psicologa-panel');
        if (!m) return;

        m.classList.remove('hidden');
        m.classList.add('flex');
        setTimeout(() => {
            if (b) b.classList.remove('opacity-0');
            if (p) p.classList.remove('scale-95', 'opacity-0');
            iniciarCanvasFirmaPsicologa();
        }, 30);
    }

    function cerrarModalFirmaPsicologa() {
        const m = document.getElementById('modal-firma-psicologa');
        const b = document.getElementById('modal-firma-psicologa-backdrop');
        const p = document.getElementById('modal-firma-psicologa-panel');
        if (!m) return;

        if (b) b.classList.add('opacity-0');
        if (p) p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }, 220);
    }

    function cambiarTabFirmaPsicologa(modo) {
        modoFirmaActual = modo;
        document.getElementById('input-psicologa-modo-firma').value = modo;

        const btnDibujar = document.getElementById('btn-tab-firma-dibujar');
        const btnArchivo = document.getElementById('btn-tab-firma-archivo');
        const panelDibujar = document.getElementById('panel-firma-dibujar');
        const panelArchivo = document.getElementById('panel-firma-archivo');

        if (modo === 'dibujar') {
            btnDibujar.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.remove('text-slate-600');
            btnArchivo.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.add('text-slate-600');

            panelDibujar.classList.remove('hidden');
            panelArchivo.classList.add('hidden');
            setTimeout(iniciarCanvasFirmaPsicologa, 40);
        } else {
            btnArchivo.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.remove('text-slate-600');
            btnDibujar.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.add('text-slate-600');

            panelArchivo.classList.remove('hidden');
            panelDibujar.classList.add('hidden');
        }
    }

    function iniciarCanvasFirmaPsicologa() {
        canvasPsicologa = document.getElementById('canvas-firma-psicologa');
        if (!canvasPsicologa) return;

        const rect = canvasPsicologa.getBoundingClientRect();
        if (rect.width <= 0) return;

        canvasPsicologa.width = rect.width * 2;
        canvasPsicologa.height = rect.height * 2;

        ctxPsicologa = canvasPsicologa.getContext('2d');
        ctxPsicologa.scale(2, 2);
        ctxPsicologa.strokeStyle = '#000000';
        ctxPsicologa.lineWidth = 3.6;
        ctxPsicologa.lineCap = 'round';
        ctxPsicologa.lineJoin = 'round';

        const placeholder = document.getElementById('canvas-firma-placeholder');

        function obtenerCoordenadas(e) {
            const r = canvasPsicologa.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: clientX - r.left,
                y: clientY - r.top
            };
        }

        function inicioTrazo(e) {
            dibujandoPsicologa = true;
            tieneTrazoPsicologa = true;
            if (placeholder) placeholder.classList.add('hidden');
            const pos = obtenerCoordenadas(e);
            ctxPsicologa.beginPath();
            ctxPsicologa.moveTo(pos.x, pos.y);
            if (e.cancelable) e.preventDefault();
        }

        function moverTrazo(e) {
            if (!dibujandoPsicologa) return;
            const pos = obtenerCoordenadas(e);
            ctxPsicologa.lineTo(pos.x, pos.y);
            ctxPsicologa.stroke();
            if (e.cancelable) e.preventDefault();
        }

        function finTrazo() {
            if (dibujandoPsicologa) {
                dibujandoPsicologa = false;
                document.getElementById('input-psicologa-firma-canvas').value = canvasPsicologa.toDataURL('image/png');
            }
        }

        canvasPsicologa.onmousedown = inicioTrazo;
        canvasPsicologa.onmousemove = moverTrazo;
        window.addEventListener('mouseup', finTrazo);

        canvasPsicologa.ontouchstart = inicioTrazo;
        canvasPsicologa.ontouchmove = moverTrazo;
        window.addEventListener('touchend', finTrazo);
    }

    function limpiarCanvasFirmaPsicologa() {
        if (!canvasPsicologa || !ctxPsicologa) return;
        ctxPsicologa.clearRect(0, 0, canvasPsicologa.width, canvasPsicologa.height);
        tieneTrazoPsicologa = false;
        document.getElementById('input-psicologa-firma-canvas').value = '';
        const placeholder = document.getElementById('canvas-firma-placeholder');
        if (placeholder) placeholder.classList.remove('hidden');
    }

    function previsualizarArchivoFirmaPsicologa(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-preview-archivo-psicologa').src = e.target.result;
                document.getElementById('box-archivo-empty').classList.add('hidden');
                document.getElementById('box-archivo-preview').classList.remove('hidden');
                document.getElementById('box-archivo-preview').classList.add('flex');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validarEnvioFirmaPsicologa() {
        const errorDiv = document.getElementById('alerta-error-firma-psicologa');
        const errorText = document.getElementById('texto-error-firma-psicologa');
        errorDiv.classList.add('hidden');

        if (modoFirmaActual === 'dibujar') {
            document.getElementById('input-psicologa-firma-canvas').value = canvasPsicologa.toDataURL('image/png');
            const dataCanvas = document.getElementById('input-psicologa-firma-canvas').value;
            if (!tieneTrazoPsicologa || !dataCanvas) {
                errorText.textContent = 'Por favor dibuja tu firma en el recuadro antes de guardar.';
                errorDiv.classList.remove('hidden');
                return false;
            }
        } else {
            const fileInput = document.getElementById('input-archivo-firma-psicologa');
            if (!fileInput.files || fileInput.files.length === 0) {
                errorText.textContent = 'Por favor selecciona un archivo de imagen con tu firma.';
                errorDiv.classList.remove('hidden');
                return false;
            }
        }

        return true;
    }

    // Auto-apertura si debe registrar firma por primera vez
    document.addEventListener('DOMContentLoaded', function() {
        @if($debeRegistrarFirma)
            setTimeout(() => {
                abrirModalFirmaPsicologa();
            }, 300);
        @endif
    });
</script>

