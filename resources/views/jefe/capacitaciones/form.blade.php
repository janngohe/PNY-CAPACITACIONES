@extends('layouts.' . ($panel ?? 'jefe'))

@php
    $editando = (bool) $capacitacion;
    $input = 'w-full px-4 py-2.5 rounded-xl border border-line bg-white text-xs sm:text-[13px] font-sans text-brand-dark placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all';
    $fechaDisp = old('fecha_disponibilidad', $capacitacion?->fecha_disponibilidad?->format('Y-m-d'));
    $fechaLim = old('fecha_limite', $capacitacion?->fecha_limite?->format('Y-m-d'));
    $tieneFechaFin = old('tiene_fecha_fin', !empty($fechaLim) ? 'si' : 'no');
    $imagenActual = $capacitacion?->ruta_imagen ? asset($capacitacion->ruta_imagen) : asset('images/Img-login.jpg');
@endphp

@section('title', $editando ? 'Editar Capacitación' : 'Crear Capacitación')
@section('page_title', $editando ? 'Editar Capacitación' : 'Estudio de Creación de Capacitaciones')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- ========================================================
         BANNER SUPERIOR CON INDICADOR DE PASOS Y OLA MARINA
         ======================================================== -->
    <div class="relative rounded-3xl bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white p-6 sm:p-8 overflow-hidden shadow-lg border border-brand-blue/20">
        <div class="absolute -right-10 -top-10 w-64 h-64 rounded-full bg-brand-sky/15 blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-16 w-80 h-80 rounded-full bg-brand-blue/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-brand-sky">
                    <span class="w-2 h-2 rounded-full bg-brand-sky animate-ping"></span>
                    <span id="badge-paso-actual">Paso 1 de 3: Empezando...</span>
                </div>

                <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-white tracking-tight">
                    {{ $editando ? 'Editar: ' . $capacitacion->titulo : 'Crear Nueva Capacitación o Inducción' }}
                </h1>

                <p id="subtitulo-paso-actual" class="text-xs sm:text-sm text-slate-200/90 max-w-2xl leading-relaxed">
                    Empezando: Ingresa el título, portada, duración e información general.
                </p>
            </div>

            <!-- Progreso de Pasos (Step Bar) -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 md:w-80 shrink-0 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold text-white">
                    <span id="progreso-texto">Empezando (33%)</span>
                    <span id="progreso-porcentaje" class="text-brand-sky">Paso 1/3</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2.5 overflow-hidden">
                    <div id="progreso-barra" class="bg-gradient-to-r from-cyan-300 to-brand-sky h-full rounded-full transition-all duration-500 ease-out" style="width: 33.33%"></div>
                </div>
                <div class="grid grid-cols-3 text-[10px] font-semibold text-slate-300 text-center pt-1">
                    <span id="label-step-1" class="text-white font-bold">1. Inicio</span>
                    <span id="label-step-2" class="opacity-70">2. Módulos</span>
                    <span id="label-step-3" class="opacity-70">3. Publicar</span>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none opacity-30">
            <svg class="block w-full h-4 text-white" viewBox="0 0 1200 40" preserveAspectRatio="none">
                <path d="M0,0 C150,35 350,10 500,25 C650,40 850,5 1000,20 C1100,30 1160,15 1200,25 L1200,40 L0,40 Z" fill="currentColor"/>
            </svg>
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

    <!-- ========================================================
         CONTENEDOR SPLIT: FORMULARIO MULTIPASO + VISTA PREVIA EN VIVO
         ======================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- COLUMNA IZQUIERDA: FORMULARIO POR PASOS (7 cols en lg) -->
        <div class="lg:col-span-7">
            <form id="form-capacitacion" method="POST" enctype="multipart/form-data"
                  action="{{ $editando ? route(($panel ?? 'jefe') . '.capacitaciones.update', $capacitacion) : route(($panel ?? 'jefe') . '.capacitaciones.store') }}"
                  class="space-y-6">
                @csrf
                @if ($editando) @method('PUT') @endif

                <!-- ================= PASO 1: EMPEZANDO... ================= -->
                <div id="seccion-paso-1" class="bg-white rounded-3xl border border-line/80 shadow-xs p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-line/60 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-brand-light text-brand-blue font-extrabold text-[11px] uppercase tracking-wide">
                                Paso 1 · Empezando...
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark mt-1">Datos Generales de la Capacitación</h2>
                        </div>
                        <span class="text-xl text-brand-blue flex items-center justify-center w-10 h-10 rounded-xl bg-brand-light">
                            <i class="fa-solid fa-rocket"></i>
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="titulo" class="block text-xs font-bold text-brand-dark mb-1.5">
                                Título de la capacitación <span class="text-red-500">*</span>
                            </label>
                            <input id="titulo" type="text" name="titulo" value="{{ old('titulo', $capacitacion?->titulo) }}" required maxlength="255"
                                   placeholder="Ej: Inducción en Bioseguridad y Manejo Sanitario" class="{{ $input }}">
                        </div>

                        <div>
                            <label for="descripcion" class="block text-xs font-bold text-brand-dark mb-1.5">
                                Descripción general <span class="text-red-500">*</span>
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="3" required maxlength="5000"
                                      placeholder="Explica el objetivo principal, temas clave y a quién va dirigida esta formación..." class="{{ $input }} resize-y">{{ old('descripcion', $capacitacion?->descripcion) }}</textarea>
                        </div>

                        <div>
                            <label for="imagen" class="block text-xs font-bold text-brand-dark mb-1.5">
                                Imagen de portada <span class="font-normal text-muted">(opcional · JPG, PNG o WEBP)</span>
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-slate-50 p-3.5 rounded-2xl border border-line/60">
                                <div class="w-20 h-14 rounded-xl bg-slate-200 overflow-hidden shrink-0 border border-line relative shadow-2xs">
                                    <img id="img-preview-thumb" src="{{ $imagenActual }}" alt="Vista previa miniatura" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <input id="imagen" type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp"
                                           class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:bg-brand-light file:text-brand-blue file:font-bold file:text-xs hover:file:bg-brand-blue hover:file:text-white file:transition-colors file:cursor-pointer">
                                    <p class="text-[11px] text-muted mt-1">Se previsualizará de inmediato en la tarjeta lateral.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label for="duracion_estimada" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Duración estimada (horas)
                                </label>
                                <input id="duracion_estimada" type="number" name="duracion_estimada" value="{{ old('duracion_estimada', $capacitacion?->duracion_estimada) }}" min="1" max="1000" placeholder="Ej: 4" class="{{ $input }}">
                            </div>

                            <div>
                                <label for="incentivo" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Incentivo o beneficio <span class="font-normal text-muted">(opcional)</span>
                                </label>
                                <input id="incentivo" type="text" name="incentivo" value="{{ old('incentivo', $capacitacion?->incentivo) }}" maxlength="2000" placeholder="Ej: Certificado oficial + Bono" class="{{ $input }}">
                            </div>

                            <div>
                                <label for="fecha_disponibilidad" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Disponible desde <span class="font-normal text-muted">(opcional)</span>
                                </label>
                                <input id="fecha_disponibilidad" type="date" name="fecha_disponibilidad" value="{{ $fechaDisp }}" class="{{ $input }}">
                            </div>

                            <div class="rounded-2xl bg-cyan-50/70 border border-cyan-200/70 p-3.5 flex items-start gap-2.5">
                                <span class="text-brand-blue text-xs mt-0.5 shrink-0"><i class="fa-solid fa-circle-info"></i></span>
                                <div class="text-[11px] text-slate-600 leading-relaxed">
                                    <strong class="text-brand-dark font-heading">Evaluación de la capacitación:</strong> El porcentaje mínimo de aprobación y los intentos de examen se configurarán directamente al crear la evaluación correspondiente.
                                </div>
                            </div>

                            <!-- Interruptor opcional: ¿Tiene fecha de fin? -->
                            <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 border border-line/80 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-brand-dark">
                                            ¿Esta capacitación tiene una fecha de fin / límite?
                                        </label>
                                        <p class="text-[11px] text-muted">
                                            Selecciona si la capacitación permanecerá indefinida o si tendrá una fecha de cierre obligatoria.
                                        </p>
                                    </div>
                                    <div class="inline-flex p-1 bg-white rounded-xl border border-line shadow-2xs shrink-0" role="radiogroup">
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="tiene_fecha_fin" value="no" class="sr-only peer" @checked($tieneFechaFin === 'no') onchange="toggleFechaFin(this.value)">
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 peer-checked:bg-brand-light peer-checked:text-brand-blue peer-checked:border peer-checked:border-brand-blue/30 transition-all">
                                                <i class="fa-solid fa-infinity text-[11px]"></i> No (Indefinida)
                                            </span>
                                        </label>
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="tiene_fecha_fin" value="si" class="sr-only peer" @checked($tieneFechaFin === 'si') onchange="toggleFechaFin(this.value)">
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 peer-checked:bg-brand-blue peer-checked:text-white transition-all">
                                                <i class="fa-regular fa-calendar-check text-[11px]"></i> Sí (Con fecha fin)
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div id="contenedor-fecha-limite" class="{{ $tieneFechaFin === 'si' ? '' : 'hidden' }} pt-2 border-t border-line/60">
                                    <label for="fecha_limite" class="block text-xs font-bold text-brand-dark mb-1.5">
                                        Fecha límite de finalización <span class="text-red-500">*</span>
                                    </label>
                                    <input id="fecha_limite" type="date" name="fecha_limite" value="{{ $fechaLim }}" class="{{ $input }}" {{ $tieneFechaFin === 'si' ? '' : 'disabled' }}>
                                    <p class="text-[11px] text-muted mt-1">Los colaboradores no podrán acceder o enviar evaluaciones luego de este plazo.</p>
                                    @error('fecha_limite')
                                        <p class="text-xs text-red-600 mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-line/60">
                        <a href="{{ route(($panel ?? 'jefe') . '.capacitaciones.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors">
                            Cancelar
                        </a>
                        <div class="flex items-center gap-2">
                            <button type="submit" name="accion" value="borrador"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs border border-line transition-all cursor-pointer"
                                    title="Guardar los datos actuales como borrador">
                                <i class="fa-solid fa-file-pen text-amber-600 text-xs"></i>
                                <span>Guardar Borrador</span>
                            </button>
                            <button type="button" onclick="irAPaso(2)"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                <span>Siguiente: Módulos y Recursos</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ================= PASO 2: MÓDULOS Y RECURSOS (ASISTIDO PASO A PASO) ================= -->
                <div id="seccion-paso-2" class="bg-white rounded-3xl border border-line/80 shadow-xs p-6 sm:p-8 space-y-6 hidden">
                    <div class="flex items-center justify-between border-b border-line/60 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-cyan-50 text-brand-blue font-extrabold text-[11px] uppercase tracking-wide border border-cyan-100">
                                Paso 2 · Módulos y Recursos
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark mt-1">Estructura del Contenido Educativo</h2>
                        </div>
                        <span class="text-xl text-brand-blue flex items-center justify-center w-10 h-10 rounded-xl bg-cyan-50">
                            <i class="fa-solid fa-book-open"></i>
                        </span>
                    </div>

                    <!-- Asistente Superior: Pestañas de progreso entre módulos -->
                    <div class="bg-slate-50/90 p-4 rounded-2xl border border-line/80 space-y-3 shadow-2xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-line/50 pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-brand-blue animate-pulse"></span>
                                <span class="text-xs font-bold text-brand-dark uppercase tracking-wider font-heading">
                                    Navegación Asistida de Módulos
                                </span>
                                <span class="text-[11px] text-muted hidden sm:inline">· Vista limpia individual</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <span class="text-muted">Total módulos:</span>
                                <span id="badge-contador-modulos" class="px-2.5 py-0.5 rounded-full bg-brand-light text-brand-blue font-extrabold text-[11px]">
                                    1
                                </span>
                            </div>
                        </div>

                        <!-- Píldoras / Tabs de módulos -->
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 pt-0.5 scrollbar-thin" id="nav-tabs-modulos">
                            <!-- Generado dinámicamente con JS -->
                        </div>
                    </div>

                    <!-- Banner de Módulo Actual en Edición -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-gradient-to-r from-sky-50/80 via-blue-50/50 to-white border border-brand-blue/20">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-brand-blue text-white flex items-center justify-center font-heading font-extrabold text-sm shrink-0 shadow-2xs">
                                <span id="modulo-asistido-num-badge">1</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-extrabold uppercase tracking-wide text-brand-blue">
                                        Editando Módulo <span id="modulo-asistido-actual-num">1</span> de <span id="modulo-asistido-total-num">1</span>
                                    </span>
                                    <span id="modulo-asistido-estado-chip" class="text-[10px] px-2 py-0.5 rounded-md bg-white border border-blue-200 text-slate-600 font-bold">
                                        En edición
                                    </span>
                                </div>
                                <p id="modulo-asistido-titulo-resumen" class="text-xs font-bold text-brand-dark truncate mt-0.5">
                                    (Nuevo módulo sin título)
                                </p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 italic shrink-0 hidden md:block">
                            <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Puedes regresar y editarlo cuando gustes.
                        </p>
                    </div>

                    <!-- Contenedor con todos los módulos (los inactivos se ocultan con 'hidden') -->
                    <div id="lista-modulos" class="space-y-5">
                        @foreach ($modulosForm as $i => $modulo)
                            @include('jefe.capacitaciones._modulo', ['i' => $i, 'modulo' => $modulo, 'input' => $input, 'tiposContenido' => $tiposContenido])
                        @endforeach
                    </div>

                    <!-- Barra Asistida de Controles (Anterior, Siguiente, Crear Nuevo Módulo) -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-line/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <button type="button" id="btn-asistente-anterior"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-heading font-bold text-xs border border-line transition-all cursor-pointer shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-arrow-left text-[11px]"></i>
                            <span>Módulo Anterior</span>
                        </button>

                        <div class="text-center font-heading text-xs text-slate-500 font-medium">
                            Progreso: <strong class="text-brand-dark" id="txt-progreso-modulo-asistido">Módulo 1 de 1</strong>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Botón para avanzar si está revisando un módulo previo -->
                            <button type="button" id="btn-asistente-siguiente"
                                    class="hidden inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-heading font-bold text-xs transition-all cursor-pointer shadow-2xs">
                                <span>Siguiente Módulo</span>
                                <i class="fa-solid fa-arrow-right text-[11px]"></i>
                            </button>

                            <!-- Botón para continuar a un nuevo módulo limpio -->
                            <button type="button" id="btn-asistente-crear-siguiente"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs transition-all cursor-pointer shadow-xs hover:-translate-y-0.5">
                                <i class="fa-solid fa-plus text-[11px]"></i>
                                <span>+ Guardar y Siguiente Módulo</span>
                            </button>
                        </div>
                    </div>

                    <!-- Botón original preservado invisible para compatibilidad -->
                    <button type="button" id="btn-add-modulo" class="hidden"></button>

                    <!-- Navegación General entre Pasos -->
                    <div class="flex items-center justify-between pt-4 border-t border-line/60">
                        <button type="button" onclick="irAPaso(1)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                            <span>Volver a Datos Generales</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="submit" name="accion" value="borrador"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs border border-line transition-all cursor-pointer"
                                    title="Guardar avances en borrador">
                                <i class="fa-solid fa-file-pen text-amber-600 text-xs"></i>
                                <span>Guardar Borrador</span>
                            </button>
                            <button type="button" onclick="irAPaso(3)"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                                <span>Siguiente: Ya Casi Terminas</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ================= PASO 3: YA CASI TERMINAS... ================= -->
                <div id="seccion-paso-3" class="bg-white rounded-3xl border border-line/80 shadow-xs p-6 sm:p-8 space-y-6 hidden">
                    <div class="flex items-center justify-between border-b border-line/60 pb-4">
                        <div>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[11px] uppercase tracking-wide border border-emerald-200">
                                Paso 3 · ¡Ya casi terminas!
                            </span>
                            <h2 class="font-heading font-extrabold text-lg sm:text-xl text-brand-dark mt-1">Revisión Final y Publicación</h2>
                        </div>
                        <span class="text-xl text-emerald-600 flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50">
                            <i class="fa-solid fa-trophy"></i>
                        </span>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-50 via-teal-50/40 to-blue-50/30 p-5 rounded-2xl border border-emerald-200/80 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-bold text-sm text-emerald-950">¡Tu capacitación está lista para publicarse!</h3>
                                <p class="text-xs text-emerald-800">Verifica que el alcance y los módulos mostrados en la vista previa lateral sean correctos.</p>
                            </div>
                        </div>

                        <ul class="text-xs space-y-2 text-slate-700 pt-2 border-t border-emerald-200/60">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs shrink-0"></i>
                                <span>Se habilitará automáticamente para los colaboradores del área <strong>{{ $usuario->area->nombre ?? 'Tu Área' }}</strong>.</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs shrink-0"></i>
                                <span>Los empleados podrán registrar su avance por cada módulo completado.</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs shrink-0"></i>
                                <span>Podrás crear la evaluación o cuestionario final inmediatamente después de guardar.</span>
                            </li>
                        </ul>
                    </div>

                    @isset($areas)
                        @php $areasSel = collect(old('areas', $areasSeleccionadas ?? []))->map(fn ($v) => (int) $v)->all(); @endphp
                        <div class="space-y-3 pt-2" id="bloque-areas">
                            <h4 class="font-heading font-bold text-xs text-brand-dark uppercase tracking-wider">Áreas que podrán realizar esta capacitación</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach ($areas as $area)
                                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-line bg-slate-50 hover:bg-brand-light/60 cursor-pointer transition-colors">
                                        <input type="checkbox" name="areas[]" value="{{ $area->id }}" {{ in_array($area->id, $areasSel, true) ? 'checked' : '' }}
                                               class="rounded text-brand-blue focus:ring-brand-blue">
                                        <span class="text-xs font-bold text-brand-dark">{{ $area->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('areas') <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endisset

                    <div class="space-y-3 pt-2">
                        <h4 class="font-heading font-bold text-xs text-brand-dark uppercase tracking-wider">Estado inicial de publicación</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start p-3.5 rounded-2xl border border-line bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                                <input type="radio" name="estado_publicacion" value="1" @checked(! $capacitacion || (bool)$capacitacion->estado) class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-brand-dark">Publicar Inmediatamente</span>
                                    <span class="block text-[11px] text-muted">Visible desde hoy en el catálogo de los colaboradores.</span>
                                </div>
                            </label>
                            <label class="relative flex items-start p-3.5 rounded-2xl border border-line bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                                <input type="radio" name="estado_publicacion" value="0" @checked($capacitacion && ! (bool)$capacitacion->estado) class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-brand-dark">Guardar como Borrador</span>
                                    <span class="block text-[11px] text-muted">Se guarda en tu bandeja de borradores sin ser pública.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-line/60">
                        <button type="button" onclick="irAPaso(2)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                            <span>Volver a Módulos</span>
                        </button>
                        <div class="flex items-center gap-2.5">
                            <button type="submit" name="accion" value="borrador"
                                    class="inline-flex items-center gap-1.5 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs sm:text-sm border border-line transition-all cursor-pointer">
                                <i class="fa-solid fa-file-pen text-amber-600 text-xs"></i>
                                <span>Guardar en Borradores</span>
                            </button>
                            <button type="submit" name="accion" value="publicar" id="btn-guardar-capacitacion"
                                    class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-gradient-to-r from-brand-blue via-brand-deep to-brand-dark hover:brightness-110 text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                                <i class="fa-solid fa-paper-plane mr-0.5"></i>
                                <span>{{ $editando ? 'Guardar Cambios' : 'Publicar Capacitación' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- COLUMNA DERECHA: VISTA PREVIA LATERAL EN TIEMPO REAL (5 cols en lg, sticky) -->
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
                        Vista del Empleado
                    </span>
                </div>

                <!-- TARJETA DE VISTA PREVIA EN VIVO -->
                <div class="bg-white text-brand-dark rounded-2xl overflow-hidden shadow-md border border-slate-200">
                    <!-- Header con Portada u Olas -->
                    <div class="relative h-44 overflow-hidden bg-brand-dark">
                        <img id="pv-imagen" src="{{ $imagenActual }}" alt="Portada Vista Previa" class="w-full h-full object-cover transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/80 via-brand-dark/20 to-transparent"></div>

                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-brand-blue/90 backdrop-blur-md text-white text-[10px] font-extrabold tracking-wide uppercase">
                            @isset($areas) Capacitación Institucional @else Área: {{ $usuario->area->nombre ?? 'Mi Área' }} @endisset
                        </span>

                        <div class="absolute bottom-3 left-3 right-3 text-white space-y-0.5">
                            <span class="text-[10px] font-bold text-brand-sky uppercase tracking-wider">Capacitación Institucional</span>
                            <h3 id="pv-titulo" class="font-heading font-extrabold text-base leading-tight truncate">
                                {{ old('titulo', $capacitacion?->titulo) ?: 'Título de la capacitación...' }}
                            </h3>
                        </div>
                    </div>

                    <!-- Cuerpo de la tarjeta -->
                    <div class="p-4 space-y-3 text-xs">
                        <p id="pv-descripcion" class="text-slate-600 line-clamp-2 leading-relaxed">
                            {{ old('descripcion', $capacitacion?->descripcion) ?: 'Aquí se visualizará la descripción y objetivos de la capacitación para los empleados de tu área.' }}
                        </p>

                        <!-- Píldoras de parámetros -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div class="bg-slate-50 p-2 rounded-xl border border-line/60 flex items-center gap-2">
                                <span class="text-brand-blue text-sm"><i class="fa-regular fa-clock"></i></span>
                                <div>
                                    <span class="block text-[10px] text-muted font-medium">Duración</span>
                                    <span id="pv-duracion" class="font-heading font-bold text-xs text-slate-700">
                                        {{ old('duracion_estimada', $capacitacion?->duracion_estimada) ? old('duracion_estimada', $capacitacion?->duracion_estimada) . ' hrs' : 'Por definir' }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-xl border border-line/60 flex items-center gap-2">
                                <span class="text-amber-600 text-sm"><i class="fa-solid fa-gift"></i></span>
                                <div>
                                    <span class="block text-[10px] text-muted font-medium">Incentivo</span>
                                    <span id="pv-incentivo" class="font-heading font-bold text-xs text-amber-700 truncate max-w-[90px]" title="{{ old('incentivo', $capacitacion?->incentivo) }}">
                                        {{ old('incentivo', $capacitacion?->incentivo) ?: 'Certificado' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-span-2 bg-slate-50 p-2 rounded-xl border border-line/60 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-brand-blue text-sm"><i class="fa-solid fa-graduation-cap"></i></span>
                                    <div>
                                        <span class="block text-[10px] text-muted font-medium">Evaluación / Examen</span>
                                        <span class="font-heading font-bold text-xs text-brand-dark">
                                            @if($capacitacion && $capacitacion->evaluaciones->isNotEmpty())
                                                {{ rtrim(rtrim(number_format((float) $capacitacion->evaluaciones->first()->porcentaje_aprobacion, 2, '.', ''), '0'), '.') }}% mín. · {{ $capacitacion->evaluaciones->first()->intentos_permitidos }} intentos
                                            @else
                                                Se define al crear la evaluación
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-100 text-brand-blue font-bold tracking-wide uppercase">Cuestionario</span>
                            </div>
                        </div>

                        <!-- Lista de Módulos en Vista Previa -->
                        <div class="pt-2 border-t border-line/60 space-y-2">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold text-brand-dark">Módulos del curso</span>
                                <span id="pv-modulos-count" class="text-brand-blue font-extrabold bg-brand-light px-2 py-0.5 rounded-full">
                                    0 Módulos
                                </span>
                            </div>
                            <div id="pv-modulos-list" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                <!-- Se llena dinámicamente mediante JS -->
                            </div>
                        </div>

                        <!-- Botón simulado del empleado -->
                        <div class="pt-2">
                            <button type="button" class="w-full py-2.5 rounded-xl bg-brand-blue text-white font-heading font-bold text-xs shadow-xs pointer-events-none opacity-90">
                                Comenzar Capacitación
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 text-center italic">
                    <i class="fa-regular fa-lightbulb text-amber-400 mr-1"></i> Los cambios que realizas en los pasos se reflejan automáticamente aquí.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Plantillas para módulos y contenidos nuevos -->
<template id="tpl-modulo">
    @include('jefe.capacitaciones._modulo', ['i' => '__M__', 'modulo' => ['id' => null, 'titulo' => '', 'descripcion' => '', 'contenidos' => []], 'input' => $input, 'tiposContenido' => $tiposContenido])
</template>
<template id="tpl-contenido">
    @include('jefe.capacitaciones._contenido', ['i' => '__M__', 'j' => '__C__', 'contenido' => ['id' => null, 'titulo' => '', 'tipo' => 'TEXTO', 'contenido' => '', 'ruta_archivo_actual' => null], 'input' => $input, 'tiposContenido' => $tiposContenido])
</template>
@endsection

@push('scripts')
<script>
    let pasoActual = 1;

    function irAPaso(paso) {
        pasoActual = paso;
        document.getElementById('seccion-paso-1').classList.toggle('hidden', paso !== 1);
        document.getElementById('seccion-paso-2').classList.toggle('hidden', paso !== 2);
        document.getElementById('seccion-paso-3').classList.toggle('hidden', paso !== 3);

        const badge = document.getElementById('badge-paso-actual');
        const subtitulo = document.getElementById('subtitulo-paso-actual');
        const barra = document.getElementById('progreso-barra');
        const texto = document.getElementById('progreso-texto');
        const porcentaje = document.getElementById('progreso-porcentaje');

        const l1 = document.getElementById('label-step-1');
        const l2 = document.getElementById('label-step-2');
        const l3 = document.getElementById('label-step-3');

        l1.className = paso >= 1 ? 'text-white font-bold' : 'opacity-70';
        l2.className = paso >= 2 ? 'text-white font-bold' : 'opacity-70';
        l3.className = paso >= 3 ? 'text-white font-bold' : 'opacity-70';

        if (paso === 1) {
            badge.textContent = 'Paso 1 de 3: Empezando...';
            subtitulo.textContent = 'Empezando: Ingresa el título, portada y condiciones generales de aprobación.';
            barra.style.width = '33.33%';
            texto.textContent = 'Empezando (33%)';
            porcentaje.textContent = 'Paso 1/3';
        } else if (paso === 2) {
            badge.textContent = 'Paso 2 de 3: Módulos y Recursos';
            subtitulo.textContent = 'Módulos: Agrega y organiza el material de lectura, videos y PDFs para tus colaboradores.';
            barra.style.width = '66.66%';
            texto.textContent = 'Módulos y Recursos (66%)';
            porcentaje.textContent = 'Paso 2/3';
            if (typeof window.actualizarAsistenteModulos === 'function') {
                window.actualizarAsistenteModulos();
            }
        } else {
            badge.textContent = 'Paso 3 de 3: ¡Ya casi terminas!';
            subtitulo.textContent = 'Ya casi terminas: Revisa la vista previa en tiempo real y confirma la publicación.';
            barra.style.width = '100%';
            texto.textContent = '¡Ya casi terminas! (100%)';
            porcentaje.textContent = 'Paso 3/3';
        }

        const mainCol = document.querySelector('.lg\\:overflow-y-auto');
        if (mainCol) mainCol.scrollTo({ top: 0, behavior: 'smooth' });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    window.toggleFechaFin = function(val) {
        const contenedor = document.getElementById('contenedor-fecha-limite');
        const inputLimite = document.getElementById('fecha_limite');
        if (!contenedor || !inputLimite) return;

        if (val === 'si') {
            contenedor.classList.remove('hidden');
            inputLimite.disabled = false;
            try {
                inputLimite.focus({ preventScroll: true });
            } catch (e) {
                inputLimite.focus();
            }
        } else {
            contenedor.classList.add('hidden');
            inputLimite.disabled = true;
            inputLimite.value = '';
            // Restablecer de inmediato si la ventana o el panel quedaron desplazados
            if (window.scrollY > 0) {
                window.scrollTo({ top: 0, behavior: 'instant' });
            }
        }
    };

    (function () {
        const lista = document.getElementById('lista-modulos');
        const tplModulo = document.getElementById('tpl-modulo').innerHTML;
        const tplContenido = document.getElementById('tpl-contenido').innerHTML;
        let contador = Date.now();

        // Elementos de la vista previa lateral
        const pvTitulo = document.getElementById('pv-titulo');
        const pvDesc = document.getElementById('pv-descripcion');
        const pvDuracion = document.getElementById('pv-duracion');
        const pvIncentivo = document.getElementById('pv-incentivo');
        const pvImg = document.getElementById('pv-imagen');
        const pvImgThumb = document.getElementById('img-preview-thumb');
        const pvModulosList = document.getElementById('pv-modulos-list');
        const pvModulosCount = document.getElementById('pv-modulos-count');

        const etiquetas = {
            TEXTO: 'Texto del contenido',
            VIDEO: 'URL del video (YouTube, Vimeo, etc.)',
            ENLACE: 'URL del enlace externo',
            IMAGEN: 'Descripción de la imagen (opcional)',
            PDF: 'Descripción del documento (opcional)',
        };

        function actualizarVistaPrevia() {
            const inputTitulo = document.getElementById('titulo').value.trim();
            const inputDesc = document.getElementById('descripcion').value.trim();
            const inputDuracion = document.getElementById('duracion_estimada').value;
            const inputIncentivo = document.getElementById('incentivo').value.trim();

            pvTitulo.textContent = inputTitulo || 'Título de la capacitación...';
            pvDesc.textContent = inputDesc || 'Aquí se visualizará la descripción y objetivos de la capacitación para los empleados de tu área.';
            if (pvDuracion) pvDuracion.textContent = inputDuracion ? inputDuracion + ' hrs' : 'Por definir';
            if (pvIncentivo) {
                pvIncentivo.textContent = inputIncentivo || 'Certificado';
                pvIncentivo.title = inputIncentivo || 'Certificado';
            }

            // Actualizar lista de módulos en live preview
            const modulos = lista.querySelectorAll('[data-modulo]');
            pvModulosCount.textContent = modulos.length + (modulos.length === 1 ? ' Módulo' : ' Módulos');
            pvModulosList.innerHTML = '';

            if (modulos.length === 0) {
                pvModulosList.innerHTML = '<p class="text-[11px] text-slate-400 italic">No has agregado módulos aún.</p>';
            } else {
                modulos.forEach((m, idx) => {
                    const tInput = m.querySelector('input[name*="[titulo]"]');
                    const titulo = tInput ? (tInput.value.trim() || `Módulo ${idx + 1}`) : `Módulo ${idx + 1}`;
                    const items = m.querySelectorAll('[data-contenido]').length;

                    const item = document.createElement('div');
                    item.className = 'p-2 rounded-xl bg-slate-50 border border-line/60 flex items-center justify-between text-[11px]';
                    item.innerHTML = `
                        <div class="flex items-center gap-1.5 truncate">
                            <span class="w-4 h-4 rounded-full bg-brand-light text-brand-blue font-bold flex items-center justify-center text-[9px] shrink-0">${idx + 1}</span>
                            <span class="font-bold text-slate-700 truncate">${titulo}</span>
                        </div>
                        <span class="text-[10px] text-muted shrink-0">${items} ${items === 1 ? 'recurso' : 'recursos'}</span>
                    `;
                    pvModulosList.appendChild(item);
                });
            }
        }

        // Cargar vista previa al seleccionar nueva portada
        document.getElementById('imagen').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    pvImg.src = evt.target.result;
                    if (pvImgThumb) pvImgThumb.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Escuchadores de eventos para tiempo real
        ['titulo', 'descripcion', 'duracion_estimada', 'incentivo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', actualizarVistaPrevia);
            }
        });

        function aplicarTipo(item) {
            const tipo = item.querySelector('[data-tipo-select]').value;
            const esVideo = tipo === 'VIDEO';
            const esArchivo = tipo === 'IMAGEN' || tipo === 'PDF';

            const grupoVideo = item.querySelector('[data-grupo-video]');
            const grupoTexto = item.querySelector('[data-grupo-texto]');
            const grupoArchivo = item.querySelector('[data-grupo-archivo]');

            if (grupoVideo) grupoVideo.classList.toggle('hidden', !esVideo);
            if (grupoTexto) grupoTexto.classList.toggle('hidden', esVideo);
            if (grupoArchivo) grupoArchivo.classList.toggle('hidden', !esArchivo);

            if (grupoTexto) {
                const labelTexto = item.querySelector('[data-label-texto]');
                if (labelTexto) labelTexto.textContent = etiquetas[tipo] || 'Contenido';
                const campo = item.querySelector('[data-campo-texto]');
                if (campo) {
                    campo.required = (tipo === 'TEXTO' || tipo === 'ENLACE');
                    campo.rows = (tipo === 'ENLACE' || esArchivo) ? 2 : 4;
                }
            }
        }

        function validarArchivoVideo(input) {
            const file = input.files[0];
            const feedback = input.closest('[data-grupo-video]')?.querySelector('[data-video-feedback]');
            if (!file) {
                if (feedback) {
                    feedback.className = 'hidden';
                    feedback.textContent = '';
                }
                return;
            }

            // Peso máximo: 250 MB (250 * 1024 * 1024 bytes)
            const maxBytes = 250 * 1024 * 1024;
            const pesoMb = (file.size / (1024 * 1024)).toFixed(1);
            if (file.size > maxBytes) {
                input.value = '';
                if (feedback) {
                    feedback.className = 'text-[11px] font-semibold mt-1.5 p-2 rounded-lg bg-red-100 text-red-800 border border-red-200 block';
                    feedback.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1 text-red-600"></i> El archivo pesa <strong>${pesoMb} MB</strong>, superando el límite máximo permitido de <strong>250 MB</strong>. Selecciona un archivo optimizado o comprimido en 720p HD.`;
                }
                return;
            }

            // Duración máxima: 10 minutos (600 segundos)
            if (feedback) {
                feedback.className = 'text-[11px] font-semibold mt-1.5 p-2 rounded-lg bg-blue-50 text-brand-blue border border-blue-200 block';
                feedback.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Comprobando duración y especificaciones (${pesoMb} MB)...`;
            }

            const videoElement = document.createElement('video');
            videoElement.preload = 'metadata';
            const objectUrl = URL.createObjectURL(file);

            videoElement.onloadedmetadata = function() {
                URL.revokeObjectURL(objectUrl);
                const duracionSegundos = Math.round(videoElement.duration);
                const minutos = Math.floor(duracionSegundos / 60);
                const segundos = duracionSegundos % 60;
                const duracionFormateada = `${minutos}m ${segundos < 10 ? '0' : ''}${segundos}s`;

                if (duracionSegundos > 600) {
                    input.value = '';
                    if (feedback) {
                        feedback.className = 'text-[11px] font-semibold mt-1.5 p-2 rounded-lg bg-red-100 text-red-800 border border-red-200 block';
                        feedback.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1 text-red-600"></i> El video seleccionado dura <strong>${duracionFormateada}</strong> (${duracionSegundos}s), superando el límite máximo de <strong>10 minutos (600 segundos)</strong>. Por favor sube un video de menor duración.`;
                    }
                } else {
                    if (feedback) {
                        feedback.className = 'text-[11px] font-semibold mt-1.5 p-2 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200 block';
                        feedback.innerHTML = `<i class="fa-solid fa-circle-check mr-1 text-emerald-600"></i> Video válido: <strong>${duracionFormateada}</strong> (${pesoMb} MB) · Cumple los requisitos (máx. 10 min / 250 MB).`;
                    }
                }
            };

            videoElement.onerror = function() {
                URL.revokeObjectURL(objectUrl);
                if (feedback) {
                    feedback.className = 'text-[11px] font-semibold mt-1.5 p-2 rounded-lg bg-amber-100 text-amber-800 border border-amber-200 block';
                    feedback.innerHTML = `<i class="fa-solid fa-circle-info mr-1 text-amber-600"></i> Archivo cargado (${pesoMb} MB). Asegúrate de que el video esté codificado en MP4 (H.264 + AAC) en resolución recomendada 720p HD.`;
                }
            };

            videoElement.src = objectUrl;
        }

        let moduloActivoIndex = 0;

        function renumerar() {
            lista.querySelectorAll('[data-modulo]').forEach((m, idx) => {
                const badge = m.querySelector('[data-modulo-numero]');
                if (badge) badge.textContent = idx + 1;
            });
            actualizarVistaPrevia();
        }

        function iniciar(elemento) {
            elemento.querySelectorAll('[data-contenido]').forEach(aplicarTipo);
        }

        function actualizarAsistenteModulos(nuevoIndice = null) {
            const modulos = lista.querySelectorAll('[data-modulo]');
            if (modulos.length === 0) return;

            if (nuevoIndice !== null) {
                moduloActivoIndex = Math.max(0, Math.min(nuevoIndice, modulos.length - 1));
            } else {
                moduloActivoIndex = Math.max(0, Math.min(moduloActivoIndex, modulos.length - 1));
            }

            // Mostrar SOLO el módulo activo (vista limpia individual) y ocultar los demás
            modulos.forEach((m, idx) => {
                if (idx === moduloActivoIndex) {
                    m.classList.remove('hidden');
                } else {
                    m.classList.add('hidden');
                }
            });

            const modActual = modulos[moduloActivoIndex];
            const inputTitulo = modActual ? modActual.querySelector('input[name*="[titulo]"]') : null;
            const tituloActual = inputTitulo && inputTitulo.value.trim() ? inputTitulo.value.trim() : `Módulo ${moduloActivoIndex + 1}`;

            const badgeContador = document.getElementById('badge-contador-modulos');
            if (badgeContador) badgeContador.textContent = modulos.length + (modulos.length === 1 ? ' Módulo' : ' Módulos');

            const numBadge = document.getElementById('modulo-asistido-num-badge');
            if (numBadge) numBadge.textContent = (moduloActivoIndex + 1);

            const actNum = document.getElementById('modulo-asistido-actual-num');
            if (actNum) actNum.textContent = (moduloActivoIndex + 1);

            const totNum = document.getElementById('modulo-asistido-total-num');
            if (totNum) totNum.textContent = modulos.length;

            const titResumen = document.getElementById('modulo-asistido-titulo-resumen');
            if (titResumen) titResumen.textContent = tituloActual;

            const txtProgreso = document.getElementById('txt-progreso-modulo-asistido');
            if (txtProgreso) txtProgreso.textContent = `Módulo ${moduloActivoIndex + 1} de ${modulos.length}`;

            // Control botón Anterior
            const btnAnt = document.getElementById('btn-asistente-anterior');
            if (btnAnt) {
                btnAnt.disabled = (moduloActivoIndex === 0);
                if (moduloActivoIndex === 0) {
                    btnAnt.innerHTML = '<i class="fa-solid fa-arrow-left text-[11px]"></i> <span>Módulo Anterior</span>';
                } else {
                    btnAnt.innerHTML = `<i class="fa-solid fa-arrow-left text-[11px]"></i> <span>← Módulo ${moduloActivoIndex}</span>`;
                }
            }

            // Control botón Siguiente (si ya existe un módulo posterior creado)
            const btnSig = document.getElementById('btn-asistente-siguiente');
            if (btnSig) {
                if (moduloActivoIndex < modulos.length - 1) {
                    btnSig.classList.remove('hidden');
                    btnSig.innerHTML = `<span>Módulo ${moduloActivoIndex + 2} →</span> <i class="fa-solid fa-arrow-right text-[11px]"></i>`;
                } else {
                    btnSig.classList.add('hidden');
                }
            }

            // Control botón Crear Siguiente / Guardar
            const btnCrearSig = document.getElementById('btn-asistente-crear-siguiente');
            if (btnCrearSig) {
                if (moduloActivoIndex === modulos.length - 1) {
                    btnCrearSig.innerHTML = '<i class="fa-solid fa-plus text-[11px]"></i> <span>+ Guardar y Siguiente Módulo</span>';
                } else {
                    btnCrearSig.innerHTML = '<i class="fa-solid fa-plus text-[11px]"></i> <span>+ Agregar Nuevo Módulo</span>';
                }
            }

            // Re-renderizar tabs/pestañas de módulos superiores
            const navTabs = document.getElementById('nav-tabs-modulos');
            if (navTabs) {
                navTabs.innerHTML = '';
                modulos.forEach((m, idx) => {
                    const tInput = m.querySelector('input[name*="[titulo]"]');
                    const tit = tInput && tInput.value.trim() ? tInput.value.trim() : `Módulo ${idx + 1}`;
                    const esActivo = (idx === moduloActivoIndex);
                    const tieneTitulo = Boolean(tInput && tInput.value.trim());

                    const btnTab = document.createElement('button');
                    btnTab.type = 'button';
                    btnTab.className = esActivo
                        ? 'tab-modulo-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-brand-blue text-white font-heading font-bold text-xs shadow-xs shrink-0 transition-all cursor-pointer ring-2 ring-brand-blue/30'
                        : 'tab-modulo-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-heading font-bold text-xs border border-line shrink-0 transition-all cursor-pointer';

                    btnTab.innerHTML = `
                        <span class="w-5 h-5 rounded-lg ${esActivo ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'} flex items-center justify-center text-[10px] font-extrabold">${idx + 1}</span>
                        <span class="truncate max-w-[130px] sm:max-w-[160px] text-left tab-titulo-texto">${tit}</span>
                        ${tieneTitulo ? `<i class="fa-solid fa-check text-[10px] ${esActivo ? 'text-cyan-300' : 'text-emerald-500'} ml-0.5"></i>` : ''}
                    `;

                    btnTab.addEventListener('click', () => {
                        actualizarAsistenteModulos(idx);
                        hacerScrollAModulo();
                    });

                    navTabs.appendChild(btnTab);
                });

                // Botón rápido en la barra de pestañas para añadir módulo
                const btnTabAdd = document.createElement('button');
                btnTabAdd.type = 'button';
                btnTabAdd.className = 'inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs shrink-0 transition-colors cursor-pointer';
                btnTabAdd.innerHTML = '<i class="fa-solid fa-plus text-[10px]"></i> <span>Nuevo</span>';
                btnTabAdd.addEventListener('click', () => {
                    agregarModulo();
                });
                navTabs.appendChild(btnTabAdd);
            }
        }

        function hacerScrollAModulo() {
            const paso2 = document.getElementById('seccion-paso-2');
            if (paso2) {
                paso2.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        window.actualizarAsistenteModulos = actualizarAsistenteModulos;

        function agregarModulo() {
            const html = tplModulo.replaceAll('__M__', ++contador);
            lista.insertAdjacentHTML('beforeend', html);
            renumerar();
            iniciar(lista.lastElementChild);

            const modulos = lista.querySelectorAll('[data-modulo]');
            const nuevoIdx = modulos.length - 1;
            actualizarAsistenteModulos(nuevoIdx);

            const nuevo = modulos[nuevoIdx];
            hacerScrollAModulo();
            const inputTxt = nuevo.querySelector('input[type="text"]');
            if (inputTxt) {
                try { inputTxt.focus({ preventScroll: true }); } catch (e) { inputTxt.focus(); }
            }
        }

        // Eventos de botones del asistente
        const btnAnt = document.getElementById('btn-asistente-anterior');
        if (btnAnt) {
            btnAnt.addEventListener('click', () => {
                if (moduloActivoIndex > 0) {
                    actualizarAsistenteModulos(moduloActivoIndex - 1);
                    hacerScrollAModulo();
                }
            });
        }

        const btnSig = document.getElementById('btn-asistente-siguiente');
        if (btnSig) {
            btnSig.addEventListener('click', () => {
                const total = lista.querySelectorAll('[data-modulo]').length;
                if (moduloActivoIndex < total - 1) {
                    actualizarAsistenteModulos(moduloActivoIndex + 1);
                    hacerScrollAModulo();
                }
            });
        }

        const btnCrearSig = document.getElementById('btn-asistente-crear-siguiente');
        if (btnCrearSig) {
            btnCrearSig.addEventListener('click', () => {
                const total = lista.querySelectorAll('[data-modulo]').length;
                if (moduloActivoIndex === total - 1) {
                    agregarModulo();
                } else {
                    actualizarAsistenteModulos(moduloActivoIndex + 1);
                    hacerScrollAModulo();
                }
            });
        }

        const btnLegacyAdd = document.getElementById('btn-add-modulo');
        if (btnLegacyAdd) {
            btnLegacyAdd.addEventListener('click', agregarModulo);
        }

        lista.addEventListener('click', (e) => {
            const quitarModulo = e.target.closest('[data-remove-modulo]');
            const agregarContenido = e.target.closest('[data-add-contenido]');
            const quitarContenido = e.target.closest('[data-remove-contenido]');

            if (quitarModulo) {
                const totalModulos = lista.querySelectorAll('[data-modulo]').length;
                if (totalModulos === 1) {
                    alert('La capacitación debe tener al menos un módulo.');
                    return;
                }
                if (confirm('¿Quitar este módulo? Si ya estaba publicado, quedará desactivado.')) {
                    const moduloEl = quitarModulo.closest('[data-modulo]');
                    const todos = Array.from(lista.querySelectorAll('[data-modulo]'));
                    const idxEliminado = todos.indexOf(moduloEl);

                    moduloEl.remove();
                    renumerar();

                    const nuevosTodos = lista.querySelectorAll('[data-modulo]');
                    if (idxEliminado <= moduloActivoIndex) {
                        moduloActivoIndex = Math.max(0, Math.min(moduloActivoIndex, nuevosTodos.length - 1));
                    }
                    actualizarAsistenteModulos(moduloActivoIndex);
                    hacerScrollAModulo();
                }
            }

            if (agregarContenido) {
                const modulo = agregarContenido.closest('[data-modulo]');
                const html = tplContenido
                    .replaceAll('__M__', modulo.dataset.index)
                    .replaceAll('__C__', ++contador);
                const contenedor = modulo.querySelector('[data-contenidos]');
                contenedor.insertAdjacentHTML('beforeend', html);
                aplicarTipo(contenedor.lastElementChild);
                actualizarVistaPrevia();
            }

            if (quitarContenido) {
                quitarContenido.closest('[data-contenido]').remove();
                actualizarVistaPrevia();
            }
        });

        lista.addEventListener('input', (e) => {
            if (e.target.matches('input') || e.target.matches('textarea')) {
                actualizarVistaPrevia();
                if (e.target.matches('input[name*="[titulo]"]')) {
                    const modulos = lista.querySelectorAll('[data-modulo]');
                    const modActual = modulos[moduloActivoIndex];
                    if (modActual && modActual.contains(e.target)) {
                        const val = e.target.value.trim() || `Módulo ${moduloActivoIndex + 1}`;
                        const titResumen = document.getElementById('modulo-asistido-titulo-resumen');
                        if (titResumen) titResumen.textContent = val;

                        const tabs = document.querySelectorAll('#nav-tabs-modulos .tab-modulo-btn');
                        if (tabs[moduloActivoIndex]) {
                            const txtSpan = tabs[moduloActivoIndex].querySelector('.tab-titulo-texto');
                            if (txtSpan) txtSpan.textContent = val;
                        }
                    }
                }
            }
        });

        lista.addEventListener('change', (e) => {
            if (e.target.matches('[data-tipo-select]')) {
                aplicarTipo(e.target.closest('[data-contenido]'));
                actualizarVistaPrevia();
            } else if (e.target.matches('[data-video-file]')) {
                validarArchivoVideo(e.target);
            }
        });

        iniciar(lista);
        actualizarVistaPrevia();

        // Si hay módulos con errores de validación de Laravel, posicionarse en el que tenga el error
        const moduloConError = Array.from(lista.querySelectorAll('[data-modulo]')).findIndex(m => m.querySelector('.text-red-600, input:invalid'));
        if (moduloConError !== -1) {
            moduloActivoIndex = moduloConError;
        }

        actualizarAsistenteModulos(moduloActivoIndex);

        @if ($errors->has('modulos') || $errors->has('modulos.*'))
            irAPaso(2);
        @endif
    })();
</script>
@endpush
