@extends('layouts.jefe')

@php
    $editando = (bool) $capacitacion;
    $input = 'w-full px-4 py-2.5 rounded-xl border border-line bg-white text-xs sm:text-[13px] font-sans text-brand-dark placeholder:text-slate-400 focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all';
    $fechaDisp = old('fecha_disponibilidad', $capacitacion?->fecha_disponibilidad?->format('Y-m-d'));
    $fechaLim = old('fecha_limite', $capacitacion?->fecha_limite?->format('Y-m-d'));
    $porcentaje = old('porcentaje_aprobacion', $capacitacion ? rtrim(rtrim(number_format((float) $capacitacion->porcentaje_aprobacion, 2, '.', ''), '0'), '.') : 80);
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
                    Empezando: Ingresa el título, portada y condiciones generales de aprobación.
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
                  action="{{ $editando ? route('jefe.capacitaciones.update', $capacitacion) : route('jefe.capacitaciones.store') }}"
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
                                <label for="porcentaje_aprobacion" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Porcentaje mínimo de aprobación (%) <span class="text-red-500">*</span>
                                </label>
                                <input id="porcentaje_aprobacion" type="number" name="porcentaje_aprobacion" value="{{ $porcentaje }}" min="1" max="100" step="0.01" required class="{{ $input }}">
                            </div>

                            <div>
                                <label for="intentos_permitidos" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Intentos permitidos de examen <span class="text-red-500">*</span>
                                </label>
                                <input id="intentos_permitidos" type="number" name="intentos_permitidos" value="{{ old('intentos_permitidos', $capacitacion?->intentos_permitidos ?? 3) }}" min="1" max="20" required class="{{ $input }}">
                            </div>

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
                                    Disponible desde
                                </label>
                                <input id="fecha_disponibilidad" type="date" name="fecha_disponibilidad" value="{{ $fechaDisp }}" class="{{ $input }}">
                            </div>

                            <div>
                                <label for="fecha_limite" class="block text-xs font-bold text-brand-dark mb-1.5">
                                    Fecha límite
                                </label>
                                <input id="fecha_limite" type="date" name="fecha_limite" value="{{ $fechaLim }}" class="{{ $input }}">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-line/60">
                        <a href="{{ route('jefe.capacitaciones.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors">
                            Cancelar
                        </a>
                        <button type="button" onclick="irAPaso(2)"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                            <span>Siguiente: Módulos y Recursos</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </button>
                    </div>
                </div>

                <!-- ================= PASO 2: MÓDULOS Y RECURSOS ================= -->
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

                    <p class="text-xs text-muted leading-relaxed">
                        Organiza la capacitación en secciones estructuradas. Agrega videos, lecturas en texto, guías en PDF o enlaces externos para tus colaboradores.
                    </p>

                    <div id="lista-modulos" class="space-y-5">
                        @foreach ($modulosForm as $i => $modulo)
                            @include('jefe.capacitaciones._modulo', ['i' => $i, 'modulo' => $modulo, 'input' => $input, 'tiposContenido' => $tiposContenido])
                        @endforeach
                    </div>

                    <button type="button" id="btn-add-modulo"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl border-2 border-dashed border-brand-blue/40 text-brand-blue hover:bg-brand-light font-heading font-bold text-xs sm:text-sm transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        + Agregar Nuevo Módulo
                    </button>

                    <div class="flex items-center justify-between pt-4 border-t border-line/60">
                        <button type="button" onclick="irAPaso(1)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                            <span>Anterior</span>
                        </button>
                        <button type="button" onclick="irAPaso(3)"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs sm:text-sm shadow-xs hover:-translate-y-0.5 transition-all cursor-pointer">
                            <span>Siguiente: Ya Casi Terminas</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </button>
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
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                ✓
                            </div>
                            <div>
                                <h3 class="font-heading font-bold text-sm text-emerald-950">¡Tu capacitación está lista para publicarse!</h3>
                                <p class="text-xs text-emerald-800">Verifica que el alcance y los módulos mostrados en la vista previa lateral sean correctos.</p>
                            </div>
                        </div>

                        <ul class="text-xs space-y-2 text-slate-700 pt-2 border-t border-emerald-200/60">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Se habilitará automáticamente para los colaboradores del área <strong>{{ $usuario->area->nombre ?? 'Tu Área' }}</strong>.</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Los empleados podrán registrar su avance por cada módulo completado.</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Podrás crear la evaluación o cuestionario final inmediatamente después de guardar.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-3 pt-2">
                        <h4 class="font-heading font-bold text-xs text-brand-dark uppercase tracking-wider">Estado inicial de publicación</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start p-3.5 rounded-2xl border border-line bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                                <input type="radio" name="estado_publicacion" value="1" checked class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-brand-dark">Publicar e Iniciar Inmediatamente</span>
                                    <span class="block text-[11px] text-muted">Visible desde hoy en el panel de los empleados.</span>
                                </div>
                            </label>
                            <label class="relative flex items-start p-3.5 rounded-2xl border border-line bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                                <input type="radio" name="estado_publicacion" value="0" class="mt-0.5 text-brand-blue focus:ring-brand-blue">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-brand-dark">Guardar como Borrador (Desactivada)</span>
                                    <span class="block text-[11px] text-muted">Podrás activarla más adelante desde tu panel.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-line/60">
                        <button type="button" onclick="irAPaso(2)"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-heading font-bold text-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                            <span>Volver a Módulos</span>
                        </button>
                        <button type="submit" id="btn-guardar-capacitacion"
                                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-brand-blue via-brand-deep to-brand-dark hover:brightness-110 text-white font-heading font-extrabold text-xs sm:text-sm shadow-md hover:-translate-y-0.5 transition-all cursor-pointer">
                            <span><i class="fa-solid fa-paper-plane mr-1.5"></i> {{ $editando ? 'Guardar Cambios' : 'Publicar Capacitación Ahora' }}</span>
                        </button>
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
                            Área: {{ $usuario->area->nombre ?? 'Mi Área' }}
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
                                <span class="text-brand-blue text-sm"><i class="fa-solid fa-bullseye"></i></span>
                                <div>
                                    <span class="block text-[10px] text-muted font-medium">Aprobación</span>
                                    <span id="pv-aprobacion" class="font-heading font-bold text-xs text-brand-blue">
                                        {{ $porcentaje }}%
                                    </span>
                                </div>
                            </div>
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
                                <span class="text-brand-blue text-sm"><i class="fa-solid fa-rotate-right"></i></span>
                                <div>
                                    <span class="block text-[10px] text-muted font-medium">Intentos</span>
                                    <span id="pv-intentos" class="font-heading font-bold text-xs text-slate-700">
                                        {{ old('intentos_permitidos', $capacitacion?->intentos_permitidos ?? 3) }} máximos
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
        } else {
            badge.textContent = 'Paso 3 de 3: ¡Ya casi terminas!';
            subtitulo.textContent = 'Ya casi terminas: Revisa la vista previa en tiempo real y confirma la publicación.';
            barra.style.width = '100%';
            texto.textContent = '¡Ya casi terminas! (100%)';
            porcentaje.textContent = 'Paso 3/3';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    (function () {
        const lista = document.getElementById('lista-modulos');
        const tplModulo = document.getElementById('tpl-modulo').innerHTML;
        const tplContenido = document.getElementById('tpl-contenido').innerHTML;
        let contador = Date.now();

        // Elementos de la vista previa lateral
        const pvTitulo = document.getElementById('pv-titulo');
        const pvDesc = document.getElementById('pv-descripcion');
        const pvAprob = document.getElementById('pv-aprobacion');
        const pvDuracion = document.getElementById('pv-duracion');
        const pvIntentos = document.getElementById('pv-intentos');
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
            const inputAprob = document.getElementById('porcentaje_aprobacion').value;
            const inputDuracion = document.getElementById('duracion_estimada').value;
            const inputIntentos = document.getElementById('intentos_permitidos').value;
            const inputIncentivo = document.getElementById('incentivo').value.trim();

            pvTitulo.textContent = inputTitulo || 'Título de la capacitación...';
            pvDesc.textContent = inputDesc || 'Aquí se visualizará la descripción y objetivos de la capacitación para los empleados de tu área.';
            pvAprob.textContent = (inputAprob || 80) + '%';
            pvDuracion.textContent = inputDuracion ? inputDuracion + ' hrs' : 'Por definir';
            pvIntentos.textContent = (inputIntentos || 3) + ' máximos';
            pvIncentivo.textContent = inputIncentivo || 'Certificado';
            pvIncentivo.title = inputIncentivo || 'Certificado';

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
        ['titulo', 'descripcion', 'porcentaje_aprobacion', 'duracion_estimada', 'intentos_permitidos', 'incentivo'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', actualizarVistaPrevia);
            }
        });

        function aplicarTipo(item) {
            const tipo = item.querySelector('[data-tipo-select]').value;
            const esArchivo = tipo === 'IMAGEN' || tipo === 'PDF';
            item.querySelector('[data-grupo-archivo]').classList.toggle('hidden', !esArchivo);
            item.querySelector('[data-label-texto]').textContent = etiquetas[tipo];
            const campo = item.querySelector('[data-campo-texto]');
            campo.required = !esArchivo;
            campo.rows = (tipo === 'VIDEO' || tipo === 'ENLACE' || esArchivo) ? 2 : 4;
        }

        function renumerar() {
            lista.querySelectorAll('[data-modulo]').forEach((m, idx) => {
                m.querySelector('[data-modulo-numero]').textContent = idx + 1;
            });
            actualizarVistaPrevia();
        }

        function iniciar(elemento) {
            elemento.querySelectorAll('[data-contenido]').forEach(aplicarTipo);
        }

        function agregarModulo() {
            const html = tplModulo.replaceAll('__M__', ++contador);
            lista.insertAdjacentHTML('beforeend', html);
            renumerar();
            const nuevo = lista.lastElementChild;
            nuevo.scrollIntoView({ behavior: 'smooth', block: 'center' });
            nuevo.querySelector('input[type="text"]').focus({ preventScroll: true });
        }

        document.getElementById('btn-add-modulo').addEventListener('click', agregarModulo);

        lista.addEventListener('click', (e) => {
            const quitarModulo = e.target.closest('[data-remove-modulo]');
            const agregarContenido = e.target.closest('[data-add-contenido]');
            const quitarContenido = e.target.closest('[data-remove-contenido]');

            if (quitarModulo) {
                if (lista.querySelectorAll('[data-modulo]').length === 1) {
                    alert('La capacitación debe tener al menos un módulo.');
                    return;
                }
                if (confirm('¿Quitar este módulo? Si ya estaba publicado, quedará desactivado.')) {
                    quitarModulo.closest('[data-modulo]').remove();
                    renumerar();
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
            }
        });

        lista.addEventListener('change', (e) => {
            if (e.target.matches('[data-tipo-select]')) {
                aplicarTipo(e.target.closest('[data-contenido]'));
                actualizarVistaPrevia();
            }
        });

        iniciar(lista);
        actualizarVistaPrevia();
    })();
</script>
@endpush
