<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestión Humana') · Psicología · C.I. Piscícola New York</title>
    <meta name="description" content="Módulo de Psicología y Gestión Humana para la aplicación y calificación de pruebas psicotécnicas de C.I. Piscícola New York S.A.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/Logo.png') }}">

    <!-- Tipografía Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <meta name="color-scheme" content="light">

    <!-- Tailwind CSS compilado por Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: light;
        }
        input, select, textarea {
            color: #0f172a;
        }
        input::placeholder, textarea::placeholder {
            color: #94a3b8;
        }
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
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
        'Principal' => [
            [
                'id' => 'nav-dashboard',
                'ruta' => 'psicologia.dashboard',
                'patrones' => ['psicologia.dashboard'],
                'icono' => 'fa-gauge-high',
                'etiqueta' => 'Panel de Inicio',
            ],
        ],
        'Pruebas Psicotécnicas' => [
            [
                'id' => 'nav-pruebas-historial',
                'ruta' => 'psicologia.pruebas.historial',
                'patrones' => ['psicologia.pruebas.historial', 'psicologia.paca.resultado'],
                'icono' => 'fa-clock-rotate-left',
                'etiqueta' => 'Historial de Pruebas',
            ],
        ],
        'Acceso de Aspirantes' => [
            [
                'id' => 'nav-qr-evaluados',
                'ruta' => 'psicologia.qr.index',
                'patrones' => ['psicologia.qr.index', 'psicologia.qr.imprimir'],
                'icono' => 'fa-qrcode',
                'etiqueta' => 'Código QR Evaluados',
            ],
        ],
    ];
@endphp

<div class="flex h-screen overflow-hidden bg-slate-50">

    <!-- SIDEBAR DE NAVEGACIÓN -->
    <aside id="sidebar-psicologia" class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 -translate-x-full">
        <!-- Logo y Encabezado de Marca -->
        <div class="flex h-20 shrink-0 items-center gap-3.5 border-b border-slate-100 px-6">
            <a href="{{ route('psicologia.dashboard') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/Logo.png') }}" alt="C.I. Piscícola New York" class="h-10 w-auto object-contain transition-transform group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="font-heading font-extrabold text-base tracking-tight text-brand-dark leading-tight">Gestión Humana</span>
                    <span class="text-[11px] font-semibold text-brand-blue tracking-wide uppercase">Pruebas Psicotécnicas</span>
                </div>
            </a>
        </div>

        <!-- Menú de Enlaces con scroll -->
        <div class="flex-1 overflow-y-auto px-4 py-5 sidebar-scrollbar">
            <nav class="space-y-6">
                @foreach($menu as $grupo => $items)
                    <div>
                        <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">{{ $grupo }}</h3>
                        <div class="space-y-1">
                            @foreach($items as $item)
                                @php
                                    $activo = false;
                                    foreach ($item['patrones'] as $patron) {
                                        if (request()->routeIs($patron)) {
                                            $activo = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <a href="{{ route($item['ruta']) }}"
                                   id="{{ $item['id'] }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-xs transition-colors duration-150 {{ $activo ? $claseItemActivo : $claseItemInactivo }}">
                                    <i class="fa-solid {{ $item['icono'] }} w-4 text-center text-sm {{ $activo ? 'text-brand-blue' : 'text-slate-400 group-hover:text-brand-blue' }}"></i>
                                    <span>{{ $item['etiqueta'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </div>

        <!-- Perfil y Cierre de Sesión en el Pie del Sidebar -->
        <div class="border-t border-slate-100 p-4 bg-slate-50/50">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-blue text-white font-bold text-xs shadow-2xs">
                        {{ strtoupper(substr($usuario->nombre_completo ?? 'PS', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-slate-900 leading-tight">{{ $usuario->nombre_completo }}</p>
                        <p class="truncate text-[11px] text-slate-500 font-medium">{{ $usuario->cargoOficial() }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Cerrar Sesión" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ÁREA PRINCIPAL -->
    <div class="flex flex-1 flex-col overflow-hidden">
        
        <!-- Header Superior -->
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 bg-white px-6">
            <div class="flex items-center gap-4">
                <button type="button" id="btn-toggle-sidebar" class="lg:hidden text-slate-600 hover:text-slate-900">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="font-semibold text-slate-800">@yield('page_title', 'Módulo de Psicología')</span>
                    @hasSection('page_subtitle')
                        <span class="text-slate-300">/</span>
                        <span>@yield('page_subtitle')</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Indicador de Rol -->
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-200/60">
                    <i class="fa-solid fa-brain text-[11px]"></i>
                    Rol Psicología
                </span>

                <!-- Enlace Rápido Código QR -->
                <a href="{{ route('psicologia.qr.index') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-2xs hover:bg-emerald-700 transition-colors">
                    <i class="fa-solid fa-qrcode text-[11px]"></i>
                    Código QR
                </a>
            </div>
        </header>

        <!-- Contenedor con Scroll de la Página -->
        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- Alertas de Flash (Éxito, Error, Advertencia) -->
                @if(session('success'))
                    <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-emerald-900 shadow-2xs">
                        <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600 text-sm"></i>
                        <div class="text-xs leading-relaxed font-medium">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50/80 p-4 text-rose-900 shadow-2xs">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-600 text-sm"></i>
                        <div class="text-xs leading-relaxed font-medium">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/80 p-4 text-amber-900 shadow-2xs">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-600 text-sm"></i>
                        <div class="text-xs leading-relaxed font-medium">
                            {{ session('warning') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>

<!-- VISOR DE PDF A PANTALLA COMPLETA EN LA MISMA PESTAÑA -->
<div id="modalVisorPdf" class="fixed inset-0 z-50 hidden w-screen h-screen bg-slate-950 flex flex-col overflow-hidden" aria-modal="true" role="dialog">
    
    <!-- Barra Superior de Herramientas del Visor -->
    <div class="flex items-center justify-between px-3 sm:px-6 py-2.5 bg-slate-900 border-b border-slate-800 shrink-0 text-white shadow-md">
        
        <!-- Izquierda: Botón Volver y Datos del Documento -->
        <div class="flex items-center gap-3 min-w-0 pr-2">
            <button type="button"
                    onclick="cerrarModalPdf()"
                    title="Volver a la vista anterior (Esc)"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white px-3.5 py-1.5 text-xs font-bold border border-slate-700 hover:border-slate-600 transition-all cursor-pointer shadow-sm shrink-0">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Volver</span>
            </button>

            <div class="min-w-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400 block leading-tight">Formato Oficial GH-FO-025</span>
                <h3 id="tituloModalPdf" class="text-xs sm:text-sm font-bold text-slate-100 truncate max-w-xs sm:max-w-md md:max-w-xl">
                    Consentimiento Informado
                </h3>
            </div>
        </div>
        
        <!-- Derecha: Acciones (Imprimir, Descargar, Pestaña Nueva, Cerrar) -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Botón Imprimir -->
            <button type="button"
                    onclick="imprimirPdfModal()"
                    title="Imprimir documento directamente"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 px-3 py-1.5 text-xs font-semibold border border-slate-700 transition-colors cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span class="hidden md:inline">Imprimir</span>
            </button>

            <!-- Botón Descargar PDF -->
            <a id="btnDescargarPdfModal"
               href="#"
               class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white px-3.5 py-1.5 text-xs font-bold transition-colors shadow-sm">
                <i class="fa-solid fa-download text-[11px]"></i>
                <span class="hidden sm:inline">Descargar PDF</span>
            </a>

            <!-- Abrir en Pestaña Independiente -->
            <a id="btnPestanaModal"
               href="#"
               target="_blank"
               title="Abrir en pestaña independiente"
               class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs transition-colors">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
            </a>

            <!-- Botón Cerrar (X) -->
            <button type="button"
                    onclick="cerrarModalPdf()"
                    title="Cerrar visor (Esc)"
                    class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white transition-colors cursor-pointer border border-slate-700">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Contenedor del PDF (Iframe al 100% de la Pantalla) -->
    <div class="relative flex-1 w-full h-full bg-slate-800 overflow-hidden flex flex-col">
        <!-- Spinner indicador de carga -->
        <div id="spinnerCargaPdf" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-10 transition-opacity">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-blue-400 mb-3"></i>
            <span class="text-xs font-semibold text-slate-300">Cargando documento en pantalla completa...</span>
        </div>

        <!-- Iframe renderizador al 100% de la ventana -->
        <iframe id="iframeVisorPdf"
                src="about:blank"
                class="w-full h-full flex-1 border-0 bg-white"
                onload="document.getElementById('spinnerCargaPdf')?.classList.add('hidden')">
        </iframe>

        <!-- Mensaje de respaldo para dispositivos móviles -->
        <div class="p-2 text-center bg-slate-900 border-t border-slate-800 text-[11px] text-slate-400 sm:hidden">
            ¿No carga el visor en tu móvil? <a id="btnFallbackMobile" href="#" target="_blank" class="text-blue-400 font-bold underline">Toca aquí para abrir directamente</a>.
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnToggle = document.getElementById('btn-toggle-sidebar');
        const sidebar = document.getElementById('sidebar-psicologia');

        if (btnToggle && sidebar) {
            btnToggle.addEventListener('click', function () {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    });

    function abrirModalPdf(urlInline, urlDescarga, nombrePersona) {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');
        const spinner = document.getElementById('spinnerCargaPdf');
        const titulo = document.getElementById('tituloModalPdf');
        const btnDescargar = document.getElementById('btnDescargarPdfModal');
        const btnPestana = document.getElementById('btnPestanaModal');
        const btnFallback = document.getElementById('btnFallbackMobile');

        if (!modal) return;

        titulo.textContent = 'Consentimiento Informado · ' + (nombrePersona || '');
        btnDescargar.href = urlDescarga || urlInline;
        btnPestana.href = urlInline;
        if (btnFallback) btnFallback.href = urlInline;

        if (spinner) spinner.classList.remove('hidden');
        if (iframe) iframe.src = urlInline;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function cerrarModalPdf() {
        const modal = document.getElementById('modalVisorPdf');
        const iframe = document.getElementById('iframeVisorPdf');

        if (!modal) return;

        modal.classList.add('hidden');
        if (iframe) iframe.src = 'about:blank';
        document.body.classList.remove('overflow-hidden');
    }

    function imprimirPdfModal() {
        const iframe = document.getElementById('iframeVisorPdf');
        if (iframe && iframe.contentWindow) {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                window.open(iframe.src, '_blank');
            }
        }
    }

    // Cerrar al presionar tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalPdf();
        }
    });
</script>

@stack('scripts')
</body>
</html>
