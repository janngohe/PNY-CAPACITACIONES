@extends('layouts.evaluacion')

@section('title', 'Cuestionario PAC-A')
@section('paso_activo', '3')

@section('content')
<div class="space-y-4 pb-28 sm:pb-24">

    <!-- Tarjeta de Instrucciones Oficiales -->
    <div class="bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Instrumento Psicotécnico</span>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900 font-serif">
                    Cuestionario PAC-A
                </h2>
                <p class="text-xs text-slate-500">
                    Aspirante: <strong class="text-slate-800">{{ $candidato->nombre_completo }}</strong>
                    <span class="block sm:inline">(C.C. {{ $candidato->numero_identificacion }})</span>
                </p>
            </div>

            <!-- Progreso en Vivo -->
            <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-2.5 sm:p-3 text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Avance Total</span>
                <span class="text-sm sm:text-lg font-black text-brand-blue font-mono" id="txtProgresoHeader">
                    {{ round($aplicacion->progreso_porcentaje) }}% ({{ count($respuestasGuardadas) }}/40)
                </span>
            </div>
        </div>

        <!-- Barra de Progreso Visual Superior -->
        <div class="w-full bg-slate-100 h-2 sm:h-2.5 rounded-full overflow-hidden mb-3">
            <div id="barraProgreso"
                 class="bg-brand-blue h-full rounded-full transition-all duration-300"
                 style="width: {{ $aplicacion->progreso_porcentaje }}%;"></div>
        </div>

        <!-- Instrucciones Claras y Resumen de Opciones -->
        <div class="p-3 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed space-y-2">
            <p class="font-bold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-brand-blue"></i> Instrucciones para responder:
            </p>
            <p class="text-[11px] sm:text-xs">
                A continuación encontrará <strong>40 afirmaciones</strong> sobre situaciones y actitudes cotidianas. Seleccione en cada una la opción que mejor describa su forma habitual de actuar o pensar:
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 sm:gap-2 pt-1 font-bold text-[11px]">
                <div class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-md bg-blue-100 text-brand-blue flex items-center justify-center font-black text-xs">S</span>
                    <span>Siempre</span>
                </div>
                <div class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-md bg-cyan-100 text-cyan-800 flex items-center justify-center font-black text-xs">F</span>
                    <span>Frecuentemente</span>
                </div>
                <div class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-800 flex items-center justify-center font-black text-xs">R</span>
                    <span>Rara vez</span>
                </div>
                <div class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-md bg-slate-200 text-slate-700 flex items-center justify-center font-black text-xs">N</span>
                    <span>Nunca</span>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 italic pt-0.5">
                No hay respuestas correctas ni incorrectas. Sea lo más sincero posible.
            </p>
        </div>
    </div>

    <!-- Formulario de las 40 Afirmaciones -->
    <form action="{{ route('evaluacion.paca.finalizar', ['token' => $candidato->token_acceso, 'appToken' => $aplicacion->token_sesion]) }}"
          method="POST"
          id="formPaca"
          class="space-y-3 sm:space-y-4">
        @csrf

        @php
            $faltantes = session('preguntas_faltantes', []);
        @endphp

        @foreach($afirmaciones as $numero => $texto)
            @php
                $esFaltante = in_array($numero, $faltantes, true);
                $seleccionada = old("respuestas.{$numero}", $respuestasGuardadas[$numero] ?? null);
            @endphp

            <div id="pregunta-box-{{ $numero }}"
                 class="bg-white border {{ $esFaltante ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200' }} rounded-3xl p-3.5 sm:p-5 shadow-xs transition-all hover:border-slate-300">
                
                <div class="flex items-start gap-2.5 sm:gap-3 mb-2.5 sm:mb-3">
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-black shrink-0 border border-slate-200">
                        {{ $numero }}
                    </span>
                    <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-snug pt-0.5 sm:pt-1">
                        {{ $texto }}
                    </p>
                </div>

                <!-- 4 Alternativas de Respuesta (2x2 en móvil, 4 columnas en desktop) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
                    @foreach($opciones as $clave => $op)
                        @php
                            $checked = ($seleccionada === $clave);
                        @endphp
                        <label class="cursor-pointer select-none block">
                            <input type="radio"
                                   name="respuestas[{{ $numero }}]"
                                   value="{{ $clave }}"
                                   data-pregunta="{{ $numero }}"
                                   {{ $checked ? 'checked' : '' }}
                                   class="peer sr-only radio-paca"
                                   onchange="alCambiarOpcion({{ $numero }})">
                            <div class="flex items-center gap-2 p-2.5 sm:p-3 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 transition-all duration-150 active:scale-95 hover:bg-slate-100 hover:border-slate-300
                                        peer-checked:bg-brand-blue peer-checked:text-white peer-checked:border-brand-blue peer-checked:shadow-sm">
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black shrink-0 transition-colors
                                             bg-white text-slate-800 border border-slate-200
                                             peer-checked:bg-white/20 peer-checked:text-white peer-checked:border-white/30">
                                    {{ $clave }}
                                </span>
                                <span class="text-xs font-bold leading-tight truncate">
                                    {{ $op['nombre'] }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Barra Inferior Fija de Control y Avance en Vivo (Ultra responsiva en móvil) -->
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg px-3 py-2.5 sm:py-3.5">
            <div class="max-w-3xl mx-auto flex items-center justify-between gap-2 sm:gap-4">
                
                <!-- Indicador de Avance y Estado -->
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-brand-blue/10 text-brand-blue flex items-center justify-center text-xs font-black shrink-0 border border-brand-blue/20">
                        <span id="txtRespondidasNum">{{ count($respuestasGuardadas) }}</span>/40
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-900 block truncate" id="txtProgresoResumen">
                            {{ round($aplicacion->progreso_porcentaje) }}% respondido
                        </span>
                        <span class="text-[10px] text-slate-400 flex items-center gap-1 truncate" id="txtEstadoGuardado">
                            <i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i>
                            <span class="hidden sm:inline">Avance auto-guardado en el servidor</span>
                            <span class="sm:hidden">Auto-guardado activo</span>
                        </span>
                    </div>
                </div>

                <!-- Botones de Acción Móviles -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <button type="button"
                            id="btnSaltarSiguiente"
                            onclick="saltarSiguienteIncompleta()"
                            title="Saltar a la siguiente pregunta no respondida"
                            class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold text-xs flex items-center gap-1.5 transition-all cursor-pointer">
                        <i class="fa-solid fa-angles-down text-brand-blue text-xs"></i>
                        <span class="hidden md:inline">Siguiente pendiente</span>
                        <span class="md:hidden">Pendiente</span>
                    </button>

                    <button type="submit"
                            id="btnFinalizar"
                            class="py-2.5 px-3.5 sm:px-5 rounded-2xl bg-brand-blue hover:bg-brand-dark active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-1.5 cursor-pointer">
                        <span>Finalizar</span>
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    const URL_BORRADOR = "{{ route('evaluacion.paca.borrador', ['token' => $candidato->token_acceso, 'appToken' => $aplicacion->token_sesion]) }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let debounceTimer = null;

    function alCambiarOpcion(numeroPregunta) {
        // Remover alerta roja de pregunta faltante si existía
        const box = document.getElementById(`pregunta-box-${numeroPregunta}`);
        if (box) {
            box.classList.remove('border-rose-400', 'ring-2', 'ring-rose-200');
        }

        actualizarProgresoEnVivo();

        // Auto-guardado con debounce
        const txtEstado = document.getElementById('txtEstadoGuardado');
        if (txtEstado) {
            txtEstado.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-500"></i> Guardando...';
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            guardarBorradorAjax();
        }, 500);
    }

    function actualizarProgresoEnVivo() {
        const respondidas = document.querySelectorAll('#formPaca input[type="radio"]:checked').length;
        const porcentaje = Math.round((respondidas / 40) * 100);

        const txtHeader = document.getElementById('txtProgresoHeader');
        const txtNum = document.getElementById('txtRespondidasNum');
        const txtResumen = document.getElementById('txtProgresoResumen');
        const barra = document.getElementById('barraProgreso');
        const btnFinalizar = document.getElementById('btnFinalizar');

        if (txtHeader) txtHeader.textContent = `${porcentaje}% (${respondidas}/40)`;
        if (txtNum) txtNum.textContent = respondidas;
        if (txtResumen) txtResumen.textContent = `${porcentaje}% respondido`;
        if (barra) barra.style.width = `${porcentaje}%`;

        if (btnFinalizar) {
            if (respondidas === 40) {
                btnFinalizar.classList.remove('bg-brand-blue');
                btnFinalizar.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                btnFinalizar.innerHTML = '<span>¡Enviar Todo!</span> <i class="fa-solid fa-check text-xs"></i>';
            } else {
                btnFinalizar.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                btnFinalizar.classList.add('bg-brand-blue');
                btnFinalizar.innerHTML = '<span>Finalizar</span> <i class="fa-solid fa-paper-plane text-xs"></i>';
            }
        }
    }

    function saltarSiguienteIncompleta() {
        for (let i = 1; i <= 40; i++) {
            const checked = document.querySelector(`input[name="respuestas[${i}]"]:checked`);
            if (!checked) {
                const box = document.getElementById(`pregunta-box-${i}`);
                if (box) {
                    box.classList.add('border-amber-400', 'ring-2', 'ring-amber-200');
                    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => {
                        box.classList.remove('border-amber-400', 'ring-2', 'ring-amber-200');
                    }, 2000);
                }
                return;
            }
        }
        alert('¡Excelente! Has respondido las 40 preguntas. Puedes pulsar en "¡Enviar Todo!"');
    }

    function recolectarRespuestas() {
        const formData = new FormData(document.getElementById('formPaca'));
        const respuestas = {};
        for (let [key, val] of formData.entries()) {
            if (key.startsWith('respuestas[')) {
                const match = key.match(/respuestas\[(\d+)\]/);
                if (match && match[1]) {
                    respuestas[match[1]] = val;
                }
            }
        }
        return respuestas;
    }

    async function guardarBorradorAjax() {
        const txtEstado = document.getElementById('txtEstadoGuardado');
        const respuestas = recolectarRespuestas();

        try {
            const resp = await fetch(URL_BORRADOR, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ respuestas: respuestas })
            });

            if (resp.ok) {
                const data = await resp.json();
                if (txtEstado) {
                    txtEstado.innerHTML = `<i class="fa-solid fa-cloud-check text-emerald-500"></i> <span class="hidden sm:inline">Avance auto-guardado (${data.respondidas}/40)</span><span class="sm:hidden">Guardado (${data.respondidas}/40)</span>`;
                }
            } else {
                if (txtEstado) {
                    txtEstado.innerHTML = '<i class="fa-solid fa-cloud-slash text-rose-500"></i> Error al sincronizar';
                }
            }
        } catch (e) {
            if (txtEstado) {
                txtEstado.innerHTML = '<i class="fa-solid fa-wifi-slash text-amber-500"></i> Sin conexión directa';
            }
        }
    }

    document.getElementById('formPaca').addEventListener('submit', (e) => {
        const respondidas = document.querySelectorAll('#formPaca input[type="radio"]:checked').length;
        if (respondidas < 40) {
            e.preventDefault();
            alert(`Aún te faltan ${40 - respondidas} preguntas por responder. Por favor responde las 40 afirmaciones completas para enviar.`);
            saltarSiguienteIncompleta();
            return false;
        }

        if (!confirm('¿Estás seguro de que deseas enviar tus 40 respuestas definitivamente? Una vez enviadas no podrás modificarlas.')) {
            e.preventDefault();
            return false;
        }
    });

    // Inicializar al cargar
    actualizarProgresoEnVivo();
</script>
@endpush
