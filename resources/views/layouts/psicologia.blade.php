<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Psicología') · C.I. Piscícola New York</title>
    <meta name="description" content="Panel de Psicología de C.I. Piscícola New York.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-ink bg-[#f8fafc] antialiased selection:bg-brand-blue/20 selection:text-brand-dark lg:overflow-hidden">
@php
    /** @var \App\Models\Usuario $usuario */
    $usuario = $usuario ?? auth()->user();

    $enRealizarPrueba = request()->routeIs('psicologia.pruebas.realizar');
    $enReportes = request()->routeIs('psicologia.reportes*');
    $enHistorialPruebas = request()->routeIs('psicologia.pruebas.historial*');
    $enCertificadoAptitudinal = request()->routeIs('psicologia.certificado_aptitudinal*');

    $claseItemActivo = 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5';
    $claseItemInactivo = 'text-slate-600 hover:text-brand-blue hover:bg-slate-50';
@endphp

    <div class="min-h-screen lg:h-screen flex flex-col lg:flex-row bg-[#f8fafc] lg:overflow-hidden">

        <!-- BACKDROP MÓVIL PARA SIDEBAR -->
        <div id="sidebar-backdrop"
             class="fixed inset-0 bg-brand-dark/40 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300"
             onclick="toggleSidebar(false)">
        </div>

        <!-- ========================================================
             SIDEBAR DE PSICOLOGÍA · PALETA PISCÍCOLA NEW YORK
             ======================================================== -->
        <aside id="sidebar-psicologia"
               class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white border-r border-line/70 flex flex-col justify-between shadow-lg lg:shadow-xs transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto shrink-0 lg:h-full lg:overflow-y-auto">

            <!-- PARTE SUPERIOR DEL SIDEBAR -->
            <div class="flex flex-col">

                <!-- CABECERA CON GRADIENTE CORPORATIVO Y OLAS MARINAS -->
                <div class="relative bg-gradient-to-br from-brand-dark via-brand-deep to-brand-blue pt-6 pb-9 px-6 text-white overflow-hidden shadow-xs">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute right-10 -top-8 w-24 h-24 rounded-full bg-brand-sky/10 pointer-events-none"></div>

                    <!-- Logo & Marca -->
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="h-11 w-11 p-1 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/Logo.png') }}"
                                 alt="Logo C.I. Piscícola New York"
                                 class="h-full w-full object-contain">
                        </div>
                        <div>
                            <span class="block font-heading font-extrabold text-[13px] tracking-wider uppercase leading-tight text-white">
                                Piscícola New York
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold tracking-wide text-brand-sky uppercase mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-sky animate-pulse"></span>
                                Panel de Psicología
                            </span>
                        </div>
                    </div>

                    <!-- Olas marinas conectadas al fondo del menú -->
                    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none">
                        <svg class="relative block w-full h-4 text-brand-sky/30" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,0 C60,18 120,5 180,14 C240,22 280,6 320,12 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                        <svg class="relative block w-full h-3 text-white -mt-2" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,5 C50,18 110,4 170,15 C230,22 270,7 320,14 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>

                <!-- MENÚ SOLICITADO: EXACTAMENTE 4 ELEMENTOS -->
                <nav class="px-4 py-5 flex flex-col gap-1.5" aria-label="Menú principal de psicología">

                    <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-muted/70">
                        Evaluación & Aptitud
                    </div>

                    <!-- 1. Realizar Prueba -->
                    <a href="{{ route('psicologia.pruebas.realizar') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enRealizarPrueba ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-question text-base w-5 text-center transition-colors {{ $enRealizarPrueba ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                            <span>Realizar Prueba</span>
                        </div>
                    </a>

                    <!-- 2. Reportes -->
                    <a href="{{ route('psicologia.reportes') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enReportes ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-chart-column text-base w-5 text-center transition-colors {{ $enReportes ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                            <span>Reportes</span>
                        </div>
                    </a>

                    <!-- 3. Historial de Pruebas -->
                    <a href="{{ route('psicologia.pruebas.historial') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enHistorialPruebas ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center transition-colors {{ $enHistorialPruebas ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                            <span>Historial de Pruebas</span>
                        </div>
                    </a>

                    <!-- 4. Certificado Aptitudinal -->
                    <a href="{{ route('psicologia.certificado_aptitudinal') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enCertificadoAptitudinal ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-circle-check text-base w-5 text-center transition-colors {{ $enCertificadoAptitudinal ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                            <span>Certificado Aptitudinal</span>
                        </div>
                    </a>

                    <!-- SEPARADOR Y OPCIONES DE ENTORNO -->
                    <div class="px-3 pt-5 pb-2 text-[10px] font-bold uppercase tracking-wider text-muted/70">
                        Otros Entornos
                    </div>

                    <!-- Acceso rápido a Capacitaciones (Modo Colaborador) -->
                    <a href="{{ route('empleado.capacitaciones') }}"
                       class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-heading font-bold transition-all duration-150 text-brand-dark bg-brand-light/70 hover:bg-brand-light border border-brand-blue/20">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-graduation-cap text-brand-blue text-sm"></i>
                            <span>Mis Capacitaciones</span>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-blue text-white">
                            Ir
                        </span>
                    </a>

                    <!-- Selector de Portales -->
                    <a href="{{ route('psicologia.portal_seleccion') }}"
                       class="group flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-brand-blue hover:bg-slate-50 transition">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-arrow-right-arrow-left text-slate-400 group-hover:text-brand-blue text-xs"></i>
                            <span>Cambiar de Entorno</span>
                        </div>
                    </a>

                </nav>
            </div>

            <!-- CONTENEDOR INFERIOR: USUARIO Y CERRAR SESIÓN -->
            <div class="relative bg-slate-50 border-t border-line/60 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-sm flex items-center justify-center shadow-xs shrink-0">
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'PS', 0, 1)) }}{{ strtoupper(substr(strstr($usuario->nombre_completo ?? ' P', ' ') ?: 'P', 1, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand-dark truncate leading-tight" title="{{ $usuario->nombre_completo ?? 'Psicología' }}">
                            {{ $usuario->nombre_completo ?? 'Psicología' }}
                        </p>
                        <p class="text-[10px] text-brand-blue font-semibold truncate mt-0.5">
                            C.C. {{ $usuario->identificacion ?? '' }}
                        </p>
                    </div>
                </div>

                <!-- Botón Cerrar Sesión -->
                <form method="POST" action="{{ route('logout') }}" class="m-0 mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200 font-heading font-bold text-xs transition cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ========================================================
             CONTENIDO PRINCIPAL Y HEADER SUPERIOR
             ======================================================== -->
        <div class="flex-1 flex flex-col min-w-0 lg:h-full lg:overflow-hidden">

            <!-- TOPBAR HEADER -->
            <header class="bg-white border-b border-line/70 px-4 sm:px-6 py-3.5 flex items-center justify-between shrink-0 z-30">
                <!-- Izquierda: Botón Menú Móvil y Título -->
                <div class="flex items-center gap-3">
                    <button type="button"
                            onclick="toggleSidebar(true)"
                            class="p-2 -ml-2 text-slate-600 hover:text-brand-blue hover:bg-slate-100 rounded-lg lg:hidden"
                            aria-label="Abrir menú lateral">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-muted hidden sm:inline">Psicología /</span>
                        <h1 class="font-heading font-bold text-sm sm:text-base text-brand-dark">
                            @yield('page_title', 'Panel')
                        </h1>
                    </div>
                </div>

                <!-- Derecha: Switcher Rápido a Modo Colaborador & Perfil -->
                <div class="flex items-center gap-3 sm:gap-4">

                    <!-- Switcher Button a Capacitaciones -->
                    <a href="{{ route('empleado.capacitaciones') }}"
                       title="Ir a realizar mis capacitaciones como colaboradora"
                       class="inline-flex items-center gap-2 px-3 sm:px-3.5 py-1.5 rounded-full bg-brand-light hover:bg-brand-blue/15 text-brand-blue border border-brand-blue/30 font-heading font-bold text-xs transition shadow-2xs hover:shadow-xs group">
                        <i class="fa-solid fa-graduation-cap text-brand-blue group-hover:scale-110 transition-transform"></i>
                        <span class="hidden md:inline">Modo Colaborador</span>
                        <span class="md:hidden">Capacitaciones</span>
                        <i class="fa-solid fa-arrow-right text-[10px] text-brand-blue/70"></i>
                    </a>

                    <!-- Fecha actual -->
                    <span class="hidden xl:inline-flex items-center gap-1.5 text-xs text-muted font-medium bg-slate-50 px-3 py-1.5 rounded-full border border-line/60">
                        <i class="fa-regular fa-calendar text-brand-blue"></i>
                        {{ date('d M Y') }}
                    </span>

                    <!-- Usuario -->
                    <div class="flex items-center gap-2 pl-2 border-l border-line/60">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-brand-dark leading-tight">
                                {{ $usuario->nombre_completo ?? 'Psicología' }}
                            </p>
                            <p class="text-[10px] text-brand-blue font-semibold">
                                {{ $usuario->sede->nombre ?? 'Sede Central' }}
                            </p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-brand-light text-brand-blue font-heading font-bold text-xs flex items-center justify-center border border-brand-blue/20">
                            {{ strtoupper(substr($usuario->nombre_completo ?? 'P', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- MENSAJES FLASH AUTO-OCULTABLES -->
            @if(session('success') || session('exito'))
                <div data-auto-dismiss="4000" class="notificacion-flash m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between gap-2.5 transition-all duration-500 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <span>{{ session('success') ?? session('exito') }}</span>
                    </div>
                    <button type="button" onclick="ocultarNotificacion(this.parentElement)" class="text-emerald-600 hover:text-emerald-900 p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
            @if(session('error') || session('error_validacion'))
                <div data-auto-dismiss="5000" class="notificacion-flash m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between gap-2.5 transition-all duration-500 shadow-2xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                        <span>{{ session('error') ?? session('error_validacion') }}</span>
                    </div>
                    <button type="button" onclick="ocultarNotificacion(this.parentElement)" class="text-rose-600 hover:text-rose-900 p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <!-- ÁREA DE CONTENIDO SCROLLEABLE -->
            <main class="flex-1 lg:overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- SCRIPT DE INTERACCIÓN MÓVIL DEL SIDEBAR Y AUTO-OCULTAMIENTO DE NOTIFICACIONES -->
    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar-psicologia');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function ocultarNotificacion(el) {
            if (!el) return;
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => {
                el.remove();
            }, 400);
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-auto-dismiss]').forEach((alerta) => {
                const tiempo = parseInt(alerta.getAttribute('data-auto-dismiss') || '4000', 10);
                setTimeout(() => {
                    ocultarNotificacion(alerta);
                }, tiempo);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
