@php
    $titulo = $cap->titulo ?? ($cap['titulo'] ?? 'Capacitación');
    $descripcion = $cap->descripcion ?? ($cap['descripcion'] ?? '');
    $imagen = is_object($cap) ? $cap->url_imagen : (empty($cap['imagen']) ? asset('images/Img-login.jpg') : asset(ltrim($cap['imagen'], '/')));
    $duracion = $cap->duracion_estimada ? $cap->duracion_estimada . ' horas' : ($cap['duracion'] ?? '4 horas');
    $modulosCount = isset($cap->modulos) ? $cap->modulos->count() : ($cap['modulos_count'] ?? 0);
    $modulosCompletados = $cap->modulos_completados_count ?? ($cap['modulos_completados'] ?? 0);
    $porcentaje = $cap->porcentaje_calculado ?? ($cap['porcentaje'] ?? 0);
    $aprobacion = $cap->porcentaje_aprobacion ?? ($cap['porcentaje_aprobacion'] ?? 80);
    $intentos = $cap->intentos_permitidos ?? ($cap['intentos_permitidos'] ?? 3);
    $estadoUsuario = $cap->estado_usuario ?? ($cap['estado'] ?? 'PENDIENTE');
@endphp
<div class="bg-white rounded-3xl border border-line/75 shadow-xs hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between group">
    
    <!-- Redirección directa al curso al hacer clic en la tarjeta -->
    <a href="{{ route('empleado.capacitaciones.ver', $cap) }}" class="block text-inherit flex-1 group/card" title="Haz clic para ver el contenido y módulos de {{ $titulo }}">
        <div>
            <!-- Contenedor de Imagen -->
            <div class="relative h-44 sm:h-48 w-full overflow-hidden bg-brand-light">
                <img src="{{ $imagen }}" 
                     alt="{{ $titulo }}" 
                     class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-500">
                
                <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/30 to-transparent"></div>

                <div class="absolute top-3.5 left-3.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border backdrop-blur-md bg-white/20 text-white border-white/30">
                        <i class="fa-solid fa-award text-brand-sky text-xs"></i> Capacitación Oficial
                    </span>
                </div>

                <div class="absolute top-3.5 right-3.5">
                    @if($estadoUsuario === 'COMPLETADA')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-600 text-white backdrop-blur-xs shadow-xs">
                            <i class="fa-solid fa-circle-check text-xs"></i> Aprobada
                        </span>
                    @elseif($porcentaje >= 100 || $estadoUsuario === 'MODULOS_COMPLETOS')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-600 text-white backdrop-blur-xs shadow-xs">
                            <i class="fa-solid fa-list-check text-xs"></i> Módulos Completos
                        </span>
                    @elseif($estadoUsuario === 'EN_PROGRESO' || $porcentaje > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/95 text-white backdrop-blur-xs shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            En Progreso
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-600/95 text-white backdrop-blur-xs shadow-xs">
                            <i class="fa-solid fa-sparkles text-xs"></i> Disponible
                        </span>
                    @endif
                </div>

                <div class="absolute bottom-3 left-4 right-4 text-white">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-brand-sky flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-xs"></i> Duración: {{ $duracion }} · {{ $modulosCount }} Módulos
                    </span>
                    <h3 class="font-heading font-extrabold text-base sm:text-lg leading-tight mt-0.5 line-clamp-1 group-hover/card:text-brand-sky transition-colors">
                        {{ $titulo }}
                    </h3>
                </div>

                <svg class="absolute -bottom-1 left-0 w-full h-4 text-white" viewBox="0 0 600 20" preserveAspectRatio="none">
                    <path d="M0,5 C100,18 200,3 300,14 C400,22 500,6 600,15 L600,20 L0,20 Z" fill="currentColor"/>
                </svg>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="p-5 sm:p-6 space-y-4">
                <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 leading-relaxed">
                    {{ $descripcion }}
                </p>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700">Progreso del curso</span>
                        <span class="font-bold {{ $porcentaje >= 100 ? 'text-emerald-600' : 'text-brand-blue' }}">{{ $porcentaje }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="{{ $porcentaje >= 100 ? 'bg-emerald-500' : 'bg-brand-blue' }} h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-muted">
                        <span>{{ $modulosCompletados }} de {{ $modulosCount }} módulos completados</span>
                        <span>Mínimo aprobación: {{ $aprobacion }}%</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-line/60 text-[11px] text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-right text-brand-blue"></i>
                        <span>{{ $intentos }} intentos de examen</span>
                    </div>
                    <div class="flex items-center gap-1.5 justify-end">
                        <i class="fa-solid fa-graduation-cap text-brand-blue"></i>
                        <span>Certificado Oficial</span>
                    </div>
                </div>
            </div>
        </div>
    </a>

    <!-- Botón de Acción Principal -->
    <div class="px-5 pb-5 sm:px-6 sm:pb-6 pt-0">
        @if($estadoUsuario === 'COMPLETADA')
            <a href="{{ route('empleado.certificados') }}"
               class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-emerald-600 hover:bg-emerald-700 text-white hover:-translate-y-0.5">
                <i class="fa-solid fa-graduation-cap text-base"></i>
                <span>Ver / Descargar Certificado</span>
            </a>
        @elseif($porcentaje >= 100 || $estadoUsuario === 'MODULOS_COMPLETOS')
            <form method="POST" action="{{ route('empleado.capacitaciones.finalizar', $cap) }}" class="w-full m-0">
                @csrf
                <button type="submit"
                        class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-emerald-600 hover:bg-emerald-700 text-white hover:-translate-y-0.5 cursor-pointer">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>Finalizar Capacitación</span>
                </button>
            </form>
        @elseif($estadoUsuario === 'EN_PROGRESO' || $porcentaje > 0)
            <a href="{{ route('empleado.capacitaciones.ver', $cap) }}"
               style="background-color: #0056b3; color: #ffffff;"
               class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-[#0056b3] hover:bg-[#003d80] text-white hover:-translate-y-0.5">
                <i class="fa-solid fa-circle-play text-base"></i>
                <span>Continuar Capacitación ({{ $porcentaje }}%)</span>
            </a>
        @else
            <form method="POST" action="{{ route('empleado.capacitaciones.iniciar', $cap) }}" class="w-full m-0">
                @csrf
                <button type="submit"
                        style="background-color: #0056b3; color: #ffffff;"
                        class="w-full py-3 px-4 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all duration-200 flex items-center justify-center gap-2 shadow-xs bg-[#0056b3] hover:bg-[#003d80] text-white hover:-translate-y-0.5 cursor-pointer">
                    <i class="fa-solid fa-play text-base"></i>
                    <span>Iniciar Capacitación</span>
                </button>
            </form>
        @endif
    </div>

</div>
