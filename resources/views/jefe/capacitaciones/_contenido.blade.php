@php
    /** Un contenido (video, texto, PDF, imagen o enlace) dentro de un módulo. */
    $cid = "modulos[$i][contenidos][$j]";
    $tipoActual = $contenido['tipo'] ?? 'TEXTO';
    $rutaActual = $contenido['ruta_archivo_actual'] ?? null;
    $errKey = "modulos.$i.contenidos.$j";
    $esVideoActual = $tipoActual === 'VIDEO' && $rutaActual;
@endphp
<div class="contenido-item rounded-2xl border border-line/80 bg-white shadow-2xs overflow-hidden transition-all duration-200" data-contenido>
    <input type="hidden" name="{{ $cid }}[id]" value="{{ $contenido['id'] ?? '' }}">
    <input type="hidden" name="{{ $cid }}[ruta_archivo_actual]" value="{{ $rutaActual }}">

    <!-- ENCABEZADO MINIMIZADO / COLAPSABLE (Clic para abrir o cerrar) -->
    <div class="contenido-header flex items-center justify-between gap-3 px-4 py-3 bg-slate-50/90 hover:bg-slate-100/90 cursor-pointer select-none transition-colors border-b border-line/60" data-toggle-contenido title="Clic para expandir o minimizar este contenido">
        <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <span class="w-7 h-7 rounded-lg bg-brand-light text-brand-blue flex items-center justify-center text-xs shrink-0 shadow-2xs" data-contenido-icono>
                @if($tipoActual === 'VIDEO')
                    <i class="fa-solid fa-circle-play text-red-500"></i>
                @elseif($tipoActual === 'IMAGEN')
                    <i class="fa-solid fa-image text-emerald-600"></i>
                @elseif($tipoActual === 'PDF')
                    <i class="fa-solid fa-file-pdf text-red-600"></i>
                @elseif($tipoActual === 'PPTX')
                    <i class="fa-solid fa-file-powerpoint text-orange-500"></i>
                @elseif($tipoActual === 'ENLACE')
                    <i class="fa-solid fa-arrow-up-right-from-square text-cyan-600"></i>
                @else
                    <i class="fa-solid fa-align-left text-brand-blue"></i>
                @endif
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span class="font-heading font-bold text-xs text-brand-dark truncate block" data-contenido-resumen-titulo>
                        {{ !empty($contenido['titulo']) ? $contenido['titulo'] : 'Nuevo recurso / contenido' }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-line/80 text-slate-600 shrink-0 hidden sm:inline" data-contenido-resumen-tipo>
                        {{ ['TEXTO' => 'Texto explicativo', 'VIDEO' => 'Video', 'IMAGEN' => 'Imagen', 'PDF' => 'PDF', 'PPTX' => 'PowerPoint', 'ENLACE' => 'Enlace'][$tipoActual] ?? 'Texto explicativo' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" data-remove-contenido title="Quitar este contenido"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <span class="w-6 h-6 rounded-lg text-slate-400 flex items-center justify-center transition-transform duration-200" data-contenido-chevron>
                <i class="fa-solid fa-chevron-up text-xs"></i>
            </span>
        </div>
    </div>

    <!-- CUERPO EDITABLE DEL CONTENIDO -->
    <div class="contenido-cuerpo p-4 space-y-3.5 bg-white" data-contenido-cuerpo>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-brand-dark mb-1">Título del contenido <span class="text-red-500">*</span></label>
                <input type="text" name="{{ $cid }}[titulo]" value="{{ $contenido['titulo'] ?? '' }}" required maxlength="255"
                       placeholder="Ej: Presentación de Bioseguridad en Estanques"
                       class="{{ $input }}" data-input-titulo-contenido>
                @error("$errKey.titulo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">Tipo de contenido</label>
                <select name="{{ $cid }}[tipo]" data-tipo-select class="{{ $input }}">
                    @foreach ($tiposContenido as $tipo)
                        <option value="{{ $tipo }}" @selected($tipoActual === $tipo)>
                            {{ ['TEXTO' => 'Texto explicativo', 'VIDEO' => 'Video (Archivo MP4 o URL)', 'IMAGEN' => 'Imagen', 'PDF' => 'Documento PDF', 'PPTX' => 'Presentación PowerPoint (.pptx, .ppt)', 'ENLACE' => 'Enlace externo'][$tipo] ?? $tipo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- SECCIÓN ESPECIAL PARA VIDEO (SUBIDA DE ARCHIVO O URL) -->
        <div data-grupo-video class="{{ $tipoActual === 'VIDEO' ? '' : 'hidden' }} space-y-3 p-3.5 bg-slate-50/70 rounded-xl border border-line/80">
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    Cargar archivo de video <span class="font-normal text-muted">(MP4, MOV o WEBM)</span>
                </label>
                <input type="file" name="{{ $cid }}[archivo]" accept="video/mp4,video/quicktime,video/webm,video/x-m4v,.mp4,.mov,.webm" data-video-file
                       class="pny-file-input pny-file-brand block w-full text-xs text-slate-600"
                       {{ $tipoActual === 'VIDEO' ? '' : 'disabled' }}>

                <div data-video-feedback class="hidden text-[11px] font-semibold mt-1.5 p-2 rounded-lg"></div>

                <!-- Ficha de Condiciones Técnicas del Video -->
                <div class="mt-2.5 p-3 rounded-xl bg-sky-50/70 border border-sky-100 text-[11px] text-slate-700 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-brand-blue">
                        <i class="fa-solid fa-film text-xs"></i>
                        <span>Condiciones y especificaciones técnicas para videos:</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[10px]">
                        <div class="bg-white p-2.5 rounded-xl border border-sky-100 shadow-2xs space-y-1">
                            <span class="flex items-center gap-1.5 font-bold text-brand-dark">
                                <i class="fa-solid fa-stopwatch text-brand-blue text-xs"></i>
                                <span>Duración máx.</span>
                            </span>
                            <span class="text-slate-600 block">10 minutos (600s)</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-sky-100 shadow-2xs space-y-1">
                            <span class="flex items-center gap-1.5 font-bold text-brand-dark">
                                <i class="fa-solid fa-hard-drive text-brand-blue text-xs"></i>
                                <span>Peso máx.</span>
                            </span>
                            <span class="text-slate-600 block">250 MB por video</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-sky-100 shadow-2xs space-y-1">
                            <span class="flex items-center gap-1.5 font-bold text-brand-dark">
                                <i class="fa-solid fa-tv text-brand-blue text-xs"></i>
                                <span>Calidad salida</span>
                            </span>
                            <span class="text-slate-600 block">720p HD (1280 × 720)</span>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-sky-100 shadow-2xs space-y-1">
                            <span class="flex items-center gap-1.5 font-bold text-brand-dark">
                                <i class="fa-solid fa-file-video text-brand-blue text-xs"></i>
                                <span>Formato salida</span>
                            </span>
                            <span class="text-slate-600 block">MP4 (H.264 + AAC)</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($esVideoActual)
                <div class="p-3 bg-slate-100/80 rounded-xl border border-line space-y-2">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="font-bold text-brand-dark flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-play text-emerald-600"></i>
                            Video cargado: {{ basename($rutaActual) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Activo</span>
                    </div>
                    <video controls src="{{ asset($rutaActual) }}" class="w-full max-h-48 rounded-lg bg-black shadow-xs" preload="metadata"></video>
                    <p class="text-[10px] text-muted">Selecciona un nuevo archivo arriba si deseas reemplazar este video.</p>
                </div>
            @endif

            <div class="pt-2 border-t border-line/60">
                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                    O ingresa un enlace externo de video <span class="font-normal text-muted">(YouTube / Vimeo / URL si no subes archivo)</span>
                </label>
                <input type="text" name="{{ $cid }}[contenido]" value="{{ $contenido['contenido'] ?? '' }}"
                       placeholder="https://www.youtube.com/watch?v=... o https://youtu.be/..." class="{{ $input }}">
            </div>
            @error("$errKey.archivo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
            @error("$errKey.contenido")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- SECCIÓN PARA POWERPOINT (PPTX) -->
        <div data-grupo-pptx class="{{ $tipoActual === 'PPTX' ? '' : 'hidden' }} space-y-3 p-3.5 bg-orange-50/60 rounded-xl border border-orange-200">
            <div>
                <label class="block text-[11px] font-bold text-brand-dark mb-1">
                    Cargar Presentación PowerPoint <span class="font-normal text-muted">(.pptx o .ppt · Máx. 100 MB)</span> <span class="text-red-500">*</span>
                </label>
                <input type="file" name="{{ $cid }}[archivo]" accept=".pptx,.ppt,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" data-pptx-file
                       class="pny-file-input pny-file-pptx block w-full text-xs text-slate-700"
                       {{ $tipoActual === 'PPTX' ? '' : 'disabled' }}>

                <div data-pptx-feedback class="hidden text-[11px] font-semibold mt-2 p-2.5 rounded-xl"></div>

                @if ($rutaActual && $tipoActual === 'PPTX')
                    <div class="mt-2.5 p-2.5 bg-white rounded-xl border border-orange-200 flex items-center justify-between text-xs text-orange-950 font-bold shadow-2xs">
                        <span class="truncate flex items-center gap-1.5">
                            <i class="fa-solid fa-file-powerpoint text-orange-600"></i>
                            <span>Presentación actual: <strong>{{ basename($rutaActual) }}</strong></span>
                        </span>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-orange-800 bg-orange-100 px-2 py-0.5 rounded-md">Cargada</span>
                    </div>
                    <p class="text-[10px] text-muted mt-1">Sube un nuevo archivo arriba si deseas reemplazar la presentación actual.</p>
                @endif

                <div class="mt-2.5 p-3 rounded-xl bg-white border border-orange-200/70 text-[11px] text-slate-700 space-y-1.5">
                    <div class="flex items-center gap-2 font-bold text-orange-900">
                        <i class="fa-solid fa-chalkboard-user text-orange-600 text-xs"></i>
                        <span>Visor Interactivo para Empleados:</span>
                    </div>
                    <p class="text-[10px] text-slate-600 leading-relaxed">
                        Las diapositivas se procesarán en el servidor con el mismo visor interactivo de las inducciones. El empleado podrá pasarlas una a una con botones de avance y retroceso, pantalla completa y teclado.
                    </p>
                </div>
            </div>
            @error("$errKey.archivo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- SECCIÓN PARA TEXTO O ENLACE -->
        <div data-grupo-texto class="{{ in_array($tipoActual, ['VIDEO', 'PPTX'], true) ? 'hidden' : '' }}">
            <label class="block text-[11px] font-bold text-brand-dark mb-1" data-label-texto>Texto del contenido</label>
            <textarea name="{{ $cid }}[contenido]" rows="2" maxlength="20000"
                      placeholder="Escribe el contenido educativo o explicativo..."
                      class="{{ $input }} resize-none overflow-hidden transition-all duration-150" data-campo-texto>{{ $contenido['contenido'] ?? '' }}</textarea>
            @error("$errKey.contenido")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
        </div>

        <!-- SECCIÓN PARA ARCHIVO (PDF / IMAGEN) -->
        <div data-grupo-archivo class="{{ in_array($tipoActual, ['IMAGEN', 'PDF'], true) ? '' : 'hidden' }}">
            <label class="block text-[11px] font-bold text-brand-dark mb-1">Archivo adjunto (PDF, JPG, PNG o WEBP · máx. 20 MB)</label>
            <input type="file" name="{{ $cid }}[archivo]" accept=".pdf,.jpg,.jpeg,.png,.webp" data-archivo-file
                   class="pny-file-input pny-file-brand block w-full text-xs text-slate-600"
                   {{ in_array($tipoActual, ['IMAGEN', 'PDF'], true) ? '' : 'disabled' }}>
            @if ($rutaActual && !in_array($tipoActual, ['VIDEO', 'PPTX'], true))
                <p class="mt-1.5 text-[11px] text-muted">
                    Archivo actual:
                    <a href="{{ asset($rutaActual) }}" target="_blank" rel="noopener" class="font-semibold text-brand-blue hover:underline">{{ basename($rutaActual) }}</a>
                    · sube otro para reemplazarlo.
                </p>
            @endif
            @error("$errKey.archivo")<p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
