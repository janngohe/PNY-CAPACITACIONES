@extends('layouts.evaluacion')

@section('title', 'Consentimiento Informado GH-FO-025')
@section('paso_activo', '2')

@section('content')
<div class="space-y-6">

    <!-- Documento GH-FO-025 Interactivo -->
    <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-8 shadow-xs">

        <!-- Membrete Oficial -->
        <div class="border-b border-slate-200 pb-5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-center sm:text-left">
            <div class="flex items-center gap-3 justify-center sm:justify-start">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 p-2 shadow-2xs shrink-0 flex items-center justify-center">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo PNY" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Documento Institucional</span>
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900 font-serif">
                        CONSENTIMIENTO INFORMADO
                    </h2>
                </div>
            </div>

            <div class="flex items-center justify-center sm:justify-end gap-2 text-xs font-mono font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    CÓDIGO: {{ $consentimiento->codigo_documental }}
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                    VERSIÓN: {{ $consentimiento->version_documental }}
                </span>
            </div>
        </div>

        <!-- Metadatos de Expedición -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 mb-5 text-xs">
            <div>
                <span class="text-slate-400 font-medium block">Ciudad / Municipio:</span>
                <strong class="text-slate-900 font-bold">{{ $consentimiento->ciudad_municipio }}</strong>
            </div>
            <div>
                <span class="text-slate-400 font-medium block">Fecha:</span>
                <strong class="text-slate-900 font-bold">{{ $consentimiento->fecha_consentimiento->format('d/m/Y') }}</strong>
            </div>
        </div>

        <!-- Cláusula Oficial Invariable -->
        <div class="p-4 sm:p-6 rounded-2xl bg-blue-50/50 border border-blue-200/60 text-slate-800 text-xs sm:text-sm leading-relaxed space-y-3 mb-6">
            <p>
                Yo, <strong class="text-slate-950 font-extrabold underline decoration-brand-blue decoration-2 underline-offset-2">{{ $candidato->nombre_completo }}</strong>,
                identificado(a) con cédula de ciudadanía No. <strong class="text-slate-950 font-bold">{{ $candidato->numero_identificacion }}</strong>
                expedida en <strong class="text-slate-950 font-bold">{{ $candidato->lugar_expedicion }}</strong>,
                <strong class="text-brand-blue uppercase font-black">autorizo</strong> realizar las pruebas psicotécnicas como parte del proceso de selección y contratación de personal en la empresa <strong class="text-slate-950">C.I. PISCÍCOLA NEW YORK S.A.</strong>
            </p>
            <p class="text-[11px] sm:text-xs text-slate-600 italic bg-white/70 p-3 rounded-xl border border-blue-100">
                <i class="fa-solid fa-lock text-brand-blue mr-1"></i>
                "El resultado de la aplicación de los instrumentos de evaluación psicotécnicos es de total confidencialidad."
            </p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de Firma Digital en Pantalla -->
        <form action="{{ route('evaluacion.consentimiento.firmar') }}" method="POST" id="formConsentimiento" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $candidato->token_acceso }}">
            <input type="hidden" name="firma_canvas" id="inputFirmaCanvas" value="">

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Firma Digital del Aspirante <span class="text-rose-600">*</span>
                    </label>
                    <button type="button" onclick="limpiarCanvas()"
                            class="text-[11px] font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-rotate-left"></i> Limpiar Firma
                    </button>
                </div>

                <!-- Recuadro Canvas de Firma -->
                <div class="relative bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl p-1 overflow-hidden transition-all focus-within:border-brand-blue">
                    <canvas id="canvasFirma"
                            width="600"
                            height="180"
                            class="w-full h-36 sm:h-44 bg-white rounded-xl touch-none cursor-crosshair block"></canvas>

                    <div id="firmaPlaceholder" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-slate-400 text-xs gap-1">
                        <i class="fa-solid fa-signature text-2xl text-slate-300"></i>
                        <span>Dibuja tu firma aquí con tu dedo o mouse</span>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Tu firma quedará estampada en el documento oficial GH-FO-025.</p>
            </div>

            <!-- Checkbox de Aceptación -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="acepto_terminos"
                           id="acepto_terminos"
                           value="1"
                           required
                           {{ old('acepto_terminos') ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 text-brand-blue rounded border-slate-300 focus:ring-brand-blue/30 cursor-pointer">
                    <span class="text-xs text-slate-700 leading-snug font-medium select-none">
                        He leído, comprendo y acepto en su totalidad los términos del <strong>Consentimiento Informado (GH-FO-025)</strong> para la aplicación de pruebas psicotécnicas en <strong>C.I. Piscícola New York S.A.</strong>
                    </span>
                </label>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-2 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('evaluacion.inicio', ['token' => $candidato->token_acceso]) }}"
                   class="py-3 px-5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider text-center transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Modificar Datos
                </a>

                <button type="submit"
                        id="btnEnviarFirma"
                        class="flex-1 py-3.5 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm uppercase tracking-wider transition-all shadow-md active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Firmar y Pasar a las Pruebas</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('canvasFirma');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const inputFirma = document.getElementById('inputFirmaCanvas');
        const placeholder = document.getElementById('firmaPlaceholder');
        const form = document.getElementById('formConsentimiento');

        let dibujando = false;
        let tieneFirma = false;

        ctx.strokeStyle = '#0f172a';
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function start(e) {
            e.preventDefault();
            dibujando = true;
            placeholder.style.display = 'none';
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function move(e) {
            if (!dibujando) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            tieneFirma = true;
        }

        function stop(e) {
            if (dibujando) {
                dibujando = false;
                if (tieneFirma) {
                    inputFirma.value = canvas.toDataURL('image/png');
                }
            }
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', stop);

        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', move, { passive: false });
        window.addEventListener('touchend', stop);

        window.limpiarCanvas = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            inputFirma.value = '';
            tieneFirma = false;
            placeholder.style.display = 'flex';
        };

        form.addEventListener('submit', (e) => {
            if (!tieneFirma || !inputFirma.value) {
                e.preventDefault();
                alert('Por favor dibuja tu firma en el recuadro antes de continuar.');
                return false;
            }
        });
    });
</script>
@endpush
