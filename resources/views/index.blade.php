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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ==========================================================================
           TEMA OCEÁNICO DIURNO (DÍA POR DEFECTO - C.I. PISCÍCOLA NEW YORK)
           ========================================================================== */
        :root {
            --font-main: 'Plus Jakarta Sans', 'DM Sans', sans-serif;
            --font-head: 'Manrope', sans-serif;

            /* Paleta Diurna Cristalina */
            --bg-sky-top: #e0f2fe;
            --bg-sky-mid: #bae6fd;
            --bg-sea-deep: #f0f9ff;
            --ambient-glow: rgba(14, 165, 233, 0.16);

            --wave-color-back: rgba(56, 189, 248, 0.28);
            --wave-color-mid: rgba(2, 132, 199, 0.38);
            --wave-color-fore: rgba(0, 86, 179, 0.75);
            --wave-crest: rgba(255, 255, 255, 0.95);

            --card-bg: rgba(255, 255, 255, 0.92);
            --card-border: rgba(186, 230, 253, 0.9);
            --card-shadow: 0 25px 60px -15px rgba(0, 61, 128, 0.14), 0 0 0 1px rgba(255, 255, 255, 0.95);

            --text-heading: #002d5e;
            --text-sub: #475569;
            --text-highlight: #0056b3;
            --badge-bg: #e0f2fe;
            --badge-text: #0369a1;
            --badge-border: rgba(56, 189, 248, 0.45);

            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --input-focus-border: #0284c7;
            --input-text: #0f172a;
            --input-placeholder: #94a3b8;
            --input-wave-glow: #0284c7;

            --btn-gradient-1: #0284c7;
            --btn-gradient-2: #0056b3;
            --btn-gradient-3: #002d5e;
            --btn-shadow: 0 12px 28px -5px rgba(0, 86, 179, 0.35);
            --btn-shadow-hover: 0 18px 36px -6px rgba(0, 86, 179, 0.45);

            --header-bg: rgba(255, 255, 255, 0.92);
            --header-border: rgba(226, 232, 240, 0.85);
            --footer-bg: rgba(255, 255, 255, 0.92);
        }

        body {
            font-family: var(--font-main);
        }

        .ocean-atmosphere {
            background: linear-gradient(180deg, var(--bg-sky-top) 0%, var(--bg-sky-mid) 42%, var(--bg-sea-deep) 100%);
        }

        /* ==========================================================================
           1. OLAS SVG ANIMADAS & RESPONSIVAS AL CURSOR
           ========================================================================== */
        .waves-container {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 48vh;
            min-height: 280px;
            pointer-events: none;
            overflow: hidden;
            z-index: 1;
        }

        .wave-layer {
            position: absolute;
            left: -10%;
            bottom: 0;
            width: 120%;
            height: 100%;
            will-change: transform;
            transition: transform 0.18s cubic-bezier(0.2, 0, 0.2, 1);
        }

        @keyframes tidalWaveBack {
            0%   { transform: translate3d(0, 0, 0); }
            50%  { transform: translate3d(-3%, 8px, 0); }
            100% { transform: translate3d(0, 0, 0); }
        }

        @keyframes tidalWaveMid {
            0%   { transform: translate3d(0, 0, 0); }
            50%  { transform: translate3d(3.5%, -10px, 0); }
            100% { transform: translate3d(0, 0, 0); }
        }

        @keyframes tidalWaveFore {
            0%   { transform: translate3d(0, 0, 0); }
            50%  { transform: translate3d(-4%, 6px, 0); }
            100% { transform: translate3d(0, 0, 0); }
        }

        .wave-motion-back {
            animation: tidalWaveBack 16s ease-in-out infinite;
        }
        .wave-motion-mid {
            animation: tidalWaveMid 11s ease-in-out infinite;
        }
        .wave-motion-fore {
            animation: tidalWaveFore 8s ease-in-out infinite;
        }

        /* Partículas de burbujas marinas */
        .bubble-particle {
            position: absolute;
            bottom: -20px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.9), rgba(14, 165, 233, 0.35));
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.45);
            pointer-events: none;
            animation: floatBubble linear infinite;
        }

        @keyframes floatBubble {
            0% {
                transform: translateY(0) translateX(0) scale(0.6);
                opacity: 0;
            }
            15% {
                opacity: 0.8;
            }
            85% {
                opacity: 0.7;
            }
            100% {
                transform: translateY(-90vh) translateX(var(--drift-x, 30px)) scale(1.1);
                opacity: 0;
            }
        }

        /* ==========================================================================
           2. BOTÓN DE ACCESO CON EFECTO RIPPLE TIPO AGUA
           ========================================================================== */
        .water-button {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--btn-gradient-1) 0%, var(--btn-gradient-2) 50%, var(--btn-gradient-3) 100%);
            box-shadow: var(--btn-shadow);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
            isolation: isolate;
        }

        .water-button:hover {
            box-shadow: var(--btn-shadow-hover);
            transform: translateY(-2px);
        }

        .water-button:active {
            transform: translateY(1px);
        }

        .water-button::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.04) 45%, transparent 100%);
            pointer-events: none;
            z-index: 2;
        }

        .water-button::after {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: skewX(-20deg);
            animation: buttonGleam 5s infinite;
            pointer-events: none;
            z-index: 2;
        }

        @keyframes buttonGleam {
            0%, 65% { left: -150%; }
            100%    { left: 180%; }
        }

        /* Ripple concéntrico de agua al clic */
        .water-ripple-ring {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            transform: translate(-50%, -50%) scale(0);
            animation: waterDropletRipple 0.85s cubic-bezier(0.1, 0.7, 0.1, 1) forwards;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.7) 0%, rgba(56, 189, 248, 0.45) 40%, rgba(255, 255, 255, 0.1) 70%, transparent 100%);
            box-shadow: 0 0 16px rgba(255, 255, 255, 0.75), inset 0 0 10px rgba(56, 189, 248, 0.6);
            z-index: 3;
        }

        @keyframes waterDropletRipple {
            0% {
                width: 0px;
                height: 0px;
                opacity: 0.95;
                transform: translate(-50%, -50%) scale(0);
            }
            60% {
                opacity: 0.6;
            }
            100% {
                width: 480px;
                height: 480px;
                opacity: 0;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Microgotas splash */
        .water-splash-dot {
            position: absolute;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.9);
            pointer-events: none;
            z-index: 4;
            animation: splashFly 0.6s cubic-bezier(0.15, 0.9, 0.25, 1) forwards;
        }

        @keyframes splashFly {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--splash-x), var(--splash-y)) scale(0);
                opacity: 0;
            }
        }

        /* ==========================================================================
           3. CAMPO DE ENTRADA LIMPIO Y CON ILUMINACIÓN ACUÁTICA AL FOCO
           ========================================================================== */
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .input-wrapper-clean:focus-within {
            border-color: var(--input-focus-border);
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.16), 0 8px 20px -4px rgba(2, 132, 199, 0.14);
            transform: translateY(-1px);
        }


        /* ==========================================================================
           FORMA COLGANTE CORPORATIVA PNY (ESTILO PENDÓN FOTOGRAFÍA OFICIAL)
           ========================================================================== */
        .pny-pendant-badge {
            background: #ffffff;
            border-bottom-left-radius: 9999px;
            border-bottom-right-radius: 9999px;
            box-shadow: 0 14px 32px -4px rgba(0, 61, 128, 0.15), 0 4px 10px rgba(0, 0, 0, 0.05);
            border-left: 1.5px solid rgba(226, 232, 240, 0.95);
            border-right: 1.5px solid rgba(226, 232, 240, 0.95);
            border-bottom: 2px solid rgba(226, 232, 240, 0.95);
        }

        /* Glassmorphism de la tarjeta */
        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .glass-header {
            background: var(--header-bg);
            border-bottom: 1px solid var(--header-border);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .glass-footer {
            background: var(--footer-bg);
            border-top: 1px solid var(--header-border);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .water-caustics-overlay {
            background: radial-gradient(circle at 50% 30%, rgba(255, 255, 255, 0.25) 0%, transparent 60%);
            mix-blend-mode: overlay;
        }

        #water-cursor-ripple {
            position: fixed;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            pointer-events: none;
            border: 1.5px solid rgba(14, 165, 233, 0.45);
            box-shadow: 0 0 12px rgba(56, 189, 248, 0.3);
            transform: translate(-50%, -50%) scale(0.6);
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.15s ease-out;
            z-index: 9999;
        }
    </style>
</head>
<body class="min-h-full ocean-atmosphere text-slate-800 antialiased selection:bg-cyan-500/30 selection:text-cyan-900 relative overflow-x-hidden">

    <!-- Círculo ripple decorativo que acompaña suavemente al cursor en la ventana -->
    <div id="water-cursor-ripple" aria-hidden="true"></div>

    <div class="min-h-screen flex flex-col justify-between relative z-10">

        <!-- ====================================================================
             HEADER CORPORATIVO (SIN "PORTAL DE CAPACITACIONES", SOLO RAZÓN SOCIAL)
             ==================================================================== -->
        <header class="w-full glass-header sticky top-0 z-40" aria-label="Cabecera principal de C.I. Piscícola New York">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-10 py-3.5 flex items-center justify-center sm:justify-between gap-4">
                
                <!-- Logo & Razón Social Limpia (Sin "Portal de Capacitaciones") -->
                <div class="flex items-center justify-center gap-3 sm:gap-3.5 text-center sm:text-left">
                    <div class="h-11 sm:h-12 md:h-13 flex items-center justify-center p-1 rounded-xl bg-white shadow-xs border border-slate-200/80">
                        <img src="{{ asset('images/Logo.png') }}" 
                             alt="Logo C.I. Piscícola New York" 
                             class="h-9 sm:h-10 md:h-11 w-auto max-w-[140px] object-contain">
                    </div>
                    <div class="font-heading font-extrabold text-[13px] sm:text-[14px] md:text-[15px] leading-tight tracking-[0.09em] uppercase select-none" style="color: var(--text-heading);">
                        C.I. PISCÍCOLA <span class="font-bold" style="color: var(--text-highlight);">NEW YORK</span>
                    </div>
                </div>

                <!-- Insignia Corporativa Limpia de Acceso Seguro (Oculta en móvil) -->
                <div class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3.5 py-1.5 rounded-full border shadow-2xs backdrop-blur-md"
                     style="background: var(--badge-bg); border-color: var(--badge-border); color: var(--badge-text);">
                    <span class="w-2 h-2 rounded-full bg-cyan-600 animate-pulse"></span>
                    <span>Acceso Seguro</span>
                </div>

            </div>
        </header>

        <!-- ====================================================================
             CONTENIDO CENTRAL / CONTENEDOR CON LA FORMA DEL PENDÓN PNY EN LA MITAD
             ==================================================================== -->
        <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-10 lg:px-12 py-8 sm:py-10 md:py-14 flex-1 flex items-center justify-center relative z-20">
            
            <div class="w-full max-w-5xl glass-card rounded-3xl p-6 sm:p-8 md:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative">
                
                <!-- Destello acuático de fondo -->
                <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full blur-3xl pointer-events-none opacity-40"
                     style="background: var(--input-wave-glow);"></div>

                <!-- ============================================================
                     COLUMNA IZQUIERDA: FORMULARIO DE ACCESO (LÓGICA INTACTA)
                     ============================================================ -->
                <section class="lg:col-span-6 w-full max-w-md mx-auto lg:mx-0 flex flex-col justify-center relative z-10" aria-labelledby="login-heading">
                    
                    <!-- ENCABEZADO CON BOTÓN DE INGRESO INDUCCIÓN -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3 gap-2">
                            <!-- Badge de sección -->
                            <div id="badge-contenedor" class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-2xs border backdrop-blur-sm"
                                 style="background: var(--badge-bg); border-color: var(--badge-border); color: var(--badge-text);">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" />
                                </svg>
                                <span id="badge-texto">Plataforma de Capacitaciones</span>
                            </div>

                            <!-- Botoncito Pequeño: Ingreso Inducción -->
                            <button type="button"
                                    id="btn-switch-induccion"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wide transition-all cursor-pointer shadow-2xs border bg-sky-50 text-sky-700 border-sky-200 hover:bg-sky-100"
                                    title="Alternar entre inicio de sesión y acceso a inducciones">
                                <svg class="w-3.5 h-3.5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                                </svg>
                                <span id="btn-switch-texto">Ingreso Inducción</span>
                            </button>
                        </div>

                        <!-- TÍTULO PRINCIPAL DINÁMICO -->
                        <h1 id="login-heading" class="font-heading font-extrabold text-3xl sm:text-4xl tracking-tight leading-[1.12] mb-2 select-none" style="color: var(--text-heading);">
                            Iniciar sesión
                        </h1>

                        <!-- SUBTÍTULO DINÁMICO -->
                        <p id="login-subtitulo" class="text-xs sm:text-sm leading-relaxed text-slate-500 font-medium">
                            Ingresa tu número de identificación para acceder al sistema.
                        </p>
                    </div>

                    <!-- FORMULARIO DE INICIO DE SESIÓN -->
                    <form class="flex flex-col gap-4.5" method="POST" action="{{ route('login.post') }}" id="form-login">
                        @csrf

                        <!-- Mensajes de Error de Validación -->
                        @if ($errors->any())
                            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-xs sm:text-[13px] text-red-700 flex items-start gap-3 shadow-xs">
                                <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5 text-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="flex-1 font-medium">
                                    @foreach ($errors->all() as $error)
                                        <p class="leading-relaxed">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Mensaje de Estado / Notificación de Sesión -->
                        @if (session('status'))
                            <div class="p-4 rounded-2xl bg-cyan-50 border border-cyan-200 text-xs sm:text-[13px] text-cyan-800 flex items-center gap-3 shadow-xs">
                                <div class="w-5 h-5 rounded-full bg-cyan-100 flex items-center justify-center shrink-0 text-cyan-700">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <span class="font-medium">{{ session('status') }}</span>
                            </div>
                        @endif

                        <!-- ====================================================
                             CAMPO 1: IDENTIFICACIÓN (LIMPIO, SIN OLITAS AL TIPEAR)
                             ==================================================== -->
                        <div class="input-group-clean">
                            <label for="identification" class="font-heading font-bold text-[13px] tracking-tight flex items-center justify-between" style="color: var(--text-heading);">
                                <span>Número de identificación</span>
                                <span class="text-[11px] font-normal text-slate-400">Cédula de ciudadanía</span>
                            </label>

                            <div class="input-wrapper-clean">
                                <div class="h-13 flex items-center px-3.5 relative">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-cyan-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                        </svg>
                                    </div>

                                    <input type="text"
                                           id="identification"
                                           name="identification"
                                           value="{{ old('identification') }}"
                                           inputmode="numeric"
                                           autocomplete="username"
                                           placeholder="Ingresa tu número de cédula"
                                           required
                                           class="w-full h-full px-3 border-0 outline-none bg-transparent text-sm sm:text-[15px] font-medium font-sans"
                                           style="color: var(--input-text);">
                                </div>
                            </div>
                        </div>

                        <!-- ====================================================
                             CAMPO 2: CONTRASEÑA O PIN DE INDUCCIÓN
                             ==================================================== -->
                        <div class="input-group-clean">
                            <label for="password" class="font-heading font-bold text-[13px] tracking-tight flex items-center justify-between" style="color: var(--text-heading);">
                                <span id="label-campo-2">Contraseña</span>
                                <span id="sublabel-campo-2" class="text-[11px] font-normal text-slate-400">Clave de acceso</span>
                            </label>

                            <div class="input-wrapper-clean">
                                <div class="h-13 flex items-center pl-3.5 pr-1.5 relative">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-cyan-600 transition-colors">
                                        <svg id="icono-campo-2" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>

                                    <input type="password"
                                           id="password"
                                           name="password"
                                           autocomplete="current-password"
                                           placeholder="Ingresa tu contraseña"
                                           required
                                           class="w-full h-full px-3 pr-11 border-0 outline-none bg-transparent text-sm sm:text-[15px] font-medium font-sans"
                                           style="color: var(--input-text);">

                                    <!-- Botón alternar visibilidad de contraseña -->
                                    <button type="button"
                                            id="toggle-password"
                                            class="w-10 h-10 absolute right-2 top-1.5 flex items-center justify-center rounded-xl text-slate-400 hover:text-cyan-600 hover:bg-cyan-50 focus-visible:outline-2 focus-visible:outline-cyan-500 transition-colors cursor-pointer"
                                            aria-label="Mostrar contraseña"
                                            aria-pressed="false">
                                        <!-- Icono Ojo (Visible) -->
                                        <svg id="icon-eye" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <!-- Icono Ojo Tachado (Oculto) -->
                                        <svg id="icon-eye-off" class="w-5 h-5 hidden transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ====================================================
                             BOTÓN DE ACCESO CON EFECTO RIPPLE TIPO AGUA
                             ==================================================== -->
                        <div class="mt-2">
                            <button type="submit"
                                    id="login-submit"
                                    class="water-button h-13.5 w-full rounded-2xl text-white font-heading font-extrabold text-[15px] tracking-wide cursor-pointer flex items-center justify-center gap-2.5 group">
                                <span id="submit-texto" class="relative z-10">Iniciar sesión</span>
                                <svg class="w-4.5 h-4.5 transition-transform duration-300 group-hover:translate-x-1 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Nota al pie del formulario -->
                    <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-center lg:justify-start gap-2 text-[11.5px] leading-relaxed text-slate-500">
                        <svg class="w-4 h-4 text-cyan-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Acceso exclusivo para colaboradores de C.I. Piscícola New York.</span>
                    </div>

                </section>

                <!-- ============================================================
                     COLUMNA DERECHA: IMAGEN LIMPIA CON SUBTÍTULO
                     ============================================================ -->
                <div class="hidden lg:flex lg:col-span-6 items-center justify-center relative select-none lg:pl-2" aria-hidden="true">
                    
                    <div class="relative w-full max-w-[420px] aspect-4/4.5 rounded-3xl overflow-hidden shadow-xl border-2 border-white group">
                        
                        <!-- ============================================================
                             PENDÓN CORPORATIVO PNY EN LA MITAD DE LA IMAGEN (FOTO OFICIAL)
                             Forma vertical blanca idéntica a la imagen corporativa oficial
                             con terminación semicircular y el logo en la curvatura inferior
                             ============================================================ -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-30 pny-pendant-badge w-[88px] sm:w-[98px] h-[142px] sm:h-[162px] flex flex-col items-center justify-end pb-3 pointer-events-none transition-all duration-300">
                            <!-- Espacio superior blanco puro -->
                            <div class="w-full flex-1"></div>
                            <!-- Logo centrado en la curvatura inferior -->
                            <div class="w-[64px] sm:w-[72px] h-[64px] sm:h-[72px] flex items-center justify-center">
                                <img src="{{ asset('images/Logo.png') }}" 
                                     alt="Logo PNY C.I. Piscícola New York" 
                                     class="w-full h-full object-contain drop-shadow-xs">
                            </div>
                        </div>

                        <!-- Imagen institucional con zoom suave al hover -->
                        <img src="{{ asset('images/Img-login.jpg') }}"
                             alt="C.I. Piscícola New York — Acuicultura de Excelencia"
                             loading="lazy"
                             decoding="async"
                             class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105">

                        <!-- Efecto caústico y gradiente sutil sobre la foto -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-slate-900/10 to-transparent pointer-events-none"></div>
                        <div class="absolute inset-0 water-caustics-overlay pointer-events-none"></div>

                        <!-- Olas SVG decorativas inferiores sobre la imagen -->
                        <svg class="absolute w-full h-20 bottom-0 left-0 pointer-events-none z-10" viewBox="0 0 500 120" preserveAspectRatio="none">
                            <path d="M0,45 C150,95 320,15 500,55 L500,120 L0,120 Z" fill="rgba(14, 165, 233, 0.4)"/>
                            <path d="M0,65 C180,25 350,105 500,75 L500,120 L0,120 Z" fill="rgba(2, 132, 199, 0.55)"/>
                            <path d="M0,85 C140,115 320,55 500,90 L500,120 L0,120 Z" fill="rgba(0, 45, 94, 0.75)"/>
                            <path d="M0,85 C140,115 320,55 500,90" fill="none" stroke="rgba(255, 255, 255, 0.8)" stroke-width="2"/>
                        </svg>

                        <!-- Insignia flotante superior Rivera - Huila -->
                        <div class="absolute top-4 right-4 z-20 backdrop-blur-md bg-white/80 border border-white/90 px-3.5 py-1.5 rounded-full text-slate-800 text-xs font-bold shadow-md flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></span>
                            <span>Rivera · Huila</span>
                        </div>

                        <!-- SUBTÍTULO SOBRE LA IMAGEN (SIN DESCRIPCIÓN) -->
                        <div class="absolute bottom-5 left-6 right-6 z-20">
                            <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-200 bg-slate-950/60 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-cyan-400/30 shadow-md">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                <span>Líderes en Calidad y Sostenibilidad</span>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </main>

        <!-- ====================================================================
             1. FONDO CON OLAS SVG ANIMADAS QUE REACCIONAN AL CURSOR
             ==================================================================== -->
        <div class="waves-container" id="waves-scene" aria-hidden="true">
            
            <!-- CAPA 1: OLA TRASERA -->
            <svg class="wave-layer wave-motion-back" id="wave-layer-back" viewBox="0 0 1600 320" preserveAspectRatio="none">
                <path d="M0,160 C320,80 480,240 800,150 C1120,60 1280,220 1600,140 L1600,320 L0,320 Z"
                      fill="var(--wave-color-back)"></path>
            </svg>

            <!-- CAPA 2: OLA INTERMEDIA -->
            <svg class="wave-layer wave-motion-mid" id="wave-layer-mid" viewBox="0 0 1600 320" preserveAspectRatio="none">
                <path d="M0,190 C280,260 520,110 800,200 C1080,280 1320,130 1600,210 L1600,320 L0,320 Z"
                      fill="var(--wave-color-mid)"></path>
            </svg>

            <!-- CAPA 3: OLA DELANTERA CON CRESTA BRILLANTE -->
            <svg class="wave-layer wave-motion-fore" id="wave-layer-fore" viewBox="0 0 1600 320" preserveAspectRatio="none">
                <path d="M0,230 C360,170 560,280 900,220 C1240,160 1420,270 1600,230 L1600,320 L0,320 Z"
                      fill="var(--wave-color-fore)"></path>
                <path d="M0,230 C360,170 560,280 900,220 C1240,160 1420,270 1600,230"
                      fill="none" stroke="var(--wave-crest)" stroke-width="2.5"></path>
            </svg>

            <!-- Burbujas marinas flotantes -->
            <div id="bubbles-container"></div>
        </div>

        <!-- ====================================================================
             FOOTER ACTUALIZADO: RIVERA - HUILA, COLOMBIA
             ==================================================================== -->
        <footer class="w-full glass-footer relative z-30 py-4 px-4 sm:px-8 text-center" aria-label="Pie de página">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <p class="m-0">
                    &copy; {{ date('Y') }} <strong class="font-bold text-slate-800">C.I. Piscícola New York S.A.</strong> Todos los derechos reservados.
                </p>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-cyan-600"></span>
                    <span>Rivera - Huila, Colombia</span>
                </div>
            </div>
        </footer>

    </div>

    <!-- ====================================================================
         SCRIPTS: INTERACTIVIDAD (OLAS, RIPPLE, CURSOR, PASSWORD TOGGLE)
         ==================================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {


            /* ================================================================
               2. FONDO CON OLAS SVG ANIMADAS QUE REACCIONAN AL CURSOR (REQ #1)
               ================================================================ */
            const waveBack = document.getElementById('wave-layer-back');
            const waveMid  = document.getElementById('wave-layer-mid');
            const waveFore = document.getElementById('wave-layer-fore');
            const cursorRipple = document.getElementById('water-cursor-ripple');

            let mouseX = window.innerWidth / 2;
            let mouseY = window.innerHeight / 2;
            let targetX = 0;
            let targetY = 0;
            let currentX = 0;
            let currentY = 0;

            window.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;

                // Normalización de -1 a 1 relativa al centro
                targetX = (mouseX / window.innerWidth - 0.5) * 2;
                targetY = (mouseY / window.innerHeight - 0.5) * 2;

                // Círculo acuático sutil que acompaña al cursor
                if (cursorRipple) {
                    cursorRipple.style.left = `${mouseX}px`;
                    cursorRipple.style.top = `${mouseY}px`;
                    cursorRipple.style.opacity = '0.45';
                }
            }, { passive: true });

            window.addEventListener('mouseleave', () => {
                targetX = 0;
                targetY = 0;
                if (cursorRipple) cursorRipple.style.opacity = '0';
            });

            // Loop de interpolación suave (LERP 60fps) para mover las olas con profundidad
            function animateWavesParallax() {
                currentX += (targetX - currentX) * 0.055;
                currentY += (targetY - currentY) * 0.055;

                if (waveBack) {
                    waveBack.style.transform = `translate3d(${currentX * -18}px, ${currentY * 8}px, 0)`;
                }
                if (waveMid) {
                    waveMid.style.transform = `translate3d(${currentX * 28}px, ${currentY * -12}px, 0)`;
                }
                if (waveFore) {
                    waveFore.style.transform = `translate3d(${currentX * -38}px, ${currentY * 16}px, 0)`;
                }

                requestAnimationFrame(animateWavesParallax);
            }
            animateWavesParallax();

            // Generación de burbujas acuáticas que ascienden suavemente
            const bubblesContainer = document.getElementById('bubbles-container');
            if (bubblesContainer) {
                const bubbleCount = 14;
                for (let i = 0; i < bubbleCount; i++) {
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble-particle';
                    const size = Math.random() * 14 + 6;
                    const left = Math.random() * 100;
                    const duration = Math.random() * 10 + 8;
                    const delay = Math.random() * 8;
                    const drift = (Math.random() - 0.5) * 80;

                    bubble.style.width = `${size}px`;
                    bubble.style.height = `${size}px`;
                    bubble.style.left = `${left}%`;
                    bubble.style.animationDuration = `${duration}s`;
                    bubble.style.animationDelay = `${delay}s`;
                    bubble.style.setProperty('--drift-x', `${drift}px`);

                    bubblesContainer.appendChild(bubble);
                }
            }

            /* ================================================================
               3. BOTÓN DE ACCESO CON EFECTO RIPPLE TIPO AGUA (REQ #2)
               ================================================================ */
            const loginSubmitBtn = document.getElementById('login-submit');
            if (loginSubmitBtn) {
                loginSubmitBtn.addEventListener('pointerdown', (e) => {
                    const rect = loginSubmitBtn.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    // Anillo de onda concéntrica de agua
                    const rippleRing = document.createElement('span');
                    rippleRing.className = 'water-ripple-ring';
                    rippleRing.style.left = `${x}px`;
                    rippleRing.style.top = `${y}px`;
                    loginSubmitBtn.appendChild(rippleRing);

                    // Micro-gotas radiales
                    const splashCount = 6;
                    for (let i = 0; i < splashCount; i++) {
                        const dot = document.createElement('span');
                        dot.className = 'water-splash-dot';
                        dot.style.left = `${x}px`;
                        dot.style.top = `${y}px`;
                        
                        const angle = (i / splashCount) * 2 * Math.PI + (Math.random() * 0.4);
                        const distance = Math.random() * 40 + 25;
                        const splashX = Math.cos(angle) * distance;
                        const splashY = Math.sin(angle) * distance;

                        dot.style.setProperty('--splash-x', `${splashX}px`);
                        dot.style.setProperty('--splash-y', `${splashY}px`);
                        loginSubmitBtn.appendChild(dot);

                        setTimeout(() => dot.remove(), 600);
                    }

                    setTimeout(() => rippleRing.remove(), 900);
                });
            }

            /* ================================================================
               4. MOSTRAR / OCULTAR CONTRASEÑA (LÓGICA EXISTENTE PRESERVADA)
               ================================================================ */
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

            /* ================================================================
               5. TOGGLE DINÁMICO: LOGIN NORMAL VS INGRESO INDUCCIÓN
               ================================================================ */
            let modoInduccion = false;
            const formLogin = document.getElementById('form-login');
            const btnSwitch = document.getElementById('btn-switch-induccion');
            const btnSwitchTexto = document.getElementById('btn-switch-texto');
            const badgeTexto = document.getElementById('badge-texto');
            const loginHeading = document.getElementById('login-heading');
            const loginSubtitulo = document.getElementById('login-subtitulo');
            const labelCampo2 = document.getElementById('label-campo-2');
            const sublabelCampo2 = document.getElementById('sublabel-campo-2');
            const iconoCampo2 = document.getElementById('icono-campo-2');
            const submitTexto = document.getElementById('submit-texto');

            function activarModoInduccion() {
                modoInduccion = true;
                formLogin.action = "{{ route('induccion.acceso.post') }}";
                badgeTexto.textContent = "Portal de Inducciones";
                loginHeading.textContent = "Acceso a Inducción";
                loginSubtitulo.textContent = "Ingresa tu número de cédula y el código PIN de 4 dígitos de facilitador.";
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
                btnSwitch.classList.replace('text-sky-700', 'text-amber-800');
                btnSwitch.classList.replace('border-sky-200', 'border-amber-300');
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
                btnSwitch.classList.replace('text-amber-800', 'text-sky-700');
                btnSwitch.classList.replace('border-amber-300', 'border-sky-200');
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

            // Activar automáticamente modo inducción si viene por query param ?modo=induccion o si hay error de PIN
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('modo') === 'induccion' || @json(old('pin') !== null || $errors->has('pin'))) {
                activarModoInduccion();
            }

        });
    </script>
</body>
</html>
