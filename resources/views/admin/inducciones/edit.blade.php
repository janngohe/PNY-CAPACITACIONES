@extends('layouts.admin')

@section('title', 'Editar Inducción: ' . $induccion->titulo . ' · Panel Administrador')
@section('page_title', 'Editar Inducción')

@php
    $imagenActual = $induccion->imagen_portada_url;
    $recursosIniciales = !empty($induccion->recursos) && is_array($induccion->recursos)
        ? array_values($induccion->recursos)
        : [];

    // Fallback para inducciones creadas previamente sin array de recursos
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
        'insignia' => 'Modificación de Inducción',
        'titulo' => 'Editar: ' . $induccion->titulo,
        'descripcion' => 'Actualiza los contenidos multimedia, reordena diapositivas PPTX, añade videos interactivos o reemplaza la portada de proyección en sala.',
        'icono' => 'fa-pen-to-square',
    ])

    <!-- Barra de navegación rápida y estado -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inducciones.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-cyan-800 hover:border-cyan-300 text-xs font-bold transition-all shadow-2xs">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Volver a Inducciones Publicadas</span>
            </a>

            @if($induccion->estado)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Activa en Sala</span>
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs">
                    Inactiva (Oculta)
                </span>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('induccion.index') }}"
               target="_blank"
               class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-chalkboard-user text-cyan-400"></i>
                <span>Ver en Sala Facilitador</span>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Simulador en Tiempo Real</span>
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

        <!-- COLUMNA IZQUIERDA: FORMULARIO DE EDICIÓN -->
        <div class="lg:col-span-7 space-y-6">
            <form id="form-induccion" method="POST" enctype="multipart/form-data"
                  action="{{ route('admin.inducciones.update', $induccion->id) }}"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- SECCIÓN 1: DATOS GENERALES -->
                <div class="induccion-form-card rounded-3xl border shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-900 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Información General
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Datos Principales</h2>
                        </div>
                        <span class="text-xl text-cyan-700 flex items-center justify-center w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-200 shadow-2xs">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                    </div>

                    <div class="space-y-5">
                        <!-- Título -->
                        <div>
                            <label for="titulo" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Título de la Inducción <span class="text-rose-600">*</span>
                            </label>
                            <input type="text"
                                   id="titulo"
                                   name="titulo"
                                   value="{{ old('titulo', $induccion->titulo) }}"
                                   required
                                   maxlength="255"
                                   placeholder="Ej: Inducción General de Seguridad y Salud en el Trabajo"
                                   class="induccion-form-input w-full px-4 py-3 rounded-2xl border text-sm font-semibold transition-all">
                            <p class="text-[11px] text-slate-500 font-medium mt-1">Título principal proyectado a los colaboradores en la sala.</p>
                        </div>

                        <!-- Estado de Disponibilidad -->
                        <div>
                            <label for="estado" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                Estado de Disponibilidad
                            </label>
                            <select id="estado"
                                    name="estado"
                                    class="induccion-form-input w-full px-4 py-3 rounded-2xl border text-xs sm:text-sm font-bold transition-all cursor-pointer">
                                <option value="1" {{ old('estado', $induccion->estado ? '1' : '0') == '1' ? 'selected' : '' }}>🟢 Activa (Visible para facilitadores en sala)</option>
                                <option value="0" {{ old('estado', $induccion->estado ? '1' : '0') == '0' ? 'selected' : '' }}>⚪ Inactiva (Oculta temporalmente)</option>
                            </select>
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
                                      placeholder="Describe brevemente de qué trata esta inducción y las metas esperadas..."
                                      class="induccion-form-input w-full px-4 py-3 rounded-2xl border text-sm transition-all resize-y leading-relaxed">{{ old('descripcion', $induccion->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: PORTADA DE LA INDUCCIÓN -->
                <div class="induccion-form-card rounded-3xl border shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-900 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Imagen de Portada
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Carátula de Proyección</h2>
                        </div>
                        <span class="text-xl text-cyan-700 flex items-center justify-center w-10 h-10 rounded-2xl bg-cyan-50 border border-cyan-200 shadow-2xs">
                            <i class="fa-solid fa-image"></i>
                        </span>
                    </div>

                    <div class="space-y-4">
                        @if($induccion->imagen_portada)
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-4">
                                <img src="{{ $induccion->imagen_portada_url }}" alt="Portada actual" class="w-24 h-16 rounded-xl object-cover border border-slate-300 shadow-2xs shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-slate-900">Portada actual cargada</div>
                                    <div class="text-[11px] text-slate-500 truncate mt-0.5">{{ $induccion->imagen_portada }}</div>
                                    <label class="inline-flex items-center gap-2 mt-2 cursor-pointer text-xs text-rose-600 hover:text-rose-700 font-bold">
                                        <input type="checkbox" name="eliminar_portada" value="1" class="rounded text-rose-600 focus:ring-rose-500">
                                        <span>Quitar portada y utilizar logo institucional por defecto</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="imagen_portada" class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-1.5">
                                {{ $induccion->imagen_portada ? 'Reemplazar Imagen de Portada' : 'Subir Imagen de Portada' }} <span class="text-slate-400 font-normal lowercase">(JPG, PNG, WEBP)</span>
                            </label>
                            <input type="file"
                                   id="imagen_portada"
                                   name="imagen_portada"
                                   accept="image/png,image/jpeg,image/webp"
                                   class="block w-full text-xs text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-100 file:text-cyan-900 hover:file:bg-cyan-700 hover:file:text-white cursor-pointer border border-slate-300 rounded-2xl p-2 bg-white shadow-2xs">
                            <p class="text-[11px] text-slate-500 mt-1">Resolución recomendada: 1920x1080 o 1280x720 (Máx. 10 MB).</p>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: GESTOR DE RECURSOS MULTIMEDIA (SLIDES / PRESENTACIÓN) -->
                <div class="induccion-form-card rounded-3xl border shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-900 border border-cyan-200 font-extrabold text-[11px] uppercase tracking-wide">
                                Contenido Multimedia
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-slate-900 mt-1">Diapositivas y Recursos en Sala</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Reordena o añade diapositivas PPTX, videos de YouTube, PDFs o infografías</p>
                        </div>

                        <!-- Botones Rápidos para Agregar Recursos -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" onclick="agregarRecurso('pptx')"
                                    class="px-2.5 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-950 border border-orange-200 text-xs font-bold transition-all flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-file-powerpoint text-orange-600"></i> + PPTX
                            </button>
                            <button type="button" onclick="agregarRecurso('video')"
                                    class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-950 border border-red-200 text-xs font-bold transition-all flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-video text-red-600"></i> + Video
                            </button>
                            <button type="button" onclick="agregarRecurso('pdf')"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-950 border border-rose-200 text-xs font-bold transition-all flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> + PDF
                            </button>
                            <button type="button" onclick="agregarRecurso('imagen')"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-950 border border-cyan-200 text-xs font-bold transition-all flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-image text-cyan-600"></i> + Imagen
                            </button>
                            <button type="button" onclick="agregarRecurso('texto')"
                                    class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-950 border border-indigo-200 text-xs font-bold transition-all flex items-center gap-1 shadow-2xs">
                                <i class="fa-solid fa-file-lines text-indigo-600"></i> + Texto
                            </button>
                        </div>
                    </div>

                    <!-- Lista Dinámica de Recursos -->
                    <div id="contenedor-recursos" class="space-y-3.5">
                        <!-- Generado dinámicamente con JavaScript -->
                    </div>

                    <div id="sin-recursos-msg" class="hidden p-8 rounded-2xl border-2 border-dashed border-slate-300 text-center">
                        <i class="fa-solid fa-layer-group text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-bold text-slate-700">No hay diapositivas ni recursos agregados</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Usa los botones superiores para añadir presentaciones PPTX, videos de YouTube, PDFs o infografías.</p>
                    </div>
                </div>

                <!-- Botones de Acción del Formulario -->
                <div class="flex items-center justify-between gap-4 pt-2">
                    <a href="{{ route('admin.inducciones.index') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider transition-colors border border-slate-200">
                        Cancelar
                    </a>

                    <button type="submit"
                            class="px-6 py-3 rounded-2xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>

            @if($tieneRegistros ?? false)
                <!-- Inducción Protegida: No se puede eliminar porque cuenta con registros históricos -->
                <div class="bg-amber-50/80 rounded-3xl border border-amber-300 p-6 sm:p-7 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="text-xs font-black uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-amber-600"></i> Inducción Protegida
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Esta inducción no puede ser eliminada</h3>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-xl">
                                Cuenta con <strong class="text-amber-950 font-bold">{{ $totalSesiones }} {{ $totalSesiones === 1 ? 'sesión dictada' : 'sesiones dictadas' }}</strong> y <strong class="text-amber-950 font-bold">{{ $totalAsistencias }} {{ $totalAsistencias === 1 ? 'participante registrado' : 'participantes registrados' }}</strong>. Para proteger la evidencia histórica de asistencia y firmas, esta inducción no se puede borrar; puedes inactivarla para que no se proyecte en la sala de inducciones.
                            </p>
                        </div>
                        <form action="{{ route('admin.inducciones.toggle_estado', $induccion->id) }}" method="POST" class="shrink-0 self-start sm:self-center">
                            @csrf
                            @method('PATCH')
                            @if($induccion->estado)
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-2 cursor-pointer"
                                        title="Inactivar para que no se use en la sala">
                                    <i class="fa-solid fa-ban"></i>
                                    <span>Inactivar Inducción</span>
                                </button>
                            @else
                                <button type="submit"
                                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-2 cursor-pointer"
                                        title="Activar para habilitarla en la sala">
                                    <i class="fa-solid fa-check"></i>
                                    <span>Activar Inducción</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @else
                <!-- Zona de Peligro: Eliminar Inducción (Solo para inducciones recién creadas sin registros) -->
                <div class="bg-rose-50/70 rounded-3xl border border-rose-200 p-6 sm:p-7 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-black uppercase tracking-wider text-rose-700 flex items-center gap-1.5 mb-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Zona de Peligro
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Eliminar permanentemente esta inducción</h3>
                            <p class="text-xs text-slate-600 mt-0.5">Esta inducción no tiene sesiones ni participantes registrados. Se eliminará el registro y los archivos multimedia asociados.</p>
                        </div>
                        <button type="button" onclick="abrirModalEliminar()"
                                class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-xs flex items-center gap-2 self-start sm:self-center cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Eliminar Inducción</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- COLUMNA DERECHA: SIMULADOR DE SALA EN TIEMPO REAL -->
        <div class="lg:col-span-5 sticky top-24 space-y-4">
            <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl p-5 text-white overflow-hidden relative">
                <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-cyan-500/15 blur-3xl pointer-events-none"></div>

                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4 relative z-10">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-mono text-slate-300">Simulador de Sala de Inducción</span>
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

                        <div class="relative z-10 space-y-1.5">
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
                            <span class="font-bold text-slate-300">Recursos en Presentación:</span>
                            <span id="preview-total-recursos" class="font-mono text-cyan-400">0 recursos</span>
                        </div>

                        <!-- Miniaturas de Diapositivas Similares al Visor Real -->
                        <div id="preview-lista-recursos" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                            <!-- Items simulados -->
                        </div>
                    </div>
                </div>

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

@if(!($tieneRegistros ?? false))
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
        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
            Se eliminará permanentemente la inducción <strong>"{{ $induccion->titulo }}"</strong> y sus archivos multimedia asociados.
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
@endif

<script>
    (function () {
        const recursosIniciales = @json($recursosIniciales);
        let recursos = [];
        let contadorId = 0;

        const pvTitulo = document.getElementById('preview-titulo');
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
            const desc = document.getElementById('descripcion').value.trim();

            if (pvTitulo) pvTitulo.textContent = tit || 'Título de la Inducción';
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
                        else if (r.tipo === 'texto') { icono = 'fa-file-lines text-indigo-400'; etiqueta = 'TEXTO'; }

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
                texto: datosExistentes ? (datosExistentes.texto || '') : '',
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

            let icono = 'fa-file-powerpoint text-orange-600 bg-orange-100 border-orange-300';
            let tipoLabel = 'Presentación PowerPoint (.pptx)';
            if (r.tipo === 'video') {
                icono = 'fa-video text-red-600 bg-red-100 border-red-300';
                tipoLabel = 'Video Multimedia (YouTube, Vimeo o URL directa)';
            } else if (r.tipo === 'pdf') {
                icono = 'fa-file-pdf text-rose-600 bg-rose-100 border-rose-300';
                tipoLabel = 'Documento PDF para Proyección';
            } else if (r.tipo === 'imagen') {
                icono = 'fa-image text-cyan-600 bg-cyan-100 border-cyan-300';
                tipoLabel = 'Infografía o Imagen Explicativa';
            } else if (r.tipo === 'texto') {
                icono = 'fa-file-lines text-indigo-600 bg-indigo-100 border-indigo-300';
                tipoLabel = 'Contenido Informativo / Texto Explicativo';
            }

            const card = document.createElement('div');
            card.id = 'cardRecurso_' + r.id;
            card.className = 'p-4 rounded-2xl border border-slate-300 bg-slate-100/90 space-y-3 relative shadow-2xs';

            let campoContenidoHtml = '';
            if (r.tipo === 'video') {
                campoContenidoHtml = `
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                            URL del Video (YouTube, Vimeo, MP4) <span class="text-rose-600">*</span>
                        </label>
                        <input type="url"
                               name="recursos[${index}][url]"
                               value="${escaparHtml(r.url)}"
                               oninput="actualizarUrlRecurso(${r.id}, this.value)"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="induccion-form-input w-full px-3.5 py-2.5 rounded-xl border text-xs font-semibold">
                    </div>
                `;
            } else if (r.tipo === 'texto') {
                campoContenidoHtml = `
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                            Contenido de la Diapositiva de Texto <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="recursos[${index}][texto]"
                                  rows="4"
                                  oninput="actualizarTextoRecurso(${r.id}, this.value)"
                                  placeholder="Escribe aquí los lineamientos, temario, instrucciones o contenido a exponer..."
                                  class="induccion-form-input w-full px-3.5 py-2.5 rounded-xl border text-xs font-medium leading-relaxed">${escaparHtml(r.texto || '')}</textarea>
                        <p class="text-[10px] text-slate-500 mt-1">Escribe el texto detallado que se proyectará como diapositiva en la sala</p>
                    </div>
                `;
            } else {
                let accept = '.pptx,.ppt';
                let formatoInfo = 'PowerPoint .pptx o .ppt (Máx. 50 MB)';
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
                        <div class="mb-2 p-2.5 bg-emerald-100 border border-emerald-300 rounded-xl flex items-center justify-between text-xs text-emerald-950 font-bold shadow-2xs">
                            <span class="truncate flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-700"></i>
                                <span>Archivo conservado: <strong>${escaparHtml(r.archivoNombre || r.archivo)}</strong></span>
                            </span>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800">Cargado</span>
                        </div>
                    `;
                }

                campoContenidoHtml = `
                    <div>
                        ${archivoActualBadge}
                        <input type="hidden" name="recursos[${index}][archivo_actual]" value="${escaparHtml(r.archivo)}">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                            ${r.archivo ? 'Reemplazar Archivo (Opcional)' : 'Subir Archivo'} <span class="text-slate-500 font-normal lowercase">(${formatoInfo})</span>
                        </label>
                        <input type="file"
                               name="recursos[${index}][archivo]"
                               accept="${accept}"
                               onchange="actualizarArchivoRecurso(${r.id}, this)"
                               class="block w-full text-xs text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-white file:text-slate-900 hover:file:bg-slate-100 cursor-pointer border border-slate-300 rounded-xl p-1.5 bg-white shadow-2xs">
                    </div>
                `;
            }

            card.innerHTML = `
                <input type="hidden" name="recursos[${index}][tipo]" value="${r.tipo}">

                <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 pb-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="recurso-pos-num w-6 h-6 rounded-lg bg-cyan-700 text-white text-xs font-mono font-bold flex items-center justify-center shadow-2xs">
                            ${index + 1}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <span class="w-7 h-7 rounded-lg border flex items-center justify-center text-xs shadow-2xs ${icono}">
                                <i class="fa-solid ${r.tipo === 'video' ? 'fa-video' : (r.tipo === 'pptx' ? 'fa-file-powerpoint' : (r.tipo === 'pdf' ? 'fa-file-pdf' : (r.tipo === 'texto' ? 'fa-file-lines' : 'fa-image')))}"></i>
                            </span>
                            <span class="text-xs font-extrabold text-slate-900">${tipoLabel}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" onclick="moverRecursoRelativo(${r.id}, -1)" title="Subir orden"
                                class="btn-mover-arriba p-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors disabled:opacity-30 disabled:pointer-events-none shadow-2xs cursor-pointer">
                            <i class="fa-solid fa-chevron-up text-xs"></i>
                        </button>
                        <button type="button" onclick="moverRecursoRelativo(${r.id}, 1)" title="Bajar orden"
                                class="btn-mover-abajo p-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors disabled:opacity-30 disabled:pointer-events-none shadow-2xs cursor-pointer">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>
                        <button type="button" onclick="eliminarRecurso(${r.id})" title="Eliminar recurso"
                                class="p-1.5 rounded-lg bg-white border border-rose-200 text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition-colors ml-1 shadow-2xs cursor-pointer">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-8">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                            Título / Nombre de la Diapositiva
                        </label>
                        <input type="text"
                               name="recursos[${index}][titulo]"
                               value="${escaparHtml(r.titulo)}"
                               oninput="actualizarTituloRecurso(${r.id}, this.value)"
                               placeholder="Ej: Diapositiva 1 - Presentación General"
                               class="induccion-form-input w-full px-3.5 py-2.5 rounded-xl border text-xs font-semibold">
                    </div>

                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-800 mb-1">
                            Posición en Proyección
                        </label>
                        <select onchange="cambiarPosicionRecurso(${r.id}, parseInt(this.value))"
                                class="recurso-pos-select induccion-form-input w-full px-3 py-2.5 rounded-xl border text-xs font-bold cursor-pointer">
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

                    const txtInp = card.querySelector('textarea[name*="[texto]"]');
                    if (txtInp) txtInp.name = `recursos[${i}][texto]`;

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
                    const txtInput = card.querySelector('textarea[name*="[texto]"]');
                    if (txtInput) r.texto = txtInput.value;
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

        window.actualizarTextoRecurso = function (id, valor) {
            const item = recursos.find(r => r.id === id);
            if (item) {
                item.texto = valor;
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

        const inputPortada = document.getElementById('imagen_portada');
        if (inputPortada) {
            inputPortada.addEventListener('change', function (e) {
                const file = e.target.files && e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (pvImg) pvImg.src = evt.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        ['titulo', 'descripcion'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', actualizarVistaPrevia);
        });

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
        const modal = document.getElementById('modalEliminarInduccion');
        if (modal) modal.classList.remove('hidden');
    }

    function cerrarModalEliminar() {
        const modal = document.getElementById('modalEliminarInduccion');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection
