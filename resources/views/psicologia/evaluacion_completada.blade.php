<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Evaluación Psicotécnica Completada · C.I. Piscícola New York</title>

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
                <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-heading font-bold text-[11px] sm:text-xs border border-emerald-200 shadow-2xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span class="sm:hidden font-extrabold">Finalizado</span>
                    <span class="hidden sm:inline">Proceso Completado</span>
                </span>
            </div>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="flex-1 w-full max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-12 flex flex-col items-center justify-center">

        <!-- STEPPER DE 3 PASOS: TODOS COMPLETADOS -->
        <div class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-line/80 shadow-2xs mb-6 sm:mb-8">
            <div class="flex items-center justify-between text-center relative max-w-md mx-auto">
                <div class="absolute top-3.5 sm:top-4 left-6 right-6 sm:left-8 sm:right-8 h-0.5 bg-emerald-500 -z-0"></div>

                <!-- Paso 1 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 mt-1 sm:mt-1.5">
                        <span class="sm:hidden">1. Datos</span>
                        <span class="hidden sm:inline">1. Autorizado</span>
                    </span>
                </div>

                <!-- Paso 2 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 mt-1 sm:mt-1.5">
                        <span class="sm:hidden">2. VALANTI</span>
                        <span class="hidden sm:inline">2. Prueba 1 (VALANTI)</span>
                    </span>
                </div>

                <!-- Paso 3 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[10px] sm:text-[11px] font-bold text-emerald-700 mt-1 sm:mt-1.5">
                        <span class="sm:hidden">3. PAC-A</span>
                        <span class="hidden sm:inline">3. Prueba 2 (PAC-A)</span>
                    </span>
                </div>
            </div>

            <!-- Barra de progreso visual compacta para móvil -->
            <div class="sm:hidden mt-3 pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-600 mb-1.5">
                    <span class="text-emerald-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                        <span>Evaluaciones Concluidas</span>
                    </span>
                    <span class="text-slate-400 font-mono">100% Completado</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: 100%;"></div>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>

        <!-- TARJETA DE AGRADECIMIENTO FINAL -->
        <div class="w-full bg-white rounded-3xl p-6 sm:p-8 border border-line/80 shadow-sm text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-brand-blue"></div>

            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-2xl mx-auto mb-4 shadow-2xs">
                <i class="fa-solid fa-award"></i>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 mb-3">
                <i class="fa-solid fa-check-double"></i> Evaluación Psicotécnica 100% Completada
            </span>

            <h1 class="font-heading font-extrabold text-xl sm:text-2xl text-brand-dark">
                ¡Muchas gracias por tu participación!
            </h1>

            <p class="text-xs sm:text-sm text-muted mt-2 leading-relaxed">
                Tus respuestas en las dos pruebas psicotécnicas (<strong>VALANTI</strong> y <strong>PAC-A</strong>) junto con tu consentimiento informado <strong>GH-FO-025</strong> han sido registradas de forma confidencial.
            </p>

            @if($candidato)
                <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-500">Candidato(a):</span>
                        <strong class="font-bold text-slate-800">{{ $candidato->nombre_completo }}</strong>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-500">Cédula:</span>
                        <strong class="font-mono font-bold text-slate-800">{{ $candidato->identificacion }}</strong>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-500">Prueba 1 (VALANTI):</span>
                        <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> Finalizada (30/30 · 90 pts)</span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-slate-500">Prueba 2 (PAC-A):</span>
                        <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> Finalizada (40/40)</span>
                    </div>
                </div>
            @endif

            <div class="mt-6 p-4 rounded-2xl bg-brand-light/70 border border-brand-blue/30 text-brand-dark text-xs text-left flex items-start gap-3">
                <i class="fa-solid fa-shield-halved text-brand-blue text-base mt-0.5 shrink-0"></i>
                <div>
                    <strong class="font-heading font-extrabold block mb-0.5">Proceso Concluido</strong>
                    <span>Ya puedes cerrar esta ventana en tu dispositivo móvil o informar al profesional de Psicología.</span>
                </div>
            </div>

        </div>

    </main>

    <footer class="w-full py-4 text-center text-xs text-muted border-t border-line/70 bg-white">
        &copy; {{ date('Y') }} C.I. Piscícola New York S.A.S. · Todos los derechos reservados.
    </footer>

</body>
</html>

