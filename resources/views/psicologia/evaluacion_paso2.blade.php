<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paso 2: Prueba 1 (Cuestionario PAC-A) · C.I. Piscícola New York</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink bg-[#f8fafc] antialiased flex flex-col justify-between selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- CABECERA INSTITUCIONAL CORPORATIVA -->
    <header class="w-full bg-white border-b border-line/80 shadow-2xs sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 p-1 bg-white rounded-xl shadow-xs border border-line/60 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo C.I. Piscícola New York" class="h-full w-full object-contain">
                </div>
                <div>
                    <span class="block font-heading font-extrabold text-xs sm:text-sm tracking-wider uppercase text-brand-dark leading-tight">
                        C.I. Piscícola New York S.A.
                    </span>
                    <span class="block text-[10px] sm:text-[11px] font-semibold text-brand-blue tracking-wide uppercase">
                        Gestión Humana & Psicología Organizacional
                    </span>
                </div>
            </div>

            <!-- Código de sesión activa -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-light text-brand-blue font-mono font-bold text-xs border border-brand-blue/20">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sesión: {{ $codigo }}</span>
            </span>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="flex-1 w-full max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-8 pb-32 sm:pb-28 space-y-5">

        <!-- STEPPER DE 3 PASOS: INDICADOR DE PROGRESO -->
        <div class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-line/80 shadow-2xs">
            <div class="flex items-center justify-between text-center relative max-w-md mx-auto">
                <!-- Línea conectora de fondo -->
                <div class="absolute top-4 left-8 right-8 h-0.5 bg-slate-200 -z-0"></div>

                <!-- Paso 1: Autorización (Completado) -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 mt-1.5">1. Autorizado</span>
                </div>

                <!-- Paso 2: 1ª Prueba PAC-A (Activo) -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-brand-blue text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-brand-light">
                        2
                    </div>
                    <span class="text-[11px] font-bold text-brand-blue mt-1.5">2. Prueba 1 (PAC-A)</span>
                </div>

                <!-- Paso 3: 2ª Prueba (Pendiente) -->
                <div class="relative z-10 flex flex-col items-center opacity-60">
                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 border border-slate-300 flex items-center justify-center text-xs font-bold">
                        3
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 mt-1.5">3. Prueba 2</span>
                </div>
            </div>
        </div>

        <!-- Contenedor flotante para notificaciones auto-ocultables -->
        <div id="toastNotificacion" class="fixed top-16 left-1/2 -translate-x-1/2 z-50 hidden max-w-md w-[92%] rounded-2xl p-4 shadow-xl border text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300">
            <div class="flex items-center gap-2.5">
                <i id="toastIcono" class="fa-solid fa-circle-info text-base shrink-0"></i>
                <span id="toastMensaje"></span>
            </div>
            <button type="button" onclick="ocultarToast()" class="opacity-70 hover:opacity-100 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        @if(session('error_validacion'))
            <div id="alertaServidorPaca" class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-xs font-semibold text-rose-800 flex items-center justify-between gap-2.5 shadow-2xs transition-all duration-500">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm mt-0.5"></i>
                    <div>{{ session('error_validacion') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @php
            $totalRespondidasInicial = count($respuestasGuardadas ?? []);
            $porcentajeInicial = round(($totalRespondidasInicial / 40) * 100);
        @endphp

        <!-- Tarjeta de Instrucciones Oficiales -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Instrumento Psicotécnico · Primera Prueba</span>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 font-heading">
                        Cuestionario PAC-A
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Aspirante: <strong class="text-slate-800">{{ $candidato->nombre_completo }}</strong>
                        <span class="block sm:inline">(C.C. {{ $candidato->identificacion }})</span>
                    </p>
                </div>

                <!-- Progreso en Vivo -->
                <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-2.5 sm:p-3 text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Avance Total</span>
                    <span class="text-sm sm:text-lg font-black text-brand-blue font-mono" id="txtProgresoHeader">
                        {{ $porcentajeInicial }}% ({{ $totalRespondidasInicial }}/40)
                    </span>
                </div>
            </div>

            <!-- Barra de Progreso Visual Superior -->
            <div class="w-full bg-slate-100 h-2 sm:h-2.5 rounded-full overflow-hidden mb-3">
                <div id="barraProgreso"
                     class="bg-brand-blue h-full rounded-full transition-all duration-300"
                     style="width: {{ $porcentajeInicial }}%;"></div>
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
        <form action="{{ route('psicologia.evaluacion.paca.finalizar', ['codigo' => $codigo, 'evaluacion' => $candidato->id]) }}"
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
                            <span id="txtRespondidasNum">{{ $totalRespondidasInicial }}</span>/40
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-900 block truncate" id="txtProgresoResumen">
                                {{ $porcentajeInicial }}% respondido
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

    </main>

    <script>
        const URL_BORRADOR = "{{ route('psicologia.evaluacion.paca.borrador', ['codigo' => $codigo, 'evaluacion' => $candidato->id]) }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let debounceTimer = null;

        function alCambiarOpcion(numeroPregunta) {
            const box = document.getElementById(`pregunta-box-${numeroPregunta}`);
            if (box) {
                box.classList.remove('border-rose-400', 'ring-2', 'ring-rose-200');
            }

            actualizarProgresoEnVivo();

            const txtEstado = document.getElementById('txtEstadoGuardado');
            if (txtEstado) {
                txtEstado.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-500"></i> Guardando...';
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                guardarBorradorAjax();
            }, 450);
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

        let toastTimer = null;

        function mostrarToast(mensaje, tipo = 'exito') {
            const toast = document.getElementById('toastNotificacion');
            const msg = document.getElementById('toastMensaje');
            const icono = document.getElementById('toastIcono');
            if (!toast || !msg || !icono) return;

            msg.textContent = mensaje;
            if (tipo === 'error') {
                toast.className = 'fixed top-16 left-1/2 -translate-x-1/2 z-50 max-w-md w-[92%] rounded-2xl p-4 shadow-xl border border-rose-300 bg-rose-50 text-rose-800 text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300';
                icono.className = 'fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0';
            } else {
                toast.className = 'fixed top-16 left-1/2 -translate-x-1/2 z-50 max-w-md w-[92%] rounded-2xl p-4 shadow-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300';
                icono.className = 'fa-solid fa-circle-check text-emerald-600 text-base shrink-0';
            }

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                ocultarToast();
            }, 4000);
        }

        function ocultarToast() {
            const toast = document.getElementById('toastNotificacion');
            if (toast) toast.classList.add('hidden');
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
            mostrarToast('¡Excelente! Has respondido las 40 preguntas. Puedes pulsar en "¡Enviar Todo!"', 'exito');
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
                        txtEstado.innerHTML = `<i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i> <span class="hidden sm:inline">Avance auto-guardado (${data.respondidas}/40)</span><span class="sm:hidden">Guardado (${data.respondidas}/40)</span>`;
                    }
                } else {
                    if (txtEstado) {
                        txtEstado.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Error al sincronizar';
                    }
                }
            } catch (e) {
                if (txtEstado) {
                    txtEstado.innerHTML = '<i class="fa-solid fa-wifi text-amber-500"></i> Sin conexión directa';
                }
            }
        }

        document.getElementById('formPaca').addEventListener('submit', (e) => {
            const respondidas = document.querySelectorAll('#formPaca input[type="radio"]:checked').length;
            if (respondidas < 40) {
                e.preventDefault();
                mostrarToast(`Aún te faltan ${40 - respondidas} preguntas por responder para finalizar la Prueba 1.`, 'error');
                saltarSiguienteIncompleta();
                return false;
            }
        });

        // Auto-ocultar alerta del servidor si existe
        const alertaServ = document.getElementById('alertaServidorPaca');
        if (alertaServ) {
            setTimeout(() => {
                alertaServ.style.opacity = '0';
                setTimeout(() => alertaServ.remove(), 400);
            }, 4500);
        }

        actualizarProgresoEnVivo();
    </script>
</body>
</html>
