<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · Administración · C.I. Piscícola New York</title>
    <meta name="description" content="Panel de administración de la plataforma de inducción y capacitación de C.I. Piscícola New York: usuarios, capacitaciones, evaluaciones, progreso, certificados y reportes.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- Tipografía Google Fonts (misma de los paneles de empleado y jefe) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</head>
<body class="min-h-full font-sans text-ink bg-slate-50 antialiased selection:bg-brand-blue/20 selection:text-brand-dark">
@php
    /** @var \App\Models\Usuario $usuario */
    $usuario = $usuario ?? auth()->user();

    $claseItemActivo = 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5';
    $claseItemInactivo = 'text-slate-600 hover:text-brand-blue hover:bg-slate-50';

    $menu = [
        'Panel' => [
            [
                'tipo' => 'enlace',
                'id' => 'nav-dashboard',
                'ruta' => 'admin.dashboard',
                'patrones' => ['admin.dashboard'],
                'icono' => 'fa-gauge-high',
                'etiqueta' => 'Resumen General',
            ],
        ],
        'Gestión de Formación' => [
            [
                'tipo' => 'desplegable',
                'id' => 'submenu-capacitaciones',
                'icono' => 'fa-book-open',
                'etiqueta' => 'Capacitaciones',
                'patrones' => ['admin.capacitaciones.*'],
                'subitems' => [
                    [
                        'id' => 'nav-capacitaciones-todas',
                        'ruta' => 'admin.capacitaciones.index',
                        'patrones' => ['admin.capacitaciones.index', 'admin.capacitaciones.create', 'admin.capacitaciones.edit'],
                        'etiqueta' => 'Todas las Capacitaciones',
                    ],
                    [
                        'id' => 'nav-capacitaciones-borradores',
                        'ruta' => 'admin.capacitaciones.borradores',
                        'patrones' => ['admin.capacitaciones.borradores'],
                        'etiqueta' => 'Borradores',
                    ],
                ],
            ],
            [
                'tipo' => 'desplegable',
                'id' => 'submenu-evaluaciones',
                'icono' => 'fa-clipboard-question',
                'etiqueta' => 'Evaluaciones',
                'patrones' => ['admin.evaluaciones.*', 'admin.resultados.*'],
                'subitems' => [
                    [
                        'id' => 'nav-evaluaciones-gestion',
                        'ruta' => 'admin.evaluaciones.index',
                        'patrones' => ['admin.evaluaciones.*'],
                        'etiqueta' => 'Gestión de Evaluaciones',
                    ],
                    [
                        'id' => 'nav-evaluaciones-resultados',
                        'ruta' => 'admin.resultados.index',
                        'patrones' => ['admin.resultados.*'],
                        'etiqueta' => 'Resultados de Evaluaciones',
                    ],
                ],
            ],
        ],
        'Seguimiento' => [
            [
                'tipo' => 'enlace',
                'id' => 'nav-progreso',
                'ruta' => 'admin.progreso.index',
                'patrones' => ['admin.progreso.*'],
                'icono' => 'fa-chart-line',
                'etiqueta' => 'Progreso de Participantes',
            ],
            [
                'tipo' => 'enlace',
                'id' => 'nav-certificados',
                'ruta' => 'admin.certificados.index',
                'patrones' => ['admin.certificados.*'],
                'icono' => 'fa-award',
                'etiqueta' => 'Certificados',
            ],
            [
                'tipo' => 'enlace',
                'id' => 'nav-reportes',
                'ruta' => 'admin.reportes.index',
                'patrones' => ['admin.reportes.*'],
                'icono' => 'fa-file-arrow-down',
                'etiqueta' => 'Reportes',
            ],
        ],
        'Administración' => [
            [
                'tipo' => 'enlace',
                'id' => 'nav-usuarios',
                'ruta' => 'admin.usuarios.index',
                'patrones' => ['admin.usuarios.*'],
                'icono' => 'fa-users-gear',
                'etiqueta' => 'Usuarios',
            ],
            [
                'tipo' => 'desplegable',
                'id' => 'submenu-areas',
                'icono' => 'fa-building-shield',
                'etiqueta' => 'Sedes y Áreas',
                'patrones' => ['admin.sedes.*', 'admin.areas.*', 'admin.asignaciones.*'],
                'subitems' => [
                    [
                        'id' => 'nav-sedes-gestion',
                        'ruta' => 'admin.sedes.index',
                        'patrones' => ['admin.sedes.*'],
                        'etiqueta' => 'Gestión de Sedes',
                    ],
                    [
                        'id' => 'nav-areas-gestion',
                        'ruta' => 'admin.areas.index',
                        'patrones' => ['admin.areas.*'],
                        'etiqueta' => 'Gestión de Áreas',
                    ],
                    [
                        'id' => 'nav-areas-asignaciones',
                        'ruta' => 'admin.asignaciones.index',
                        'patrones' => ['admin.asignaciones.*'],
                        'etiqueta' => 'Asignar a Áreas',
                    ],
                ],
            ],
            [
                'tipo' => 'enlace',
                'id' => 'nav-beneficios',
                'ruta' => 'admin.beneficios.index',
                'patrones' => ['admin.beneficios.*'],
                'icono' => 'fa-gift',
                'etiqueta' => 'Beneficios y Puntos',
            ],
        ],
    ];
@endphp
    <div class="min-h-screen lg:h-screen lg:h-[100dvh] flex flex-col lg:flex-row bg-[#f8fafc] lg:overflow-hidden">

        <!-- BACKDROP MÓVIL PARA SIDEBAR -->
        <div id="sidebar-backdrop"
             class="fixed inset-0 bg-brand-dark/40 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300"
             onclick="toggleSidebar(false)">
        </div>

        <!-- ========================================================
             MENÚ LATERAL IZQUIERDO (SIDEBAR) CON SCROLL INTERNO
             ======================================================== -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 h-[100dvh] max-h-[100dvh] bg-white border-r border-line/70 flex flex-col shadow-lg lg:shadow-xs transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto lg:h-full lg:max-h-full shrink-0 overflow-hidden">

            <!-- 1. CABECERA FIJA SUPERIOR (shrink-0) -->
            <div class="shrink-0 relative bg-gradient-to-br from-brand-dark via-brand-deep to-brand-blue pt-3.5 pb-5 px-4 sm:px-5 text-white overflow-hidden shadow-xs">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/5 pointer-events-none"></div>
                <div class="absolute right-10 -top-8 w-24 h-24 rounded-full bg-brand-sky/10 pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div class="h-9 w-9 sm:h-10 sm:w-10 p-1 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/Logo.png') }}"
                             alt="Logo C.I. Piscícola New York"
                             class="h-full w-full object-contain">
                    </div>
                    <div>
                        <span class="block font-heading font-extrabold text-[12px] sm:text-[12.5px] tracking-wider uppercase leading-tight text-white">
                            Piscícola New York
                        </span>
                        <span class="inline-block text-[9.5px] sm:text-[10px] font-semibold tracking-wide text-brand-sky/90 uppercase mt-0.5">
                            Portal Administrador
                        </span>
                    </div>
                </div>

                <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none">
                    <svg class="relative block w-full h-3 text-brand-sky/30" viewBox="0 0 320 20" preserveAspectRatio="none">
                        <path d="M0,0 C60,18 120,5 180,14 C240,22 280,6 320,12 L320,20 L0,20 Z" fill="currentColor"/>
                    </svg>
                    <svg class="relative block w-full h-2 text-white -mt-1" viewBox="0 0 320 20" preserveAspectRatio="none">
                        <path d="M0,5 C50,18 110,4 170,15 C230,22 270,7 320,14 L320,20 L0,20 Z" fill="currentColor"/>
                    </svg>
                </div>
            </div>

            <!-- 2. NAVEGACIÓN PRINCIPAL DESPLAZABLE (flex-1 overflow-y-auto min-h-0) -->
            <nav class="flex-1 overflow-y-auto min-h-0 px-3 py-2 flex flex-col gap-0.5 sidebar-scrollbar" aria-label="Menú principal del administrador">
                @foreach ($menu as $grupo => $items)
                    <div class="px-2.5 {{ $loop->first ? 'pb-0.5' : 'pt-2 pb-0.5' }} text-[9.5px] font-bold uppercase tracking-wider text-muted/70">
                        {{ $grupo }}
                    </div>
                    @foreach ($items as $item)
                        @if ($item['tipo'] === 'enlace')
                            @php $activo = request()->routeIs(...$item['patrones']); @endphp
                            <a href="{{ route($item['ruta']) }}" id="{{ $item['id'] }}"
                               @if ($activo) aria-current="page" @endif
                               class="group flex items-center gap-2.5 px-3 py-1.5 sm:py-2 rounded-xl text-xs sm:text-[13px] font-semibold transition-all duration-150 {{ $activo ? $claseItemActivo : $claseItemInactivo }}">
                                <i class="fa-solid {{ $item['icono'] }} w-4.5 text-center text-sm transition-colors {{ $activo ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                                <span>{{ $item['etiqueta'] }}</span>
                            </a>
                        @elseif ($item['tipo'] === 'desplegable')
                            @php
                                $desplegableActivo = request()->routeIs(...$item['patrones']);
                            @endphp
                            <div class="flex flex-col" data-dropdown-id="{{ $item['id'] }}">
                                <!-- Botón disparador del desplegable -->
                                <button type="button"
                                        onclick="toggleSubmenu('{{ $item['id'] }}')"
                                        id="btn-{{ $item['id'] }}"
                                        aria-expanded="{{ $desplegableActivo ? 'true' : 'false' }}"
                                        class="group w-full flex items-center justify-between px-3 py-1.5 sm:py-2 rounded-xl text-xs sm:text-[13px] font-semibold transition-all duration-150 cursor-pointer {{ $desplegableActivo ? 'bg-brand-light/50 text-brand-blue font-bold' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                                    <div class="flex items-center gap-2.5">
                                        <i class="fa-solid {{ $item['icono'] }} w-4.5 text-center text-sm transition-colors {{ $desplegableActivo ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                                        <span>{{ $item['etiqueta'] }}</span>
                                    </div>
                                    <i id="chevron-{{ $item['id'] }}"
                                       class="fa-solid fa-chevron-down text-[9.5px] transition-transform duration-200 {{ $desplegableActivo ? 'rotate-180 text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                                </button>

                                <!-- Contenedor de subitems -->
                                <div id="{{ $item['id'] }}"
                                     class="flex flex-col gap-1 mt-1 pl-3.5 border-l-2 border-line/70 ml-4.5 transition-all duration-200 {{ $desplegableActivo ? '' : 'hidden' }}">
                                    @foreach ($item['subitems'] as $sub)
                                        @php $subActivo = request()->routeIs(...$sub['patrones']); @endphp
                                        <a href="{{ route($sub['ruta']) }}" id="{{ $sub['id'] }}"
                                           @if ($subActivo) aria-current="page" @endif
                                           class="group flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all duration-150 {{ $subActivo ? 'bg-brand-light text-brand-blue font-bold shadow-2xs' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50 font-medium' }}">
                                            <span class="w-1.5 h-1.5 rounded-full transition-colors {{ $subActivo ? 'bg-brand-blue ring-2 ring-brand-blue/30' : 'bg-slate-300 group-hover:bg-brand-blue' }}"></span>
                                            <span>{{ $sub['etiqueta'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </nav>

            <!-- 3. TARJETA DE USUARIO FIJA EN EL PIE (shrink-0 mt-auto) CON CERRAR SESIÓN SIEMPRE VISIBLE -->
            <div class="shrink-0 mt-auto relative bg-slate-50/95 border-t border-line/70 p-3 z-10 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8.5 h-8.5 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-xs flex items-center justify-center shadow-xs shrink-0">
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'AD', 0, 1)) }}{{ strtoupper(substr(strstr($usuario->nombre_completo ?? ' A', ' ') ?: 'A', 1, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand-dark truncate leading-tight" title="{{ $usuario->nombre_completo }}">
                            {{ $usuario->nombre_completo }}
                        </p>
                        <p class="text-[10px] text-muted truncate mt-0.5">Administrador del sistema</p>
                    </div>
                </div>

                <!-- Botón Cerrar Sesión fijo y 100% visible en laptops y móviles -->
                <form method="POST" action="{{ route('logout') }}" class="m-0 mt-2">
                    @csrf
                    <button type="submit" id="btn-sidebar-logout-admin"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 border border-red-200/80 font-heading font-bold text-xs shadow-2xs transition-all cursor-pointer group"
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
            <header style="min-height: 80px;" class="min-h-[80px] py-4 bg-white border-b border-line/70 px-4 sm:px-6 md:px-8 flex items-center justify-between sticky top-0 z-20 shadow-2xs">
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
            <main class="flex-1 p-4 sm:p-6 md:p-8 w-full min-w-0">
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

        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            const chevron = document.getElementById('chevron-' + id);
            const btn = document.getElementById('btn-' + id);
            if (!submenu) return;

            const isHidden = submenu.classList.contains('hidden');
            if (isHidden) {
                submenu.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
                if (btn) btn.setAttribute('aria-expanded', 'true');
            } else {
                submenu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
