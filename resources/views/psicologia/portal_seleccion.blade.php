<!DOCTYPE html>
<html lang="es" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Portal · C.I. Piscícola New York</title>

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
<body class="min-h-screen font-sans text-ink bg-[#f8fafc] antialiased flex flex-col justify-between selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- CABECERA PRINCIPAL CON IDENTIDAD PISCÍCOLA NEW YORK -->
    <header class="w-full bg-white border-b border-line/70 shadow-2xs shrink-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 p-1 bg-white rounded-xl shadow-xs border border-line/60 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo Piscícola New York" class="h-full w-full object-contain">
                </div>
                <div>
                    <span class="block font-heading font-extrabold text-sm sm:text-base tracking-wider uppercase text-brand-dark leading-tight">
                        C.I. Piscícola New York
                    </span>
                    <span class="block text-[11px] font-semibold text-brand-blue tracking-wide uppercase">
                        Plataforma de Talento & Formación
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-light border border-brand-blue/20 text-brand-blue text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-brand-sky"></span>
                    Rol: Psicología
                </span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                            class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 border border-line/70 hover:border-red-200 text-xs font-heading font-bold transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span class="hidden sm:inline">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL: CENTRADO CON GENEROSO PADDING SUPERIOR E INFERIOR -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 flex flex-col items-center">

        <!-- BANNER DE BIENVENIDA -->
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <!-- Icono corporativo con olas y agua piscícola -->
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-brand-dark via-brand-deep to-brand-blue text-white shadow-md shadow-brand-blue/20 mb-4 ring-4 ring-brand-light">
                <i class="fa-solid fa-water text-xl text-brand-sky"></i>
            </div>

            <h1 class="font-heading font-extrabold text-2xl sm:text-3xl text-brand-dark tracking-tight">
                ¡Bienvenida/o, {{ $usuario->nombre_completo ?? 'Especialista' }}!
            </h1>

            <p class="mt-2.5 text-xs sm:text-sm text-muted max-w-xl mx-auto leading-relaxed">
                Selecciona el entorno de trabajo al que deseas acceder. Puedes alternar fácilmente entre tu <strong class="text-brand-dark">Panel de Psicología</strong> y tus <strong class="text-brand-dark">Capacitaciones</strong> en cualquier momento.
            </p>

            <!-- Datos del Colaborador (C.C., Sede, Área) bien espaciados -->
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2.5 text-xs font-semibold text-muted">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-line/80 shadow-2xs text-slate-700">
                    <i class="fa-solid fa-id-card text-brand-blue text-[11px]"></i>
                    <span>C.C. {{ $usuario->identificacion }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-line/80 shadow-2xs text-slate-700">
                    <i class="fa-solid fa-location-dot text-brand-blue text-[11px]"></i>
                    <span>{{ $usuario->sede->nombre ?? 'Sede Central' }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-line/80 shadow-2xs text-slate-700">
                    <i class="fa-solid fa-sitemap text-brand-blue text-[11px]"></i>
                    <span>{{ $usuario->area->nombre ?? 'Área de Psicología' }}</span>
                </span>
            </div>
        </div>

        <!-- GRID DE LAS 2 TARJETAS CON CLARA SEPARACIÓN -->
        <div class="w-full max-w-3xl grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 mb-8 sm:mb-10">

            <!-- TARJETA 1: PANEL DE PSICOLOGÍA -->
            <a href="{{ route('psicologia.dashboard') }}"
               class="group relative rounded-2xl p-6 sm:p-7 bg-white border border-line/90 hover:border-slate-300 transition-all duration-200 shadow-xs hover:shadow-md flex flex-col justify-between overflow-hidden">
                
                <!-- Borde decorativo superior con degradado azul marino -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-deep to-brand-blue"></div>

                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-xl bg-brand-light text-brand-blue flex items-center justify-center text-xl shadow-2xs">
                            <i class="fa-solid fa-brain"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-brand-light text-brand-blue border border-brand-blue/20">
                            Rol Profesional
                        </span>
                    </div>

                    <h2 class="font-heading font-extrabold text-xl text-brand-dark">
                        Psicología
                    </h2>

                    <p class="mt-2 text-xs sm:text-sm text-muted leading-relaxed">
                        Ingresa a tu panel para la aplicación de pruebas, consulta de reportes, historial y emisión de certificados aptitudinales.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-line/70 flex items-center justify-between text-brand-blue font-heading font-bold text-xs sm:text-sm">
                    <span>Ingresar</span>
                    <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- TARJETA 2: CAPACITACIONES (MODO COLABORADOR) -->
            <a href="{{ route('empleado.capacitaciones') }}"
               class="group relative rounded-2xl p-6 sm:p-7 bg-white border border-line/90 hover:border-slate-300 transition-all duration-200 shadow-xs hover:shadow-md flex flex-col justify-between overflow-hidden">
                
                <!-- Borde decorativo superior con degradado agua/cielo -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-brand-blue to-brand-sky"></div>

                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-xl bg-sky-50 text-brand-sky flex items-center justify-center text-xl shadow-2xs">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-sky-50 text-brand-sky border border-brand-sky/20">
                            Modo Colaborador
                        </span>
                    </div>

                    <h2 class="font-heading font-extrabold text-xl text-brand-dark">
                        Capacitaciones
                    </h2>

                    <p class="mt-2 text-xs sm:text-sm text-muted leading-relaxed">
                        Ingresa a tu portal de colaboradora para realizar tus cursos asignados, presentar evaluaciones, acumular puntos y descargar certificados.
                    </p>
                </div>

                <div class="mt-6 pt-4 border-t border-line/70 flex items-center justify-between text-brand-sky font-heading font-bold text-xs sm:text-sm">
                    <span>Ingresar</span>
                    <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

        </div>

        <!-- NOTA INFORMATIVA INFERIOR CON BUEN ESPACIO -->
        <p class="text-xs text-muted text-center flex items-center justify-center gap-1.5 pb-2">
            <i class="fa-solid fa-circle-info text-brand-blue text-[11px]"></i>
            <span>Puedes cambiar entre ambos paneles en cualquier momento desde el botón rápido ubicado en la cabecera.</span>
        </p>

    </main>

    <!-- PIE DE PÁGINA CORPORATIVO -->
    <footer class="w-full py-4 text-center text-xs text-muted border-t border-line/70 bg-white shrink-0">
        &copy; {{ date('Y') }} C.I. Piscícola New York S.A.S. · Todos los derechos reservados.
    </footer>

</body>
</html>
