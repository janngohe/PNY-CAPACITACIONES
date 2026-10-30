@extends('layouts.psicologia')

@section('title', 'Panel de Inicio')
@section('page_title', 'Módulo de Psicología')
@section('page_subtitle', 'Gestión Humana y Pruebas Psicotécnicas')

@section('content')
<div class="space-y-8">

    <!-- Banner de Bienvenida -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-brand-dark via-slate-900 to-indigo-950 p-6 md:p-8 text-white shadow-md">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 backdrop-blur-md px-3 py-1 text-xs font-semibold text-blue-200 border border-white/10 mb-3">
                    <i class="fa-solid fa-building-user text-[11px]"></i> C.I. Piscícola New York S.A. · Selección de Personal
                </span>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Evaluación Psicotécnica Institucional</h1>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                    Monitoree las aplicaciones de pruebas psicotécnicas, consulte resultados calificados de la prueba PAC-A y gestione el portal de auto-registro y firma de consentimientos mediante código QR.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 shrink-0">
                <a href="{{ route('psicologia.qr.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                    Código QR Evaluados
                </a>
                <a href="{{ route('psicologia.pruebas.historial') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-blue hover:bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                    Historial de Pruebas
                </a>
            </div>
        </div>
        <!-- Decoración de fondo sutil -->
        <div class="absolute -right-8 -bottom-10 h-48 w-48 rounded-full bg-brand-blue/10 blur-2xl"></div>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Personas Evaluadas vía QR -->
        <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Evaluados vía QR</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-brand-blue">
                    <i class="fa-solid fa-qrcode text-base"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalEvaluadosQr }}</span>
                <span class="text-xs text-slate-400 font-medium">aspirantes</span>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-brand-blue font-semibold">
                <a href="{{ route('psicologia.qr.index') }}" class="hover:underline flex items-center gap-1">
                    Gestionar código QR <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Consentimientos Firmados -->
        <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Consentimientos Válidos</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-file-signature text-base"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ $consentimientosFirmados }}</span>
                <span class="text-xs text-slate-400 font-medium">otorgados digitalmente</span>
            </div>
            <div class="mt-3 text-xs text-slate-500 font-medium">
                @if($consentimientosPendientes > 0)
                    <span class="text-amber-600 font-semibold">{{ $consentimientosPendientes }} pendientes</span> de firma
                @else
                    <span class="text-emerald-600 font-semibold">Todos los tramitados</span> están firmados
                @endif
            </div>
        </div>

        <!-- Pruebas PAC-A Finalizadas -->
        <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">PAC-A Calificadas</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fa-solid fa-square-check text-base"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-indigo-600 tracking-tight">{{ $pacaFinalizadas }}</span>
                <span class="text-xs text-slate-400 font-medium">finalizadas</span>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-indigo-600 font-semibold">
                <a href="{{ route('psicologia.pruebas.historial') }}" class="hover:underline flex items-center gap-1">
                    Consultar historial <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Pruebas en Curso / Borrador -->
        <div class="rounded-xl border border-slate-200/80 bg-white p-5 shadow-2xs hover:shadow-xs transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">En Curso / Borradores</span>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="fa-solid fa-clock-rotate-left text-base"></i>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-amber-600 tracking-tight">{{ $pacaBorradores }}</span>
                <span class="text-xs text-slate-400 font-medium">sesiones activas</span>
            </div>
            <div class="mt-3 text-xs text-slate-500 font-medium">
                Respuestas guardadas para continuar
            </div>
        </div>
    </div>

    <!-- Sección en 2 Columnas: Pruebas Recientes y Personas Recientes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Evaluaciones Recientes (2 Columnas) -->
        <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-2xs">
            <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Aplicaciones Recientes de Pruebas</h2>
                    <p class="text-xs text-slate-500">Últimas sesiones de PAC-A registradas en el sistema</p>
                </div>
                <a href="{{ route('psicologia.pruebas.historial') }}" class="text-xs font-semibold text-brand-blue hover:underline">
                    Ver historial completo
                </a>
            </div>

            @if($aplicacionesRecientes->isEmpty())
                <div class="py-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-3">
                        <i class="fa-solid fa-clipboard-list text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Aún no se han registrado aplicaciones de pruebas</p>
                    <p class="text-xs text-slate-400 mt-1">Los aspirantes pueden escanear el Código QR para iniciar su evaluación.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 mt-2">
                    @foreach($aplicacionesRecientes as $app)
                        @php $badge = $app->badgeEstado(); @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-4 hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                            <div class="flex items-start gap-3.5">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700 font-bold text-xs mt-0.5">
                                    {{ $app->prueba_codigo }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">{{ $app->candidato->nombre_completo }}</h3>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500">
                                        <span>CC: {{ $app->candidato->numero_identificacion }}</span>
                                        <span>•</span>
                                        <span>Fecha: {{ $app->fecha_aplicacion->format('d/m/Y') }}</span>
                                        @if($app->edad_evaluado)
                                            <span>•</span>
                                            <span>{{ $app->edad_evaluado }} años</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center flex-wrap justify-end">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold border {{ $badge['clase'] }}">
                                        <i class="fa-solid {{ $badge['icono'] }} text-[10px]"></i>
                                        {{ $badge['texto'] }}
                                    </span>

                                    @if($app->consentimiento)
                                        <button type="button"
                                           onclick="abrirModalPdf('{{ route('psicologia.consentimientos.pdf', [$app->consentimiento, 'inline' => 1]) }}', '{{ route('psicologia.consentimientos.pdf', $app->consentimiento) }}', 'Consentimiento - {{ addslashes($app->candidato->nombre_completo) }}')"
                                           title="Previsualizar Consentimiento Informado (PDF)"
                                           class="inline-flex items-center gap-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/70 px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer">
                                            <i class="fa-solid fa-file-pdf text-[11px] text-rose-600"></i>
                                            <span class="hidden sm:inline">Consentimiento</span>
                                        </button>
                                    @endif

                                    @if($app->estaFinalizada())
                                        <a href="{{ route('psicologia.paca.resultado', $app) }}" class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-chart-simple text-[11px]"></i> Resultados
                                        </a>
                                    @else
                                        <a href="{{ route('psicologia.paca.aplicar', $app) }}" class="inline-flex items-center gap-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-play text-[11px]"></i> Continuar
                                        </a>
                                    @endif
                                </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Personas Evaluadas Recientemente vía QR (1 Columna) -->
        <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Evaluados vía QR</h2>
                        <p class="text-xs text-slate-500">Últimos aspirantes auto-registrados</p>
                    </div>
                    <a href="{{ route('psicologia.qr.index') }}" class="text-xs font-semibold text-brand-blue hover:underline">
                        Ver panel QR
                    </a>
                </div>

                @if($evaluadosRecientes->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-xs">
                        No hay personas registradas recientemente vía QR.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 mt-2">
                        @foreach($evaluadosRecientes as $cand)
                            @php
                                $appPaca = $cand->aplicaciones->where('prueba_codigo', 'PAC-A')->first();
                            @endphp
                            <div class="py-3.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="block text-xs font-bold text-slate-900 truncate">
                                        {{ $cand->nombre_completo }}
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">CC: {{ $cand->numero_identificacion }} · {{ $cand->ciudad_municipio }}</p>
                                </div>
                                <div class="shrink-0 flex items-center gap-1.5 flex-wrap justify-end">
                                    @php $cons = $cand->ultimoConsentimiento(); @endphp
                                    @if($cons)
                                        <button type="button"
                                           onclick="abrirModalPdf('{{ route('psicologia.consentimientos.pdf', [$cons, 'inline' => 1]) }}', '{{ route('psicologia.consentimientos.pdf', $cons) }}', 'Consentimiento - {{ addslashes($cand->nombre_completo) }}')"
                                           title="Previsualizar Consentimiento PDF con Firma"
                                           class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200 hover:bg-rose-100 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-file-pdf text-[9px] text-rose-600"></i>
                                            <span>Consentimiento</span>
                                        </button>
                                    @endif
                                    @if($appPaca && $appPaca->estaFinalizada())
                                        <a href="{{ route('psicologia.paca.resultado', $appPaca) }}" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-200 hover:bg-emerald-100">
                                            <i class="fa-solid fa-check text-[9px]"></i> Calificada
                                        </a>
                                    @elseif($appPaca)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[9px]"></i> En curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">
                                            Registrado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('psicologia.qr.index') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 p-3 text-xs font-bold border border-emerald-200 transition-colors">
                    <i class="fa-solid fa-qrcode text-xs"></i>
                    Abrir y Proyectar Código QR
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
