<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paso 2: Prueba 1 (Cuestionario VALANTI) · C.I. Piscícola New York</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink bg-[#f8fafc] antialiased flex flex-col justify-between selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- CABECERA INSTITUCIONAL CORPORATIVA -->
    <header class="w-full bg-white/95 backdrop-blur-md border-b border-line/80 shadow-2xs sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-3.5 sm:px-6 py-2.5 sm:py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="h-9 w-9 sm:h-10 sm:w-10 p-1 bg-white rounded-xl shadow-xs border border-line/60 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo C.I. Piscícola New York" class="h-full w-full object-contain">
                </div>
                <div class="min-w-0">
                    <span class="block font-heading font-extrabold text-xs sm:text-sm tracking-wider uppercase text-brand-dark leading-tight truncate">
                        C.I. Piscícola New York S.A.
                    </span>
                    <span class="hidden sm:block text-[10px] sm:text-[11px] font-semibold text-brand-blue tracking-wide uppercase truncate">
                        Gestión Humana & Psicología Organizacional
                    </span>
                </div>
            </div>

            <!-- Estado del Proceso en Header -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full bg-brand-light text-brand-blue font-heading font-bold text-[11px] sm:text-xs border border-brand-blue/20 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="sm:hidden font-extrabold">Paso 2/3</span>
                    <span class="hidden sm:inline">Paso 2 de 3 · Prueba 1 (VALANTI)</span>
                </span>
                <span class="hidden md:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-mono text-[11px] border border-slate-200" title="Sesión activa">
                    <i class="fa-solid fa-qrcode text-[10px] text-slate-400"></i>
                    <span>{{ $codigo }}</span>
                </span>
            </div>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="flex-1 w-full max-w-3xl mx-auto px-4 sm:px-6 py-5 sm:py-8 pb-32 sm:pb-28 space-y-4 sm:space-y-5">

        <!-- STEPPER DE 3 PASOS: INDICADOR DE PROGRESO ADAPTATIVO -->
        <div class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-line/80 shadow-2xs">
            <div class="flex items-center justify-between text-center relative max-w-md mx-auto">
                <!-- Línea conectora de fondo -->
                <div class="absolute top-3.5 sm:top-4 left-6 right-6 sm:left-8 sm:right-8 h-0.5 bg-slate-200 -z-0"></div>

                <!-- Paso 1: Autorización (Completado) -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 mt-1 sm:mt-1.5">
                        <span class="sm:hidden">1. Datos</span>
                        <span class="hidden sm:inline">1. Autorizado</span>
                    </span>
                </div>

                <!-- Paso 2: 1ª Prueba VALANTI (Activo) -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-brand-blue text-white flex items-center justify-center text-[11px] sm:text-xs font-bold shadow-xs ring-4 ring-brand-light">
                        2
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-brand-blue mt-1 sm:mt-1.5">
                        <span class="sm:hidden">2. VALANTI</span>
                        <span class="hidden sm:inline">2. Prueba 1 (VALANTI)</span>
                    </span>
                </div>

                <!-- Paso 3: 2ª Prueba PAC-A (Pendiente) -->
                <div class="relative z-10 flex flex-col items-center opacity-60">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-slate-100 text-slate-500 border border-slate-300 flex items-center justify-center text-[11px] sm:text-xs font-bold">
                        3
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-semibold text-slate-500 mt-1 sm:mt-1.5">
                        <span class="sm:hidden">3. PAC-A</span>
                        <span class="hidden sm:inline">3. Prueba 2 (PAC-A)</span>
                    </span>
                </div>
            </div>

            <!-- Barra de progreso visual compacta para móvil -->
            <div class="sm:hidden mt-3 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1.5">
                    <span class="text-brand-blue flex items-center gap-1.5">
                        <i class="fa-solid fa-bars-progress text-[10px]"></i>
                        <span>Estado: Prueba 1 (VALANTI)</span>
                    </span>
                    <span class="text-slate-400 font-mono">Paso 2 de 3 (66%)</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-brand-blue rounded-full transition-all duration-300" style="width: 66.6%;"></div>
                </div>
            </div>
        </div>

        <!-- Contenedor flotante para notificaciones auto-ocultables -->
        <div id="toastNotificacion" class="fixed top-16 left-1/2 -translate-x-1/2 z-50 hidden max-w-md w-[92%] rounded-2xl p-4 shadow-xl border text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300">
            <div class="flex items-center gap-2.5">
                <i id="toastIcono" class="fa-solid fa-circle-info text-base shrink-0"></i>
                <span id="toastMensaje"></span>
            </div>
            <button type="button" onclick="ocultarToast()" class="opacity-70 hover:opacity-100 p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        @if(session('error_validacion'))
            <div id="alertaServidorValanti" class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-xs font-semibold text-rose-800 flex items-center justify-between gap-2.5 shadow-2xs transition-all duration-500">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm mt-0.5"></i>
                    <div>{{ session('error_validacion') }}</div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @php
            $totalRespondidasInicial = count($respuestasGuardadas ?? []);
            $porcentajeInicial = round(($totalRespondidasInicial / 30) * 100);
            $puntosTotalesInicial = $totalRespondidasInicial * 3;
        @endphp

        <!-- Tarjeta de Instrucciones Oficiales VALANTI -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3 border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue block">Instrumento Psicotécnico · Primera Prueba</span>
                    <h1 class="text-base sm:text-lg font-extrabold text-slate-900 font-heading">
                        Cuestionario de Valores y Antivalores (VALANTI)
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Aspirante: <strong class="text-slate-800">{{ $candidato->nombre_completo }}</strong>
                        <span class="block sm:inline">(C.C. {{ $candidato->identificacion }})</span>
                    </p>
                </div>

                <!-- Progreso en Vivo -->
                <div class="bg-blue-50/70 border border-blue-100 rounded-2xl p-2.5 sm:p-3 text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Avance VALANTI</span>
                    <span class="text-sm sm:text-lg font-black text-brand-blue font-mono" id="txtProgresoHeader">
                        {{ $porcentajeInicial }}% ({{ $totalRespondidasInicial }}/30 · {{ $puntosTotalesInicial }}/90 pts)
                    </span>
                </div>
            </div>

            <!-- Barra de Progreso Visual Superior -->
            <div class="w-full bg-slate-100 h-2 sm:h-2.5 rounded-full overflow-hidden mb-3">
                <div id="barraProgreso"
                     class="bg-brand-blue h-full rounded-full transition-all duration-300"
                     style="width: {{ $porcentajeInicial }}%;"></div>
            </div>

            <!-- Instrucciones Claras y Ejemplo de Puntuación -->
            <div class="p-3 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed space-y-2.5">
                <p class="font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-brand-blue"></i> Instrucciones de Diligenciamiento:
                </p>
                <p class="text-[11px] sm:text-xs">
                    El cuestionario consta de <strong>30 preguntas</strong>, cada una con un par de afirmaciones (<strong>A</strong> y <strong>B</strong>).
                    En cada fila usted debe distribuir <strong>exactamente 3 puntos</strong> entre las opciones A y B según su preferencia:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-brand-blue font-mono font-black text-xs border border-blue-100">
                            3 - 0
                        </span>
                        <span class="text-[11px] font-medium text-slate-700 mt-1 leading-tight">
                            <strong class="text-slate-900">A todo</strong> / B nada
                        </span>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-brand-blue font-mono font-black text-xs border border-blue-100">
                            0 - 3
                        </span>
                        <span class="text-[11px] font-medium text-slate-700 mt-1 leading-tight">
                            A nada / <strong class="text-slate-900">B todo</strong>
                        </span>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-brand-blue font-mono font-black text-xs border border-blue-100">
                            2 - 1
                        </span>
                        <span class="text-[11px] font-medium text-slate-700 mt-1 leading-tight">
                            <strong class="text-slate-900">A más</strong> / B menos
                        </span>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-blue-50 text-brand-blue font-mono font-black text-xs border border-blue-100">
                            1 - 2
                        </span>
                        <span class="text-[11px] font-medium text-slate-700 mt-1 leading-tight">
                            A menos / <strong class="text-slate-900">B más</strong>
                        </span>
                    </div>
                </div>
                <p class="text-[10px] text-amber-700 font-semibold bg-amber-50 p-2 rounded-xl border border-amber-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>La suma de cada fila siempre debe ser 3 puntos. Al finalizar los 30 ítems habrá distribuido un total exacto de 90 puntos.</span>
                </p>
            </div>
        </div>

        <!-- Formulario de las 30 Preguntas VALANTI -->
        <form action="{{ route('psicologia.evaluacion.valanti.finalizar', ['codigo' => $codigo, 'evaluacion' => $candidato->id]) }}"
              method="POST"
              id="formValanti"
              class="space-y-4">
            @csrf

            @php
                $faltantes = session('preguntas_faltantes', []);
            @endphp

            @foreach($preguntas as $numero => $item)
                {{-- Encabezado de Sección Parte 1 (Pregunta 1) --}}
                @if($numero === 1)
                    <div class="rounded-2xl bg-gradient-to-r from-brand-dark to-brand-blue p-4 sm:p-5 text-white shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2.5 py-1 rounded-lg bg-white/15 font-mono text-xs font-extrabold uppercase tracking-wider">
                                Parte 1 · Ítems 1 al 9
                            </span>
                            <h2 class="font-heading font-extrabold text-sm sm:text-base">
                                Selección de Valores por Importancia
                            </h2>
                        </div>
                        <p class="text-xs text-blue-100 mt-1.5 leading-relaxed">
                            Marque <strong>3-0, 0-3, 2-1 o 1-2</strong> según la importancia que cada afirmación tiene en su vida personal (otorgue mayor puntaje a la frase más importante para usted).
                        </p>
                    </div>
                @endif

                {{-- Encabezado de Sección Parte 2 (Pregunta 10) --}}
                @if($numero === 10)
                    <div class="rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 p-4 sm:p-5 text-white shadow-xs mt-6">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2.5 py-1 rounded-lg bg-amber-400/20 text-amber-300 border border-amber-400/30 font-mono text-xs font-extrabold uppercase tracking-wider">
                                Parte 2 · Ítems 10 al 30
                            </span>
                            <h2 class="font-heading font-extrabold text-sm sm:text-base">
                                Juicio de Inaceptabilidad (Antivalores)
                            </h2>
                        </div>
                        <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">
                            En las siguientes preguntas se juzga el nivel de <strong>inaceptabilidad</strong> de una conducta. Otorgue el mayor puntaje a la acción que considere <strong>peor o más condenable</strong>.
                        </p>
                    </div>
                @endif

                @php
                    $respGuardada = $respuestasGuardadas[$numero] ?? null;
                    $esFaltante = in_array($numero, $faltantes, true);
                    $partes = $respGuardada ? explode('-', $respGuardada) : [null, null];
                    $ptsAActual = $partes[0];
                    $ptsBActual = $partes[1];
                @endphp

                <!-- Tarjeta de Pregunta Par (A y B) -->
                <div id="pregunta-box-{{ $numero }}"
                     class="bg-white border {{ $esFaltante ? 'border-rose-400 ring-2 ring-rose-200' : 'border-slate-200' }} rounded-3xl p-4 sm:p-5 shadow-xs transition-all duration-200 hover:border-slate-300">
                    
                    <!-- Encabezado de la Fila -->
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2.5 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center font-bold text-xs shrink-0 font-mono">
                                #{{ $numero }}
                            </span>
                            <span class="text-xs font-bold text-slate-700">
                                Comparación de Afirmaciones
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span id="badge-suma-{{ $numero }}"
                                  class="text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full border {{ $respGuardada ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                @if($respGuardada)
                                    A={{ $ptsAActual }} | B={{ $ptsBActual }} (Suma: 3)
                                @else
                                    Sin responder
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Enunciados A y B con sus insignias de puntos en vivo -->
                    <div class="space-y-2 mb-3.5 text-xs sm:text-sm">
                        <!-- Frase A -->
                        <div class="p-3 rounded-2xl bg-blue-50/40 border border-blue-100/80 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-brand-blue text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                    A
                                </span>
                                <span class="text-slate-800 leading-snug">
                                    {{ $item['texto_a'] ?? $item['a'] ?? '' }}
                                </span>
                            </div>
                            <span id="pts-a-{{ $numero }}"
                                  class="shrink-0 px-2 py-1 rounded-lg font-mono text-xs font-black {{ $respGuardada ? 'bg-brand-blue text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $ptsAActual !== null ? $ptsAActual . ' pts' : '—' }}
                            </span>
                        </div>

                        <!-- Frase B -->
                        <div class="p-3 rounded-2xl bg-sky-50/40 border border-sky-100/80 flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-sky-700 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                    B
                                </span>
                                <span class="text-slate-800 leading-snug">
                                    {{ $item['texto_b'] ?? $item['b'] ?? '' }}
                                </span>
                            </div>
                            <span id="pts-b-{{ $numero }}"
                                  class="shrink-0 px-2 py-1 rounded-lg font-mono text-xs font-black {{ $respGuardada ? 'bg-sky-700 text-white' : 'bg-slate-200 text-slate-500' }}">
                                {{ $ptsBActual !== null ? $ptsBActual . ' pts' : '—' }}
                            </span>
                        </div>
                    </div>

                    <!-- Selector Táctil de las 4 Combinaciones Válidas (Suma 3) -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($combinaciones as $clavePar => $infoPar)
                            @php
                                $checked = ($respGuardada === $clavePar);
                            @endphp
                            <label class="relative cursor-pointer">
                                <input type="radio"
                                       name="respuestas[{{ $numero }}]"
                                       value="{{ $clavePar }}"
                                       data-pregunta="{{ $numero }}"
                                       data-a="{{ $infoPar['a'] }}"
                                       data-b="{{ $infoPar['b'] }}"
                                       {{ $checked ? 'checked' : '' }}
                                       class="peer sr-only radio-valanti"
                                       onchange="alCambiarCombinacion({{ $numero }}, {{ $infoPar['a'] }}, {{ $infoPar['b'] }})">
                                <div class="flex flex-col items-center justify-center p-2.5 rounded-2xl border border-slate-200 bg-slate-50 text-slate-700 transition-all duration-150 active:scale-95 hover:bg-slate-100 hover:border-slate-300
                                            peer-checked:bg-brand-blue peer-checked:text-white peer-checked:border-brand-blue peer-checked:shadow-sm">
                                    <span class="font-mono text-sm font-black tracking-tight">
                                        {{ $infoPar['etiqueta'] }}
                                    </span>
                                    <span class="text-[10px] font-semibold opacity-80 mt-0.5">
                                        A: {{ $infoPar['a'] }} · B: {{ $infoPar['b'] }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Barra Inferior Fija de Control y Avance en Vivo -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg px-3 py-2 sm:py-3">
                <div class="absolute top-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
                    <div id="barraProgresoInferior" class="h-full bg-gradient-to-r from-brand-blue to-emerald-500 transition-all duration-300" style="width: {{ $porcentajeInicial }}%;"></div>
                </div>
                <div class="max-w-3xl mx-auto flex items-center justify-between gap-2 sm:gap-3">
                    
                    <!-- Indicador de Avance y Suma de 90 Puntos -->
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-brand-blue/10 text-brand-blue flex items-center justify-center text-[11px] sm:text-xs font-black shrink-0 border border-brand-blue/20">
                            <span id="txtRespondidasNum">{{ $totalRespondidasInicial }}</span>/30
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-xs sm:text-sm font-extrabold text-slate-900 block truncate" id="txtProgresoResumen">
                                {{ $puntosTotalesInicial }}/90 pts ({{ $porcentajeInicial }}%)
                            </span>
                            <span class="text-[10px] text-slate-500 flex items-center gap-1 truncate" id="txtEstadoGuardado">
                                <i class="fa-solid fa-cloud-arrow-up text-emerald-500 shrink-0"></i>
                                <span class="hidden sm:inline">Avance auto-guardado en el servidor</span>
                                <span class="sm:hidden">Auto-guardado activo</span>
                            </span>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        <button type="button"
                                id="btnSaltarSiguiente"
                                onclick="saltarSiguienteIncompleta()"
                                title="Saltar a la siguiente pregunta no respondida"
                                class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl sm:rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold text-xs flex items-center gap-1 transition-all cursor-pointer">
                            <i class="fa-solid fa-angles-down text-brand-blue text-xs"></i>
                            <span class="hidden sm:inline">Pendiente</span>
                        </button>

                        <button type="submit"
                                id="btnFinalizar"
                                class="h-9 sm:h-10 px-3 sm:px-4 rounded-xl sm:rounded-2xl {{ $totalRespondidasInicial == 30 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-brand-blue hover:bg-brand-dark' }} active:scale-95 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-sm flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            @if($totalRespondidasInicial == 30)
                                <span class="sm:hidden">¡Avanzar!</span>
                                <span class="hidden sm:inline">¡Avanzar a PAC-A!</span>
                            @else
                                <span class="sm:hidden">Siguiente</span>
                                <span class="hidden sm:inline">Continuar a PAC-A</span>
                            @endif
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </main>

    <script>
        const URL_BORRADOR = "{{ route('psicologia.evaluacion.valanti.borrador', ['codigo' => $codigo, 'evaluacion' => $candidato->id]) }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let debounceTimer = null;

        function alCambiarCombinacion(numeroPregunta, ptsA, ptsB) {
            const box = document.getElementById(`pregunta-box-${numeroPregunta}`);
            if (box) {
                box.classList.remove('border-rose-400', 'ring-2', 'ring-rose-200');
            }

            // Actualizar etiquetas visuales de puntos en A y B
            const badgeSuma = document.getElementById(`badge-suma-${numeroPregunta}`);
            const elPtsA = document.getElementById(`pts-a-${numeroPregunta}`);
            const elPtsB = document.getElementById(`pts-b-${numeroPregunta}`);

            if (badgeSuma) {
                badgeSuma.textContent = `A=${ptsA} | B=${ptsB} (Suma: 3)`;
                badgeSuma.className = 'text-[11px] font-mono font-bold px-2.5 py-0.5 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200';
            }
            if (elPtsA) {
                elPtsA.textContent = `${ptsA} pts`;
                elPtsA.className = 'shrink-0 px-2 py-1 rounded-lg font-mono text-xs font-black bg-brand-blue text-white';
            }
            if (elPtsB) {
                elPtsB.textContent = `${ptsB} pts`;
                elPtsB.className = 'shrink-0 px-2 py-1 rounded-lg font-mono text-xs font-black bg-sky-700 text-white';
            }

            actualizarProgresoEnVivo();

            const txtEstado = document.getElementById('txtEstadoGuardado');
            if (txtEstado) {
                txtEstado.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-500"></i> Guardando...';
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                guardarBorradorAjax();
            }, 450);
        }

        function actualizarProgresoEnVivo() {
            const respondidas = document.querySelectorAll('#formValanti input[type="radio"]:checked').length;
            const puntosTotales = respondidas * 3;
            const porcentaje = Math.round((respondidas / 30) * 100);

            const txtHeader = document.getElementById('txtProgresoHeader');
            const txtNum = document.getElementById('txtRespondidasNum');
            const txtResumen = document.getElementById('txtProgresoResumen');
            const barra = document.getElementById('barraProgreso');
            const barraInferior = document.getElementById('barraProgresoInferior');
            const btnFinalizar = document.getElementById('btnFinalizar');

            if (txtHeader) txtHeader.textContent = `${porcentaje}% (${respondidas}/30 · ${puntosTotales}/90 pts)`;
            if (txtNum) txtNum.textContent = respondidas;
            if (txtResumen) txtResumen.textContent = `${puntosTotales}/90 pts (${porcentaje}%)`;
            if (barra) barra.style.width = `${porcentaje}%`;
            if (barraInferior) barraInferior.style.width = `${porcentaje}%`;

            if (btnFinalizar) {
                if (respondidas === 30) {
                    btnFinalizar.classList.remove('bg-brand-blue');
                    btnFinalizar.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                    btnFinalizar.innerHTML = '<span class="sm:hidden">¡Avanzar!</span><span class="hidden sm:inline">¡Avanzar a PAC-A!</span> <i class="fa-solid fa-arrow-right text-xs"></i>';
                } else {
                    btnFinalizar.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    btnFinalizar.classList.add('bg-brand-blue');
                    btnFinalizar.innerHTML = '<span class="sm:hidden">Siguiente</span><span class="hidden sm:inline">Continuar a PAC-A</span> <i class="fa-solid fa-arrow-right text-xs"></i>';
                }
            }
        }

        let toastTimer = null;

        function mostrarToast(mensaje, tipo = 'exito') {
            const toast = document.getElementById('toastNotificacion');
            const msg = document.getElementById('toastMensaje');
            const icono = document.getElementById('toastIcono');
            if (!toast || !msg || !icono) return;

            msg.textContent = mensaje;
            if (tipo === 'error') {
                toast.className = 'fixed top-16 left-1/2 -translate-x-1/2 z-50 max-w-md w-[92%] rounded-2xl p-4 shadow-xl border border-rose-300 bg-rose-50 text-rose-800 text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300';
                icono.className = 'fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0';
            } else {
                toast.className = 'fixed top-16 left-1/2 -translate-x-1/2 z-50 max-w-md w-[92%] rounded-2xl p-4 shadow-xl border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold flex items-center justify-between gap-3 transition-all duration-300';
                icono.className = 'fa-solid fa-circle-check text-emerald-600 text-base shrink-0';
            }

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                ocultarToast();
            }, 4000);
        }

        function ocultarToast() {
            const toast = document.getElementById('toastNotificacion');
            if (toast) toast.classList.add('hidden');
        }

        function saltarSiguienteIncompleta() {
            for (let i = 1; i <= 30; i++) {
                const checked = document.querySelector(`input[name="respuestas[${i}]"]:checked`);
                if (!checked) {
                    const box = document.getElementById(`pregunta-box-${i}`);
                    if (box) {
                        box.classList.add('border-amber-400', 'ring-2', 'ring-amber-200');
                        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => {
                            box.classList.remove('border-amber-400', 'ring-2', 'ring-amber-200');
                        }, 2000);
                    }
                    return;
                }
            }
            mostrarToast('¡Excelente! Has completado los 30 pares (90 puntos asignados). Puedes avanzar a la Prueba 2 (PAC-A).', 'exito');
        }

        function recolectarRespuestas() {
            const formData = new FormData(document.getElementById('formValanti'));
            const respuestas = {};
            for (let [key, val] of formData.entries()) {
                if (key.startsWith('respuestas[')) {
                    const match = key.match(/respuestas\[(\d+)\]/);
                    if (match && match[1]) {
                        respuestas[match[1]] = val;
                    }
                }
            }
            return respuestas;
        }

        async function guardarBorradorAjax() {
            const txtEstado = document.getElementById('txtEstadoGuardado');
            const respuestas = recolectarRespuestas();

            try {
                const resp = await fetch(URL_BORRADOR, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ respuestas: respuestas })
                });

                if (resp.ok) {
                    const data = await resp.json();
                    if (txtEstado) {
                        txtEstado.innerHTML = `<i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i> <span class="hidden sm:inline">Avance auto-guardado (${data.respondidas}/30)</span><span class="sm:hidden">Guardado (${data.respondidas}/30)</span>`;
                    }
                } else {
                    if (txtEstado) {
                        txtEstado.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Error al sincronizar';
                    }
                }
            } catch (e) {
                if (txtEstado) {
                    txtEstado.innerHTML = '<i class="fa-solid fa-wifi text-amber-500"></i> Sin conexión directa';
                }
            }
        }

        document.getElementById('formValanti').addEventListener('submit', (e) => {
            const respondidas = document.querySelectorAll('#formValanti input[type="radio"]:checked').length;
            if (respondidas < 30) {
                e.preventDefault();
                mostrarToast(`Aún te faltan ${30 - respondidas} preguntas por responder (debes completar los 90 puntos).`, 'error');
                saltarSiguienteIncompleta();
                return false;
            }
        });

        // Auto-ocultar alerta del servidor si existe
        const alertaServ = document.getElementById('alertaServidorValanti');
        if (alertaServ) {
            setTimeout(() => {
                alertaServ.style.opacity = '0';
                setTimeout(() => alertaServ.remove(), 400);
            }, 4500);
        }

        actualizarProgresoEnVivo();
    </script>
</body>
</html>
