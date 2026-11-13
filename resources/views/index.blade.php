<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión · C.I. Piscícola New York</title>
    <meta name="description" content="Plataforma de capacitaciones de C.I. Piscícola New York. Ingresa tu número de identificación para acceder.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- Tipografía Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ==========================================================================
           TEMA OCEÁNICO CORPORATIVO (C.I. PISCÍCOLA NEW YORK)
           TIPOGRAFÍA EN NEGRO Y OLAS ESTÁTICAS DE ALTO RENDIMIENTO
           ========================================================================== */
        :root {
            --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-head: 'Manrope', system-ui, -apple-system, sans-serif;

            /* Paleta Diurna Cristalina */
            --bg-sky-top: #e0f2fe;
            --bg-sky-mid: #bae6fd;
            --bg-sea-deep: #f0f9ff;

            --text-heading: #000000;
            --text-sub: #000000;
            --text-highlight: #0056b3;
            --badge-bg: #e0f2fe;
            --badge-text: #002d5e;
            --badge-border: rgba(56, 189, 248, 0.5);

            --input-bg: #ffffff;
            --input-border: #94a3b8;
            --input-focus-border: #0284c7;
            --input-text: #000000;
            --input-placeholder: #475569;

            --btn-gradient-1: #0284c7;
            --btn-gradient-2: #0056b3;
            --btn-gradient-3: #002d5e;
            --btn-shadow: 0 10px 24px -4px rgba(0, 86, 179, 0.35);
            --btn-shadow-hover: 0 16px 32px -4px rgba(0, 86, 179, 0.45);
        }

        body {
            font-family: var(--font-main);
            color: #000000;
        }

        .ocean-atmosphere {
            background: linear-gradient(180deg, var(--bg-sky-top) 0%, var(--bg-sky-mid) 40%, var(--bg-sea-deep) 100%);
        }

        /* Glassmorphism optimizado */
        .glass-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1.5px solid rgba(186, 230, 253, 0.95);
            box-shadow: 0 25px 60px -15px rgba(0, 61, 128, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid rgba(226, 232, 240, 0.95);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .glass-footer {
            background: rgba(255, 255, 255, 0.96);
            border-top: 1px solid rgba(226, 232, 240, 0.95);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Pendón Corporativo PNY (Foto Oficial) */
        .pny-pendant-badge {
            background: #ffffff;
            border-bottom-left-radius: 9999px;
            border-bottom-right-radius: 9999px;
            box-shadow: 0 14px 28px -4px rgba(0, 61, 128, 0.16), 0 4px 10px rgba(0, 0, 0, 0.05);
            border-left: 1.5px solid rgba(226, 232, 240, 0.95);
            border-right: 1.5px solid rgba(226, 232, 240, 0.95);
            border-bottom: 2px solid rgba(226, 232, 240, 0.95);
        }

        /* Campos de Entrada Limpios */
        .input-group-clean {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .input-wrapper-clean {
            position: relative;
            background: var(--input-bg);
            border-radius: 14px;
            border: 1.5px solid var(--input-border);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-wrapper-clean:focus-within {
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 3.5px rgba(2, 132, 199, 0.2), 0 6px 16px -4px rgba(2, 132, 199, 0.14);
        }

        /* Botón de Acceso Corporativo */
        .water-button {
            position: relative;
            background: linear-gradient(135deg, var(--btn-gradient-1) 0%, var(--btn-gradient-2) 50%, var(--btn-gradient-3) 100%);
            box-shadow: var(--btn-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            user-select: none;
        }

        .water-button:hover:not(:disabled) {
            box-shadow: var(--btn-shadow-hover);
            transform: translateY(-2px);
            filter: brightness(1.04);
        }

        .water-button:active:not(:disabled) {
            transform: translateY(0);
        }

        .water-button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            filter: grayscale(0.2);
        }

        /* Contenedor de Olas SVG 100% Estáticas (Sin movimiento) */
        .waves-container {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 38vh;
            min-height: 220px;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }
    </style>
</head>
<body class="min-h-full ocean-atmosphere text-black antialiased selection:bg-cyan-500/30 selection:text-black relative overflow-x-hidden">

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- ====================================================================
             HEADER CORPORATIVO (C.I. PISCÍCOLA NEW YORK)
             ==================================================================== -->
        <header class="w-full glass-header sticky top-0 z-40" aria-label="Cabecera principal de C.I. Piscícola New York">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-10 py-3.5 flex items-center justify-center sm:justify-between gap-4">
                
                <!-- Logo & Razón Social en Negro Nítido -->
                <div class="flex items-center justify-center gap-3 sm:gap-3.5 text-center sm:text-left">
                    <div class="h-11 sm:h-12 flex items-center justify-center p-1 rounded-xl bg-white shadow-xs border border-slate-200">
                        <img src="{{ asset('images/Logo.png') }}" 
                             alt="Logo C.I. Piscícola New York" 
                             class="h-9 sm:h-10 w-auto max-w-[140px] object-contain">
                    </div>
                    <div class="font-heading font-black text-[13px] sm:text-[14px] md:text-[15px] leading-tight tracking-[0.09em] uppercase select-none text-black">
                        C.I. PISCÍCOLA <span class="font-black text-brand-blue">NEW YORK</span>
                    </div>
                </div>

                <!-- Insignia Corporativa Limpia de Acceso Seguro (Oculta en móvil) -->
                <div class="hidden sm:flex items-center gap-2 text-xs font-bold px-3.5 py-1.5 rounded-full border shadow-2xs backdrop-blur-md"
                     style="background: var(--badge-bg); border-color: var(--badge-border); color: var(--badge-text);">
                    <span class="w-2 h-2 rounded-full bg-cyan-700 animate-pulse"></span>
                    <span>Acceso Seguro</span>
                </div>

            </div>
        </header>

        <!-- ====================================================================
             CONTENIDO CENTRAL / TARJETA PRINCIPAL DE LOGIN
             ==================================================================== -->
        <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-10 lg:px-12 py-8 sm:py-10 md:py-14 flex-1 flex items-center justify-center relative z-20">
            
            <div class="w-full max-w-5xl glass-card rounded-3xl p-6 sm:p-8 md:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative">
                
                <!-- Destello acuático sutil de fondo (Estático) -->
                <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full blur-3xl pointer-events-none opacity-30 bg-sky-300"></div>

                <!-- ============================================================
                     COLUMNA IZQUIERDA: FORMULARIO DE ACCESO (LETRAS EN NEGRO)
                     ============================================================ -->
                <section class="lg:col-span-6 w-full max-w-md mx-auto lg:mx-0 flex flex-col justify-center relative z-10" aria-labelledby="login-heading">
                    
                    <!-- ENCABEZADO CON BOTÓN DE INGRESO INDUCCIÓN -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3 gap-2">
                            <!-- Badge de sección -->
                            <div id="badge-contenedor" class="inline-flex items-center gap-2 text-[11px] font-extrabold uppercase tracking-wider px-3 py-1 rounded-full shadow-2xs border"
                                 style="background: var(--badge-bg); border-color: var(--badge-border); color: var(--badge-text);">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" />
                                </svg>
                                <span id="badge-texto">Plataforma de Capacitaciones</span>
                            </div>

                            <!-- Botoncito Pequeño: Ingreso Inducción -->
                            <button type="button"
                                    id="btn-switch-induccion"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wide transition-all cursor-pointer shadow-2xs border bg-sky-50 text-sky-800 border-sky-300 hover:bg-sky-100"
                                    title="Alternar entre inicio de sesión y acceso a inducciones">
                                <svg class="w-3.5 h-3.5 text-sky-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                                <span id="btn-switch-texto">Ingreso Inducción</span>
                            </button>
                        </div>

                        <!-- TÍTULO PRINCIPAL DINÁMICO EN NEGRO -->
                        <h1 id="login-heading" class="font-heading font-black text-3xl sm:text-4xl tracking-tight leading-[1.12] mb-2 select-none text-black">
                            Iniciar sesión
                        </h1>

                        <!-- SUBTÍTULO DINÁMICO EN NEGRO -->
                        <p id="login-subtitulo" class="text-xs sm:text-sm leading-relaxed text-black font-semibold">
                            Ingresa tu número de identificación para acceder al sistema.
                        </p>
                    </div>

                    <!-- FORMULARIO DE INICIO DE SESIÓN -->
                    <form class="flex flex-col gap-4.5" method="POST" action="{{ route('login.post') }}" id="form-login">
                        @csrf

                        <!-- Mensajes de Error de Validación -->
                        @if ($errors->any())
                            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-[13px] text-red-800 flex items-start gap-3 shadow-xs font-bold">
                                <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5 text-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    @foreach ($errors->all() as $error)
                                        <p class="leading-relaxed">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Mensaje de Estado / Notificación de Sesión -->
                        @if (session('status'))
                            <div class="p-4 rounded-2xl bg-cyan-50 border border-cyan-200 text-xs sm:text-[13px] text-cyan-900 flex items-center gap-3 shadow-xs font-bold">
                                <div class="w-5 h-5 rounded-full bg-cyan-100 flex items-center justify-center shrink-0 text-cyan-700">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        <!-- ====================================================
                             CAMPO 1: IDENTIFICACIÓN (LETRAS EN NEGRO NÍTIDO)
                             ==================================================== -->
                        <div class="input-group-clean">
                            <label for="identification" class="font-heading font-black text-[13.5px] tracking-tight flex items-center justify-between text-black">
                                <span>Número de identificación</span>
                                <span class="text-[11.5px] font-bold text-black">Cédula o documento</span>
                            </label>

                            <div class="input-wrapper-clean">
                                <div class="h-13 flex items-center px-3.5 relative">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-cyan-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                        </svg>
                                    </div>

                                    <input type="text"
                                           id="identification"
                                           name="identification"
                                           value="{{ old('identification') }}"
                                           autocomplete="username"
                                           placeholder="Ingresa tu número o documento de identificación"
                                           required
                                           class="w-full h-full px-3 border-0 outline-none bg-transparent text-sm sm:text-[15px] font-bold text-black placeholder:text-slate-600">
                                </div>
                            </div>
                        </div>

                        <!-- ====================================================
                             CAMPO 2: CONTRASEÑA O PIN (LETRAS EN NEGRO NÍTIDO)
                             ==================================================== -->
                        <div class="input-group-clean">
                            <label for="password" class="font-heading font-black text-[13.5px] tracking-tight flex items-center justify-between text-black">
                                <span id="label-campo-2">Contraseña</span>
                                <span id="sublabel-campo-2" class="text-[11.5px] font-bold text-black">Clave de acceso</span>
                            </label>

                            <div class="input-wrapper-clean">
                                <div class="h-13 flex items-center pl-3.5 pr-1.5 relative">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-cyan-700 transition-colors">
                                        <svg id="icono-campo-2" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>

                                    <input type="password"
                                           id="password"
                                           name="password"
                                           autocomplete="current-password"
                                           placeholder="Ingresa tu contraseña"
                                           required
                                           class="w-full h-full px-3 pr-11 border-0 outline-none bg-transparent text-sm sm:text-[15px] font-bold text-black placeholder:text-slate-600">

                                    <!-- Botón alternar visibilidad de contraseña -->
                                    <button type="button"
                                            id="toggle-password"
                                            class="w-10 h-10 absolute right-2 top-1.5 flex items-center justify-center rounded-xl text-slate-800 hover:text-black hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-cyan-500 transition-colors cursor-pointer"
                                            aria-label="Mostrar contraseña"
                                            aria-pressed="false">
                                        <!-- Icono Ojo (Visible) -->
                                        <svg id="icon-eye" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <!-- Icono Ojo Tachado (Oculto) -->
                                        <svg id="icon-eye-off" class="w-5 h-5 hidden transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ====================================================
                             BOTÓN DE ACCESO CORPORATIVO
                             ==================================================== -->
                        <div class="mt-2">
                            <button type="submit"
                                    id="login-submit"
                                    class="water-button h-13.5 w-full rounded-2xl text-white font-heading font-black text-[15.5px] tracking-wide cursor-pointer flex items-center justify-center gap-2.5 group">
                                <span id="submit-spinner" class="hidden">
                                    <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </span>
                                <span id="submit-texto" class="relative z-10">Iniciar sesión</span>
                                <svg id="submit-icono" class="w-4.5 h-4.5 transition-transform duration-200 group-hover:translate-x-1 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Nota al pie del formulario en Negro Nítido -->
                    <div class="mt-5 pt-4 border-t border-slate-300 flex items-center justify-center lg:justify-start gap-2 text-[12px] font-bold leading-relaxed text-black">
                        <svg class="w-4 h-4 text-cyan-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Acceso exclusivo para colaboradores de C.I. Piscícola New York.</span>
                    </div>

                </section>

                <!-- ============================================================
                     COLUMNA DERECHA: FOTOGRAFÍA INSTITUCIONAL CON PENDÓN PNY
                     ============================================================ -->
                <div class="hidden lg:flex lg:col-span-6 items-center justify-center relative select-none lg:pl-2" aria-hidden="true">
                    
                    <div class="relative w-full max-w-[420px] aspect-4/4.5 rounded-3xl overflow-hidden shadow-xl border-2 border-white group bg-slate-900">
                        
                        <!-- PENDÓN CORPORATIVO PNY EN LA MITAD DE LA IMAGEN (FOTO OFICIAL) -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-30 pny-pendant-badge w-[88px] sm:w-[98px] h-[142px] sm:h-[162px] flex flex-col items-center justify-end pb-3 pointer-events-none transition-transform duration-300">
                            <!-- Espacio superior blanco puro -->
                            <div class="w-full flex-1"></div>
                            <!-- Logo centrado en la curvatura inferior -->
                            <div class="w-[64px] sm:w-[72px] h-[64px] sm:h-[72px] flex items-center justify-center">
                                <img src="{{ asset('images/Logo.png') }}" 
                                     alt="Logo PNY C.I. Piscícola New York" 
                                     class="w-full h-full object-contain drop-shadow-xs">
                            </div>
                        </div>

                        <!-- Imagen institucional nítida con zoom suave al hover -->
                        <img src="{{ asset('images/Img-login.jpg') }}"
                             alt="C.I. Piscícola New York — Acuicultura de Excelencia"
                             loading="eager"
                             decoding="async"
                             class="w-full h-full object-cover object-center transition-transform duration-500 ease-out group-hover:scale-105">

                        <!-- Gradiente protector inferior para legibilidad de insignias -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/15 to-transparent pointer-events-none"></div>

                        <!-- Insignia flotante superior Rivera - Huila -->
                        <div class="absolute top-4 right-4 z-20 backdrop-blur-md bg-white/90 border border-white px-3.5 py-1.5 rounded-full text-black text-xs font-black shadow-md flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-600 animate-ping"></span>
                            <span>Rivera · Huila</span>
                        </div>

                        <!-- SUBTÍTULO SOBRE LA IMAGEN -->
                        <div class="absolute bottom-5 left-6 right-6 z-20">
                            <div class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-white bg-slate-950/80 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-cyan-400/40 shadow-md">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                <span>Líderes en Calidad y Sostenibilidad</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </main>

        <!-- ====================================================================
             FONDO CON OLAS SVG 100% ESTÁTICAS (SIN MOVIMIENTO, 0% CPU)
             ==================================================================== -->
        <div class="waves-container" aria-hidden="true">
            <!-- CAPA 1: OLA TRASERA ESTÁTICA -->
            <svg class="absolute left-[-5%] bottom-0 w-[110%] h-full opacity-50" viewBox="0 0 1600 320" preserveAspectRatio="none">
                <path d="M0,160 C320,90 480,230 800,150 C1120,70 1280,210 1600,140 L1600,320 L0,320 Z"
                      fill="#38bdf8"></path>
            </svg>

            <!-- CAPA 2: OLA DELANTERA ESTÁTICA CON CRESTA BRILLANTE -->
            <svg class="absolute left-[-5%] bottom-0 w-[110%] h-full opacity-85" viewBox="0 0 1600 320" preserveAspectRatio="none">
                <path d="M0,220 C360,160 560,270 900,210 C1240,150 1420,260 1600,220 L1600,320 L0,320 Z"
                      fill="#0284c7"></path>
                <path d="M0,220 C360,160 560,270 900,210 C1240,150 1420,260 1600,220"
                      fill="none" stroke="rgba(255, 255, 255, 0.85)" stroke-width="2.5"></path>
            </svg>
        </div>

        <!-- ====================================================================
             FOOTER: RIVERA - HUILA, COLOMBIA (TEXTO EN NEGRO NÍTIDO)
             ==================================================================== -->
        <footer class="w-full glass-footer relative z-30 py-4 px-4 sm:px-8 text-center" aria-label="Pie de página">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-black font-bold">
                <p class="m-0">
                    &copy; {{ date('Y') }} <strong class="font-black text-black">C.I. Piscícola New York S.A.</strong> Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-cyan-700"></span>
                    <span class="text-black font-black">Rivera - Huila, Colombia</span>
                </div>
            </div>
        </footer>

    </div>

    <!-- ====================================================================
         SCRIPTS: LÓGICA DE NEGOCIO OPTIMIZADA (SIN BUCLES NI RECALCULOS)
         ==================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* 1. MOSTRAR / OCULTAR CONTRASEÑA */
            const toggleBtn = document.getElementById('toggle-password');
            const passInput = document.getElementById('password');
            const iconEye = document.getElementById('icon-eye');
            const iconEyeOff = document.getElementById('icon-eye-off');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', () => {
                    const isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';

                    toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    toggleBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

                    if (isPassword) {
                        iconEye.classList.add('hidden');
                        iconEyeOff.classList.remove('hidden');
                    } else {
                        iconEye.classList.remove('hidden');
                        iconEyeOff.classList.add('hidden');
                    }
                });
            }

            /* 2. TOGGLE DINÁMICO: LOGIN NORMAL VS INGRESO INDUCCIÓN */
            let modoInduccion = false;
            const formLogin = document.getElementById('form-login');
            const btnSwitch = document.getElementById('btn-switch-induccion');
            const btnSwitchTexto = document.getElementById('btn-switch-texto');
            const badgeTexto = document.getElementById('badge-texto');
            const loginHeading = document.getElementById('login-heading');
            const loginSubtitulo = document.getElementById('login-subtitulo');
            const labelCampo2 = document.getElementById('label-campo-2');
            const sublabelCampo2 = document.getElementById('sublabel-campo-2');
            const submitTexto = document.getElementById('submit-texto');

            function activarModoInduccion() {
                modoInduccion = true;
                formLogin.action = "{{ route('induccion.acceso.post') }}";
                badgeTexto.textContent = "Portal de Inducciones";
                loginHeading.textContent = "Acceso a Inducción";
                loginSubtitulo.textContent = "Ingresa tu identificación y el código PIN de 4 dígitos de facilitador.";
                labelCampo2.textContent = "PIN de Facilitador (4 dígitos)";
                sublabelCampo2.textContent = "Código de Sala";
                passInput.name = "pin";
                passInput.placeholder = "•••• (4 dígitos)";
                passInput.maxLength = 4;
                passInput.pattern = "\\d{4}";
                passInput.inputMode = "numeric";
                passInput.value = "";
                submitTexto.textContent = "Ingresar a Inducciones";
                btnSwitchTexto.textContent = "← Login Normal";
                btnSwitch.classList.replace('bg-sky-50', 'bg-amber-50');
                btnSwitch.classList.replace('text-sky-800', 'text-amber-900');
                btnSwitch.classList.replace('border-sky-300', 'border-amber-400');
            }

            function activarModoNormal() {
                modoInduccion = false;
                formLogin.action = "{{ route('login.post') }}";
                badgeTexto.textContent = "Plataforma de Capacitaciones";
                loginHeading.textContent = "Iniciar sesión";
                loginSubtitulo.textContent = "Ingresa tu número de identificación para acceder al sistema.";
                labelCampo2.textContent = "Contraseña";
                sublabelCampo2.textContent = "Clave de acceso";
                passInput.name = "password";
                passInput.placeholder = "Ingresa tu contraseña";
                passInput.removeAttribute('maxLength');
                passInput.removeAttribute('pattern');
                passInput.removeAttribute('inputMode');
                passInput.value = "";
                submitTexto.textContent = "Iniciar sesión";
                btnSwitchTexto.textContent = "Ingreso Inducción";
                btnSwitch.classList.replace('bg-amber-50', 'bg-sky-50');
                btnSwitch.classList.replace('text-amber-900', 'text-sky-800');
                btnSwitch.classList.replace('border-amber-400', 'border-sky-300');
            }

            if (btnSwitch) {
                btnSwitch.addEventListener('click', () => {
                    if (modoInduccion) {
                        activarModoNormal();
                    } else {
                        activarModoInduccion();
                    }
                });
            }

            /* 3. ACTIVAR MODO INDUCCIÓN AUTOMÁTICAMENTE SI CORRESPONDE */
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('modo') === 'induccion' || @json(old('pin') !== null || $errors->has('pin'))) {
                activarModoInduccion();
            }

            /* 4. PREVENCIÓN DE DOBLE ENVÍO Y FEEDBACK VISUAL INSTANTÁNEO */
            const submitBtn = document.getElementById('login-submit');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitIcono = document.getElementById('submit-icono');

            if (formLogin && submitBtn) {
                formLogin.addEventListener('submit', () => {
                    submitBtn.disabled = true;
                    if (submitSpinner) submitSpinner.classList.remove('hidden');
                    if (submitIcono) submitIcono.classList.add('hidden');
                    if (submitTexto) submitTexto.textContent = modoInduccion ? 'Validando PIN...' : 'Iniciando sesión...';
                });
            }

        });
    </script>
</body>
</html>
