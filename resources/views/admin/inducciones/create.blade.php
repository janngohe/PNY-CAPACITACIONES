@extends('layouts.admin')

@section('title', 'Crear Nueva Inducción · Panel Administrador')
@section('page_title', 'Crear Nueva Inducción')

@php
    $imagenActual = asset('images/Img-login.jpg');
@endphp

@section('content')
<style>
    /* Asegurar contraste absoluto y legible en todo el formulario */
    .induccion-form-input {
        color: #0f172a !important;
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
    }
    .induccion-form-input:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15) !important;
    }
    .induccion-form-input::placeholder {
        color: #94a3b8 !important;
    }
    .induccion-form-card {
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
    }
</style>

<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Banner Superior Institucional -->
    @include('admin._banner', [
        'insignia' => 'Gestión de Inducciones',
        'titulo' => 'Crear Nueva Inducción',
        'descripcion' => 'Diseña el material formativo presencial con diapositivas PowerPoint (.pptx), videos interactivos, documentos PDF e infografías para proyectar en sala con código QR de asistencia.',
        'icono' => 'fa-chalkboard-user',
    ])

    <!-- Barra de navegación rápida -->
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('admin.inducciones.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-cyan-800 hover:border-cyan-300 text-xs font-bold transition-all shadow-2xs">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>Volver a Inducciones Publicadas</span>
        </a>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Simulador en Tiempo Real Activo</span>
        </span>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-xs" role="alert">
            <p class="font-bold mb-1">Por favor verifica los siguientes puntos:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach (collect($errors->all())->unique() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Contenedor Dividido: Formulario (Izquierda) + Vista Previa en Vivo (Derecha) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: FORMULARIO DE CONFIGURACIÓN -->
        <div class="lg:col-span-7">
            <form id="form-induccion" method="POST" enctype="multipart/form-data"
                  action="{{ route('admin.inducciones.store') }}"
                  class="space-y-6">
                @csrf

                <!-- SECCIÓN 1: DATOS GENERALES -->
                <div class="induccion-form-card rounded-3xl border shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-900 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Información General
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Datos Principales de la Inducción</h2>
                        </div>
                        <span class="text-xl text-cyan-700 flex items-center justify-center w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-200 shadow-2xs">
                            <i class="fa-solid fa-pen-nib"></i>
                        </span>
                    </div>

                    <div class="space-y-5">
                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Título de la Inducción <span class="text-rose-600">*</span>
                            </label>
                            <input id="titulo"
                                   type="text"
                                   name="titulo"
                                   value="{{ old('titulo') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Ej: Inducción General de Seguridad, Calidad y Buenas Prácticas"
                                   class="induccion-form-input w-full px-4 py-3 rounded-2xl border text-sm font-semibold transition-all">
                            <p class="text-[11px] text-slate-500 font-medium mt-1">Nombre oficial visible en el portal del facilitador y en el certificado de asistencia.</p>
                        </div>

                        <!-- Modalidad -->
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Modalidad de Ejecución
                            </label>
                            <div class="px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-2">
                                <i class="fa-solid fa-qrcode text-cyan-600 text-sm"></i>
                                <span>Presencial con Asistencia QR y Firma Digital</span>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Descripción y Objetivos
                            </label>
                            <textarea id="descripcion"
                                      name="descripcion"
                                      rows="3"
                                      maxlength="3000"
                                      placeholder="Describe brevemente de qué trata esta inducción, los temas clave y las metas esperadas para los colaboradores..."
                                      class="induccion-form-input w-full px-4 py-3 rounded-2xl border text-sm transition-all resize-y leading-relaxed">{{ old('descripcion') }}</textarea>
                        </div>

                        <!-- Imagen de Portada -->
                        <div>
                            <label for="imagen_portada" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Imagen de Portada / Carátula <span class="text-slate-400 font-normal lowercase">(opcional · JPG, PNG, WEBP)</span>
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                <div class="w-20 h-14 rounded-xl bg-slate-200 overflow-hidden shrink-0 border border-slate-300 relative shadow-2xs">
                                    <img id="img-preview-thumb" src="{{ $imagenActual }}" alt="Vista previa miniatura" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input id="imagen_portada"
                                           type="file"
                                           name="imagen_portada"
                                           accept=".jpg,.jpeg,.png,.webp"
                                           class="block w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:bg-cyan-100 file:text-cyan-900 file:font-bold file:text-xs hover:file:bg-cyan-700 hover:file:text-white file:transition-colors file:cursor-pointer">
                                    <p class="text-[11px] text-slate-500 mt-1">Se proyectará como carátula de bienvenida en el televisor o proyector de la sala.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: RECURSOS MULTIMEDIA (PowerPoint, Videos, PDFs, Imágenes) -->
                <div class="induccion-form-card rounded-3xl border shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-900 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Material de Proyección
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Diapositivas y Recursos Multimedia</h2>
                            <p class="text-xs text-slate-500 mt-0.5">El facilitador podrá navegar secuencialmente por estos recursos en la pantalla gigante</p>
                        </div>
                        <span id="badge-total-recursos" class="text-xs font-bold px-3 py-1 rounded-full bg-cyan-100 text-cyan-900 self-start sm:self-center border border-cyan-200">
                            0 recursos
                        </span>
                    </div>

                    <!-- Botonera de Acción Rápida para Agregar Recursos -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                        <button type="button" onclick="agregarRecurso('pptx')"
                                class="p-3 rounded-2xl bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-950 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <span class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid fa-file-powerpoint"></i>
                            </span>
                            <span>+ PowerPoint (.pptx)</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('video')"
                                class="p-3 rounded-2xl bg-red-50 hover:bg-red-100 border border-red-200 text-red-950 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <span class="w-8 h-8 rounded-xl bg-red-600 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid fa-video"></i>
                            </span>
                            <span>+ Video (YouTube)</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('pdf')"
                                class="p-3 rounded-2xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-950 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid fa-file-pdf"></i>
                            </span>
                            <span>+ Documento PDF</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('imagen')"
                                class="p-3 rounded-2xl bg-cyan-50 hover:bg-cyan-100 border border-cyan-200 text-cyan-950 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <span class="w-8 h-8 rounded-xl bg-cyan-600 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid fa-image"></i>
                            </span>
                            <span>+ Infografía / Imagen</span>
                        </button>

                        <button type="button" onclick="agregarRecurso('texto')"
                                class="p-3 rounded-2xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-950 font-bold text-xs flex flex-col items-center gap-1.5 transition-all cursor-pointer shadow-2xs hover:scale-102">
                            <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm shadow-xs">
                                <i class="fa-solid fa-file-lines"></i>
                            </span>
                            <span>+ Texto / Guía</span>
                        </button>
                    </div>

                    <!-- Contenedor dinámico de tarjetas de recursos -->
                    <div id="contenedor-recursos" class="space-y-3.5">
                        <!-- Generado por JavaScript -->
                    </div>

                    <!-- Mensaje de lista vacía -->
                    <div id="vacio-recursos-msg" class="p-8 rounded-2xl border-2 border-dashed border-slate-300 text-center text-xs text-slate-500">
                        <i class="fa-solid fa-folder-open text-3xl text-slate-300 mb-2"></i>
                        <p class="font-bold text-slate-700">No has agregado ninguna diapositiva o recurso aún</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Usa los botones superiores para incorporar diapositivas de PowerPoint (.pptx), videos o documentos PDF.</p>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center justify-between gap-4 pt-2">
                    <a href="{{ route('admin.inducciones.index') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer border border-slate-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-8 py-3.5 rounded-2xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk text-sm"></i>
                        <span>Guardar y Crear Inducción</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- COLUMNA DERECHA: SIMULADOR DE SALA EN TIEMPO REAL -->
        <div class="lg:col-span-5 sticky top-24 space-y-4">
            <div class="bg-slate-900 text-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-800 space-y-4 relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>

                <div class="flex items-center justify-between border-b border-slate-800 pb-3 relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-cyan-300">
                            Simulador de Pantalla en Sala
                        </span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 text-[10px] font-mono">
                        En Vivo
                    </span>
                </div>

                <!-- Marco de la Pantalla del Proyector / Presentador -->
                <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 relative z-10 shadow-inner">
                    <!-- Portada / Pantalla Principal -->
                    <div class="relative aspect-video bg-slate-950 flex flex-col justify-end p-5 overflow-hidden group">
                        <img id="pv-imagen"
                             src="{{ $imagenActual }}"
                             alt="Portada Vista Previa"
                             class="absolute inset-0 w-full h-full object-cover opacity-40 group-hover:scale-105 transition-transform duration-700">

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

                        <div class="relative z-10 space-y-1.5">
                            <h3 id="pv-titulo" class="text-base sm:text-lg font-black text-white leading-tight font-heading">
                                Título de la inducción...
                            </h3>
                            <p id="pv-descripcion" class="text-[11px] text-slate-300 line-clamp-2 leading-relaxed">
                                Aquí se previsualizarán los objetivos y temas principales proyectados en la sala.
                            </p>
                        </div>
                    </div>

                    <!-- Barra de Navegación de Recursos (Simulador del Reproductor) -->
                    <div class="p-3 bg-slate-900 border-t border-slate-800">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                            <span class="font-bold text-slate-300">Recursos en Presentación:</span>
                            <span id="pv-total-recursos-badge" class="font-mono text-cyan-400">0 recursos</span>
                        </div>

                        <!-- Miniaturas de Diapositivas Similares al Visor Real -->
                        <div id="pv-recursos-list" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                            <div class="text-[11px] text-slate-500 text-center py-2">Sin recursos agregados</div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-cyan-400"></i> Código QR activo
                    </span>
                    <span class="font-mono text-emerald-400 text-[10px]">PIN Requerido</span>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    (function () {
        const TIPOS_RECURSO = {
            pptx: {
                label: 'Presentación PowerPoint (.pptx o .ppt)',
                shortLabel: 'PPTX',
                iconClass: 'fa-solid fa-file-powerpoint text-orange-500',
                badgeClass: 'bg-orange-100 text-orange-950 border-orange-300',
                placeholder: 'Ej: Diapositivas Generales de la Inducción',
                fileAccept: '.pptx,.ppt',
                fileHint: 'Sube un archivo de PowerPoint .pptx o .ppt'
            },
            video: {
                label: 'Video Multimedia (YouTube, Vimeo o URL directa)',
                shortLabel: 'VIDEO',
                iconClass: 'fa-solid fa-video text-red-500',
                badgeClass: 'bg-red-100 text-red-950 border-red-300',
                placeholder: 'Ej: Video Institucional de Bienvenida',
                urlHint: 'Pega el enlace de YouTube o Vimeo (ej: https://www.youtube.com/watch?v=...)'
            },
            pdf: {
                label: 'Documento PDF para Proyección',
                shortLabel: 'PDF',
                iconClass: 'fa-solid fa-file-pdf text-rose-500',
                badgeClass: 'bg-rose-100 text-rose-950 border-rose-300',
                placeholder: 'Ej: Manual de Convivencia y Políticas PDF',
                fileAccept: '.pdf',
                fileHint: 'Sube un documento en formato PDF para visualizar en sala'
            },
            imagen: {
                label: 'Infografía o Imagen Explicativa',
                shortLabel: 'IMAGEN',
                iconClass: 'fa-solid fa-image text-cyan-500',
                badgeClass: 'bg-cyan-100 text-cyan-950 border-cyan-300',
                placeholder: 'Ej: Organigrama o Mapa de Rutas de Evacuación',
                fileAccept: '.png,.jpg,.jpeg,.webp',
                fileHint: 'Sube una imagen o infografía de alta resolución'
            },
            texto: {
                label: 'Contenido Informativo / Texto Explicativo',
                shortLabel: 'TEXTO',
                iconClass: 'fa-solid fa-file-lines text-indigo-500',
                badgeClass: 'bg-indigo-100 text-indigo-950 border-indigo-300',
                placeholder: 'Ej: Normas Internas, Políticas o Temas Clave',
                textHint: 'Escribe el texto detallado que se proyectará como diapositiva en la sala'
            }
        };

        let recursos = [];
        let recursoContador = 0;

        const inputTitulo = document.getElementById('titulo');
        const inputDesc = document.getElementById('descripcion');
        const inputPortada = document.getElementById('imagen_portada');

        const pvTitulo = document.getElementById('pv-titulo');
        const pvDesc = document.getElementById('pv-descripcion');
        const pvImg = document.getElementById('pv-imagen');
        const pvThumb = document.getElementById('img-preview-thumb');
        const pvRecursosList = document.getElementById('pv-recursos-list');
        const pvTotalRecursos = document.getElementById('pv-total-recursos-badge');

        function escaparHtml(texto) {
            if (!texto) return '';
            return String(texto)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function actualizarVistaPrevia() {
            if (pvTitulo) pvTitulo.textContent = (inputTitulo && inputTitulo.value.trim()) || 'Título de la inducción...';
            if (pvDesc) pvDesc.textContent = (inputDesc && inputDesc.value.trim()) || 'Aquí se previsualizarán los objetivos y temas principales proyectados en la sala.';

            if (pvTotalRecursos) {
                pvTotalRecursos.textContent = `${recursos.length} ${recursos.length === 1 ? 'recurso' : 'recursos'}`;
            }

            if (pvRecursosList) {
                if (recursos.length === 0) {
                    pvRecursosList.innerHTML = `<div class="text-[11px] text-slate-500 text-center py-2">Sin recursos agregados</div>`;
                } else {
                    pvRecursosList.innerHTML = recursos.map((rec, idx) => {
                        const config = TIPOS_RECURSO[rec.tipo] || TIPOS_RECURSO.pptx;
                        const tit = rec.titulo || `${config.shortLabel} #${idx + 1}`;
                        return `
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950/70 border border-slate-800 text-[11px]">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-md bg-slate-800 text-[10px] font-mono flex items-center justify-center font-bold text-slate-300">
                                        ${idx + 1}
                                    </span>
                                    <i class="${config.iconClass} text-xs"></i>
                                    <span class="truncate font-medium text-slate-200">${escaparHtml(tit)}</span>
                                </div>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 shrink-0">
                                    ${config.shortLabel}
                                </span>
                            </div>
                        `;
                    }).join('');
                }
            }
        }

        window.agregarRecurso = function (tipo, tituloSugerido = '') {
            if (!TIPOS_RECURSO[tipo]) return;
            recursoContador++;
            recursos.push({
                id: recursoContador,
                tipo: tipo,
                titulo: tituloSugerido,
                url: '',
                texto: '',
                archivoNombre: ''
            });

            renderizarFormRecursos();
            actualizarVistaPrevia();
        };

        window.eliminarRecursoInduccion = function (id) {
            recursos = recursos.filter(r => r.id !== id);
            renderizarFormRecursos();
            actualizarVistaPrevia();
        };

        window.moverRecurso = function (id, delta) {
            const index = recursos.findIndex(r => r.id === id);
            if (index < 0) return;
            const nuevoIndex = index + delta;
            if (nuevoIndex < 0 || nuevoIndex >= recursos.length) return;

            // Conservar valores actuales de los inputs
            recursos.forEach(r => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const tit = card.querySelector('input[name*="[titulo]"]');
                    if (tit) r.titulo = tit.value;
                    const url = card.querySelector('input[name*="[url]"]');
                    if (url) r.url = url.value;
                    const txt = card.querySelector('textarea[name*="[texto]"]');
                    if (txt) r.texto = txt.value;
                }
            });

            const [item] = recursos.splice(index, 1);
            recursos.splice(nuevoIndex, 0, item);

            renderizarFormRecursos();
            actualizarVistaPrevia();
        };

        window.cambiarPosicionRecurso = function (id, nuevaPos) {
            const index = recursos.findIndex(r => r.id === id);
            if (index < 0 || nuevaPos < 0 || nuevaPos >= recursos.length || index === nuevaPos) return;

            recursos.forEach(r => {
                const card = document.getElementById('cardRecurso_' + r.id);
                if (card) {
                    const tit = card.querySelector('input[name*="[titulo]"]');
                    if (tit) r.titulo = tit.value;
                    const url = card.querySelector('input[name*="[url]"]');
                    if (url) r.url = url.value;
                    const txt = card.querySelector('textarea[name*="[texto]"]');
                    if (txt) r.texto = txt.value;
                }
            });

            const [item] = recursos.splice(index, 1);
            recursos.splice(nuevaPos, 0, item);

            renderizarFormRecursos();
            actualizarVistaPrevia();
        };

        window.actualizarTituloRecurso = function (id, valor) {
            const r = recursos.find(it => it.id === id);
            if (r) {
                r.titulo = valor;
                actualizarVistaPrevia();
            }
        };

        window.actualizarUrlRecurso = function (id, valor) {
            const r = recursos.find(it => it.id === id);
            if (r) {
                r.url = valor;
                actualizarVistaPrevia();
            }
        };

        window.actualizarTextoRecurso = function (id, valor) {
            const r = recursos.find(it => it.id === id);
            if (r) {
                r.texto = valor;
                actualizarVistaPrevia();
            }
        };

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
                return;
            }

            vacioMsg.classList.add('hidden');

            contenedor.innerHTML = recursos.map((rec, index) => {
                const config = TIPOS_RECURSO[rec.tipo];

                let inputEspecifico = '';
                if (rec.tipo === 'video') {
                    inputEspecifico = `
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                                Enlace del Video (YouTube o Vimeo) <span class="text-rose-600">*</span>
                            </label>
                            <input type="url"
                                   name="recursos[${index}][url]"
                                   value="${escaparHtml(rec.url)}"
                                   placeholder="https://www.youtube.com/watch?v=..."
                                   oninput="window.actualizarUrlRecurso(${rec.id}, this.value)"
                                   class="induccion-form-input w-full px-3.5 py-2.5 rounded-xl border text-xs font-semibold">
                            <p class="text-[10px] text-slate-500 mt-1">${config.urlHint}</p>
                        </div>
                    `;
                } else if (rec.tipo === 'texto') {
                    inputEspecifico = `
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                                Contenido de la Diapositiva de Texto <span class="text-rose-600">*</span>
                            </label>
                            <textarea name="recursos[${index}][texto]"
                                      rows="4"
                                      placeholder="Escribe aquí los lineamientos, temario, instrucciones o contenido a exponer..."
                                      oninput="window.actualizarTextoRecurso(${rec.id}, this.value)"
                                      class="induccion-form-input w-full px-3.5 py-2.5 rounded-xl border text-xs font-medium leading-relaxed">${escaparHtml(rec.texto || '')}</textarea>
                            <p class="text-[10px] text-slate-500 mt-1">${config.textHint}</p>
                        </div>
                    `;
                } else {
                    inputEspecifico = `
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                                Archivo a Proyectar <span class="text-rose-600">*</span>
                            </label>
                            <input type="file"
                                   name="recursos[${index}][archivo]"
                                   accept="${config.fileAccept}"
                                   class="block w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-white file:text-slate-800 file:font-bold file:text-xs hover:file:bg-cyan-50 border border-slate-300 rounded-xl p-1.5 bg-white cursor-pointer shadow-2xs">
                            <p class="text-[10px] text-slate-500 mt-1">${config.fileHint}</p>
                        </div>
                    `;
                }

                return `
                    <div class="p-4 rounded-2xl bg-slate-100/90 border border-slate-300 space-y-3 relative shadow-2xs" id="cardRecurso_${rec.id}">
                        <input type="hidden" name="recursos[${index}][tipo]" value="${rec.tipo}">

                        <div class="flex items-center justify-between gap-2 flex-wrap border-b border-slate-200/80 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="recurso-pos-num w-6 h-6 rounded-lg bg-cyan-700 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                    ${index + 1}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border ${config.badgeClass}">
                                    <i class="${config.iconClass}"></i> ${config.shortLabel}
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center gap-1 bg-white px-2 py-1 rounded-xl border border-slate-300 shadow-2xs">
                                    <span class="text-[10px] text-slate-500 font-bold uppercase">Lugar:</span>
                                    <select onchange="window.cambiarPosicionRecurso(${rec.id}, parseInt(this.value))"
                                            class="recurso-pos-select bg-transparent text-[11px] font-bold text-slate-800 focus:outline-none cursor-pointer">
                                        ${recursos.map((_, i) => `<option value="${i}" ${i === index ? 'selected' : ''}>Posición ${i + 1}</option>`).join('')}
                                    </select>
                                </div>

                                <button type="button" onclick="window.moverRecurso(${rec.id}, -1)" ${index === 0 ? 'disabled' : ''}
                                        class="btn-mover-arriba w-7 h-7 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed border border-slate-300 text-slate-700 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </button>

                                <button type="button" onclick="window.moverRecurso(${rec.id}, 1)" ${index === recursos.length - 1 ? 'disabled' : ''}
                                        class="btn-mover-abajo w-7 h-7 rounded-lg bg-white hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed border border-slate-300 text-slate-700 flex items-center justify-center text-xs transition-colors cursor-pointer shadow-2xs">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </button>

                                <button type="button" onclick="window.eliminarRecursoInduccion(${rec.id})"
                                        class="text-rose-600 hover:text-rose-800 text-xs font-bold px-2 py-1 rounded-lg hover:bg-rose-100/70 transition-colors cursor-pointer ml-1">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                                Título o Etiqueta de la Diapositiva
                            </label>
                            <input type="text"
                                   name="recursos[${index}][titulo]"
                                   value="${escaparHtml(rec.titulo)}"
                                   placeholder="Ej: Diapositiva 1 - Generalidades y Bienvenida"
                                   oninput="window.actualizarTituloRecurso(${rec.id}, this.value)"
                                   class="induccion-form-input w-full px-3.5 py-2 rounded-xl border text-xs font-semibold">
                        </div>

                        ${inputEspecifico}
                    </div>
                `;
            }).join('');
        }

        // Escucha de eventos para Live Preview
        if (inputTitulo) inputTitulo.addEventListener('input', actualizarVistaPrevia);
        if (inputDesc) inputDesc.addEventListener('input', actualizarVistaPrevia);

        if (inputPortada) {
            inputPortada.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (pvImg) pvImg.src = evt.target.result;
                        if (pvThumb) pvThumb.src = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Agregar diapositiva principal PPTX por defecto al abrir
        window.agregarRecurso('pptx', 'Diapositivas Principales de la Inducción');
        actualizarVistaPrevia();
    })();
</script>
@endsection
