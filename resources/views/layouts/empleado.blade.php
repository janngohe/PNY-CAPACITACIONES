<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Capacitaciones') · Portal del Empleado · C.I. Piscícola New York</title>
    <meta name="description" content="Portal de capacitación y formación continua de colaboradores de C.I. Piscícola New York.">

    <!-- Tipografía Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-ink bg-slate-50 antialiased selection:bg-brand-blue/20 selection:text-brand-dark">
    <div class="min-h-screen lg:h-screen flex flex-col lg:flex-row bg-[#f8fafc] lg:overflow-hidden">

        <!-- ========================================================
             BACKDROP MÓVIL PARA SIDEBAR
             ======================================================== -->
        <div id="sidebar-backdrop" 
             class="fixed inset-0 bg-brand-dark/40 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300"
             onclick="toggleSidebar(false)">
        </div>

        <!-- ========================================================
             MENÚ LATERAL IZQUIERDO (SIDEBAR) CON EFECTO DE OLAS
             ======================================================== -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 bg-white border-r border-line/70 flex flex-col justify-between shadow-lg lg:shadow-xs transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto shrink-0 lg:h-full lg:overflow-y-auto">
            
            <!-- CONTENEDOR SUPERIOR DEL SIDEBAR -->
            <div class="flex flex-col">
                
                <!-- CABECERA DEL SIDEBAR CON GRADIENTE Y OLAS MARINAS -->
                <div class="relative bg-gradient-to-br from-brand-dark via-brand-deep to-brand-blue pt-6 pb-9 px-6 text-white overflow-hidden shadow-xs">
                    <!-- Ondas de agua sutiles de fondo -->
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
                            <span class="inline-block text-[10px] font-semibold tracking-wide text-brand-sky/90 uppercase mt-0.5">
                                Portal del Empleado
                            </span>
                        </div>
                    </div>

                    <!-- OLA INFERIOR DEL HEADER DEL SIDEBAR (Conexión fluida con el menú) -->
                    <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none pointer-events-none">
                        <!-- Ola traslúcida azul cielo -->
                        <svg class="relative block w-full h-4 text-brand-sky/30" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,0 C60,18 120,5 180,14 C240,22 280,6 320,12 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                        <!-- Ola blanca principal conectada al fondo del menú -->
                        <svg class="relative block w-full h-3 text-white -mt-2" viewBox="0 0 320 20" preserveAspectRatio="none">
                            <path d="M0,5 C50,18 110,4 170,15 C230,22 270,7 320,14 L320,20 L0,20 Z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>

                <!-- NAVEGACIÓN PRINCIPAL -->
                <nav class="px-4 py-5 flex flex-col gap-1.5" aria-label="Menú principal del empleado">
                    
                    <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-muted/70">
                        Menú de Formación
                    </div>

                    <!-- 1. Capacitaciones (Vista por defecto) -->
                    <a href="{{ route('empleado.capacitaciones') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('empleado.capacitaciones') || request()->routeIs('empleado.dashboard') ? 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('empleado.capacitaciones') || request()->routeIs('empleado.dashboard') ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <span>Capacitaciones</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ request()->routeIs('empleado.capacitaciones') || request()->routeIs('empleado.dashboard') ? 'bg-brand-blue text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue' }}">
                            Activas
                        </span>
                    </a>

                    <!-- 2. Certificados -->
                    <a href="{{ route('empleado.certificados') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('empleado.certificados') ? 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('empleado.certificados') ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 002.996-2.67V5.625A2.625 2.625 0 0014.875 3h-5.75A2.625 2.625 0 006.5 5.625v7.08a3 3 0 002.996 2.67" />
                            </svg>
                            <span>Certificados</span>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue">
                            Descargas
                        </span>
                    </a>

                    <!-- 3. Capacitaciones Finalizadas -->
                    <a href="{{ route('empleado.finalizadas') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('empleado.finalizadas') ? 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('empleado.finalizadas') ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Capacitaciones Finalizadas</span>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue">
                            Historial
                        </span>
                    </a>

                    <!-- 4. Anexo Certificados -->
                    <a href="{{ route('empleado.anexo') }}"
                       class="group flex items-center justify-between px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 {{ request()->routeIs('empleado.anexo') ? 'bg-brand-light text-brand-blue shadow-2xs font-bold border-l-4 border-brand-blue pl-2.5' : 'text-slate-600 hover:text-brand-blue hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('empleado.anexo') ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            <span>Anexo Certificados</span>
                        </div>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 group-hover:bg-brand-light group-hover:text-brand-blue">
                            Externos
                        </span>
                    </a>
                </nav>
            </div>

            <!-- CONTENEDOR INFERIOR DEL SIDEBAR CON TARJETA DE USUARIO Y OLAS DECORATIVAS -->
            <div class="relative bg-slate-50/80 border-t border-line/60 p-4 overflow-hidden">
                <!-- Sutil onda decorativa superior en el pie del sidebar -->
                <div class="absolute top-0 left-0 w-full overflow-hidden leading-none pointer-events-none -translate-y-[90%]">
                    <svg class="w-full h-2.5 text-slate-50/80" viewBox="0 0 320 15" preserveAspectRatio="none">
                        <path d="M0,15 C80,0 160,12 240,3 C280,0 300,10 320,8 L320,15 L0,15 Z" fill="currentColor"/>
                    </svg>
                </div>

                <!-- Perfil del Empleado -->
                <div class="flex items-center gap-3">
                    <!-- Avatar con iniciales en degradado corporativo -->
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-deep to-brand-cyan text-white font-heading font-extrabold text-sm flex items-center justify-center shadow-xs shrink-0">
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'EM', 0, 1)) }}{{ strtoupper(substr(strstr($usuario->nombre_completo ?? ' P', ' ') ?: 'P', 1, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand-dark truncate leading-tight" title="{{ $usuario->nombre_completo ?? 'Empleado' }}">
                            {{ $usuario->nombre_completo ?? 'Empleado C.I. Piscícola' }}
                        </p>
                        <p class="text-[10px] text-muted truncate mt-0.5" title="{{ $usuario->area->nombre ?? ($usuario->area_nombre ?? 'Área de Producción') }}">
                            {{ $usuario->area->nombre ?? ($usuario->area_nombre ?? 'Área de Producción') }}
                        </p>
                    </div>
                </div>

                <!-- Botón Cerrar Sesión prominente y accesible para móvil y escritorio -->
                <form method="POST" action="{{ route('logout') }}" class="m-0 mt-3">
                    @csrf
                    <button type="submit" id="btn-sidebar-logout-empleado"
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
            
            <!-- BARRA SUPERIOR DE NAVEGACIÓN -->
            <header class="h-16 bg-white border-b border-line/70 px-4 sm:px-6 md:px-8 flex items-center justify-between sticky top-0 z-20 shadow-2xs">
                
                <!-- Botón de apertura de menú en móvil + Título de sección -->
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
                        <span class="text-xs font-semibold text-muted hidden sm:inline">Portal /</span>
                        <h2 class="font-heading font-bold text-sm sm:text-base text-brand-dark">
                            @yield('page_title', 'Capacitaciones')
                        </h2>
                    </div>
                </div>

                <!-- Elementos del extremo derecho -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Fecha actual -->
                    <span class="hidden md:inline-flex items-center gap-1.5 text-xs text-muted font-medium bg-slate-50 px-3 py-1.5 rounded-full border border-line/60">
                        <svg class="w-3.5 h-3.5 text-brand-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        {{ date('d M Y') }}
                    </span>

                    <!-- Nombre del usuario en la barra superior -->
                    <div class="flex items-center gap-2 pl-2 border-l border-line/60">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-brand-dark leading-tight">
                                {{ $usuario->nombre_completo ?? 'Colaborador PNY' }}
                            </p>
                            <p class="text-[10px] text-brand-blue font-semibold">
                                C.C. {{ $usuario->identificacion ?? '1075284910' }}
                            </p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-brand-light text-brand-blue font-heading font-bold text-xs flex items-center justify-center border border-brand-blue/20">
                            {{ strtoupper(substr($usuario->nombre_completo ?? 'E', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- MENSAJES FLASH / NOTIFICACIONES -->
            @if(session('success_password'))
                <div class="m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success_password') }}</span>
                </div>
            @endif

            @if(session('success_anexo'))
                <div class="m-4 sm:m-6 mb-0 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success_anexo') }}</span>
                </div>
            @endif

            <!-- CONTENIDO DE LA PÁGINA -->
            <main class="flex-1 p-4 sm:p-6 md:p-8 overflow-y-auto">
                @yield('content')
            </main>

            <!-- FOOTER MÍNIMO DEL PANEL -->
            <footer class="py-3 px-6 text-center text-[11px] text-slate-400 bg-white/60 border-t border-line/60">
                &copy; {{ date('Y') }} C.I. Piscícola New York S.A. · Sistema Integral de Capacitaciones y Gestión del Talento.
            </footer>

        </div>
    </div>

    @include('layouts.partials.primer-ingreso')

    <!-- Script de control del Sidebar en móviles -->
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
</body>
</html>
