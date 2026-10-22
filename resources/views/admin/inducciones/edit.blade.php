@extends('layouts.admin')

@section('title', 'Editar Inducción: ' . $induccion->titulo . ' · Panel Administrador')

@php
    $imagenActual = $induccion->imagen_portada_url;
    $recursosIniciales = !empty($induccion->recursos) && is_array($induccion->recursos)
        ? array_values($induccion->recursos)
        : [];

    // Si no tiene array de recursos pero tiene campos legacy
    if (empty($recursosIniciales)) {
        if (!empty($induccion->archivo_presentacion)) {
            $recursosIniciales[] = [
                'tipo' => 'pptx',
                'titulo' => 'Diapositivas Principales (PPTX)',
                'archivo' => $induccion->archivo_presentacion,
                'url' => null,
            ];
        }
        if (!empty($induccion->video_url)) {
            $recursosIniciales[] = [
                'tipo' => 'video',
                'titulo' => 'Video de la Inducción',
                'archivo' => null,
                'url' => $induccion->video_url,
            ];
        }
    }
@endphp

@section('content')
<div class="space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.inducciones.index') }}" class="text-xs text-slate-500 hover:text-cyan-700 transition-colors flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Inducciones
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight font-serif">Editar Inducción</h1>
                @if($induccion->estado)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Activa
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                        Inactiva
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Modifica los detalles, imágenes y recursos multimedia asociados a esta inducción</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start sm:self-center">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-chalkboard-user text-cyan-600"></i>
                <span>Ver en Sala</span>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Vista Previa en Vivo</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-xs" role="alert">
            <p class="font-bold mb-1">Revisa los siguientes puntos:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach (collect($errors->all())->unique() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Contenedor Dividido: Formulario (Izquierda) + Vista Previa en Vivo (Derecha) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: FORMULARIO DE EDICIÓN -->
        <div class="lg:col-span-7 space-y-6">
            <form id="form-induccion" method="POST" enctype="multipart/form-data"
                  action="{{ route('admin.inducciones.update', $induccion->id) }}"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- SECCIÓN 1: DATOS GENERALES -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Información General
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Datos Principales</h2>
                        </div>
                        <span class="text-xl text-cyan-600 flex items-center justify-center w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-200">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                    </div>

                    <div class="space-y-5">
                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Título de la Inducción <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="titulo"
                                   name="titulo"
                                   value="{{ old('titulo', $induccion->titulo) }}"
                                   required
                                   maxlength="255"
                                   placeholder="Ej: Inducción General de Seguridad y Salud en el Trabajo"
                                   class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 text-slate-900 text-sm font-semibold transition-all outline-none">
                            <p class="text-[11px] text-slate-400 mt-1">Título principal que verán los facilitadores en la pantalla de bienvenida y en el selector de inducciones.</p>
                        </div>

                        <!-- Tipo / Categoría y Estado -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tipo" class="block text-xs font-bold text-slate-800 mb-1.5">
                                    Categoría / Tipo de Inducción
                                </label>
                                <input type="text"
                                       id="tipo"
                                       name="tipo"
                                       value="{{ old('tipo', $induccion->tipo) }}"
                                       list="tipos-sugeridos"
                                       placeholder="Ej: SST, AMBIENTAL, GENERAL"
                                       class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 text-slate-900 text-sm transition-all outline-none uppercase font-semibold">
                                <datalist id="tipos-sugeridos">
                                    <option value="GENERAL">
                                    <option value="SST - SEGURIDAD Y SALUD">
                                    <option value="GESTIÓN AMBIENTAL">
                                    <option value="CALIDAD">
                                    <option value="TALENTO HUMANO">
                                    <option value="OPERACIONES">
                                </datalist>
                            </div>

                            <div>
                                <label for="estado" class="block text-xs font-bold text-slate-800 mb-1.5">
                                    Estado de Disponibilidad
                                </label>
                                <select id="estado"
                                        name="estado"
                                        class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 text-slate-900 text-sm font-semibold transition-all outline-none">
                                    <option value="1" {{ old('estado', $induccion->estado ? '1' : '0') == '1' ? 'selected' : '' }}>🟢 Activa (Visible en portal de inducción)</option>
                                    <option value="0" {{ old('estado', $induccion->estado ? '1' : '0') == '0' ? 'selected' : '' }}>⚪ Inactiva (Oculta para facilitadores)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Descripción / Objetivos de la Inducción
                            </label>
                            <textarea id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      maxlength="3000"
                                      placeholder="Describe brevemente de qué trata esta inducción, los temas clave y las metas esperadas..."
                                      class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 text-slate-900 text-sm transition-all outline-none resize-y">{{ old('descripcion', $induccion->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: PORTADA DE LA INDUCCIÓN -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Imagen de Portada
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Carátula de Proyección</h2>
                        </div>
                        <span class="text-xl text-cyan-600 flex items-center justify-center w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-200">
                            <i class="fa-solid fa-image"></i>
                        </span>
                    </div>

                    <div class="space-y-4">
                        @if($induccion->imagen_portada)
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-4">
                                <img src="{{ $induccion->imagen_portada_url }}" alt="Portada actual" class="w-20 h-14 rounded-xl object-cover border border-slate-300 shadow-2xs">
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-slate-800">Portada actual configurada</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ $induccion->imagen_portada }}</div>
                                    <label class="inline-flex items-center gap-2 mt-1.5 cursor-pointer text-xs text-rose-600 hover:text-rose-700 font-semibold">
                                        <input type="checkbox" name="eliminar_portada" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Quitar portada y usar logo institucional por defecto</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="imagen_portada" class="block text-xs font-bold text-slate-800 mb-1.5">
                                {{ $induccion->imagen_portada ? 'Reemplazar Imagen de Portada' : 'Subir Imagen de Portada' }}
                            </label>
                            <input type="file"
                                   id="imagen_portada"
                                   name="imagen_portada"
                                   accept="image/png,image/jpeg,image/webp"
                                   class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-800 hover:file:bg-cyan-100 cursor-pointer border border-slate-200 rounded-2xl p-2 bg-slate-50">
                            <p class="text-[11px] text-slate-400 mt-1">Formatos recomendados: JPG, PNG o WEBP (Máx. 10 MB, resolución recomendada 1920x1080 o 1280x720).</p>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: GESTOR DE RECURSOS MULTIMEDIA (SLIDES / PRESENTACIÓN) -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Contenido Multimedia
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Diapositivas y Recursos en la Sala</h2>
                            <p class="text-xs text-slate-500 mt-0.5">El facilitador podrá navegar secuencialmente por estos recursos en el proyector</p>
                        </div>

                        <!-- Botones Rápidos para Agregar Recursos -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" onclick="agregarRecurso('pptx')"
                                    class="px-2.5 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-200 text-xs font-bold transition-all flex items-center gap-1">
                                <i class="fa-solid fa-file-powerpoint text-orange-600"></i> + PPTX
                            </button>
                            <button type="button" onclick="agregarRecurso('video')"
                                    class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-800 border border-red-200 text-xs font-bold transition-all flex items-center gap-1">
                                <i class="fa-solid fa-video text-red-600"></i> + Video
                            </button>
                            <button type="button" onclick="agregarRecurso('pdf')"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 text-xs font-bold transition-all flex items-center gap-1">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> + PDF
                            </button>
                            <button type="button" onclick="agregarRecurso('imagen')"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-800 border border-cyan-200 text-xs font-bold transition-all flex items-center gap-1">
                                <i class="fa-solid fa-image text-cyan-600"></i> + Imagen
                            </button>
                        </div>
                    </div>

                    <!-- Lista Dinámica de Recursos -->
                    <div id="contenedor-recursos" class="space-y-4">
                        <!-- Las tarjetas de recursos se renderizan dinámicamente con JavaScript -->
                    </div>

                    <div id="sin-recursos-msg" class="hidden p-8 rounded-2xl border-2 border-dashed border-slate-200 text-center">
                        <i class="fa-solid fa-layer-group text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-bold text-slate-600">No hay diapositivas ni recursos agregados</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Usa los botones superiores para añadir presentaciones PPTX, videos de YouTube/Vimeo, PDFs o infografías.</p>
                    </div>
                </div>

                <!-- Botones de Acción del Formulario -->
                <div class="flex items-center justify-between gap-4 pt-2">
                    <a href="{{ route('admin.inducciones.index') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="px-6 py-3 rounded-2xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>

            <!-- Zona de Peligro: Eliminar Inducción -->
            <div class="bg-rose-50/60 rounded-3xl border border-rose-200 p-6 sm:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-black uppercase tracking-wider text-rose-700 flex items-center gap-1.5 mb-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Zona de Peligro
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Eliminar permanentemente esta inducción</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Esta acción eliminará el registro y los archivos multimedia asociados del servidor.</p>
                    </div>
                    <button type="button" onclick="abrirModalEliminar()"
                            class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-2 self-start sm:self-center cursor-pointer">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Eliminar Inducción</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: VISTA PREVIA EN TIEMPO REAL -->
        <div class="lg:col-span-5 sticky top-6">
            <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl p-5 text-white overflow-hidden relative">
                <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>

                <!-- Encabezado de la Vista Previa -->
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4 relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-[11px] font-mono text-slate-400 ml-2">Simulador de Sala de Inducción</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold text-cyan-400 tracking-wider">
                        En Vivo
                    </span>
                </div>

                <!-- Marco de la Pantalla del Proyector / Presentador -->
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 relative z-10 shadow-inner">
                    <!-- Portada / Pantalla Principal -->
                    <div class="relative aspect-video bg-slate-950 flex flex-col justify-end p-5 overflow-hidden group">
                        <img id="preview-img"
                             src="{{ $imagenActual }}"
                             alt="Portada Preview"
                             class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-105 transition-transform duration-700">

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

                        <div class="relative z-10 space-y-2">
                            <span id="preview-tipo" class="inline-block px-2.5 py-0.5 rounded-full bg-cyan-500/30 text-cyan-300 border border-cyan-400/30 text-[10px] font-black uppercase tracking-wider">
                                {{ $induccion->tipo ?: 'GENERAL' }}
                            </span>
                            <h3 id="preview-titulo" class="text-base sm:text-lg font-black text-white leading-tight font-heading">
                                {{ $induccion->titulo }}
                            </h3>
                            <p id="preview-desc" class="text-[11px] text-slate-300 line-clamp-2 leading-relaxed">
                                {{ $induccion->descripcion ?: 'Sin descripción detallada.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Barra de Navegación de Recursos (Simulador del Reproductor) -->
                    <div class="p-3 bg-slate-900 border-t border-slate-800">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                            <span class="font-bold text-slate-300">Recursos de la Presentación:</span>
                            <span id="preview-total-recursos" class="font-mono text-cyan-400">0 recursos</span>
                        </div>

                        <!-- Miniaturas de Diapositivas Similares al Visor Real -->
                        <div id="preview-lista-recursos" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                            <!-- Items simulados -->
                        </div>
                    </div>
                </div>

                <!-- Info adicional del simulador -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-cyan-400"></i> Código QR de Asistencia activo
                    </span>
                    <span class="font-mono text-emerald-400 text-[10px]">PIN Requerido</span>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal para Eliminar Inducción -->
<div id="modalEliminarInduccion" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-rose-600 font-bold text-sm uppercase tracking-wider">
                <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación
            </div>
            <button type="button" onclick="cerrarModalEliminar()" class="text-slate-400 hover:text-slate-700 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <h3 class="text-lg font-black text-slate-900 mb-2">¿Eliminar esta inducción?</h3>
        <p class="text-xs text-slate-500 mb-6 leading-relaxed">
            Se eliminará permanentemente la inducción <strong>"{{ $induccion->titulo }}"</strong> y sus archivos multimedia. Las asistencias recolectadas en sesiones anteriores se conservarán como registro histórico.
        </p>

        <form action="{{ route('admin.inducciones.destroy', $induccion->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="cerrarModalEliminar()"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Sí, Eliminar Inducción</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        // Datos iniciales de recursos desde PHP
        const recursosIniciales = @json($recursosIniciales);
        let recursos = [];
        let contadorId = 0;

        const pvTitulo = document.getElementById('preview-titulo');
        const pvTipo = document.getElementById('preview-tipo');
        const pvDesc = document.getElementById('preview-desc');
        const pvImg = document.getElementById('preview-img');
        const pvLista = document.getElementById('preview-lista-recursos');
        const pvTotal = document.getElementById('preview-total-recursos');

        function escaparHtml(texto) {
            if (!texto) return '';
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function actualizarVistaPrevia() {
            const tit = document.getElementById('titulo').value.trim();
            const tip = document.getElementById('tipo').value.trim();
            const desc = document.getElementById('descripcion').value.trim();

            if (pvTitulo) pvTitulo.textContent = tit || 'Título de la Inducción';
            if (pvTipo) pvTipo.textContent = tip ? tip.toUpperCase() : 'GENERAL';
            if (pvDesc) pvDesc.textContent = desc || 'Sin descripción detallada.';

            if (pvTotal) {
                pvTotal.textContent = recursos.length === 1 ? '1 recurso' : `${recursos.length} recursos`;
            }

            if (pvLista) {
                if (recursos.length === 0) {
                    pvLista.innerHTML = `<div class="text-[11px] text-slate-500 text-center py-2">Sin recursos agregados</div>`;
                } else {
                    pvLista.innerHTML = recursos.map((r, i) => {
                        let icono = 'fa-file-powerpoint text-orange-400';
                        let etiqueta = 'PPTX';
                        if (r.tipo === 'video') { icono = 'fa-video text-red-400'; etiqueta = 'VIDEO'; }
                        else if (r.tipo === 'pdf') { icono = 'fa-file-pdf text-rose-400'; etiqueta = 'PDF'; }
                        else if (r.tipo === 'imagen') { icono = 'fa-image text-cyan-400'; etiqueta = 'IMG'; }

                        const displayTit = r.titulo || `Recurso ${i + 1}`;
                        return `
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950/70 border border-slate-800 text-[11px]">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-slate-800 text-[10px] font-mono flex items-center justify-center font-bold text-slate-300">
                                        ${i + 1}
                                    </span>
                                    <i class="fa-solid ${icono} text-xs"></i>
                                    <span class="truncate font-medium text-slate-200">${escaparHtml(displayTit)}</span>
                                </div>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 shrink-0">
                                    ${etiqueta}
                                </span>
                            </div>
                        `;
                    }).join('');
                }
            }
        }

        window.agregarRecurso = function (tipo, tituloSugerido = '', datosExistentes = null) {
            contadorId++;
            const id = contadorId;
            const index = recursos.length;

            const recursoObj = {
                id: id,
                tipo: tipo,
                titulo: datosExistentes ? (datosExistentes.titulo || '') : (tituloSugerido || ''),
                url: datosExistentes ? (datosExistentes.url || '') : '',
                archivo: datosExistentes ? (datosExistentes.archivo || '') : '',
                archivoNombre: datosExistentes && datosExistentes.archivo ? datosExistentes.archivo.split('/').pop() : ''
            };
            recursos.push(recursoObj);

            renderizarTarjetaRecurso(recursoObj, index);
            actualizarVisibilidadVacio();
            actualizarVistaPrevia();
        };

        function renderizarTarjetaRecurso(r, index) {
            const contenedor = document.getElementById('contenedor-recursos');

            let icono = 'fa-file-powerpoint text-orange-600 bg-orange-50 border-orange-200';
            let tipoLabel = 'Presentación PowerPoint (PPTX)';
            if (r.tipo === 'video') {
                icono = 'fa-video text-red-600 bg-red-50 border-red-200';
                tipoLabel = 'Video Multimedia (YouTube, Vimeo o URL directa)';
            } else if (r.tipo === 'pdf') {
                icono = 'fa-file-pdf text-rose-600 bg-rose-50 border-rose-200';
                tipoLabel = 'Documento PDF para Proyección';
            } else if (r.tipo === 'imagen') {
                icono = 'fa-image text-cyan-600 bg-cyan-50 border-cyan-200';
                tipoLabel = 'Infografía o Imagen Explicativa';
            }

            const card = document.createElement('div');
            card.id = 'cardRecurso_' + r.id;
            card.className = 'p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-3.5 transition-all hover:border-slate-300 relative';

            let campoContenidoHtml = '';
            if (r.tipo === 'video') {
                campoContenidoHtml = `
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            URL del Video (YouTube, Vimeo, MP4) <span class="text-rose-500">*</span>
                        </label>
                        <input type="url"
                               name="recursos[${index}][url]"
                               value="${escaparHtml(r.url)}"
                               oninput="actualizarUrlRecurso(${r.id}, this.value)"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-500/10 outline-none">
                    </div>
                `;
            } else {
                let accept = '.pptx,.ppt';
                let formatoInfo = 'Archivos PowerPoint .pptx o .ppt (Máx. 50 MB)';
                if (r.tipo === 'pdf') {
                    accept = '.pdf';
                    formatoInfo = 'Documentos PDF (Máx. 30 MB)';
                } else if (r.tipo === 'imagen') {
                    accept = '.png,.jpg,.jpeg,.webp';
                    formatoInfo = 'Imágenes PNG, JPG o WEBP (Máx. 15 MB)';
                }

                let archivoActualBadge = '';
                if (r.archivo) {
                    archivoActualBadge = `
                        <div class="mb-2 p-2 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                            <span class="truncate flex items-center gap-1.5">
                                <i class="fa-solid fa-file-check text-emerald-600"></i>
                                <span>Archivo cargado: <strong>${escaparHtml(r.archivoNombre || r.archivo)}</strong></span>
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Conservado</span>
                        </div>
                    `;
                }

                campoContenidoHtml = `
                    <div>
                        ${archivoActualBadge}
                        <input type="hidden" name="recursos[${index}][archivo_actual]" value="${escaparHtml(r.archivo)}">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            ${r.archivo ? 'Reemplazar Archivo (Opcional)' : 'Subir Archivo'} <span class="text-slate-400 font-normal">(${formatoInfo})</span>
                        </label>
                        <input type="file"
                               name="recursos[${index}][archivo]"
                               accept="${accept}"
                               onchange="actualizarArchivoRecurso(${r.id}, this)"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-slate-700 hover:file:bg-slate-100 cursor-pointer border border-slate-200 rounded-xl p-1.5 bg-white">
                    </div>
                `;
            }

            card.innerHTML = `
                <input type="hidden" name="recursos[${index}][tipo]" value="${r.tipo}">

                <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 pb-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="recurso-pos-num w-6 h-6 rounded-lg bg-slate-200 text-slate-700 text-xs font-mono font-bold flex items-center justify-center">
                            ${index + 1}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs ${icono}">
                                <i class="fa-solid ${r.tipo === 'video' ? 'fa-video' : (r.tipo === 'pptx' ? 'fa-file-powerpoint' : (r.tipo === 'pdf' ? 'fa-file-pdf' : 'fa-image'))}"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-800">${tipoLabel}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="moverRecursoRelativo(${r.id}, -1)" title="Subir orden"
                                class="btn-mover-arriba p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/70 transition-colors disabled:opacity-30 disabled:pointer-events-none">
                            <i class="fa-solid fa-chevron-up text-xs"></i>
                        </button>
                        <button type="button" onclick="moverRecursoRelativo(${r.id}, 1)" title="Bajar orden"
                                class="btn-mover-abajo p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200/70 transition-colors disabled:opacity-30 disabled:pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>
                        <button type="button" onclick="eliminarRecurso(${r.id})" title="Eliminar recurso"
                                class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-100/70 transition-colors ml-1">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-8">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            Título / Nombre del Recurso
                        </label>
                        <input type="text"
                               name="recursos[${index}][titulo]"
                               value="${escaparHtml(r.titulo)}"
                               oninput="actualizarTituloRecurso(${r.id}, this.value)"
                               placeholder="Ej: Diapositiva 1 - Presentación General"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/10 outline-none">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                            Posición en Presentador
                        </label>
                        <select onchange="cambiarPosicionRecurso(${r.id}, parseInt(this.value))"
                                class="recurso-pos-select w-full px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 focus:border-cyan-500 outline-none">
                            ${recursos.map((_, pos) => `<option value="${pos}" ${pos === index ? 'selected' : ''}>Posición ${pos + 1}</option>`).join('')}
                        </select>
                    </div>
                </div>

                ${campoContenidoHtml}
            `;

            contenedor.appendChild(card);
            actualizarBotonesReordenamiento();
        }

        function actualizarVisibilidadVacio() {
            const msg = document.getElementById('sin-recursos-msg');
            if (msg) {
                if (recursos.length === 0) {
                    msg.classList.remove('hidden');
                } else {
                    msg.classList.add('hidden');
                }
            }
        }

        function actualizarBotonesReordenamiento() {
            recursos.forEach((r, idx) => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const btnUp = card.querySelector('.btn-mover-arriba');
                    if (btnUp) btnUp.disabled = (idx === 0);

                    const btnDown = card.querySelector('.btn-mover-abajo');
                    if (btnDown) btnDown.disabled = (idx === recursos.length - 1);

                    const posNum = card.querySelector('.recurso-pos-num');
                    if (posNum) posNum.textContent = idx + 1;

                    const sel = card.querySelector('.recurso-pos-select');
                    if (sel) {
                        sel.innerHTML = recursos.map((_, pos) => `<option value="${pos}" ${pos === idx ? 'selected' : ''}>Posición ${pos + 1}</option>`).join('');
                    }
                }
            });
        }

        window.eliminarRecurso = function (id) {
            const idx = recursos.findIndex(r => r.id === id);
            if (idx >= 0) {
                const card = document.getElementById('cardRecurso_' + id);
                if (card) card.remove();
                recursos.splice(idx, 1);
                reindexarFormulario();
                actualizarVisibilidadVacio();
                actualizarVistaPrevia();
            }
        };

        function reindexarFormulario() {
            const contenedor = document.getElementById('contenedor-recursos');
            recursos.forEach((r, i) => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const tipoInp = card.querySelector('input[name*="[tipo]"]');
                    if (tipoInp) tipoInp.name = `recursos[${i}][tipo]`;

                    const titInp = card.querySelector('input[name*="[titulo]"]');
                    if (titInp) titInp.name = `recursos[${i}][titulo]`;

                    const archInp = card.querySelector('input[name*="[archivo]"]');
                    if (archInp) archInp.name = `recursos[${i}][archivo]`;

                    const archActualInp = card.querySelector('input[name*="[archivo_actual]"]');
                    if (archActualInp) archActualInp.name = `recursos[${i}][archivo_actual]`;

                    const urlInp = card.querySelector('input[name*="[url]"]');
                    if (urlInp) urlInp.name = `recursos[${i}][url]`;

                    contenedor.appendChild(card);
                }
            });
            actualizarBotonesReordenamiento();
        }

        window.moverRecursoRelativo = function (id, delta) {
            const idx = recursos.findIndex(r => r.id === id);
            if (idx < 0) return;
            const nuevoIdx = idx + delta;
            if (nuevoIdx < 0 || nuevoIdx >= recursos.length) return;
            window.cambiarPosicionRecurso(id, nuevoIdx);
        };

        window.cambiarPosicionRecurso = function (id, nuevoIdx) {
            const idx = recursos.findIndex(r => r.id === id);
            if (idx < 0 || nuevoIdx < 0 || nuevoIdx >= recursos.length || idx === nuevoIdx) return;

            recursos.forEach(r => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const titInput = card.querySelector('input[name*="[titulo]"]');
                    if (titInput) r.titulo = titInput.value;
                    const urlInput = card.querySelector('input[name*="[url]"]');
                    if (urlInput) r.url = urlInput.value;
                }
            });

            const [item] = recursos.splice(idx, 1);
            recursos.splice(nuevoIdx, 0, item);

            reindexarFormulario();
            actualizarVistaPrevia();
        };

        window.actualizarTituloRecurso = function (id, valor) {
            const item = recursos.find(r => r.id === id);
            if (item) {
                item.titulo = valor;
                actualizarVistaPrevia();
            }
        };

        window.actualizarUrlRecurso = function (id, valor) {
            const item = recursos.find(r => r.id === id);
            if (item) {
                item.url = valor;
                actualizarVistaPrevia();
            }
        };

        window.actualizarArchivoRecurso = function (id, input) {
            const item = recursos.find(r => r.id === id);
            if (item && input.files && input.files[0]) {
                item.archivoNombre = input.files[0].name;
                actualizarVistaPrevia();
            }
        };

        // Escucha de cambio de portada
        const inputPortada = document.getElementById('imagen_portada');
        if (inputPortada) {
            inputPortada.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (pvImg) pvImg.src = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Live Preview de inputs de texto
        ['titulo', 'tipo', 'descripcion'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', actualizarVistaPrevia);
        });

        // Cargar los recursos existentes al inicio
        if (Array.isArray(recursosIniciales) && recursosIniciales.length > 0) {
            recursosIniciales.forEach(rec => {
                window.agregarRecurso(rec.tipo || 'pptx', rec.titulo || '', rec);
            });
        } else {
            actualizarVisibilidadVacio();
        }

        actualizarVistaPrevia();
    })();

    function abrirModalEliminar() {
        document.getElementById('modalEliminarInduccion').classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        document.getElementById('modalEliminarInduccion').classList.add('hidden');
    }
</script>
@endsection
