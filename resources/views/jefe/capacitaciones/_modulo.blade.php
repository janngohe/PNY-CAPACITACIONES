@php
    /** Un módulo (sección) de la capacitación con su lista de contenidos. */
    $mid = "modulos[$i]";
@endphp
<div class="modulo-item bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden" data-modulo data-index="{{ $i }}">
    <input type="hidden" name="{{ $mid }}[id]" value="{{ $modulo['id'] ?? '' }}">

    <div class="flex items-center justify-between gap-3 px-5 py-3 bg-gradient-to-r from-brand-light to-white border-b border-line/60">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-brand-blue text-white font-heading font-extrabold text-xs flex items-center justify-center" data-modulo-numero>1</span>
            <span class="font-heading font-bold text-sm text-brand-dark">Módulo</span>
        </div>
        <button type="button" data-remove-modulo
                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-[11px] font-bold text-slate-500 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            Quitar módulo
        </button>
    </div>

    <div class="p-5 space-y-4">
        <div class="grid grid-cols-1 gap-3">
            <div>
                <label class="block text-xs font-bold text-brand-dark mb-1.5">Título del módulo</label>
                <input type="text" name="{{ $mid }}[titulo]" value="{{ $modulo['titulo'] ?? '' }}" required maxlength="255"
                       placeholder="Ej: Normas de bioseguridad en estanques"
                       class="{{ $input }}">
                @error("modulos.$i.titulo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-brand-dark mb-1.5">Descripción del módulo</label>
                <textarea name="{{ $mid }}[descripcion]" rows="2" required maxlength="5000"
                          placeholder="¿Qué aprenderá el empleado en este módulo?"
                          class="{{ $input }} resize-y">{{ $modulo['descripcion'] ?? '' }}</textarea>
                @error("modulos.$i.descripcion")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-brand-dark uppercase tracking-wide">Contenidos</h4>
                <span class="text-[11px] text-muted">Videos, textos, PDF, imágenes o enlaces</span>
            </div>

            <div class="space-y-2.5" data-contenidos>
                @foreach (($modulo['contenidos'] ?? []) as $j => $contenido)
                    @include('jefe.capacitaciones._contenido', ['i' => $i, 'j' => $j, 'contenido' => $contenido, 'input' => $input, 'tiposContenido' => $tiposContenido])
                @endforeach
            </div>

            <button type="button" data-add-contenido
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs transition-colors cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Agregar contenido
            </button>
        </div>
    </div>
</div>
