<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Módulo de Inducción') · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-[#f8fafc] text-slate-800 flex flex-col selection:bg-brand-blue/20 selection:text-brand-dark">

    <!-- Barra de Navegación del Facilitador (Tema Institucional Claro) -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('induccion.index') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 p-1.5 flex items-center justify-center shadow-xs group-hover:border-brand-blue transition-colors">
                        <img src="{{ asset('images/Logo.png') }}" alt="PNY Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-base tracking-tight font-serif">Piscícola New York</span>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-800 border border-cyan-200 uppercase tracking-wider">
                                Inducciones
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500">Portal de Presentación y Asistencia QR</p>
                    </div>
                </a>
            </div>

            <!-- Datos del Facilitador y Acciones -->
            <div class="flex items-center gap-4">
                @php
                    $facilitadorUsuario = \App\Models\Usuario::find(session('facilitador_usuario_id') ?? auth()->id());
                @endphp

                @if($facilitadorUsuario)
                    <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="w-9 h-9 rounded-full bg-brand-light text-brand-blue flex items-center justify-center font-bold text-xs shadow-2xs border border-brand-blue/20">
                            {{ strtoupper(substr($facilitadorUsuario->nombre_completo, 0, 2)) }}
                        </div>
                        <div class="text-left text-xs">
                            <div class="font-bold text-slate-900">{{ $facilitadorUsuario->nombre_completo }}</div>
                            <div class="text-slate-500 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ $facilitadorUsuario->cargoOficial() }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('induccion.salir') }}" method="POST">
                    @csrf
                    <button type="submit"
                            title="Cerrar portal de inducciones"
                            class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200 hover:border-rose-200 text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-start gap-3 shadow-2xs">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600 text-base"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-medium flex items-start gap-3 shadow-2xs">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-600 text-base"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Pie de Página -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        C.I. Piscícola New York &middot; Sistema de Gestión de Capacitaciones e Inducciones &copy; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
