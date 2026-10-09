@php
    /** Un contenido (video, texto, PDF, imagen o enlace) dentro de un módulo. */
    $cid = "modulos[$i][contenidos][$j]";
    $tipoActual = $contenido['tipo'] ?? 'TEXTO';
    $rutaActual = $contenido['ruta_archivo_actual'] ?? null;
    $errKey = "modulos.$i.contenidos.$j";
@endphp
<div class="contenido-item rounded-2xl border border-line/80 bg-slate-50/70 p-3.5 space-y-3" data-contenido>
    <input type="hidden" name="{{ $cid }}[id]" value="{{ $contenido['id'] ?? '' }}">
    <input type="hidden" name="{{ $cid }}[ruta_archivo_actual]" value="{{ $rutaActual }}">

    <div class="flex items-start gap-2.5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 flex-1">
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-brand-dark mb-1">Título del contenido</label>
                <input type="text" name="{{ $cid }}[titulo]" value="{{ $contenido['titulo'] ?? '' }}" required maxlength="255"
                       placeholder="Ej: Video de bioseguridad"
                       class="{{ $input }}">
                @error("$errKey.titulo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">Tipo</label>
                <select name="{{ $cid }}[tipo]" data-tipo-select class="{{ $input }}">
                    @foreach ($tiposContenido as $tipo)
                        <option value="{{ $tipo }}" @selected($tipoActual === $tipo)>
                            {{ ['TEXTO' => 'Texto', 'VIDEO' => 'Video (URL)', 'IMAGEN' => 'Imagen', 'PDF' => 'Documento PDF', 'ENLACE' => 'Enlace externo'][$tipo] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="button" data-remove-contenido title="Quitar contenido"
                class="mt-5 p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <div data-grupo-texto>
        <label class="block text-[11px] font-bold text-brand-dark mb-1" data-label-texto>Contenido</label>
        <textarea name="{{ $cid }}[contenido]" rows="3" maxlength="20000"
                  class="{{ $input }} resize-y" data-campo-texto>{{ $contenido['contenido'] ?? '' }}</textarea>
        @error("$errKey.contenido")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
    </div>

    <div data-grupo-archivo class="hidden">
        <label class="block text-[11px] font-bold text-brand-dark mb-1">Archivo (PDF, JPG, PNG o WEBP · máx. 20 MB)</label>
        <input type="file" name="{{ $cid }}[archivo]" accept=".pdf,.jpg,.jpeg,.png,.webp"
               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:bg-brand-light file:text-brand-blue file:font-bold file:text-xs hover:file:bg-brand-blue hover:file:text-white file:transition-colors file:cursor-pointer">
        @if ($rutaActual)
            <p class="mt-1.5 text-[11px] text-muted">
                Archivo actual:
                <a href="{{ asset($rutaActual) }}" target="_blank" rel="noopener" class="font-semibold text-brand-blue hover:underline">{{ basename($rutaActual) }}</a>
                · sube otro para reemplazarlo.
            </p>
        @endif
        @error("$errKey.archivo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
