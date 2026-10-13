<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · Administración · C.I. Piscícola New York</title>
    <meta name="description" content="Panel de administración de la plataforma de inducción y capacitación de C.I. Piscícola New York: usuarios, capacitaciones, evaluaciones, progreso, certificados y reportes.">

    <!-- Tipografía Google Fonts (misma de los paneles de empleado y jefe) -->
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
    /** @var \App\Models\Usuario $usuario */
    $usuario = $usuario ?? auth()->user();

    $claseItemActivo = 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5';
    $claseItemInactivo = 'text-slate-600 hover:text-brand-blue hover:bg-slate-50';

    // [id, ruta, patrón de ruta activa, icono, etiqueta]
    $menu = [
        'Panel' => [
            ['nav-dashboard', 'admin.dashboard', ['admin.dashboard'], 'fa-gauge-high', 'Resumen General'],
        ],
        'Gestión de Formación' => [
            ['nav-capacitaciones', 'admin.capacitaciones.index', ['admin.capacitaciones.*'], 'fa-book-open', 'Capacitaciones'],
            ['nav-evaluaciones', 'admin.evaluaciones.index', ['admin.evaluaciones.*'], 'fa-clipboard-question', 'Evaluaciones'],
            ['nav-asignaciones', 'admin.asignaciones.index', ['admin.asignaciones.*'], 'fa-diagram-project', 'Asignar a Áreas'],
        ],
        'Seguimiento' => [
            ['nav-progreso', 'admin.progreso.index', ['admin.progreso.*'], 'fa-chart-line', 'Progreso de Participantes'],
            ['nav-resultados', 'admin.resultados.index', ['admin.resultados.*'], 'fa-square-poll-vertical', 'Resultados de Evaluaciones'],
            ['nav-certificados', 'admin.certificados.index', ['admin.certificados.*'], 'fa-award', 'Certificados'],
            ['nav-reportes', 'admin.reportes.index', ['admin.reportes.*'], 'fa-file-arrow-down', 'Reportes'],
        ],
        'Administración' => [
            ['nav-usuarios', 'admin.usuarios.index', ['admin.usuarios.*'], 'fa-users-gear', 'Usuarios'],
            ['nav-areas', 'admin.areas.index', ['admin.areas.*'], 'fa-sitemap', 'Áreas'],
        ],
    ];
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
                                Portal Administrador
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
                <nav class="px-4 py-5 flex flex-col gap-1" aria-label="Menú principal del administrador">
                    @foreach ($menu as $grupo => $items)
                        <div class="px-3 {{ $loop->first ? 'pb-2' : 'pt-4 pb-2' }} text-[10px] font-bold uppercase tracking-wider text-muted/70">
                            {{ $grupo }}
                        </div>
                        @foreach ($items as [$id, $ruta, $patrones, $icono, $etiqueta])
                            @php $activo = request()->routeIs(...$patrones); @endphp
                            <a href="{{ route($ruta) }}" id="{{ $id }}"
                               @if ($activo) aria-current="page" @endif
                               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ $activo ? $claseItemActivo : $claseItemInactivo }}">
                                <i class="fa-solid {{ $icono }} w-5 text-center text-[15px] transition-colors {{ $activo ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                                <span>{{ $etiqueta }}</span>
                            </a>
                        @endforeach
                    @endforeach
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
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'AD', 0, 1)) }}{{ strtoupper(substr(strstr($usuario->nombre_completo ?? ' A', ' ') ?: 'A', 1, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand-dark truncate leading-tight" title="{{ $usuario->nombre_completo }}">
                            {{ $usuario->nombre_completo }}
                        </p>
                        <p class="text-[10px] text-muted truncate mt-0.5">Administrador del sistema</p>
                    </div>
                </div>

                <!-- Botón Cerrar Sesión prominente y accesible para móvil y escritorio -->
                <form method="POST" action="{{ route('logout') }}" class="m-0 mt-3">
                    @csrf
                    <button type="submit" id="btn-sidebar-logout-admin"
                            class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200/70 font-heading font-bold text-xs shadow-2xs transition-all cursor-pointer group"
                            title="Cerrar sesión" aria-label="Cerrar sesión">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs transition-transform group-hover:translate-x-0.5"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
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
                        <span class="text-xs font-semibold text-muted hidden sm:inline">Administrador /</span>
                        <h2 class="font-heading font-bold text-sm sm:text-base text-brand-dark">
                            @yield('page_title', 'Resumen General')
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
                            {{ strtoupper(substr($usuario->nombre_completo ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- MENSAJES FLASH -->
            @foreach (['success', 'success_password'] as $clave)
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

    <!-- MODAL OBLIGATORIO DE PRIMER INGRESO (compartido con los demás paneles) -->
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
    </script>
    @stack('scripts')
</body>
</html>
