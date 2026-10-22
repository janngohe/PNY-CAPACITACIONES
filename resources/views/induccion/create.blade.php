@extends('layouts.induccion')

@section('title', 'Crear Nueva Inducción')

@php
    $imagenActual = asset('images/Logo.png');
@endphp

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('induccion.index') }}" class="text-xs text-slate-500 hover:text-brand-blue transition-colors flex items-center gap-1.5 font-medium">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Inducciones
                </a>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight font-serif">Crear Nueva Inducción</h1>
            <p class="text-xs sm:text-sm text-slate-500">Configura los contenidos multimedia (PowerPoint, Videos, PDFs, Imágenes) y visualiza en tiempo real cómo se verá publicada</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Vista Previa en Tiempo Real Activa</span>
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-xs" role="alert">
            <p class="font-bold mb-1">Revisa los siguientes puntos:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach (collect($errors->all())->unique() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Contenedor Dividido: Formulario (Izquierda) + Vista Previa en Vivo Idéntica a Capacitaciones (Derecha) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: FORMULARIO DE CONFIGURACIÓN (7 Cols en lg) -->
        <div class="lg:col-span-7">
            <form id="form-induccion" method="POST" enctype="multipart/form-data"
                  action="{{ route('induccion.store') }}"
                  class="space-y-6">
                @csrf

                <!-- SECCIÓN 1: DATOS GENERALES (Título, Descripción, Imagen de Portada) -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-brand-light text-brand-blue font-extrabold text-[11px] uppercase tracking-wide">
                                Información General
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Datos Principales de la Inducción</h2>
                        </div>
                        <span class="text-xl text-brand-blue flex items-center justify-center w-10 h-10 rounded-xl bg-brand-light">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </span>
                    </div>

                    <div class="space-y-5">
                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Título de la inducción <span class="text-red-500">*</span>
                            </label>
                            <input id="titulo" type="text" name="titulo" value="{{ old('titulo') }}" required maxlength="255"
                                   placeholder="Ej: Inducción General de Seguridad, Calidad y Buenas Prácticas"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-[13px] font-sans text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Descripción u objetivos <span class="text-red-500">*</span>
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="3" required maxlength="3000"
                                      placeholder="Explica el objetivo principal, temas clave y lineamientos que se proyectarán a los colaboradores..."
                                      class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs sm:text-[13px] font-sans text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all resize-y">{{ old('descripcion') }}</textarea>
                        </div>

                        <!-- Imagen de Portada -->
                        <div>
                            <label for="imagen_portada" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Imagen de portada <span class="font-normal text-slate-400">(opcional · JPG, PNG o WEBP)</span>
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                <div class="w-20 h-14 rounded-xl bg-slate-200 overflow-hidden shrink-0 border border-slate-300 relative shadow-2xs">
                                    <img id="img-preview-thumb" src="{{ $imagenActual }}" alt="Vista previa miniatura" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input id="imagen_portada" type="file" name="imagen_portada" accept=".jpg,.jpeg,.png,.webp"
                                           class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:bg-brand-light file:text-brand-blue file:font-bold file:text-xs hover:file:bg-brand-blue hover:file:text-white file:transition-colors file:cursor-pointer">
                                    <p class="text-[11px] text-slate-400 mt-1">Se previsualizará de inmediato en la tarjeta lateral.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: RECURSOS MULTIMEDIA (PowerPoint, Videos, PDFs, Imágenes) -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Material de Proyección
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Recursos Multimedia de la Inducción</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Agrega múltiples diapositivas en PowerPoint (.pptx), videos interactivos, PDFs o imágenes</p>
                        </div>
                        <span id="badge-total-recursos" class="text-xs font-bold px-3 py-1 rounded-full bg-brand-light text-brand-blue self-start sm:self-center">
                            0 recursos
                        </span>
                    </div>

                    <!-- Botonera de Acción Rápida para Agregar Recursos -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <button type="button" onclick="agregarRecurso('pptx')"
                                class="p-3 rounded-2xl bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-800 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <i class="fa-solid fa-file-powerpoint text-xl text-orange-600"></i>
                            <span>+ PowerPoint (.pptx)</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('video')"
                                class="p-3 rounded-2xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-800 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <i class="fa-brands fa-youtube text-xl text-rose-600"></i>
                            <span>+ Video en Vivo</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('pdf')"
                                class="p-3 rounded-2xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-800 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <i class="fa-solid fa-file-pdf text-xl text-red-600"></i>
                            <span>+ Documento PDF</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('imagen')"
                                class="p-3 rounded-2xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <i class="fa-solid fa-image text-xl text-emerald-600"></i>
                            <span>+ Imagen / Esquema</span>
                        </button>
                    </div>

                    <!-- Listado Dinámico de Recursos -->
                    <div id="contenedor-recursos" class="space-y-4 pt-2">
                        <!-- Generado dinámicamente con JavaScript -->
                    </div>

                    <div id="vacio-recursos-msg" class="text-center py-8 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-xs text-slate-500">
                        <i class="fa-solid fa-folder-open text-2xl text-slate-300 mb-2"></i>
                        <p class="font-medium text-slate-700">No has agregado ningún recurso aún.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Usa los botones superiores para incorporar diapositivas de PowerPoint o videos.</p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('induccion.index') }}"
                       class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-8 py-3.5 rounded-xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer hover:scale-101">
                        <i class="fa-solid fa-floppy-disk text-sm"></i>
                        <span>Guardar y Publicar Inducción</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- COLUMNA DERECHA: VISTA PREVIA LATERAL EN TIEMPO REAL (Idéntica a la de Capacitaciones) -->
        <div class="lg:col-span-5 lg:sticky lg:top-24 space-y-4">
            <div class="bg-slate-900 text-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-800 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-cyan-300">
                            Vista Previa en Tiempo Real
                        </span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 text-[10px] font-mono">
                        Vista del Colaborador
                    </span>
                </div>

                <!-- TARJETA DE VISTA PREVIA EN VIVO -->
                <div class="bg-white text-slate-800 rounded-2xl overflow-hidden shadow-md border border-slate-200">
                    <!-- Header con Portada u Olas -->
                    <div class="relative h-44 overflow-hidden bg-slate-900">
                        <img id="pv-imagen" src="{{ $imagenActual }}" alt="Portada Vista Previa" class="w-full h-full object-cover transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-brand-blue/90 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide uppercase">
                            Inducción
                        </span>

                        <div class="absolute bottom-3 left-3 right-3 text-white space-y-0.5">
                            <span class="text-[10px] font-bold text-cyan-300 uppercase tracking-wider">C.I. Piscícola New York</span>
                            <h3 id="pv-titulo" class="font-heading font-extrabold text-base leading-tight truncate">
                                {{ old('titulo') ?: 'Título de la inducción...' }}
                            </h3>
                        </div>
                    </div>

                    <!-- Cuerpo de la tarjeta -->
                    <div class="p-4 space-y-3 text-xs">
                        <p id="pv-descripcion" class="text-slate-600 line-clamp-2 leading-relaxed">
                            {{ old('descripcion') ?: 'Aquí se visualizará la descripción y objetivos de la inducción para los colaboradores.' }}
                        </p>

                        <!-- Píldoras de parámetros (como en capacitaciones) -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div class="bg-slate-50 p-2 rounded-xl border border-slate-200 flex items-center gap-2">
                                <span class="text-brand-blue text-sm"><i class="fa-solid fa-chalkboard-user"></i></span>
                                <div>
                                    <span class="block text-[10px] text-slate-400 font-medium">Modalidad</span>
                                    <span class="font-heading font-bold text-xs text-slate-700">Presencial + QR</span>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-xl border border-slate-200 flex items-center gap-2">
                                <span class="text-emerald-500 text-sm"><i class="fa-solid fa-signature"></i></span>
                                <div>
                                    <span class="block text-[10px] text-slate-400 font-medium">Asistencia</span>
                                    <span class="font-heading font-extrabold text-xs text-emerald-700">Firma Digital</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Recursos Multimedia en Vista Previa (equivalente a módulos de capacitaciones) -->
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-slate-800">Recursos Multimedia</span>
                                <span id="pv-recursos-count" class="text-brand-blue font-extrabold bg-brand-light px-2 py-0.5 rounded-full font-mono">
                                    0 Recursos
                                </span>
                            </div>
                            <div id="pv-recursos-list" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                <!-- Se llena dinámicamente mediante JS -->
                            </div>
                        </div>

                        <!-- Botón simulado del facilitador / proyección -->
                        <div class="pt-2">
                            <button type="button" class="w-full py-2.5 rounded-xl bg-brand-blue text-white font-heading font-bold text-xs shadow-xs pointer-events-none opacity-90 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-play"></i>
                                <span>Iniciar Inducción</span>
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 text-center italic">
                    <i class="fa-regular fa-lightbulb text-amber-400 mr-1"></i> Los cambios que realizas en el formulario se reflejan automáticamente aquí.
                </p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    (function () {
        // Elementos de la vista previa
        const pvImg = document.getElementById('pv-imagen');
        const pvImgThumb = document.getElementById('img-preview-thumb');
        const pvTitulo = document.getElementById('pv-titulo');
        const pvDesc = document.getElementById('pv-descripcion');
        const pvRecursosCount = document.getElementById('pv-recursos-count');
        const pvRecursosList = document.getElementById('pv-recursos-list');

        // Estado de Recursos
        let recursos = [];
        let contadorRecursos = 0;

        const TIPOS_RECURSO = {
            pptx: {
                label: 'Diapositivas PowerPoint (.pptx, .ppt)',
                badgeClass: 'bg-orange-50 text-orange-800 border-orange-200',
                iconClass: 'fa-solid fa-file-powerpoint text-orange-600',
                accept: '.ppt,.pptx,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation',
                shortLabel: 'PowerPoint',
                isFile: true
            },
            video: {
                label: 'Video en Vivo (YouTube / Vimeo / MP4)',
                badgeClass: 'bg-rose-50 text-rose-800 border-rose-200',
                iconClass: 'fa-brands fa-youtube text-rose-600',
                shortLabel: 'Video',
                isFile: false
            },
            pdf: {
                label: 'Documento PDF',
                badgeClass: 'bg-red-50 text-red-800 border-red-200',
                iconClass: 'fa-solid fa-file-pdf text-red-600',
                accept: '.pdf,application/pdf',
                shortLabel: 'PDF',
                isFile: true
            },
            imagen: {
                label: 'Imagen / Esquema / Infografía',
                badgeClass: 'bg-emerald-50 text-emerald-800 border-emerald-200',
                iconClass: 'fa-solid fa-image text-emerald-600',
                accept: 'image/jpeg,image/png,image/jpg,image/webp',
                shortLabel: 'Imagen',
                isFile: true
            }
        };

        // Función para actualizar la vista previa en tiempo real
        function actualizarVistaPrevia() {
            const inputTitulo = document.getElementById('titulo').value.trim();
            const inputDesc = document.getElementById('descripcion').value.trim();

            if (pvTitulo) {
                pvTitulo.textContent = inputTitulo || 'Título de la inducción...';
            }
            if (pvDesc) {
                pvDesc.textContent = inputDesc || 'Aquí se visualizará la descripción y objetivos de la inducción para los colaboradores.';
            }

            // Actualizar contador y lista de recursos en la vista previa
            if (pvRecursosCount) {
                pvRecursosCount.textContent = recursos.length + (recursos.length === 1 ? ' Recurso' : ' Recursos');
            }

            if (pvRecursosList) {
                pvRecursosList.innerHTML = '';
                if (recursos.length === 0) {
                    pvRecursosList.innerHTML = '<p class="text-[11px] text-slate-400 italic">No has agregado recursos aún.</p>';
                } else {
                    recursos.forEach((rec, idx) => {
                        const config = TIPOS_RECURSO[rec.tipo] || TIPOS_RECURSO.pptx;
                        const item = document.createElement('div');
                        item.className = 'p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-[11px]';
                        item.innerHTML = `
                            <div class="flex items-center gap-2 truncate">
                                <span class="w-4 h-4 rounded-full bg-brand-light text-brand-blue font-bold flex items-center justify-center text-[9px] shrink-0">${idx + 1}</span>
                                <i class="${config.iconClass} text-xs shrink-0"></i>
                                <span class="font-bold text-slate-700 truncate">${rec.titulo || (config.shortLabel + ' #' + (idx + 1))}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-semibold uppercase shrink-0">${config.shortLabel}</span>
                        `;
                        pvRecursosList.appendChild(item);
                    });
                }
            }
        }

        // Renderizado del formulario de recursos
        function renderizarFormRecursos() {
            const contenedor = document.getElementById('contenedor-recursos');
            const vacioMsg = document.getElementById('vacio-recursos-msg');
            const badgeTotal = document.getElementById('badge-total-recursos');

            if (badgeTotal) {
                badgeTotal.textContent = `${recursos.length} ${recursos.length === 1 ? 'recurso' : 'recursos'}`;
            }

            if (recursos.length === 0) {
                contenedor.innerHTML = '';
                vacioMsg.classList.remove('hidden');
                actualizarVistaPrevia();
                return;
            }

            vacioMsg.classList.add('hidden');

            contenedor.innerHTML = recursos.map((rec, index) => {
                const config = TIPOS_RECURSO[rec.tipo];
                return `
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 relative group transition-all" id="cardRecurso_${rec.id}">
                        <input type="hidden" name="recursos[${index}][tipo]" value="${rec.tipo}">

                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="recurso-pos-num w-6 h-6 rounded-lg bg-white border border-slate-200 flex items-center justify-center font-bold text-xs text-brand-blue shadow-2xs">
                                    ${index + 1}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border ${config.badgeClass}">
                                    <i class="${config.iconClass}"></i> ${config.shortLabel}
                                </span>
                            </div>

                            <!-- Controles de Reordenamiento: Selector Rápido de Posición y Flechas ▲ ▼ -->
                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl border border-slate-200 shadow-2xs">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Lugar:</span>
                                    <select onchange="window.cambiarPosicionRecurso(${rec.id}, parseInt(this.value))"
                                            class="recurso-pos-select bg-transparent text-[11px] font-bold text-slate-700 focus:outline-none cursor-pointer"
                                            title="Cambiar posición de este recurso">
                                        ${recursos.map((_, i) => `<option value="${i}" ${i === index ? 'selected' : ''}>Posición ${i + 1}</option>`).join('')}
                                    </select>
                                </div>

                                <button type="button" onclick="window.moverRecurso(${rec.id}, -1)" ${index === 0 ? 'disabled' : ''}
                                        class="btn-mover-arriba w-7 h-7 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed border border-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs"
                                        title="Subir de posición">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </button>

                                <button type="button" onclick="window.moverRecurso(${rec.id}, 1)" ${index === recursos.length - 1 ? 'disabled' : ''}
                                        class="btn-mover-abajo w-7 h-7 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed border border-slate-200 text-slate-600 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs"
                                        title="Bajar de posición">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </button>

                                <button type="button" onclick="window.eliminarRecursoInduccion(${rec.id})"
                                        class="text-slate-400 hover:text-rose-600 text-xs font-bold px-2 py-1 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer ml-1"
                                        title="Eliminar este recurso">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Título del Recurso -->
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Título o Etiqueta del Recurso
                            </label>
                            <input type="text"
                                   name="recursos[${index}][titulo]"
                                   value="${rec.titulo}"
                                   placeholder="Ej: Diapositivas de Seguridad o Video Introductorio"
                                   oninput="window.actualizarTituloRecurso(${rec.id}, this.value)"
                                   class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                        </div>

                        <!-- Campo de Archivo o URL según el Tipo -->
                        ${config.isFile ? `
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    Seleccionar Archivo ${config.shortLabel}
                                </label>
                                <input type="file"
                                       name="recursos[${index}][archivo]"
                                       accept="${config.accept}"
                                       onchange="window.actualizarArchivoRecurso(${rec.id}, this)"
                                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-slate-700 hover:file:bg-slate-100 file:border file:border-slate-200 file:cursor-pointer shadow-2xs">
                                ${rec.archivoNombre ? `
                                    <div class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Archivo cargado: <strong>${rec.archivoNombre}</strong>
                                    </div>
                                ` : `
                                    <p class="text-[10px] text-slate-400 mt-1">Formatos admitidos: ${config.accept}</p>
                                `}
                            </div>
                        ` : `
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    Enlace de Video (YouTube / Vimeo / MP4)
                                </label>
                                <div class="relative">
                                    <input type="url"
                                           name="recursos[${index}][url]"
                                           value="${rec.url || ''}"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           oninput="window.actualizarUrlRecurso(${rec.id}, this.value)"
                                           class="w-full pl-8 pr-3.5 py-2 bg-white border border-slate-300 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue">
                                    <i class="fa-brands fa-youtube absolute left-3 top-2.5 text-rose-500 text-xs"></i>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1">El video se proyectará directamente en la sala en formato iFrame.</p>
                            </div>
                        `}
                    </div>
                `;
            }).join('');

            actualizarVistaPrevia();
        }

        // Métodos expuestos globalmente
        window.agregarRecurso = function (tipo = 'pptx', tituloDefault = '') {
            contadorRecursos++;
            const id = contadorRecursos;
            const config = TIPOS_RECURSO[tipo] || TIPOS_RECURSO.pptx;

            recursos.push({
                id: id,
                tipo: tipo,
                titulo: tituloDefault || `${config.shortLabel} #${id}`,
                url: '',
                archivoNombre: ''
            });

            renderizarFormRecursos();
        };

        window.eliminarRecursoInduccion = function (id) {
            recursos = recursos.filter(r => r.id !== id);
            renderizarFormRecursos();
        };

        window.moverRecurso = function (id, direccion) {
            const idx = recursos.findIndex(r => r.id === id);
            if (idx < 0) return;
            const nuevoIdx = idx + direccion;
            if (nuevoIdx < 0 || nuevoIdx >= recursos.length) return;
            window.cambiarPosicionRecurso(id, nuevoIdx);
        };

        window.cambiarPosicionRecurso = function (id, nuevoIdx) {
            const idx = recursos.findIndex(r => r.id === id);
            if (idx < 0 || nuevoIdx < 0 || nuevoIdx >= recursos.length || idx === nuevoIdx) return;

            // 1. Guardar valores tipeados actualmente en los inputs
            recursos.forEach(r => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const titInput = card.querySelector('input[name*="[titulo]"]');
                    if (titInput) r.titulo = titInput.value;
                    const urlInput = card.querySelector('input[name*="[url]"]');
                    if (urlInput) r.url = urlInput.value;
                }
            });

            // 2. Reordenar el arreglo
            const [item] = recursos.splice(idx, 1);
            recursos.splice(nuevoIdx, 0, item);

            // 3. Reordenar los elementos en el DOM para conservar los archivos seleccionados en los input file
            const contenedor = document.getElementById('contenedor-recursos');
            recursos.forEach((r, i) => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    // Actualizar nombres de inputs para el envío correcto en PHP
                    const tipoInp = card.querySelector('input[name*="[tipo]"]');
                    if (tipoInp) tipoInp.name = `recursos[${i}][tipo]`;
                    const titInp = card.querySelector('input[name*="[titulo]"]');
                    if (titInp) titInp.name = `recursos[${i}][titulo]`;
                    const archInp = card.querySelector('input[name*="[archivo]"]');
                    if (archInp) archInp.name = `recursos[${i}][archivo]`;
                    const urlInp = card.querySelector('input[name*="[url]"]');
                    if (urlInp) urlInp.name = `recursos[${i}][url]`;

                    // Actualizar número visual y selector
                    const numEl = card.querySelector('.recurso-pos-num');
                    if (numEl) numEl.textContent = i + 1;

                    const sel = card.querySelector('.recurso-pos-select');
                    if (sel) {
                        sel.innerHTML = recursos.map((_, pos) => `<option value="${pos}" ${pos === i ? 'selected' : ''}>Posición ${pos + 1}</option>`).join('');
                    }

                    const btnUp = card.querySelector('.btn-mover-arriba');
                    if (btnUp) btnUp.disabled = (i === 0);

                    const btnDown = card.querySelector('.btn-mover-abajo');
                    if (btnDown) btnDown.disabled = (i === recursos.length - 1);

                    // Re-anexar al contenedor en el nuevo orden (preserva input file)
                    contenedor.appendChild(card);
                }
            });

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

        // Portada en vivo
        const inputPortada = document.getElementById('imagen_portada');
        if (inputPortada) {
            inputPortada.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (pvImg) pvImg.src = evt.target.result;
                        if (pvImgThumb) pvImgThumb.src = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Escuchadores de texto en tiempo real
        ['titulo', 'descripcion'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', actualizarVistaPrevia);
            }
        });

        // Inicializar con 1 presentación PowerPoint lista
        window.agregarRecurso('pptx', 'Diapositivas Principales de la Inducción');
        actualizarVistaPrevia();
    })();
</script>
@endpush
@endsection
