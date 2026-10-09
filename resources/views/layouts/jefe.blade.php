<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Capacitaciones') · Portal del Jefe de Área · C.I. Piscícola New York</title>
    <meta name="description" content="Panel del Jefe de Área para publicar capacitaciones, crear evaluaciones y consultar el progreso y resultados del personal de C.I. Piscícola New York.">

    <!-- Tipografía Google Fonts (misma del panel del empleado) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink bg-slate-50 antialiased selection:bg-brand-blue/20 selection:text-brand-dark">
@php
    $enCapacitaciones = request()->routeIs('jefe.dashboard', 'jefe.capacitaciones.index', 'jefe.capacitaciones.show', 'jefe.capacitaciones.edit');
    $enCrearCapacitacion = request()->routeIs('jefe.capacitaciones.create');
    $enCrearEvaluacion = request()->routeIs('jefe.evaluaciones.create');
    $enMisEvaluaciones = request()->routeIs('jefe.evaluaciones.index', 'jefe.evaluaciones.edit');
    $enResultados = request()->routeIs('jefe.resultados.*');
    $grupoEvaluacionAbierto = $enCrearEvaluacion || $enMisEvaluaciones || $enResultados;

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
             MENÚ LATERAL IZQUIERDO (SIDEBAR) CON EFECTO DE OLAS
             ======================================================== -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white border-r border-line/70 flex flex-col justify-between shadow-lg lg:shadow-xs transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto shrink-0 lg:h-full lg:overflow-y-auto">

            <div class="flex flex-col">

                <!-- CABECERA DEL SIDEBAR CON GRADIENTE Y OLAS MARINAS -->
                <div class="relative bg-gradient-to-br from-brand-dark via-brand-deep to-brand-blue pt-6 pb-9 px-6 text-white overflow-hidden shadow-xs">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="absolute right-10 -top-8 w-24 h-24 rounded-full bg-brand-sky/10 pointer-events-none"></div>

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
                            <span class="inline-block text-[10px] font-semibold tracking-wide text-brand-sky/90 uppercase mt-0.5">
                                Portal Jefe de Área
                            </span>
                        </div>
                    </div>

                    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none">
                        <svg class="relative block w-full h-4 text-brand-sky/30" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,0 C60,18 120,5 180,14 C240,22 280,6 320,12 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                        <svg class="relative block w-full h-3 text-white -mt-2" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,5 C50,18 110,4 170,15 C230,22 270,7 320,14 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>

                <!-- NAVEGACIÓN PRINCIPAL -->
                <nav class="px-4 py-5 flex flex-col gap-1.5" aria-label="Menú principal del jefe de área">

                    <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-muted/70">
                        Gestión de Formación
                    </div>

                    <!-- 1. Capacitaciones (Vista por defecto) -->
                    <a href="{{ route('jefe.capacitaciones.index') }}" id="nav-capacitaciones"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enCapacitaciones ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ $enCapacitaciones ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span>Capacitaciones</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $enCapacitaciones ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue' }}">
                            Publicadas
                        </span>
                    </a>

                    <!-- 2. Crear Capacitación -->
                    <a href="{{ route('jefe.capacitaciones.create') }}" id="nav-crear-capacitacion"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enCrearCapacitacion ? $claseItemActivo : $claseItemInactivo }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ $enCrearCapacitacion ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Crear Capacitación</span>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $enCrearCapacitacion ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue' }}">
                            Nueva
                        </span>
                    </a>

                    <!-- 3. Crear Evaluación + submenú -->
                    <div class="flex flex-col gap-1">
                        <div class="flex items-stretch gap-1">
                            <a href="{{ route('jefe.evaluaciones.create') }}" id="nav-crear-evaluacion"
                               class="group flex-1 flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ $enCrearEvaluacion ? $claseItemActivo : $claseItemInactivo }}">
                                <svg class="w-5 h-5 transition-colors {{ $enCrearEvaluacion ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                                </svg>
                                <span>Crear Evaluación</span>
                            </a>
                            <button type="button" id="toggle-submenu-evaluacion"
                                    onclick="toggleSubmenu('submenu-evaluacion', this)"
                                    aria-expanded="{{ $grupoEvaluacionAbierto ? 'true' : 'false' }}"
                                    aria-controls="submenu-evaluacion"
                                    title="Mostrar / ocultar submenú"
                                    class="px-2.5 rounded-xl text-slate-400 hover:text-brand-blue hover:bg-slate-50 transition-colors cursor-pointer">
                                <svg class="w-4 h-4 transition-transform duration-200 {{ $grupoEvaluacionAbierto ? 'rotate-180' : '' }}" data-chevron fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </div>

                        <ul id="submenu-evaluacion" class="ml-[22px] pl-3 border-l-2 border-brand-light flex-col gap-1 {{ $grupoEvaluacionAbierto ? 'flex' : 'hidden' }}">
                            <li>
                                <a href="{{ route('jefe.evaluaciones.index') }}" id="nav-mis-evaluaciones"
                                   class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px] font-semibold transition-colors {{ $enMisEvaluaciones ? 'bg-brand-light text-brand-blue font-bold' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 {{ $enMisEvaluaciones ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                    </svg>
                                    <span>Mis Evaluaciones</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('jefe.resultados.index') }}" id="nav-consultar-resultados"
                                   class="group flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px] font-semibold transition-colors {{ $enResultados ? 'bg-brand-light text-brand-blue font-bold' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                                    <svg class="w-4 h-4 {{ $enResultados ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                    <span>Consultar Resultados</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- TARJETA DE USUARIO CON OLA DECORATIVA -->
            <div class="relative bg-slate-50/80 border-t border-line/60 p-4 overflow-hidden">
                <div class="absolute top-0 left-0 w-full overflow-hidden leading-none pointer-events-none -translate-y-[90%]">
                    <svg class="w-full h-2.5 text-slate-50/80" viewBox="0 0 320 15" preserveAspectRatio="none">
                        <path d="M0,15 C80,0 160,12 240,3 C280,0 300,10 320,8 L320,15 L0,15 Z" fill="currentColor"/>
                    </svg>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-sm flex items-center justify-center shadow-xs shrink-0">
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'JA', 0, 1)) }}{{ strtoupper(substr(strstr($usuario->nombre_completo ?? ' J', ' ') ?: 'J', 1, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand-dark truncate leading-tight" title="{{ $usuario->nombre_completo }}">
                            {{ $usuario->nombre_completo }}
                        </p>
                        <p class="text-[10px] text-muted truncate mt-0.5" title="{{ $usuario->area->nombre ?? 'Sin área asignada' }}">
                            Jefe · {{ $usuario->area->nombre ?? 'Sin área asignada' }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors shrink-0 cursor-pointer"
                                title="Cerrar sesión">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ========================================================
             ÁREA PRINCIPAL DE CONTENIDO
             ======================================================== -->
        <div class="flex-1 flex flex-col min-w-0 lg:h-full lg:overflow-y-auto">

            <!-- BARRA SUPERIOR -->
            <header class="h-16 bg-white border-b border-line/70 px-4 sm:px-6 md:px-8 flex items-center justify-between sticky top-0 z-20 shadow-2xs">
                <div class="flex items-center gap-3">
                    <button type="button"
                            onclick="toggleSidebar(true)"
                            class="p-2 -ml-2 text-slate-600 hover:text-brand-blue hover:bg-slate-100 rounded-lg lg:hidden cursor-pointer"
                            aria-label="Abrir menú lateral">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-muted hidden sm:inline">Jefe de Área /</span>
                        <h2 class="font-heading font-bold text-sm sm:text-base text-brand-dark">
                            @yield('page_title', 'Capacitaciones')
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="hidden md:inline-flex items-center gap-1.5 text-xs text-muted font-medium bg-slate-50 px-3 py-1.5 rounded-full border border-line/60">
                        <svg class="w-3.5 h-3.5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        {{ date('d M Y') }}
                    </span>

                    <div class="flex items-center gap-2 pl-2 border-l border-line/60">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-brand-dark leading-tight">{{ $usuario->nombre_completo }}</p>
                            <p class="text-[10px] text-brand-blue font-semibold">C.C. {{ $usuario->identificacion }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-brand-light text-brand-blue font-heading font-bold text-xs flex items-center justify-center border border-brand-blue/20">
                            {{ strtoupper(substr($usuario->nombre_completo ?? 'J', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- MENSAJES FLASH -->
            @foreach (['success' => 'emerald', 'success_password' => 'emerald'] as $clave => $color)
                @if (session($clave))
                    <div class="m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5" role="status">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session($clave) }}</span>
                    </div>
                @endif
            @endforeach

            @foreach (['error', 'error_acceso'] as $clave)
                @if (session($clave))
                    <div class="m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2.5" role="alert">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span>{{ session($clave) }}</span>
                    </div>
                @endif
            @endforeach

            <!-- CONTENIDO DE LA PÁGINA -->
            <main class="flex-1 p-4 sm:p-6 md:p-8 overflow-y-auto">
                @yield('content')
            </main>

            <footer class="py-3 px-6 text-center text-[11px] text-slate-400 bg-white/60 border-t border-line/60">
                &copy; {{ date('Y') }} C.I. Piscícola New York S.A. · Sistema Integral de Capacitaciones y Gestión del Talento.
            </footer>
        </div>
    </div>

    <!-- MODAL OBLIGATORIO DE PRIMER INGRESO (compartido con el panel del empleado) -->
    @include('layouts.partials.primer-ingreso')

    <script>
        function toggleSidebar(open) {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function toggleSubmenu(id, btn) {
            const menu = document.getElementById(id);
            const abierto = menu.classList.contains('flex');
            menu.classList.toggle('flex', !abierto);
            menu.classList.toggle('hidden', abierto);
            btn.setAttribute('aria-expanded', String(!abierto));
            btn.querySelector('[data-chevron]').classList.toggle('rotate-180', !abierto);
        }
    </script>
    @stack('scripts')
</body>
</html>
