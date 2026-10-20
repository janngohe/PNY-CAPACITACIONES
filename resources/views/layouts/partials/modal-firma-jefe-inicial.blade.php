@php
    /** @var \App\Models\Usuario $usuario */
    $usuario = $usuario ?? auth()->user();
    // OCULTO TEMPORALMENTE: Desactivado para implementación futura en administrador y jefe de área
    $debeRegistrarFirma = false;
@endphp

@if(false)
<!-- ========================================================
     MODAL DE REGISTRO / CONFIGURACIÓN DE FIRMA DIGITAL DEL JEFE
     (CONSERVADO: Oculto para implementación futura)
     ======================================================== -->
<div id="modal-configurar-firma-jefe" 
     class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-5"
     role="dialog" aria-modal="true">
    
    <!-- Backdrop con desenfoque elegante -->
    <div id="modal-configurar-firma-backdrop" 
         class="fixed inset-0 bg-brand-dark/80 backdrop-blur-md transition-opacity {{ $debeRegistrarFirma ? 'opacity-100' : 'opacity-0' }}"
         @if(!$debeRegistrarFirma) onclick="cerrarModalConfigurarFirmaInicial()" @endif></div>

    <div id="modal-configurar-firma-panel" 
         class="relative bg-white rounded-3xl max-w-xl w-full shadow-2xl border-2 border-brand-sky/40 overflow-hidden transform transition-all {{ $debeRegistrarFirma ? 'scale-100 opacity-100' : 'scale-95 opacity-0' }} z-10 font-sans">
        
        <!-- Olas decorativas en la parte superior -->
        <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-5 sm:p-6 overflow-hidden">
            <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-cyan-400/20 blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-2xl bg-white/15 text-cyan-300 flex items-center justify-center text-lg font-bold shadow-xs shrink-0 border border-white/20">
                        <i class="fa-solid fa-signature"></i>
                    </span>
                    <div>
                        <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-cyan-200 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/15 mb-1">
                            {{ $debeRegistrarFirma ? 'Configuración Inicial Obligatoria' : 'Firma Digital Oficial' }}
                        </span>
                        <h3 class="font-heading font-black text-base sm:text-lg text-white leading-tight">
                            Registra tu Firma Digital Oficial
                        </h3>
                    </div>
                </div>

                @if(!$debeRegistrarFirma)
                    <button type="button" onclick="cerrarModalConfigurarFirmaInicial()"
                            class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                @endif
            </div>

            <p class="text-xs text-slate-200/90 mt-2 relative z-10 leading-relaxed font-sans">
                Hola <strong class="text-white">{{ $usuario->nombre_completo }}</strong>. Tu firma quedará guardada de forma segura y se <strong class="text-cyan-300">auto-cargará automáticamente</strong> en todas las plantillas y certificados de capacitación emitidos para tu área.
            </p>

            <!-- SVG Wave Motif Decorativo -->
            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-20">
                <svg class="relative block w-full h-4 text-white" viewBox="0 0 400 20" preserveAspectRatio="none">
                    <path d="M0,5 C70,18 140,4 210,14 C280,22 350,6 400,15 L400,20 L0,20 Z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <!-- FORMULARIO DE FIRMA -->
        <form id="form-configurar-firma-jefe" method="POST" action="{{ route('jefe.firma.perfil') }}" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4">
            @csrf
            <input type="hidden" name="modo_firma" id="input-jefe-modo-firma" value="archivo">
            <input type="hidden" name="firma_canvas" id="input-jefe-firma-canvas" value="">

            <!-- 1. CARGO OFICIAL -->
            <div class="space-y-1">
                <label for="jefe-firma-cargo" class="block font-heading font-bold text-xs text-brand-dark">
                    Tu Cargo Oficial en el Certificado <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="cargo" id="jefe-firma-cargo" required
                       value="{{ $usuario->cargoOficial() }}"
                       placeholder="Ej: Jefe de Área Producción Piscícola"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-slate-50 text-xs text-brand-dark font-semibold focus:outline-none focus:border-brand-blue focus:bg-white">
                <p class="text-[10px] text-muted">Este cargo figurará exactamente debajo de tu firma en los certificados oficiales.</p>
            </div>

            <!-- 2. PESTAÑAS: SUBIR ARCHIVO vs DIBUJAR EN PANTALLA -->
            <div class="space-y-2 pt-1">
                <label class="block font-heading font-bold text-xs text-brand-dark">
                    Rúbrica Oficial <span class="text-rose-500">*</span>
                </label>
                
                <div class="grid grid-cols-2 gap-2 bg-slate-100 p-1 rounded-xl text-xs font-heading font-bold">
                    <button type="button" id="btn-tab-firma-archivo" onclick="cambiarTabFirmaJefe('archivo')"
                            class="py-2 px-3 rounded-lg bg-white text-brand-blue shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                        <span>Subir Imagen</span>
                    </button>
                    <button type="button" id="btn-tab-firma-dibujar" onclick="cambiarTabFirmaJefe('dibujar')"
                            class="py-2 px-3 rounded-lg text-slate-600 hover:text-brand-blue transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-pen-nib text-xs"></i>
                        <span>Dibujar en Pantalla</span>
                    </button>
                </div>
            </div>

            <!-- PANEL 1: SUBIR ARCHIVO -->
            <div id="panel-tab-firma-archivo" class="space-y-3">
                <div class="border-2 border-dashed border-line rounded-2xl p-4 sm:p-5 text-center bg-slate-50/60 hover:bg-slate-50 transition-colors">
                    <input type="file" name="firma_archivo" id="input-archivo-firma-jefe" accept="image/png,image/jpeg,image/webp" class="hidden">
                    
                    <div id="box-upload-firma-empty" onclick="document.getElementById('input-archivo-firma-jefe').click()" class="cursor-pointer space-y-2">
                        <span class="w-12 h-12 rounded-2xl bg-brand-light text-brand-blue inline-flex items-center justify-center text-xl shadow-2xs">
                            <i class="fa-solid fa-file-image"></i>
                        </span>
                        <div>
                            <p class="font-heading font-bold text-xs text-brand-dark">Haz clic para seleccionar tu firma</p>
                            <p class="text-[11px] text-muted">Formato PNG con fondo transparente recomendado (o JPG/WebP, máx 3MB)</p>
                        </div>
                    </div>

                    <!-- Previsualización del archivo cargado -->
                    <div id="box-upload-firma-preview" class="hidden space-y-2">
                        <div class="h-20 bg-white rounded-xl border border-line p-2 flex items-center justify-center mx-auto max-w-[280px] shadow-2xs">
                            <img id="img-preview-firma-cargada" src="" alt="Firma cargada" class="max-h-16 max-w-full object-contain">
                        </div>
                        <button type="button" onclick="document.getElementById('input-archivo-firma-jefe').click()"
                                class="text-[11px] font-bold text-brand-blue hover:underline cursor-pointer">
                            Cambiar imagen
                        </button>
                    </div>
                </div>

                @if($usuario->tieneFirmaRegistrada())
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-line flex items-center justify-between text-xs">
                        <span class="text-slate-600 font-medium text-[11px]">Firma actual en tu cuenta:</span>
                        <div class="h-8 w-24 bg-white rounded-lg border border-line p-1 flex items-center justify-center">
                            <img src="{{ $usuario->firmaUrl() }}" alt="Firma actual" class="max-h-6 max-w-full object-contain">
                        </div>
                    </div>
                @endif
            </div>

            <!-- PANEL 2: DIBUJAR EN PANTALLA -->
            <div id="panel-tab-firma-dibujar" class="hidden space-y-2">
                <div class="relative bg-white rounded-2xl border-2 border-line overflow-hidden shadow-2xs">
                    <canvas id="canvas-firma-jefe-perfil" width="500" height="160"
                            class="w-full h-40 bg-white touch-none cursor-crosshair block"></canvas>
                    <div class="absolute bottom-2 left-3 pointer-events-none text-[10px] text-slate-400 font-mono">
                        Firma aquí con el ratón o pantalla táctil
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-[10px] text-muted">Traza tu rúbrica lo más clara posible.</span>
                    <button type="button" onclick="limpiarCanvasFirmaJefe()"
                            class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition-colors flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        <span>Limpiar Trazo</span>
                    </button>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="pt-2 flex items-center justify-end gap-3 border-t border-line/70">
                @if(!$debeRegistrarFirma)
                    <button type="button" onclick="cerrarModalConfigurarFirmaInicial()"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                        Cancelar
                    </button>
                @endif

                <button type="submit" id="btn-guardar-firma-jefe"
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Guardar Firma y Activar para Mi Área</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    let canvasJefe, ctxJefe, dibujandoJefe = false;
    let modoFirmaJefeActual = 'archivo';

    function cambiarTabFirmaJefe(modo) {
        modoFirmaJefeActual = modo;
        document.getElementById('input-jefe-modo-firma').value = modo;

        const btnArchivo = document.getElementById('btn-tab-firma-archivo');
        const btnDibujar = document.getElementById('btn-tab-firma-dibujar');
        const panelArchivo = document.getElementById('panel-tab-firma-archivo');
        const panelDibujar = document.getElementById('panel-tab-firma-dibujar');

        if (modo === 'archivo') {
            btnArchivo.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.remove('text-slate-600');
            btnDibujar.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.add('text-slate-600');

            panelArchivo.classList.remove('hidden');
            panelDibujar.classList.add('hidden');
            document.getElementById('input-jefe-firma-canvas').value = '';
        } else {
            btnDibujar.classList.add('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnDibujar.classList.remove('text-slate-600');
            btnArchivo.classList.remove('bg-white', 'text-brand-blue', 'shadow-2xs');
            btnArchivo.classList.add('text-slate-600');

            panelDibujar.classList.remove('hidden');
            panelArchivo.classList.add('hidden');
            setTimeout(initCanvasFirmaJefe, 50);
        }
    }

    function initCanvasFirmaJefe() {
        canvasJefe = document.getElementById('canvas-firma-jefe-perfil');
        if (!canvasJefe) return;
        ctxJefe = canvasJefe.getContext('2d');
        ctxJefe.strokeStyle = '#002d5e';
        ctxJefe.lineWidth = 2.8;
        ctxJefe.lineCap = 'round';
        ctxJefe.lineJoin = 'round';

        function getPos(e) {
            const rect = canvasJefe.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (canvasJefe.width / rect.width),
                y: (clientY - rect.top) * (canvasJefe.height / rect.height)
            };
        }

        function start(e) {
            dibujandoJefe = true;
            const pos = getPos(e);
            ctxJefe.beginPath();
            ctxJefe.moveTo(pos.x, pos.y);
            e.preventDefault();
        }

        function move(e) {
            if (!dibujandoJefe) return;
            const pos = getPos(e);
            ctxJefe.lineTo(pos.x, pos.y);
            ctxJefe.stroke();
            e.preventDefault();
        }

        function stop() {
            if (dibujandoJefe) {
                dibujandoJefe = false;
                document.getElementById('input-jefe-firma-canvas').value = canvasJefe.toDataURL('image/png');
            }
        }

        canvasJefe.onmousedown = start;
        canvasJefe.onmousemove = move;
        window.addEventListener('mouseup', stop);

        canvasJefe.ontouchstart = start;
        canvasJefe.ontouchmove = move;
        window.addEventListener('touchend', stop);
    }

    function limpiarCanvasFirmaJefe() {
        if (!canvasJefe || !ctxJefe) return;
        ctxJefe.clearRect(0, 0, canvasJefe.width, canvasJefe.height);
        document.getElementById('input-jefe-firma-canvas').value = '';
    }

    function abrirModalConfigurarFirmaInicial() {
        const m = document.getElementById('modal-configurar-firma-jefe');
        const b = document.getElementById('modal-configurar-firma-backdrop');
        const p = document.getElementById('modal-configurar-firma-panel');
        if (!m) return;
        m.classList.remove('hidden');
        m.classList.add('flex');
        setTimeout(() => {
            if (b) b.classList.remove('opacity-0');
            if (p) p.classList.remove('scale-95', 'opacity-0');
        }, 10);
    }

    function cerrarModalConfigurarFirmaInicial() {
        const m = document.getElementById('modal-configurar-firma-jefe');
        const b = document.getElementById('modal-configurar-firma-backdrop');
        const p = document.getElementById('modal-configurar-firma-panel');
        if (!m) return;
        if (b) b.classList.add('opacity-0');
        if (p) p.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputArchivo = document.getElementById('input-archivo-firma-jefe');
        const boxEmpty = document.getElementById('box-upload-firma-empty');
        const boxPreview = document.getElementById('box-upload-firma-preview');
        const imgPreview = document.getElementById('img-preview-firma-cargada');

        if (inputArchivo) {
            inputArchivo.addEventListener('change', function() {
                const f = this.files[0];
                if (f && imgPreview) {
                    imgPreview.src = URL.createObjectURL(f);
                    boxEmpty.classList.add('hidden');
                    boxPreview.classList.remove('hidden');
                }
            });
        }

        const form = document.getElementById('form-configurar-firma-jefe');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (modoFirmaJefeActual === 'dibujar') {
                    const canvasVal = document.getElementById('input-jefe-firma-canvas').value;
                    if (!canvasVal) {
                        e.preventDefault();
                        alert('Por favor realiza tu trazo de firma en el recuadro digital antes de guardar.');
                        return;
                    }
                } else if (modoFirmaJefeActual === 'archivo') {
                    const fileVal = inputArchivo?.files?.length;
                    const yaTieneFirma = {{ $usuario->tieneFirmaRegistrada() ? 'true' : 'false' }};
                    if (!fileVal && !yaTieneFirma) {
                        e.preventDefault();
                        alert('Por favor selecciona una imagen de tu firma oficial o utiliza la opción "Dibujar en Pantalla".');
                        return;
                    }
                }
            });
        }
    });
</script>
@endif
