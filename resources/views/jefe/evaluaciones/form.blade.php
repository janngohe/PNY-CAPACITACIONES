@extends('layouts.' . ($panel ?? 'jefe'))

@php
    $editando = (bool) $evaluacion;
    $input = 'w-full px-4 py-2.5 rounded-xl border border-line bg-white text-xs sm:text-[13px] font-sans text-brand-dark placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 disabled:bg-slate-50 disabled:text-slate-500';
    $porcentaje = old('porcentaje_aprobacion', $evaluacion ? rtrim(rtrim(number_format((float) $evaluacion->porcentaje_aprobacion, 2, '.', ''), '0'), '.') : 80);
    $capSel = (int) old('capacitacion_id', $capacitacionSeleccionada);
@endphp

@section('title', $editando ? 'Editar Evaluación' : 'Crear Evaluación')
@section('page_title', $editando ? 'Editar Evaluación' : 'Crear Evaluación')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="inline-block px-3 py-0.5 rounded-full bg-white/10 border border-white/20 text-[10px] font-bold uppercase tracking-wider text-brand-sky mb-2">
                {{ $editando ? 'Edición' : 'Nueva evaluación' }}
            </span>
            <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl leading-tight">
                {{ $editando ? 'Editar: ' . $evaluacion->titulo : 'Crear Evaluación' }}
            </h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-200/90 max-w-2xl">
                Define el cuestionario que los empleados de tu área presentarán al terminar la capacitación.
            </p>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none"><path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/></svg>
        </div>
    </div>

    @if ($capacitaciones->isEmpty())
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm" role="alert">
            <strong>Primero necesitas una capacitación.</strong> Las evaluaciones se asocian a una capacitación que hayas publicado.
            <a href="{{ route(($panel ?? 'jefe') . '.capacitaciones.create') }}" class="font-bold text-brand-blue hover:underline">Crear capacitación</a>.
        </div>
    @else

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs" role="alert">
            <p class="font-bold mb-1">Revisa los siguientes puntos:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach (collect($errors->all())->unique() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($bloqueada)
        <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 text-xs sm:text-sm flex items-start gap-3" role="status">
            <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
            <p>Esta evaluación ya tiene <strong>intentos registrados</strong>. Puedes ajustar sus datos generales, pero las preguntas quedan bloqueadas para no alterar los resultados históricos. Si necesitas otro cuestionario, crea una evaluación nueva y desactiva esta.</p>
        </div>
    @endif

    <form id="form-evaluacion" method="POST"
          action="{{ $editando ? route(($panel ?? 'jefe') . '.evaluaciones.update', $evaluacion) : route(($panel ?? 'jefe') . '.evaluaciones.store') }}"
          class="space-y-6">
        @csrf
        @if ($editando) @method('PUT') @endif

        <!-- ================= DATOS GENERALES ================= -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-5" aria-labelledby="titulo-datos-eval">
            <div>
                <h2 id="titulo-datos-eval" class="font-heading font-bold text-base sm:text-lg text-brand-dark">Datos de la evaluación</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="capacitacion_id" class="block text-xs font-bold text-brand-dark mb-1.5">Capacitación asociada</label>
                    <select id="capacitacion_id" name="capacitacion_id" required class="{{ $input }}">
                        <option value="">Selecciona una capacitación…</option>
                        @foreach ($capacitaciones as $cap)
                            <option value="{{ $cap->id }}" @selected($capSel === $cap->id)>
                                {{ $cap->titulo }}{{ $cap->estado ? '' : ' (desactivada)' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="titulo" class="block text-xs font-bold text-brand-dark mb-1.5">Título</label>
                    <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $evaluacion?->titulo) }}" required maxlength="255"
                           placeholder="Ej: Evaluación final de bioseguridad" class="{{ $input }}">
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-xs font-bold text-brand-dark mb-1.5">Instrucciones <span class="font-normal text-muted">(opcional)</span></label>
                    <textarea id="descripcion" name="descripcion" rows="2" maxlength="3000"
                              placeholder="Indicaciones que verá el empleado antes de comenzar" class="{{ $input }} resize-y">{{ old('descripcion', $evaluacion?->descripcion) }}</textarea>
                </div>

                <div>
                    <label for="porcentaje_aprobacion" class="block text-xs font-bold text-brand-dark mb-1.5">Porcentaje mínimo de aprobación (%)</label>
                    <input id="porcentaje_aprobacion" type="number" name="porcentaje_aprobacion" value="{{ $porcentaje }}" min="1" max="100" step="0.01" required class="{{ $input }}">
                </div>
                <div>
                    <label for="intentos_permitidos" class="block text-xs font-bold text-brand-dark mb-1.5">Intentos permitidos</label>
                    <input id="intentos_permitidos" type="number" name="intentos_permitidos" value="{{ old('intentos_permitidos', $evaluacion?->intentos_permitidos ?? 3) }}" min="1" max="20" required class="{{ $input }}">
                </div>
            </div>
        </section>

        <!-- ================= PREGUNTAS ================= -->
        <section class="space-y-4" aria-labelledby="titulo-preguntas">
            <div>
                <h2 id="titulo-preguntas" class="font-heading font-bold text-base sm:text-lg text-brand-dark">Preguntas</h2>
                <p class="text-xs text-muted">Marca la(s) opción(es) correcta(s) de cada pregunta. En selección única y verdadero/falso solo puede haber una.</p>
            </div>

            <fieldset @disabled($bloqueada) class="space-y-4 min-w-0 border-0 p-0 m-0">
                <div id="lista-preguntas" class="space-y-4">
                    @foreach ($preguntasForm as $i => $pregunta)
                        @include('jefe.evaluaciones._pregunta', ['i' => $i, 'pregunta' => $pregunta, 'input' => $input, 'tiposPregunta' => $tiposPregunta])
                    @endforeach
                </div>

                @unless ($bloqueada)
                    <button type="button" id="btn-add-pregunta"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border-2 border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs sm:text-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Agregar pregunta
                    </button>
                @endunless
            </fieldset>
        </section>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-2">
            <a href="{{ route(($panel ?? 'jefe') . '.evaluaciones.index') }}"
               class="text-center px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs sm:text-sm transition-colors">
                Cancelar
            </a>
            <button type="submit" id="btn-guardar-evaluacion"
                    class="px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                {{ $editando ? 'Guardar cambios' : 'Crear evaluación' }}
            </button>
        </div>
    </form>
    @endif
</div>

@unless ($bloqueada || $capacitaciones->isEmpty())
<template id="tpl-pregunta">
    @include('jefe.evaluaciones._pregunta', ['i' => '__P__', 'pregunta' => ['id' => null, 'pregunta' => '', 'tipo' => 'UNICA', 'opciones' => []], 'input' => $input, 'tiposPregunta' => $tiposPregunta])
</template>
<template id="tpl-opcion">
    @include('jefe.evaluaciones._opcion', ['i' => '__P__', 'o' => '__O__', 'opcion' => ['id' => null, 'texto' => '', 'es_correcta' => false], 'input' => $input])
</template>
@endunless
@endsection

@push('scripts')
@unless ($bloqueada || $capacitaciones->isEmpty())
<script>
    (function () {
        const lista = document.getElementById('lista-preguntas');
        const tplPregunta = document.getElementById('tpl-pregunta').innerHTML;
        const tplOpcion = document.getElementById('tpl-opcion').innerHTML;
        let contador = Date.now();

        const ayudas = {
            UNICA: 'Marca una sola respuesta correcta.',
            MULTIPLE: 'Marca una o más respuestas correctas.',
            VERDADERO_FALSO: 'Marca cuál es la respuesta correcta.',
        };

        function nuevaOpcion(pregunta, texto = '', correcta = false) {
            const html = tplOpcion
                .replaceAll('__P__', pregunta.dataset.index)
                .replaceAll('__O__', ++contador);
            const cont = pregunta.querySelector('[data-opciones]');
            cont.insertAdjacentHTML('beforeend', html);
            const op = cont.lastElementChild;
            op.querySelector('[data-texto-opcion]').value = texto;
            op.querySelector('[data-correcta]').checked = correcta;
            return op;
        }

        function aplicarTipo(pregunta) {
            const tipo = pregunta.querySelector('[data-tipo-pregunta]').value;
            const cont = pregunta.querySelector('[data-opciones]');
            const esVF = tipo === 'VERDADERO_FALSO';
            pregunta.querySelector('[data-ayuda-tipo]').textContent = ayudas[tipo];

            if (esVF) {
                let opciones = [...cont.querySelectorAll('[data-opcion]')];
                // Verdadero/Falso: exactamente dos opciones fijas
                opciones.slice(2).forEach(o => o.remove());
                opciones = [...cont.querySelectorAll('[data-opcion]')];
                while (opciones.length < 2) {
                    nuevaOpcion(pregunta);
                    opciones = [...cont.querySelectorAll('[data-opcion]')];
                }
                ['Verdadero', 'Falso'].forEach((texto, idx) => {
                    const campo = opciones[idx].querySelector('[data-texto-opcion]');
                    campo.value = texto;
                    campo.readOnly = true;
                    campo.classList.add('bg-slate-50');
                });
            } else {
                cont.querySelectorAll('[data-texto-opcion]').forEach(c => {
                    c.readOnly = false;
                    c.classList.remove('bg-slate-50');
                });
                while (cont.querySelectorAll('[data-opcion]').length < 2) nuevaOpcion(pregunta);
            }

            pregunta.querySelector('[data-add-opcion]').classList.toggle('hidden', esVF);
            cont.querySelectorAll('[data-remove-opcion]').forEach(b => b.classList.toggle('invisible', esVF));

            if (tipo !== 'MULTIPLE') {
                const marcadas = [...cont.querySelectorAll('[data-correcta]:checked')];
                marcadas.slice(1).forEach(c => c.checked = false);
            }
        }

        function renumerar() {
            lista.querySelectorAll('[data-pregunta]').forEach((p, idx) => {
                p.querySelector('[data-pregunta-numero]').textContent = idx + 1;
            });
        }

        function agregarPregunta() {
            lista.insertAdjacentHTML('beforeend', tplPregunta.replaceAll('__P__', ++contador));
            const p = lista.lastElementChild;
            nuevaOpcion(p, '', true);
            nuevaOpcion(p, '', false);
            aplicarTipo(p);
            renumerar();
            p.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        document.getElementById('btn-add-pregunta').addEventListener('click', agregarPregunta);

        lista.addEventListener('click', (e) => {
            const quitarPregunta = e.target.closest('[data-remove-pregunta]');
            const agregarOpcion = e.target.closest('[data-add-opcion]');
            const quitarOpcion = e.target.closest('[data-remove-opcion]');

            if (quitarPregunta) {
                if (lista.querySelectorAll('[data-pregunta]').length === 1) {
                    alert('La evaluación debe tener al menos una pregunta.');
                    return;
                }
                quitarPregunta.closest('[data-pregunta]').remove();
                renumerar();
            }

            if (agregarOpcion) {
                nuevaOpcion(agregarOpcion.closest('[data-pregunta]'));
            }

            if (quitarOpcion) {
                const pregunta = quitarOpcion.closest('[data-pregunta]');
                if (pregunta.querySelectorAll('[data-opcion]').length <= 2) {
                    alert('Cada pregunta debe tener al menos 2 opciones.');
                    return;
                }
                quitarOpcion.closest('[data-opcion]').remove();
            }
        });

        lista.addEventListener('change', (e) => {
            if (e.target.matches('[data-tipo-pregunta]')) {
                aplicarTipo(e.target.closest('[data-pregunta]'));
            }

            if (e.target.matches('[data-correcta]') && e.target.checked) {
                const pregunta = e.target.closest('[data-pregunta]');
                if (pregunta.querySelector('[data-tipo-pregunta]').value !== 'MULTIPLE') {
                    pregunta.querySelectorAll('[data-correcta]').forEach(c => { if (c !== e.target) c.checked = false; });
                }
            }
        });

        lista.querySelectorAll('[data-pregunta]').forEach(aplicarTipo);
        renumerar();
    })();
</script>
@endunless
@endpush
