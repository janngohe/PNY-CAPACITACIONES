@php
    /** Una opción de respuesta de una pregunta. */
    $oid = "preguntas[$i][opciones][$o]";
@endphp
<div class="opcion-item flex items-start gap-2.5" data-opcion>
    <input type="hidden" name="{{ $oid }}[id]" value="{{ $opcion['id'] ?? '' }}">

    <label class="mt-2 flex items-center gap-1.5 shrink-0 cursor-pointer select-none" title="Marcar como respuesta correcta">
        <input type="checkbox" name="{{ $oid }}[es_correcta]" value="1" data-correcta
               @checked(! empty($opcion['es_correcta']))
               class="w-4 h-4 rounded border-line text-brand-blue focus:ring-brand-blue/30 cursor-pointer">
        <span class="text-[11px] font-bold text-emerald-700">Correcta</span>
    </label>

    <div class="flex-1 min-w-0">
        <input type="text" name="{{ $oid }}[texto]" value="{{ $opcion['texto'] ?? '' }}" required maxlength="2000" data-texto-opcion
               placeholder="Texto de la opción"
               class="{{ $input }}">
    </div>

    <button type="button" data-remove-opcion title="Quitar opción"
            class="mt-1.5 p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
    </button>
</div>
