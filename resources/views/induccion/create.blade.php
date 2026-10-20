@extends('layouts.induccion')

@section('title', 'Crear Nueva Inducción')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('induccion.index') }}" class="text-xs text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Inducciones
                </a>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Crear Nueva Inducción</h1>
            <p class="text-xs text-slate-400">Define los contenidos, diapositivas y video que se proyectarán durante la sesión</p>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('induccion.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">

            <!-- Título y Tipo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="titulo" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        <i class="fa-solid fa-heading text-cyan-400 mr-1.5"></i> Título de la Inducción <span class="text-rose-400">*</span>
                    </label>
                    <input type="text"
                           name="titulo"
                           id="titulo"
                           value="{{ old('titulo') }}"
                           placeholder="Ej: Inducción General de Seguridad y Salud en el Trabajo (SST)"
                           required
                           class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-white text-sm focus:outline-none focus:border-cyan-400">
                    @error('titulo')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        <i class="fa-solid fa-tags text-cyan-400 mr-1.5"></i> Tipo de Inducción <span class="text-rose-400">*</span>
                    </label>
                    <select name="tipo" id="tipo" required
                            class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-white text-sm focus:outline-none focus:border-cyan-400">
                        <option value="GENERAL" @selected(old('tipo') == 'GENERAL')>General / Institucional</option>
                        <option value="SST" @selected(old('tipo') == 'SST')>Seguridad y Salud (SST)</option>
                        <option value="AMBIENTAL" @selected(old('tipo') == 'AMBIENTAL')>Gestión Ambiental</option>
                        <option value="PLANTA" @selected(old('tipo') == 'PLANTA')>Buenas Prácticas (BPM) / Planta</option>
                        <option value="VISITANTES" @selected(old('tipo') == 'VISITANTES')>Visitantes y Contratistas</option>
                    </select>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    <i class="fa-solid fa-align-left text-cyan-400 mr-1.5"></i> Descripción u Objetivos
                </label>
                <textarea name="descripcion"
                          id="descripcion"
                          rows="3"
                          placeholder="Describe brevemente los temas que se abordarán durante esta inducción..."
                          class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-white text-sm focus:outline-none focus:border-cyan-400">{{ old('descripcion') }}</textarea>
            </div>

            <!-- Material de Proyección: Diapositivas (PDF) y Video (iFrame) -->
            <div class="pt-4 border-t border-slate-800 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Diapositivas en PDF -->
                <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-file-pdf text-red-400 text-lg"></i>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                Diapositivas de Presentación (PDF)
                            </label>
                            <span class="text-[11px] text-slate-400">Sube el documento PDF para proyectar</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <input type="file"
                               name="archivo_presentacion"
                               id="archivo_presentacion"
                               accept=".pdf,application/pdf"
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 file:cursor-pointer">
                        <p class="text-[11px] text-slate-500 mt-2">Formato admitido: PDF (máx. 50 MB).</p>
                    </div>
                    @error('archivo_presentacion')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video para iFrame -->
                <div class="bg-slate-950/60 p-5 rounded-2xl border border-slate-800">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-brands fa-youtube text-rose-400 text-lg"></i>
                        <div>
                            <label for="video_url" class="block text-xs font-bold uppercase tracking-wider text-slate-200">
                                URL de Video (iFrame)
                            </label>
                            <span class="text-[11px] text-slate-400">Enlace de YouTube o Vimeo para reproducir</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <input type="url"
                               name="video_url"
                               id="video_url"
                               value="{{ old('video_url') }}"
                               placeholder="https://www.youtube.com/watch?v=..."
                               class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:border-cyan-400">
                        <p class="text-[11px] text-slate-500 mt-2">El video se cargará automáticamente en un reproductor iFrame interactivo.</p>
                    </div>
                    @error('video_url')
                        <p class="text-rose-400 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Sedes Convocadas -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    <i class="fa-solid fa-location-dot text-cyan-400 mr-1.5"></i> Sedes donde aplica esta Inducción
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($sedes as $s)
                        <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/80 border border-slate-700 hover:border-cyan-500/40 cursor-pointer transition-colors">
                            <input type="checkbox" name="sedes[]" value="{{ $s->id }}"
                                   @checked(in_array($s->id, old('sedes', [])))
                                   class="w-4 h-4 rounded text-cyan-500 focus:ring-cyan-400 bg-slate-900 border-slate-700">
                            <span class="text-xs text-white font-medium">{{ $s->nombre }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-[11px] text-slate-500 mt-2">Si no seleccionas ninguna, aplicará para todas las sedes institucionales.</p>
            </div>

            <!-- Áreas Convocadas -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    <i class="fa-solid fa-sitemap text-cyan-400 mr-1.5"></i> Áreas específicas convocadas (Opcional)
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 max-h-48 overflow-y-auto p-2 bg-slate-950/40 rounded-2xl border border-slate-800">
                    @foreach($areas as $a)
                        <label class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-800/60 cursor-pointer text-xs text-slate-300">
                            <input type="checkbox" name="areas[]" value="{{ $a->id }}"
                                   @checked(in_array($a->id, old('areas', [])))
                                   class="w-3.5 h-3.5 rounded text-cyan-500 focus:ring-cyan-400 bg-slate-900 border-slate-700">
                            <span class="truncate">{{ $a->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('induccion.index') }}"
               class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs uppercase tracking-wider transition-colors">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Guardar Inducción</span>
            </button>
        </div>

    </form>

</div>
@endsection
