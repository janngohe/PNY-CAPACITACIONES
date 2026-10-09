@extends('layouts.jefe')

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

    <!-- BANNER SUPERIOR CON BOTÓN DE VISTA PREVIA EN TIEMPO REAL -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-md">
        <div class="absolute -right-10 -top-10 w-56 h-56 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-0.5 rounded-full bg-white/10 border border-white/20 text-[10px] font-bold uppercase tracking-wider text-brand-sky mb-2">
                    {{ $editando ? 'Edición de cuestionario' : 'Nueva evaluación' }}
                </span>
                <h1 class="font-heading font-extrabold text-xl sm:text-2xl md:text-3xl leading-tight">
                    {{ $editando ? 'Editar: ' . $evaluacion->titulo : 'Crear Evaluación de Aprendizaje' }}
                </h1>
                <p class="mt-1.5 text-xs sm:text-sm text-slate-200/90 max-w-2xl leading-relaxed">
                    Define el cuestionario que los empleados de tu área presentarán al culminar su formación continua.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" onclick="abrirModalPreview()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 text-white font-heading font-bold text-xs sm:text-sm border border-white/25 backdrop-blur-md shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-eye text-cyan-300"></i>
                    <span>Vista Previa en Vivo</span>
                </button>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none"><path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/></svg>
        </div>
    </div>

    @if ($capacitaciones->isEmpty())
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm" role="alert">
            <strong>Primero necesitas una capacitación.</strong> Las evaluaciones se asocian a una capacitación que hayas publicado.
            <a href="{{ route('jefe.capacitaciones.create') }}" class="font-bold text-brand-blue hover:underline">Crear capacitación</a>.
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
          action="{{ $editando ? route('jefe.evaluaciones.update', $evaluacion) : route('jefe.evaluaciones.store') }}"
          class="space-y-6">
        @csrf
        @if ($editando) @method('PUT') @endif

        <!-- ================= DATOS GENERALES ================= -->
        <section class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-7 space-y-5" aria-labelledby="titulo-datos-eval">
            <div class="flex items-center justify-between border-b border-line/60 pb-3">
                <h2 id="titulo-datos-eval" class="font-heading font-bold text-base sm:text-lg text-brand-dark">Configuración General</h2>
                <span class="text-xs text-muted">Parámetros de aprobación y alcance</span>
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
                    <label for="titulo" class="block text-xs font-bold text-brand-dark mb-1.5">Título de la evaluación</label>
                    <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $evaluacion?->titulo) }}" required maxlength="255"
                           placeholder="Ej: Evaluación final de bioseguridad y control de calidad" class="{{ $input }}">
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-xs font-bold text-brand-dark mb-1.5">Instrucciones o recomendaciones <span class="font-normal text-muted">(opcional)</span></label>
                    <textarea id="descripcion" name="descripcion" rows="2" maxlength="3000"
                              placeholder="Indicaciones que verá el empleado antes de comenzar a rendir la prueba" class="{{ $input }} resize-y">{{ old('descripcion', $evaluacion?->descripcion) }}</textarea>
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 id="titulo-preguntas" class="font-heading font-bold text-base sm:text-lg text-brand-dark">Preguntas del Cuestionario</h2>
                    <p class="text-xs text-muted">Configura las preguntas y despliega sus opciones de respuesta haciendo clic sobre ellas.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btn-colapsar-todas" class="px-3 py-1.5 rounded-xl bg-white border border-line text-slate-600 hover:text-brand-blue hover:border-brand-blue/40 font-heading font-bold text-xs transition-colors shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-compress text-[11px] mr-1 text-slate-400"></i>
                        <span>Colapsar opciones</span>
                    </button>
                    <button type="button" id="btn-expandir-todas" class="px-3 py-1.5 rounded-xl bg-white border border-line text-slate-600 hover:text-brand-blue hover:border-brand-blue/40 font-heading font-bold text-xs transition-colors shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-expand text-[11px] mr-1 text-slate-400"></i>
                        <span>Expandir opciones</span>
                    </button>
                    <button type="button" onclick="abrirModalPreview()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-brand-blue font-heading font-bold text-xs border border-cyan-200 transition-colors shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-eye text-xs"></i>
                        <span>Vista previa</span>
                    </button>
                </div>
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
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Agregar nueva pregunta</span>
                    </button>
                @endunless
            </fieldset>
        </section>

        <!-- ACCIONES INFERIORES -->
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-line/60">
            <a href="{{ route('jefe.evaluaciones.index') }}"
               class="text-center px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs sm:text-sm transition-colors">
                Cancelar
            </a>
            <div class="flex items-center gap-3">
                <button type="button" onclick="abrirModalPreview()"
                        class="px-5 py-3 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-brand-blue font-heading font-bold text-xs sm:text-sm border border-cyan-200 transition-all cursor-pointer">
                    <i class="fa-solid fa-eye mr-1.5"></i> Vista Previa en Vivo
                </button>
                <button type="submit" id="btn-guardar-evaluacion"
                        class="px-7 py-3 rounded-xl bg-gradient-to-r from-brand-blue via-brand-deep to-brand-dark hover:brightness-110 text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                    <i class="fa-solid fa-paper-plane mr-1.5"></i> {{ $editando ? 'Guardar cambios' : 'Crear evaluación' }}
                </button>
            </div>
        </div>
    </form>
    @endif
</div>

<!-- ========================================================
     MODAL DE VISTA PREVIA EN TIEMPO REAL (MODO EMPLEADO)
     ======================================================== -->
<div id="modal-preview-evaluacion" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-brand-dark/70 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="pv-modal-titulo">
    <div class="bg-slate-100 rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-white/20 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header del Modal -->
        <div class="px-6 py-4 bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-cyan-300 text-base shrink-0">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-cyan-300">Simulador en Tiempo Real</span>
                    </div>
                    <h3 class="font-heading font-extrabold text-base sm:text-lg text-white leading-tight">
                        Visualización: Examen tal como lo verá el Empleado
                    </h3>
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <!-- Toggle para ver con o sin respuestas correctas -->
                <label class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-xs text-white cursor-pointer select-none">
                    <input type="checkbox" id="pv-toggle-claves" class="w-3.5 h-3.5 text-emerald-500 rounded cursor-pointer">
                    <span class="text-[11px] font-semibold">Resaltar Claves</span>
                </label>
                <button type="button" onclick="cerrarModalPreview()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar vista previa">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Contenedor Scrollable del Examen (Idéntico a la vista del empleado) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5" id="pv-contenedor-evaluacion">
            
            <div class="bg-white rounded-3xl border border-line/80 shadow-xs overflow-hidden">
                <!-- Tarjeta cabecera de la evaluación -->
                <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 to-brand-dark text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-brand-sky/20 text-brand-sky text-[10px] font-bold uppercase tracking-wider" id="pv-cap-badge">
                            Capacitación Asociada
                        </span>
                        <h3 class="font-heading font-extrabold text-lg sm:text-xl text-white mt-1" id="pv-modal-titulo">
                            Título de la evaluación...
                        </h3>
                        <p class="text-xs text-slate-300 mt-0.5" id="pv-modal-desc">
                            Instrucciones para el empleado...
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-xs bg-white/10 p-3 rounded-2xl border border-white/15 shrink-0">
                        <div class="text-center px-2">
                            <span class="block text-lg font-heading font-extrabold text-cyan-300" id="pv-modal-aprob">80%</span>
                            <span class="text-[10px] text-slate-300">Mínimo Aprobatorio</span>
                        </div>
                        <div class="text-center px-2 border-l border-white/20">
                            <span class="block text-lg font-heading font-extrabold text-white" id="pv-modal-intentos">3</span>
                            <span class="text-[10px] text-slate-300">Intentos Permitidos</span>
                        </div>
                    </div>
                </div>

                <!-- Lista de Preguntas generadas dinámicamente -->
                <div class="p-5 sm:p-7 space-y-5">
                    <div class="flex items-center justify-between border-b border-line pb-2.5">
                        <h4 class="font-heading font-extrabold text-xs sm:text-sm text-brand-dark uppercase tracking-wider" id="pv-modal-preguntas-count">
                            Preguntas del Examen (0)
                        </h4>
                        <span class="text-[11px] text-muted italic hidden sm:inline">Modo interactivo: puedes marcar opciones para probar el cuestionario</span>
                    </div>

                    <div class="space-y-4" id="pv-lista-preguntas-empleado">
                        <!-- Inyectado en tiempo real con JavaScript -->
                    </div>

                    <!-- Botón de Envío Simulado -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-line">
                        <span class="text-[11px] text-muted flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-brand-blue"></i>
                            Simulación en vivo. Las respuestas marcadas aquí no se envían al servidor.
                        </span>
                        <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-brand-blue text-white font-heading font-extrabold text-xs shadow-xs opacity-90 cursor-default">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Enviar Respuestas y Calificar (Simulado)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer del Modal -->
        <div class="px-6 py-3.5 bg-white border-t border-line/70 flex items-center justify-between shrink-0">
            <span class="text-xs text-slate-500 font-semibold" id="pv-modal-resumen-inferior">
                0 preguntas configuradas
            </span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="cerrarModalPreview()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-brand-dark font-heading font-bold text-xs transition-colors cursor-pointer">
                    Cerrar Vista Previa
                </button>
            </div>
        </div>
    </div>
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

        function actualizarContadorOpciones(pregunta) {
            const wrapper = pregunta.querySelector('[data-seccion-opciones]');
            if (!wrapper) return;
            const total = pregunta.querySelectorAll('[data-opcion]').length;
            const badge = wrapper.querySelector('[data-contador-opciones]');
            if (badge) {
                badge.textContent = `${total} ${total === 1 ? 'opción' : 'opciones'}`;
            }
        }

        function toggleOpcionesPregunta(pregunta, forzarEstado = null) {
            const wrapper = pregunta.querySelector('[data-opciones-wrapper]');
            const chevron = pregunta.querySelector('[data-chevron-opciones]');
            const texto = pregunta.querySelector('[data-texto-toggle]');
            if (!wrapper) return;

            const abrir = forzarEstado !== null ? forzarEstado : wrapper.classList.contains('hidden');
            wrapper.classList.toggle('hidden', !abrir);
            if (chevron) chevron.classList.toggle('rotate-180', abrir);
            if (texto) texto.textContent = abrir ? 'Ocultar opciones' : 'Ver opciones';
        }

        function nuevaOpcion(pregunta, texto = '', correcta = false) {
            const html = tplOpcion
                .replaceAll('__P__', pregunta.dataset.index)
                .replaceAll('__O__', ++contador);
            const cont = pregunta.querySelector('[data-opciones]');
            cont.insertAdjacentHTML('beforeend', html);
            const op = cont.lastElementChild;
            op.querySelector('[data-texto-opcion]').value = texto;
            op.querySelector('[data-correcta]').checked = correcta;
            actualizarContadorOpciones(pregunta);
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

            actualizarContadorOpciones(pregunta);
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
            actualizarContadorOpciones(p);
            // La nueva pregunta comienza con sus opciones ocultas para no alargar el scroll
            toggleOpcionesPregunta(p, false);
            renumerar();
            p.scrollIntoView({ behavior: 'smooth', block: 'center' });
            p.querySelector('[data-enunciado]')?.focus({ preventScroll: true });
        }

        document.getElementById('btn-add-pregunta').addEventListener('click', agregarPregunta);

        document.getElementById('btn-colapsar-todas')?.addEventListener('click', () => {
            lista.querySelectorAll('[data-pregunta]').forEach(p => toggleOpcionesPregunta(p, false));
        });

        document.getElementById('btn-expandir-todas')?.addEventListener('click', () => {
            lista.querySelectorAll('[data-pregunta]').forEach(p => toggleOpcionesPregunta(p, true));
        });

        lista.addEventListener('click', (e) => {
            const toggleBtn = e.target.closest('[data-toggle-opciones]');
            const quitarPregunta = e.target.closest('[data-remove-pregunta]');
            const agregarOpcion = e.target.closest('[data-add-opcion]');
            const quitarOpcion = e.target.closest('[data-remove-opcion]');

            if (toggleBtn) {
                toggleOpcionesPregunta(toggleBtn.closest('[data-pregunta]'));
                return;
            }

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
                actualizarContadorOpciones(pregunta);
            }
        });

        lista.addEventListener('input', (e) => {
            if (e.target.matches('[data-enunciado]')) {
                const p = e.target.closest('[data-pregunta]');
                const prev = p.querySelector('[data-preview-enunciado]');
                if (prev) {
                    prev.textContent = e.target.value.trim() || 'Sin enunciado aún';
                }
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

        // Al enviar el formulario, desplegamos las opciones para que las validaciones HTML5 requeridas funcionen sin error
        const formEvaluacion = document.getElementById('form-evaluacion');
        if (formEvaluacion) {
            formEvaluacion.addEventListener('submit', () => {
                lista.querySelectorAll('[data-opciones-wrapper]').forEach(w => w.classList.remove('hidden'));
            });
        }

        // ==========================================
        // LÓGICA DEL MODAL DE VISTA PREVIA EN VIVO
        // ==========================================
        window.abrirModalPreview = function() {
            const modal = document.getElementById('modal-preview-evaluacion');
            if (!modal) return;

            // Datos generales
            const titulo = document.getElementById('titulo')?.value.trim() || 'Evaluación de Aprendizaje';
            const descripcion = document.getElementById('descripcion')?.value.trim() || 'Responde las preguntas para certificar tus conocimientos en este tema.';
            const aprobacion = document.getElementById('porcentaje_aprobacion')?.value || '80';
            const intentos = document.getElementById('intentos_permitidos')?.value || '3';
            const selectCap = document.getElementById('capacitacion_id');
            const capTexto = selectCap && selectCap.selectedIndex > 0 ? selectCap.options[selectCap.selectedIndex].text : 'Capacitación del Área';

            document.getElementById('pv-modal-titulo').textContent = titulo;
            document.getElementById('pv-modal-desc').textContent = descripcion;
            document.getElementById('pv-modal-aprob').textContent = aprobacion + '%';
            document.getElementById('pv-modal-intentos').textContent = intentos;
            document.getElementById('pv-cap-badge').textContent = capTexto;

            // Renderizar preguntas
            const contenedorPreguntas = document.getElementById('pv-lista-preguntas-empleado');
            contenedorPreguntas.innerHTML = '';

            const preguntas = lista.querySelectorAll('[data-pregunta]');
            document.getElementById('pv-modal-preguntas-count').textContent = `Preguntas del Examen (${preguntas.length})`;
            document.getElementById('pv-modal-resumen-inferior').textContent = `${preguntas.length} ${preguntas.length === 1 ? 'pregunta configurada' : 'preguntas configuradas'} · Aprobación mín. ${aprobacion}%`;

            if (preguntas.length === 0) {
                contenedorPreguntas.innerHTML = `
                    <div class="p-8 text-center bg-slate-50 rounded-2xl border border-line/60 text-slate-400">
                        <i class="fa-solid fa-circle-question text-3xl mb-2 text-slate-300 block"></i>
                        <p class="text-xs font-semibold">No has agregado preguntas todavía en el cuestionario.</p>
                    </div>
                `;
            } else {
                const mostrarClaves = document.getElementById('pv-toggle-claves')?.checked ?? false;

                preguntas.forEach((p, idx) => {
                    const enunciado = p.querySelector('[data-enunciado]')?.value.trim() || `Pregunta ${idx + 1} (Sin enunciado redactado)`;
                    const tipo = p.querySelector('[data-tipo-pregunta]')?.value || 'UNICA';
                    const tipoEtiqueta = {
                        UNICA: 'Selección única',
                        MULTIPLE: 'Selección múltiple (puedes marcar varias)',
                        VERDADERO_FALSO: 'Verdadero o Falso'
                    }[tipo] || 'Pregunta';

                    const opciones = p.querySelectorAll('[data-opcion]');

                    const card = document.createElement('div');
                    card.className = 'p-4 sm:p-5 rounded-2xl bg-slate-50 border border-line/80 space-y-3';

                    let opcionesHtml = '';
                    if (opciones.length === 0) {
                        opcionesHtml = '<p class="text-xs text-slate-400 italic pl-8">Sin opciones configuradas.</p>';
                    } else {
                        opciones.forEach((op, oIdx) => {
                            const textoOp = op.querySelector('[data-texto-opcion]')?.value.trim() || `Opción ${oIdx + 1}`;
                            const esCorrecta = op.querySelector('[data-correcta]')?.checked ?? false;
                            const inputType = tipo === 'MULTIPLE' ? 'checkbox' : 'radio';
                            const inputName = `pv_respuestas_${idx}`;

                            const badgeCorrecta = (mostrarClaves && esCorrecta)
                                ? `<span class="ml-auto inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold shrink-0 border border-emerald-300">
                                       <i class="fa-solid fa-check text-[9px]"></i> Clave Correcta
                                   </span>`
                                : '';

                            const bordeClave = (mostrarClaves && esCorrecta)
                                ? 'border-emerald-400 bg-emerald-50/40 ring-1 ring-emerald-300/40'
                                : 'border-line/70 hover:border-brand-blue';

                            opcionesHtml += `
                                <label class="flex items-center gap-3 p-3 rounded-xl bg-white border ${bordeClave} cursor-pointer transition-all text-xs font-semibold text-slate-700 select-none">
                                    <input type="${inputType}" name="${inputName}" class="text-brand-blue focus:ring-brand-blue h-4 w-4">
                                    <span class="truncate">${textoOp}</span>
                                    ${badgeCorrecta}
                                </label>
                            `;
                        });
                    }

                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-brand-blue text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                    ${idx + 1}
                                </span>
                                <div>
                                    <p class="font-heading font-bold text-sm text-brand-dark leading-snug">
                                        ${enunciado}
                                    </p>
                                    <span class="text-[10px] text-muted font-normal mt-0.5 block">${tipoEtiqueta}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 pl-0 sm:pl-8 pt-1">
                            ${opcionesHtml}
                        </div>
                    `;
                    contenedorPreguntas.appendChild(card);
                });
            }

            modal.classList.remove('hidden');
        };

        window.cerrarModalPreview = function() {
            const modal = document.getElementById('modal-preview-evaluacion');
            if (modal) modal.classList.add('hidden');
        };

        document.getElementById('pv-toggle-claves')?.addEventListener('change', () => {
            window.abrirModalPreview();
        });

        // Cerrar modal al hacer clic en el backdrop
        document.getElementById('modal-preview-evaluacion')?.addEventListener('click', (e) => {
            if (e.target.id === 'modal-preview-evaluacion') {
                window.cerrarModalPreview();
            }
        });

        // Cerrar con tecla Escape
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.cerrarModalPreview();
            }
        });

        // Inicializar estado de las preguntas existentes
        lista.querySelectorAll('[data-pregunta]').forEach(p => {
            aplicarTipo(p);
            actualizarContadorOpciones(p);
        });
        renumerar();
    })();
</script>
@endunless
@endpush
