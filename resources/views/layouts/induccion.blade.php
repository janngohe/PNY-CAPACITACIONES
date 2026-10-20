<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Módulo de Inducción') · C.I. Piscícola New York</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-950 text-slate-100 flex flex-col">

    <!-- Barra de Navegación del Facilitador -->
    <header class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('induccion.index') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 p-1.5 flex items-center justify-center shadow-md group-hover:border-cyan-500/50 transition-colors">
                        <img src="{{ asset('images/Logo.png') }}" alt="PNY Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white text-base tracking-tight font-serif">Piscícola New York</span>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 uppercase tracking-wider">
                                Inducciones
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">Portal de Presentación y Asistencia QR</p>
                    </div>
                </a>
            </div>

            <!-- Datos del Facilitador y Acciones -->
            <div class="flex items-center gap-4">
                @php
                    $facilitadorUsuario = \App\Models\Usuario::find(session('facilitador_usuario_id') ?? auth()->id());
                @endphp

                @if($facilitadorUsuario)
                    <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-slate-800">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold text-xs shadow-md">
                            {{ strtoupper(substr($facilitadorUsuario->nombre_completo, 0, 2)) }}
                        </div>
                        <div class="text-left text-xs">
                            <div class="font-bold text-white">{{ $facilitadorUsuario->nombre_completo }}</div>
                            <div class="text-slate-400 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>{{ $facilitadorUsuario->cargoOficial() }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('induccion.salir') }}" method="POST">
                    @csrf
                    <button type="submit"
                            title="Cerrar portal de inducciones"
                            class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-rose-500/10 text-slate-300 hover:text-rose-400 border border-slate-700 hover:border-rose-500/30 text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer">
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
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-400 text-base"></i>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-400 text-base"></i>
                <div class="font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Pie de Página -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-6 text-center text-xs text-slate-500">
        C.I. Piscícola New York &middot; Sistema de Gestión de Capacitaciones e Inducciones &copy; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
