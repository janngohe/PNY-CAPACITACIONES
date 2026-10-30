@extends('layouts.psicologia')

@section('title', 'Aplicar Prueba PAC-A')
@section('page_title', 'Aplicación de Prueba PAC-A')
@section('page_subtitle', $aplicacion->candidato->nombre_completo)

@section('content')
<div class="space-y-6 pb-24">

    <!-- BARRA FLOTANTE SUPERIOR CON DATOS DEL EVALUADO Y PROGRESO -->
    <div class="sticky top-0 z-30 rounded-2xl border border-slate-200/90 bg-white/95 backdrop-blur-md p-4 sm:p-5 shadow-sm transition-all">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="rounded-lg bg-brand-light px-2.5 py-0.5 text-xs font-bold text-brand-blue">
                        PAC-A
                    </span>
                    <h1 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">
                        {{ $aplicacion->candidato->nombre_completo }}
                    </h1>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-slate-500 mt-1">
                    <span>CC: <strong>{{ $aplicacion->candidato->numero_identificacion }}</strong></span>
                    <span>•</span>
                    <span>Edad: <strong>{{ $aplicacion->edad_evaluado }} años</strong></span>
                    <span>•</span>
                    <span>Fecha: <strong>{{ $aplicacion->fecha_aplicacion->format('d/m/Y') }}</strong></span>
                </div>
            </div>

            <!-- Contador y Barra de Progreso -->
            <div class="sm:w-72">
                <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                    <span class="text-slate-600">Progreso de la Prueba</span>
                    <span id="contador-progreso" class="text-brand-blue">
                        {{ count($respuestasGuardadas) }} / 40 (<span id="porcentaje-progreso">{{ round((count($respuestasGuardadas) / 40) * 100) }}</span>%)
                    </span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div id="barra-progreso"
                         class="h-full rounded-full bg-gradient-to-r from-brand-blue to-emerald-500 transition-all duration-300"
                         style="width: {{ (count($respuestasGuardadas) / 40) * 100 }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- INSTRUCCIONES INSTITUCIONALES DE LA PRUEBA -->
    <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50/70 to-indigo-50/40 p-5 md:p-6 shadow-2xs">
        <div class="flex items-start gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700 text-base">
                <i class="fa-solid fa-clipboard-question"></i>
            </div>
            <div class="text-xs text-slate-700 leading-relaxed">
                <h2 class="font-extrabold text-sm text-slate-900 mb-1">Instrucciones para el Evaluado:</h2>
                <p>
                    A continuación, encontrará una serie de frases a las que usted debe responder con toda sinceridad, según la frecuencia con que se presente dicha situación en su vida. Seleccione una sola opción por afirmación:
                </p>
                <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 font-medium">
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-white/80 border border-slate-200/60">
                        <span class="h-5 w-5 rounded-md bg-blue-600 text-white font-bold text-center text-xs leading-5">S</span>
                        <span><strong>Siempre</strong> (2 pts)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-white/80 border border-slate-200/60">
                        <span class="h-5 w-5 rounded-md bg-sky-500 text-white font-bold text-center text-xs leading-5">F</span>
                        <span><strong>Frecuentemente</strong> (1 pt)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-white/80 border border-slate-200/60">
                        <span class="h-5 w-5 rounded-md bg-amber-500 text-white font-bold text-center text-xs leading-5">R</span>
                        <span><strong>Rara vez</strong> (0.5 pts)</span>
                    </div>
                    <div class="flex items-center gap-2 p-2 rounded-lg bg-white/80 border border-slate-200/60">
                        <span class="h-5 w-5 rounded-md bg-slate-400 text-white font-bold text-center text-xs leading-5">N</span>
                        <span><strong>Nunca</strong> (0 pts)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORMULARIO DE LAS 40 AFIRMACIONES -->
    <form id="form-paca" action="{{ route('psicologia.paca.finalizar', $aplicacion) }}" method="POST" class="space-y-4">
        @csrf

        @foreach($afirmaciones as $numero => $texto)
            @php
                $respuestaActual = old("respuestas.{$numero}", $respuestasGuardadas[$numero] ?? null);
            @endphp
            <div id="pregunta-card-{{ $numero }}"
                 class="pregunta-card rounded-2xl border transition-all duration-200 bg-white p-4 sm:p-5 shadow-2xs {{ $respuestaActual ? 'border-slate-200/90' : 'border-slate-200 bg-white' }}">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    
                    <!-- Número y Afirmación -->
                    <div class="flex items-start gap-3.5 flex-1">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg font-bold text-xs {{ $respuestaActual ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-600' }}" id="badge-num-{{ $numero }}">
                            {{ $numero }}
                        </span>
                        <p class="text-xs sm:text-sm font-medium text-slate-800 leading-snug pt-0.5">
                            {{ $texto }}
                        </p>
                    </div>

                    <!-- Opciones de Respuesta (S, F, R, N) -->
                    <div class="grid grid-cols-4 gap-1.5 sm:gap-2 shrink-0 self-end sm:self-center">
                        @foreach($opciones as $op)
                            @php
                                $esSeleccionada = ($respuestaActual === $op['codigo']);
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio"
                                       name="respuestas[{{ $numero }}]"
                                       value="{{ $op['codigo'] }}"
                                       data-pregunta="{{ $numero }}"
                                       class="peer sr-only radio-paca"
                                       {{ $esSeleccionada ? 'checked' : '' }}>
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-1 rounded-xl border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-600 transition-all peer-checked:border-brand-blue peer-checked:bg-brand-blue peer-checked:text-white hover:bg-slate-50 peer-checked:shadow-xs">
                                    <span class="text-xs">{{ $op['codigo'] }}</span>
                                    <span class="text-[10px] hidden md:inline font-normal opacity-90">{{ $op['nombre'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                </div>
            </div>
        @endforeach

        <!-- BARRA FIJA INFERIOR DE ACCIONES -->
        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur-md p-4 shadow-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                
                <div class="flex items-center gap-2">
                    <span id="indicador-guardado" class="text-xs text-slate-500 font-medium hidden sm:inline">
                        <i class="fa-solid fa-cloud-check text-emerald-600"></i> Borrador al día
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Botón Guardar Borrador (Sin validar completitud) -->
                    <button type="button"
                            id="btn-guardar-borrador"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-2xs transition-all">
                        <i class="fa-solid fa-floppy-disk text-slate-500"></i>
                        <span>Guardar Borrador</span>
                    </button>

                    <!-- Botón Finalizar Definitivamente -->
                    <button type="submit"
                            id="btn-finalizar-paca"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.01]">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>Finalizar y Calificar Prueba</span>
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-paca');
        const radios = document.querySelectorAll('.radio-paca');
        const contadorProgreso = document.getElementById('contador-progreso');
        const barraProgreso = document.getElementById('barra-progreso');
        const btnBorrador = document.getElementById('btn-guardar-borrador');
        const indicadorGuardado = document.getElementById('indicador-guardado');

        // Actualizar contador visual de progreso
        function actualizarProgreso() {
            const respondidas = new Set();
            radios.forEach(r => {
                if (r.checked) {
                    respondidas.add(r.dataset.pregunta);
                    // Actualizar estilo del badge numérico
                    const badge = document.getElementById(`badge-num-${r.dataset.pregunta}`);
                    if (badge) {
                        badge.classList.remove('bg-slate-100', 'text-slate-600');
                        badge.classList.add('bg-brand-blue', 'text-white');
                    }
                }
            });

            const count = respondidas.size;
            const porcentaje = Math.round((count / 40) * 100);

            contadorProgreso.innerHTML = `${count} / 40 (<span id="porcentaje-progreso">${porcentaje}</span>%)`;
            barraProgreso.style.width = `${porcentaje}%`;

            return count;
        }

        // Listener para cambios en radios
        radios.forEach(r => {
            r.addEventListener('change', function () {
                actualizarProgreso();
                // Auto-guardado en segundo plano con debounce opcional
                indicadorGuardado.innerHTML = '<i class="fa-solid fa-pen-nib text-amber-500"></i> Cambios sin guardar';
            });
        });

        // Guardado de Borrador vía AJAX
        btnBorrador.addEventListener('click', function () {
            btnBorrador.disabled = true;
            btnBorrador.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> Guardando...';

            const formData = new FormData(form);

            fetch(`{{ route('psicologia.paca.borrador', $aplicacion) }}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                btnBorrador.disabled = false;
                btnBorrador.innerHTML = '<i class="fa-solid fa-floppy-disk text-slate-500"></i> Guardar Borrador';
                indicadorGuardado.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> Borrador guardado exitosamente';
                setTimeout(() => {
                    indicadorGuardado.innerHTML = '<i class="fa-solid fa-cloud-check text-emerald-600"></i> Borrador al día';
                }, 3000);
            })
            .catch(() => {
                btnBorrador.disabled = false;
                btnBorrador.innerHTML = '<i class="fa-solid fa-floppy-disk text-slate-500"></i> Guardar Borrador';
                indicadorGuardado.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Error al guardar borrador';
            });
        });

        // Validación cliente antes de finalizar
        form.addEventListener('submit', function (e) {
            const count = actualizarProgreso();
            if (count < 40) {
                e.preventDefault();
                alert(`Atención: Debe responder las 40 preguntas antes de finalizar. Actualmente ha respondido ${count} de 40.`);
                
                // Hacer scroll a la primera pregunta sin responder
                for (let i = 1; i <= 40; i++) {
                    const chk = document.querySelector(`input[name="respuestas[${i}]"]:checked`);
                    if (!chk) {
                        const card = document.getElementById(`pregunta-card-${i}`);
                        if (card) {
                            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            card.classList.add('ring-2', 'ring-rose-400', 'bg-rose-50/20');
                            setTimeout(() => {
                                card.classList.remove('ring-2', 'ring-rose-400', 'bg-rose-50/20');
                            }, 2500);
                        }
                        break;
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
