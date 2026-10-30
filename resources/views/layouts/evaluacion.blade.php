<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Evaluación Psicotécnica') · C.I. Piscícola New York S.A.</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .touch-none {
            touch-action: none;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-800 flex flex-col justify-between selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- Encabezado Institucional -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1.5 shadow-2xs flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/Logo.png') }}" alt="C.I. Piscícola New York" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-brand-blue">
                        C.I. Piscícola New York S.A.
                    </span>
                    <h1 class="text-xs sm:text-sm font-extrabold text-slate-900 leading-tight">
                        Gestión Humana · Selección
                    </h1>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-brand-blue border border-blue-200/60">
                    <i class="fa-solid fa-user-shield text-[10px]"></i>
                    <span class="hidden sm:inline">Proceso Confidencial</span>
                    <span class="sm:hidden">Confidencial</span>
                </span>
            </div>
        </div>

        @hasSection('paso_activo')
            <!-- Stepper de avance del evaluado -->
            <div class="bg-slate-50 border-t border-slate-200/80 px-4 py-2">
                <div class="max-w-3xl mx-auto flex items-center justify-between text-[11px] font-bold text-slate-500">
                    @php $paso = trim($__env->yieldContent('paso_activo')); @endphp

                    <div class="flex items-center gap-1.5 {{ $paso >= 1 ? 'text-brand-blue' : '' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $paso >= 1 ? 'bg-brand-blue text-white shadow-2xs' : 'bg-slate-200 text-slate-600' }}">1</span>
                        <span class="hidden sm:inline">Datos</span>
                    </div>

                    <div class="h-0.5 flex-1 mx-2 {{ $paso >= 2 ? 'bg-brand-blue' : 'bg-slate-200' }}"></div>

                    <div class="flex items-center gap-1.5 {{ $paso >= 2 ? 'text-brand-blue' : '' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $paso >= 2 ? 'bg-brand-blue text-white shadow-2xs' : 'bg-slate-200 text-slate-600' }}">2</span>
                        <span class="hidden sm:inline">Consentimiento</span>
                    </div>

                    <div class="h-0.5 flex-1 mx-2 {{ $paso >= 3 ? 'bg-brand-blue' : 'bg-slate-200' }}"></div>

                    <div class="flex items-center gap-1.5 {{ $paso >= 3 ? 'text-brand-blue' : '' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $paso >= 3 ? 'bg-brand-blue text-white shadow-2xs' : 'bg-slate-200 text-slate-600' }}">3</span>
                        <span class="hidden sm:inline">Prueba</span>
                    </div>

                    <div class="h-0.5 flex-1 mx-2 {{ $paso >= 4 ? 'bg-brand-blue' : 'bg-slate-200' }}"></div>

                    <div class="flex items-center gap-1.5 {{ $paso >= 4 ? 'text-brand-blue' : '' }}">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black {{ $paso >= 4 ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-200 text-slate-600' }}">4</span>
                        <span class="hidden sm:inline">Finalización</span>
                    </div>
                </div>
            </div>
        @endif
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 w-full max-w-3xl mx-auto px-4 py-5 sm:py-8">
        @if(session('exito'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                <span>{{ session('exito') }}</span>
            </div>
        @endif

        @if(session('advertencia'))
            <div class="mb-5 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                <span>{{ session('advertencia') }}</span>
            </div>
        @endif

        @if(session('error_validacion'))
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start gap-2.5 shadow-2xs">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-base shrink-0 mt-0.5"></i>
                <div>
                    <span>{{ session('error_validacion') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Pie de Página Institucional -->
    <footer class="bg-white border-t border-slate-200 py-4 px-4 text-center text-slate-400 text-[11px]">
        <p class="font-medium text-slate-500">C.I. Piscícola New York S.A. &copy; {{ date('Y') }}</p>
        <p class="text-[10px] mt-0.5 text-slate-400">Sistema Seguro de Gestión Humana y Pruebas Psicotécnicas</p>
    </footer>

    @stack('scripts')
</body>
</html>
