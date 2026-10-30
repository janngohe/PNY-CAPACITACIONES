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
    <header class="w-full bg-white border-b border-line/80 shadow-2xs sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 p-1 bg-white rounded-xl shadow-xs border border-line/60 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="Logo C.I. Piscícola New York" class="h-full w-full object-contain">
                </div>
                <div>
                    <span class="block font-heading font-extrabold text-xs sm:text-sm tracking-wider uppercase text-brand-dark leading-tight">
                        C.I. Piscícola New York S.A.
                    </span>
                    <span class="block text-[10px] sm:text-[11px] font-semibold text-brand-blue tracking-wide uppercase">
                        Gestión Humana & Psicología Organizacional
                    </span>
                </div>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-mono font-bold text-xs border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i>
                <span>Proceso Finalizado</span>
            </span>
        </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL -->
    <main class="flex-1 w-full max-w-2xl mx-auto px-4 sm:px-6 py-8 sm:py-12 flex flex-col items-center justify-center">

        <!-- STEPPER DE 3 PASOS: TODOS COMPLETADOS -->
        <div class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-line/80 shadow-2xs mb-6 sm:mb-8">
            <div class="flex items-center justify-between text-center relative max-w-md mx-auto">
                <div class="absolute top-4 left-8 right-8 h-0.5 bg-emerald-500 -z-0"></div>

                <!-- Paso 1 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 mt-1.5">1. Autorizado</span>
                </div>

                <!-- Paso 2 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 mt-1.5">2. Prueba 1 (PAC-A)</span>
                </div>

                <!-- Paso 3 -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shadow-xs ring-4 ring-emerald-50">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 mt-1.5">3. Prueba 2 (VALANTI)</span>
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
                Tus respuestas en las dos pruebas psicotécnicas (<strong>PAC-A</strong> y <strong>VALANTI</strong>) junto con tu consentimiento informado <strong>GH-FO-025</strong> han sido registradas de forma confidencial.
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
                        <span class="text-slate-500">Prueba 1 (PAC-A):</span>
                        <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> Finalizada (40/40)</span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-slate-500">Prueba 2 (VALANTI):</span>
                        <span class="text-emerald-700 font-bold"><i class="fa-solid fa-circle-check"></i> Finalizada (30/30 · 90 pts)</span>
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

