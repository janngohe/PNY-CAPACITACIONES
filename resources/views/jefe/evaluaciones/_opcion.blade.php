@php
    /** Una opción de respuesta de una pregunta. */
    $oid = "preguntas[$i][opciones][$o]";
@endphp
<div class="opcion-item flex items-center gap-2.5 p-2 rounded-xl bg-slate-50/80 border border-line/70 hover:border-brand-blue/30 transition-colors" data-opcion>
    <input type="hidden" name="{{ $oid }}[id]" value="{{ $opcion['id'] ?? '' }}">

    <label class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-line shadow-2xs shrink-0 cursor-pointer select-none hover:border-emerald-400 transition-colors" title="Marcar como respuesta correcta">
        <input type="checkbox" name="{{ $oid }}[es_correcta]" value="1" data-correcta
               @checked(! empty($opcion['es_correcta']))
               class="w-4 h-4 rounded border-line text-emerald-600 focus:ring-emerald-500 cursor-pointer">
        <span class="text-[11px] font-bold text-emerald-700">Correcta</span>
    </label>

    <div class="flex-1 min-w-0">
        <input type="text" name="{{ $oid }}[texto]" value="{{ $opcion['texto'] ?? '' }}" required maxlength="2000" data-texto-opcion
               placeholder="Texto de la opción de respuesta..."
               class="{{ $input }}">
    </div>

    <button type="button" data-remove-opcion title="Quitar opción"
            class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer shrink-0">
        <i class="fa-regular fa-trash-can text-xs"></i>
    </button>
</div>
