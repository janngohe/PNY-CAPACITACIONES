@php
    /** Una pregunta de la evaluación con sus opciones de respuesta. */
    $pid = "preguntas[$i]";
    $tipoActual = $pregunta['tipo'] ?? 'UNICA';
    $totalOpciones = count($pregunta['opciones'] ?? []);
@endphp
<div class="pregunta-item bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden transition-all duration-200" data-pregunta data-index="{{ $i }}">
    <input type="hidden" name="{{ $pid }}[id]" value="{{ $pregunta['id'] ?? '' }}">

    <div class="flex items-center justify-between gap-3 px-5 py-3.5 bg-gradient-to-r from-brand-light/70 to-white border-b border-line/60">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-xl bg-brand-blue text-white font-heading font-extrabold text-xs flex items-center justify-center shadow-2xs shrink-0" data-pregunta-numero>1</span>
            <div class="min-w-0">
                <span class="font-heading font-bold text-sm text-brand-dark block leading-tight">Pregunta</span>
                <span class="text-[11px] text-muted truncate block max-w-xs sm:max-w-md" data-preview-enunciado>{{ $pregunta['pregunta'] ?? 'Sin enunciado aún' }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" data-remove-pregunta
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                <i class="fa-regular fa-trash-can text-xs"></i>
                <span class="hidden sm:inline">Quitar</span>
            </button>
        </div>
    </div>

    <div class="p-5 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-brand-dark mb-1.5">
                    Enunciado de la pregunta <span class="text-red-500">*</span>
                </label>
                <textarea name="{{ $pid }}[pregunta]" rows="2" required maxlength="3000"
                          placeholder="Escribe la pregunta que responderá el empleado..."
                          class="{{ $input }} resize-y" data-enunciado>{{ $pregunta['pregunta'] ?? '' }}</textarea>
                @error("preguntas.$i.pregunta")<p class="mt-1 text-[11px] text-red-600 font-semibold">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-brand-dark mb-1.5">Tipo de pregunta</label>
                <select name="{{ $pid }}[tipo]" data-tipo-pregunta class="{{ $input }}">
                    @foreach ($tiposPregunta as $tipo)
                        <option value="{{ $tipo }}" @selected($tipoActual === $tipo)>
                            {{ ['UNICA' => 'Selección única', 'MULTIPLE' => 'Selección múltiple', 'VERDADERO_FALSO' => 'Verdadero / Falso'][$tipo] }}
                        </option>
                    @endforeach
                </select>
                <span class="block text-[11px] text-muted mt-1.5 leading-snug" data-ayuda-tipo></span>
            </div>
        </div>

        <!-- SECCIÓN DE OPCIONES COLAPSABLE PARA NO ALARGAR EL SCROLL -->
        <div class="rounded-2xl border border-line/80 bg-slate-50/70 overflow-hidden" data-seccion-opciones>
            <!-- Barra de botón de despliegue interactivo -->
            <button type="button" data-toggle-opciones
                    class="w-full flex items-center justify-between p-3 sm:px-4 text-left hover:bg-slate-100/80 transition-colors cursor-pointer select-none">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-6 h-6 rounded-lg bg-white border border-line/80 text-brand-blue flex items-center justify-center text-xs shadow-2xs shrink-0">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <span class="text-xs font-bold text-brand-dark">Opciones de respuesta</span>
                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-brand-blue border border-blue-200/60 text-[10px] font-bold shrink-0" data-contador-opciones>
                        {{ $totalOpciones ?: 2 }} opciones
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-brand-blue shrink-0">
                    <span data-texto-toggle class="text-[11px]">Ver opciones</span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" data-chevron-opciones></i>
                </div>
            </button>

            <!-- Contenedor desplegable con las opciones -->
            <div class="p-3.5 sm:p-4 pt-2 border-t border-line/60 bg-white space-y-3 hidden" data-opciones-wrapper>
                <div class="flex items-center justify-between text-[11px] text-muted pb-1">
                    <span>Configura el texto de cada opción y marca la(s) casilla(s) correcta(s):</span>
                    <span class="text-[10px] text-slate-400 hidden sm:inline"><i class="fa-solid fa-check text-emerald-600 mr-1"></i>Verde = Correcta</span>
                </div>

                <div class="space-y-2" data-opciones>
                    @foreach (($pregunta['opciones'] ?? []) as $o => $opcion)
                        @include('jefe.evaluaciones._opcion', ['i' => $i, 'o' => $o, 'opcion' => $opcion, 'input' => $input])
                    @endforeach
                </div>

                @error("preguntas.$i.opciones")<p class="text-[11px] text-red-600 font-semibold">{{ $message }}</p>@enderror

                <div class="pt-1">
                    <button type="button" data-add-opcion
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs transition-colors cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Agregar opción</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
