@php
    /** Una pregunta de la evaluación con sus opciones de respuesta. */
    $pid = "preguntas[$i]";
    $tipoActual = $pregunta['tipo'] ?? 'UNICA';
@endphp
<div class="pregunta-item bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" data-pregunta data-index="{{ $i }}">
    <input type="hidden" name="{{ $pid }}[id]" value="{{ $pregunta['id'] ?? '' }}">

    <div class="flex items-center justify-between gap-3 px-5 py-3 bg-gradient-to-r from-brand-light to-white border-b border-line/60">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-brand-blue text-white font-heading font-extrabold text-xs flex items-center justify-center" data-pregunta-numero>1</span>
            <span class="font-heading font-bold text-sm text-brand-dark">Pregunta</span>
        </div>
        <button type="button" data-remove-pregunta
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            Quitar pregunta
        </button>
    </div>

    <div class="p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-brand-dark mb-1.5">Enunciado</label>
                <textarea name="{{ $pid }}[pregunta]" rows="2" required maxlength="3000"
                          placeholder="Escribe la pregunta"
                          class="{{ $input }} resize-y">{{ $pregunta['pregunta'] ?? '' }}</textarea>
                @error("preguntas.$i.pregunta")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
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
            </div>
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-brand-dark uppercase tracking-wide">Opciones de respuesta</h4>
                <span class="text-[11px] text-muted" data-ayuda-tipo></span>
            </div>

            <div class="space-y-2" data-opciones>
                @foreach (($pregunta['opciones'] ?? []) as $o => $opcion)
                    @include('jefe.evaluaciones._opcion', ['i' => $i, 'o' => $o, 'opcion' => $opcion, 'input' => $input])
                @endforeach
            </div>

            @error("preguntas.$i.opciones")<p class="text-[11px] text-red-600 font-semibold">{{ $message }}</p>@enderror

            <button type="button" data-add-opcion
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Agregar opción
            </button>
        </div>
    </div>
</div>
