@extends('layouts.admin')

@section('title', 'Gestión de Certificados')
@section('page_title', 'Certificados Digitales')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    @include('admin._banner', [
        'insignia' => 'Acreditación y Logros',
        'titulo' => 'Certificados de Capacitación',
        'descripcion' => 'Consulta los certificados oficiales emitidos a los colaboradores con su código único de autenticidad. También puedes generar certificados para capacitaciones completadas pendientes.',
        'icono' => 'fa-award',
    ])

    <!-- SECCIÓN DE CERTIFICADOS PENDIENTES POR GENERAR (si existen) -->
    @if ($pendientes->isNotEmpty())
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-3xl p-5 sm:p-6 shadow-2xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                        <i class="fa-solid fa-stamp"></i>
                    </span>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-amber-950">
                            Certificados pendientes por generar ({{ $pendientes->count() }})
                        </h3>
                        <p class="text-xs text-amber-900 mt-0.5">
                            Colaboradores que completaron satisfactoriamente sus capacitaciones pero aún no tienen certificado emitido.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.certificados.generar') }}" class="m-0"
                      onsubmit="return confirm('¿Generar todos los certificados pendientes ({{ $pendientes->count() }}) ahora?');">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-colors cursor-pointer">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>Generar Todos los Pendientes</span>
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($pendientes as $p)
                    <div class="bg-white/90 backdrop-blur-xs p-3.5 rounded-2xl border border-amber-200 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-brand-dark truncate leading-tight">{{ $p->usuario->nombre_completo }}</p>
                            <p class="text-[11px] text-muted truncate mt-0.5">{{ $p->capacitacion->titulo }}</p>
                            <span class="text-[10px] text-amber-700 font-semibold">{{ $p->usuario->area->nombre ?? 'Sin área' }}</span>
                        </div>
                        <form method="POST" action="{{ route('admin.certificados.generar') }}" class="m-0 shrink-0">
                            @csrf
                            <input type="hidden" name="usuario_id" value="{{ $p->usuario_id }}">
                            <input type="hidden" name="capacitacion_id" value="{{ $p->capacitacion_id }}">
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 font-heading font-bold text-[11px] transition-colors cursor-pointer"
                                    title="Emitir este certificado">
                                Emitir
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- TABS DE CERTIFICADOS: INSTITUCIONALES / EXTERNOS -->
    <div class="flex items-center gap-2 border-b border-line/80 pb-3">
        <button type="button" id="tab-btn-internos"
                onclick="cambiarPestana('internos')"
                class="px-4 py-2.5 rounded-2xl font-heading font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer bg-brand-blue text-white shadow-xs">
            <i class="fa-solid fa-award"></i>
            <span>Certificados Institucionales</span>
            <span class="px-2 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-mono">{{ $totalEmitidos }}</span>
        </button>

        <button type="button" id="tab-btn-externos"
                onclick="cambiarPestana('externos')"
                class="px-4 py-2.5 rounded-2xl font-heading font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200">
            <i class="fa-solid fa-file-arrow-up"></i>
            <span>Certificados Externos (Talento Humano)</span>
            @if ($externosPendientes > 0)
                <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[11px] font-mono animate-pulse font-bold">{{ $externosPendientes }} pend.</span>
            @else
                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[11px] font-mono">{{ $totalExternos }}</span>
            @endif
        </button>

        <button type="button" id="tab-btn-plantillas"
                onclick="cambiarPestana('plantillas')"
                class="px-4 py-2.5 rounded-2xl font-heading font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200">
            <i class="fa-solid fa-stamp"></i>
            <span>Plantillas de Certificado</span>
            <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[11px] font-mono">{{ $totalPlantillas }}</span>
        </button>
    </div>

    <!-- SECCIÓN 1: CERTIFICADOS INSTITUCIONALES -->
    <div id="panel-internos" class="space-y-6">
        <!-- BUSCADOR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <span class="text-xs font-semibold text-slate-700">
                Certificados oficiales emitidos: <strong class="text-brand-dark">{{ $totalEmitidos }}</strong>
            </span>

            <form method="GET" action="{{ route('admin.certificados.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-72">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Código, nombre, cédula..."
                           class="w-full pl-9 pr-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-blue hover:bg-brand-deep text-white text-xs font-bold transition-colors cursor-pointer">
                    Buscar
                </button>
                @if ($q)
                    <a href="{{ route('admin.certificados.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- LISTADO DE CERTIFICADOS EMITIDOS -->
        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                        <tr>
                            <th class="px-5 sm:px-6 py-3.5 font-bold">Código Único</th>
                            <th class="px-4 py-3.5 font-bold">Colaborador</th>
                            <th class="px-4 py-3.5 font-bold">Capacitación</th>
                            <th class="px-4 py-3.5 font-bold text-center">Calificación</th>
                            <th class="px-4 py-3.5 font-bold">Fecha de Emisión</th>
                            <th class="px-5 sm:px-6 py-3.5 font-bold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @forelse ($certificados as $cert)
                            <tr class="hover:bg-brand-light/30 transition-colors">
                                <td class="px-5 sm:px-6 py-3.5">
                                    <span class="font-mono font-bold text-brand-blue bg-blue-50 px-2 py-0.5 rounded text-[11px]">
                                        {{ $cert->codigo }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-brand-dark leading-tight">{{ $cert->nombre_empleado }}</p>
                                    <p class="text-[11px] text-muted font-mono mt-0.5">C.C. {{ $cert->identificacion }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-brand-dark line-clamp-1 max-w-xs">{{ $cert->nombre_capacitacion }}</p>
                                    <span class="text-[10px] text-muted">{{ $cert->area_nombre ?? 'Área General' }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold text-emerald-700">
                                    {{ rtrim(rtrim(number_format((float) $cert->porcentaje, 1), '0'), '.') }}%
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    {{ $cert->fecha_emision?->format('d/m/Y') }}
                                    <span class="block text-[10px] text-muted">{{ $cert->fecha_emision?->diffForHumans() }}</span>
                                </td>
                                <td class="px-5 sm:px-6 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button"
                                                class="btn-ver-certificado px-2.5 py-1.5 rounded-lg bg-brand-light hover:bg-brand-blue hover:text-white text-brand-blue font-heading font-bold text-[11px] transition-colors cursor-pointer shadow-2xs"
                                                data-codigo="{{ $cert->codigo }}"
                                                data-empleado="{{ $cert->nombre_empleado }}"
                                                data-identificacion="{{ $cert->identificacion }}"
                                                data-capacitacion="{{ $cert->nombre_capacitacion }}"
                                                data-area="{{ $cert->area_nombre ?? 'Área General' }}"
                                                data-horas="{{ $cert->capacitacion?->duracion_estimada ?? 4 }}"
                                                data-nota="{{ rtrim(rtrim(number_format((float) $cert->porcentaje, 1), '0'), '.') }}"
                                                data-fecha="{{ $cert->fecha_emision?->format('d/m/Y') ?? date('d/m/Y') }}"
                                                data-jefe-nombre="{{ $cert->plantillaCertificado->firma_1_nombre ?? 'Ing. Carlos Mendoza' }}"
                                                data-jefe-cargo="{{ $cert->plantillaCertificado->firma_1_cargo ?? 'Jefe de Operaciones' }}"
                                                data-gerente-nombre="{{ $cert->plantillaCertificado->firma_2_nombre ?? 'Dra. Elena Ramos' }}"
                                                data-gerente-cargo="{{ $cert->plantillaCertificado->firma_2_cargo ?? 'Gerente General' }}"
                                                data-pdf="{{ route('empleado.certificados.pdf', $cert) }}"
                                                title="Ver certificado en modal">
                                            <i class="fa-solid fa-eye mr-1"></i> Ver
                                        </button>
                                        <a href="{{ route('empleado.certificados.pdf', $cert) }}"
                                           class="p-1.5 rounded-lg bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 transition-colors"
                                           title="Descargar PDF">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-muted">
                                    <i class="fa-solid fa-award text-3xl mb-2 text-slate-300 block"></i>
                                    No se encontraron certificados institucionales con los criterios de búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($certificados->hasPages())
                <div class="px-6 py-4 border-t border-line/60 bg-slate-50/50">
                    {{ $certificados->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- SECCIÓN 2: CERTIFICADOS EXTERNOS RADICADOS POR EMPLEADOS -->
    <div id="panel-externos" class="space-y-6 hidden">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">Acreditación de Saberes Externos</h3>
                <p class="text-xs text-muted">Documentos y constancias aportados por los colaboradores (SENA, ICA, Bomberos, etc.) para validación oficial.</p>
            </div>
            <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1 rounded-full">
                Total Radicados: <strong>{{ $certificadosExternos->count() }}</strong>
            </span>
        </div>

        <div class="bg-white rounded-3xl border border-line/80 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-muted border-b border-line/60">
                        <tr>
                            <th class="px-5 sm:px-6 py-3.5 font-bold">Colaborador</th>
                            <th class="px-4 py-3.5 font-bold">Área</th>
                            <th class="px-4 py-3.5 font-bold">Entidad Emisora</th>
                            <th class="px-4 py-3.5 font-bold">Fecha Expedición</th>
                            <th class="px-4 py-3.5 font-bold text-center">Estado</th>
                            <th class="px-4 py-3.5 font-bold">Documento Soporte</th>
                            <th class="px-5 sm:px-6 py-3.5 font-bold text-right">Revisión / Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/50">
                        @forelse ($certificadosExternos as $ext)
                            <tr class="hover:bg-brand-light/30 transition-colors">
                                <td class="px-5 sm:px-6 py-3.5">
                                    <p class="font-bold text-brand-dark leading-tight">{{ $ext->usuario->nombre_completo ?? 'Colaborador' }}</p>
                                    <p class="text-[11px] text-muted font-mono mt-0.5">C.C. {{ $ext->usuario->identificacion ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    {{ $ext->usuario->area->nombre ?? 'Sin área' }}
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-brand-dark">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                                        <span>{{ $ext->entidad_emisora }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    {{ $ext->fecha_emision ? $ext->fecha_emision->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($ext->estado === 'APROBADO')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i> Aprobado
                                        </span>
                                    @elseif ($ext->estado === 'PENDIENTE')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[10px]"></i> Pendiente de Revisión
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i> Rechazado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <a href="{{ asset('storage/' . $ext->ruta_archivo) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-brand-blue font-heading font-bold text-[11px] transition-colors">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                        <span>Ver soporte</span>
                                    </a>
                                </td>
                                <td class="px-5 sm:px-6 py-3.5 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        @if ($ext->estado !== 'APROBADO')
                                            <form method="POST" action="{{ route('admin.certificados.externos.aprobar', $ext) }}" class="m-0"
                                                  onsubmit="return confirm('¿Aprobar este certificado externo para el colaborador?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-heading font-bold text-[11px] transition-colors cursor-pointer shadow-2xs"
                                                        title="Aprobar Certificado">
                                                    <i class="fa-solid fa-check mr-1"></i> Aprobar
                                                </button>
                                            </form>
                                        @endif

                                        @if ($ext->estado !== 'RECHAZADO')
                                            <form method="POST" action="{{ route('admin.certificados.externos.rechazar', $ext) }}" class="m-0"
                                                  onsubmit="return confirm('¿Rechazar este certificado externo?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 font-heading font-bold text-[11px] transition-colors cursor-pointer"
                                                        title="Rechazar Certificado">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Rechazar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-muted">
                                    <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                    No se registran certificados externos pendientes ni radicados por colaboradores.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: PLANTILLAS DE CERTIFICADOS -->
    <div id="panel-plantillas" class="space-y-6 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-extrabold text-base text-brand-dark">Plantillas Oficiales de Certificado</h3>
                <p class="text-xs text-muted">Sube el diseño base del certificado (fondo en imagen) y configura los datos del Jefe Inmediato y Gerente para la emisión digital.</p>
            </div>
            <button type="button"
                    onclick="abrirModalCrearPlantilla()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs sm:text-sm shadow-xs transition-all cursor-pointer">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span>Subir Nueva Plantilla</span>
            </button>
        </div>

        @if($plantillas->isEmpty())
            <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-10 text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-2xl shadow-xs">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div class="max-w-md mx-auto space-y-1">
                    <h4 class="font-heading font-extrabold text-brand-dark text-base">No hay plantillas registradas aún</h4>
                    <p class="text-xs text-muted">Sube el diseño base del diploma junto con la información del Jefe Inmediato y Gerente para empezar a emitir certificados personalizados.</p>
                </div>
                <button type="button"
                        onclick="abrirModalCrearPlantilla()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-blue hover:bg-brand-deep text-white font-heading font-bold text-xs shadow-xs transition-all cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    <span>Crear Primera Plantilla</span>
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($plantillas as $pl)
                    <div class="bg-white rounded-3xl border border-line/80 shadow-2xs p-5 sm:p-6 flex flex-col justify-between space-y-5 relative overflow-hidden group hover:border-brand-blue/40 transition-colors">
                        
                        <!-- Encabezado de la plantilla -->
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-brand-light text-brand-blue flex items-center justify-center text-lg font-bold shrink-0">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-heading font-extrabold text-sm sm:text-base text-brand-dark leading-tight">{{ $pl->nombre }}</h4>
                                            @if($pl->estado)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fa-solid fa-circle-check text-[9px]"></i> Activa
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    Inactiva
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-muted block mt-0.5">
                                            @if($pl->area)
                                                Área específica: <strong class="text-brand-dark">{{ $pl->area->nombre }}</strong>
                                            @else
                                                <span class="text-brand-blue font-semibold bg-blue-50 px-2 py-0.5 rounded">Plantilla General (Aplica a todas las áreas)</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0">
                                    <!-- Botón Editar -->
                                    <button type="button"
                                            class="btn-editar-plantilla p-2 rounded-xl text-slate-500 hover:text-brand-blue hover:bg-slate-100 transition-colors cursor-pointer"
                                            title="Editar Plantilla"
                                            data-id="{{ $pl->id }}"
                                            data-nombre="{{ $pl->nombre }}"
                                            data-descripcion="{{ $pl->descripcion }}"
                                            data-area-id="{{ $pl->area_id }}"
                                            data-organizacion="{{ $pl->nombre_organizacion }}"
                                            data-texto="{{ $pl->texto_certificado }}"
                                            data-jefe-nombre="{{ $pl->firma_1_nombre }}"
                                            data-jefe-cargo="{{ $pl->firma_1_cargo }}"
                                            data-gerente-nombre="{{ $pl->firma_2_nombre }}"
                                            data-gerente-cargo="{{ $pl->firma_2_cargo }}"
                                            data-estado="{{ $pl->estado ? '1' : '0' }}"
                                            data-action="{{ route('admin.certificados.plantillas.update', $pl) }}">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    <!-- Botón Toggle Estado -->
                                    <form method="POST" action="{{ route('admin.certificados.plantillas.estado', $pl) }}" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="p-2 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-slate-100 transition-colors cursor-pointer"
                                                title="{{ $pl->estado ? 'Desactivar plantilla' : 'Activar plantilla' }}">
                                            <i class="fa-solid {{ $pl->estado ? 'fa-toggle-on text-emerald-600 text-base' : 'fa-toggle-off text-slate-400 text-base' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Botón Eliminar -->
                                    <form method="POST" action="{{ route('admin.certificados.plantillas.destroy', $pl) }}" class="m-0"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta plantilla de certificado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-slate-100 transition-colors cursor-pointer"
                                                title="Eliminar plantilla">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($pl->descripcion)
                                <p class="text-xs text-slate-600">{{ $pl->descripcion }}</p>
                            @endif
                        </div>

                        <!-- Miniatura del archivo de plantilla cargado -->
                        <div class="relative rounded-2xl border border-line overflow-hidden bg-slate-50 aspect-[16/7] flex items-center justify-center group/preview">
                            @if($pl->ruta_plantilla)
                                <img src="{{ asset('storage/' . $pl->ruta_plantilla) }}" 
                                     alt="{{ $pl->nombre }}" 
                                     class="w-full h-full object-cover">
                                <a href="{{ asset('storage/' . $pl->ruta_plantilla) }}" target="_blank"
                                   class="absolute inset-0 bg-brand-dark/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center gap-2 text-white text-xs font-bold backdrop-blur-xs">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    <span>Ver imagen original</span>
                                </a>
                            @else
                                <span class="text-xs text-muted flex items-center gap-1.5">
                                    <i class="fa-regular fa-image"></i> Sin imagen de diseño cargada
                                </span>
                            @endif
                        </div>

                        <!-- Firmas configuradas: Jefe Inmediato y Gerente -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-line/60 text-xs">
                            <!-- Jefe Inmediato -->
                            <div class="bg-slate-50 p-3 rounded-2xl border border-line/60 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-brand-blue">Jefe Inmediato</span>
                                    @if($pl->firma_1_ruta)
                                        <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            <i class="fa-solid fa-signature"></i> Firma OK
                                        </span>
                                    @else
                                        <span class="text-[9px] text-slate-400">Firma tipográfica</span>
                                    @endif
                                </div>
                                <p class="font-bold text-brand-dark truncate leading-tight">{{ $pl->firma_1_nombre ?? 'Ing. Carlos Mendoza' }}</p>
                                <p class="text-[11px] text-muted truncate">{{ $pl->firma_1_cargo ?? 'Jefe de Operaciones' }}</p>
                            </div>

                            <!-- Gerente -->
                            <div class="bg-slate-50 p-3 rounded-2xl border border-line/60 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700">Gerencia</span>
                                    @if($pl->firma_2_ruta)
                                        <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            <i class="fa-solid fa-signature"></i> Firma OK
                                        </span>
                                    @else
                                        <span class="text-[9px] text-slate-400">Firma tipográfica</span>
                                    @endif
                                </div>
                                <p class="font-bold text-brand-dark truncate leading-tight">{{ $pl->firma_2_nombre ?? 'Dra. Elena Ramos' }}</p>
                                <p class="text-[11px] text-muted truncate">{{ $pl->firma_2_cargo ?? 'Gerente General' }}</p>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- ========================================================
     MODAL PARA VISUALIZACIÓN DEL CERTIFICADO EN LA MISMA PESTAÑA
     ======================================================== -->
<div id="modal-visor-certificado" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-visor-titulo" role="dialog" aria-modal="true">
    <div id="modal-visor-backdrop" class="fixed inset-0 bg-brand-dark/70 backdrop-blur-sm transition-opacity opacity-0"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-visor-panel" class="relative bg-white rounded-3xl max-w-4xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <!-- Barra de control superior del visor -->
            <div class="bg-slate-900 text-white px-6 py-3.5 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-white/10 text-amber-400 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-award"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-bold text-xs sm:text-sm text-white">Visualizador Oficial de Certificado</h4>
                        <span id="visor-codigo" class="text-[10px] font-mono text-cyan-300"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="#" id="visor-btn-descargar-pdf"
                       class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold text-xs flex items-center gap-1.5 transition-all shadow-xs">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>Descargar PDF</span>
                    </a>
                    <button type="button" id="visor-btn-cerrar" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer" title="Cerrar ventana">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Contenedor del Diploma (réplica exacta del diseño institucional) -->
            <div class="p-4 sm:p-6 max-h-[82vh] overflow-y-auto bg-slate-100">
                <div class="bg-white rounded-2xl border-4 border-slate-200 shadow-xl overflow-hidden relative">
                    
                    <!-- Banner superior con olas marinas -->
                    <div class="relative bg-gradient-to-r from-brand-dark via-brand-deep to-[#0056b3] text-white p-5 sm:p-6 overflow-hidden">
                        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/95 p-1.5 rounded-xl border border-white/40 shadow-sm shrink-0">
                                    <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-10 w-auto object-contain">
                                </div>
                                <div>
                                    <h2 class="font-heading font-black text-base text-white uppercase tracking-wider">
                                        C.I. PISCÍCOLA <span class="text-cyan-300">NEW YORK</span> S.A.S.
                                    </h2>
                                    <p class="text-[10px] text-slate-200">Gestión de Talento Humano & Capacitación Continuada</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cuerpo central del Diploma -->
                    <div class="p-6 sm:p-10 space-y-6 text-center bg-gradient-to-b from-white via-slate-50/50 to-white relative">
                        <!-- Marca de agua -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-5">
                            <img src="{{ asset('images/Logo.png') }}" alt="Watermark" class="w-80 h-auto">
                        </div>

                        <div class="relative z-10 space-y-2">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-[11px] font-bold border border-amber-200">
                                <i class="fa-solid fa-certificate text-amber-500"></i>
                                <span>Acreditación Institucional Oficial</span>
                            </div>
                            <h3 class="font-heading font-black text-xl sm:text-2xl text-brand-dark uppercase tracking-tight">
                                CERTIFICADO DE CAPACITACIÓN Y APROBACIÓN
                            </h3>
                            <p class="text-xs text-slate-500">La Dirección General de C.I. Piscícola New York S.A.S. hace constar formalmente que:</p>
                        </div>

                        <!-- Nombre del colaborador -->
                        <div class="relative z-10 space-y-2 py-1">
                            <h2 id="diploma-empleado" class="font-heading font-black text-2xl sm:text-3xl text-brand-dark uppercase"></h2>
                            <div class="h-0.5 w-48 bg-gradient-to-r from-transparent via-brand-blue to-transparent mx-auto"></div>
                            <p class="text-xs text-slate-600">
                                Con documento de identificación N.º <strong id="diploma-identificacion" class="text-brand-dark font-extrabold"></strong>, adscrito al área de <strong id="diploma-area" class="text-brand-blue font-extrabold"></strong>,
                            </p>
                        </div>

                        <!-- Capacitación y Acreditación -->
                        <div class="relative z-10 max-w-xl mx-auto bg-slate-50 p-5 rounded-2xl border border-line/80 space-y-3">
                            <span class="text-[10px] uppercase font-extrabold tracking-widest text-brand-sky block">
                                HA CUMPLIDO SATISFACTORIAMENTE LA CAPACITACIÓN FORMATIVA EN:
                            </span>
                            <h4 id="diploma-capacitacion" class="font-heading font-black text-base sm:text-lg text-brand-dark uppercase"></h4>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-md mx-auto">
                                Con una intensidad horaria de <strong id="diploma-horas" class="text-brand-dark font-bold">4 horas</strong>, realizada el día <strong id="diploma-fecha-realizada" class="text-brand-dark font-bold"></strong>, con el propósito de fortalecer sus conocimientos y competencias en el área correspondiente.
                            </p>
                            <p class="text-[11px] text-slate-500 italic">
                                Se expide el presente certificado como constancia de su participación y cumplimiento de la actividad formativa.
                            </p>
                            <div class="flex items-center justify-center gap-3 text-xs pt-1">
                                <span class="inline-flex items-center gap-1.5 text-slate-700 bg-white px-3.5 py-1 rounded-full border border-line font-bold">
                                    <i class="fa-solid fa-location-dot text-brand-blue"></i> Rivera - Huila, <span id="diploma-fecha"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Firmas -->
                        <!-- Firmas Dinámicas en el Visor -->
                        <div class="relative z-10 pt-4 border-t border-line/70 grid grid-cols-2 gap-4 text-center text-xs">
                            <div class="space-y-1">
                                <span class="font-serif italic text-base text-brand-dark font-bold block select-none">Firma Verificada</span>
                                <div class="w-40 mx-auto border-t border-brand-dark/40 pt-1">
                                    <p id="diploma-jefe-nombre" class="font-heading font-bold text-xs text-brand-dark truncate leading-tight">Ing. Carlos Mendoza</p>
                                    <p id="diploma-jefe-cargo" class="text-[10px] text-muted truncate">Jefe Inmediato</p>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span class="font-serif italic text-base text-brand-blue font-bold block select-none">Firma Verificada</span>
                                <div class="w-40 mx-auto border-t border-brand-dark/40 pt-1">
                                    <p id="diploma-gerente-nombre" class="font-heading font-bold text-xs text-brand-dark truncate leading-tight">Dra. Elena Ramos</p>
                                    <p id="diploma-gerente-cargo" class="text-[10px] text-muted truncate">Gerente General</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pie del diploma -->
                    <div class="bg-slate-900 text-slate-400 p-2.5 text-center text-[10px] border-t border-slate-800">
                        Documento Oficial Institucional emitido por el Sistema de Capacitación de C.I. Piscícola New York S.A.S.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL PARA CREAR / SUBIR NUEVA PLANTILLA DE CERTIFICADO
     ======================================================== -->
<div id="modal-crear-plantilla" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-crear-backdrop" class="fixed inset-0 bg-brand-dark/70 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarModalCrearPlantilla()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-crear-panel" class="relative bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <div class="bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-stamp"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm sm:text-base text-white">Subir y Configurar Plantilla</h4>
                        <p class="text-[11px] text-slate-200">Carga el diseño base y personaliza los datos del Jefe Inmediato y Gerente.</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalCrearPlantilla()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.certificados.plantillas.store') }}" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-brand-dark mb-1">Nombre de la Plantilla <span class="text-rose-500">*</span></label>
                        <input type="text" name="nombre" required placeholder="Ej: Plantilla Oficial Institucional 2026"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>

                    <div>
                        <label class="block font-bold text-brand-dark mb-1">Área Asignada</label>
                        <select name="area_id" class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            <option value="">General (Todas las Áreas de la Empresa)</option>
                            @foreach($areas as $ar)
                                <option value="{{ $ar->id }}">{{ $ar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-brand-dark mb-1">Descripción / Notas internas</label>
                    <input type="text" name="descripcion" placeholder="Ej: Diseño con orla azul y acreditación de bioseguridad"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                </div>

                <!-- Archivo de la Plantilla -->
                <div class="bg-brand-light/50 p-4 rounded-2xl border border-brand-blue/20 space-y-1.5">
                    <label class="block font-bold text-brand-dark">
                        <i class="fa-solid fa-image text-brand-blue mr-1"></i> Archivo de la Plantilla (Fondo / Maqueta) <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-muted">Sube una imagen de diseño en formato PNG, JPG o WebP (horizontal / landscape). Máximo 5MB.</p>
                    <input type="file" name="archivo_plantilla" required accept="image/png,image/jpeg,image/webp"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white hover:file:bg-brand-deep cursor-pointer">
                </div>

                <!-- SECCIÓN: JEFE INMEDIATO -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-line space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs font-bold">1</span>
                        <h5 class="font-heading font-extrabold text-xs text-brand-dark uppercase tracking-wide">Firma del Jefe Inmediato</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" name="firma_1_nombre" placeholder="Ej: Ing. Carlos Mendoza"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cargo Oficial</label>
                            <input type="text" name="firma_1_cargo" placeholder="Ej: Director de Bioseguridad y Operaciones"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Imagen de Firma Digital (PNG fondo transparente, opcional)</label>
                        <input type="file" name="firma_1_archivo" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-[11px] text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 cursor-pointer">
                    </div>
                </div>

                <!-- SECCIÓN: GERENTE -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-line space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-cyan-100 text-cyan-800 flex items-center justify-center text-xs font-bold">2</span>
                        <h5 class="font-heading font-extrabold text-xs text-brand-dark uppercase tracking-wide">Firma del Gerente</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" name="firma_2_nombre" placeholder="Ej: Dra. Elena Ramos"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cargo Oficial</label>
                            <input type="text" name="firma_2_cargo" placeholder="Ej: Gerente General"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Imagen de Firma Digital (PNG fondo transparente, opcional)</label>
                        <input type="file" name="firma_2_archivo" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-[11px] text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 cursor-pointer">
                    </div>
                </div>

                <!-- Textos y Organización -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nombre de la Organización</label>
                        <input type="text" name="nombre_organizacion" value="C.I. Piscícola New York S.A.S."
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Texto de Certificación</label>
                        <input type="text" name="texto_certificado" placeholder="La Dirección General hace constar formalmente que:"
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="estado" id="crear-estado" value="1" checked class="rounded text-brand-blue focus:ring-brand-blue h-4 w-4">
                    <label for="crear-estado" class="text-xs text-slate-700 font-semibold cursor-pointer">
                        Establecer como plantilla activa para la emisión de nuevos certificados
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-line">
                    <button type="button" onclick="cerrarModalCrearPlantilla()"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold shadow-xs transition-all cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i>
                        <span>Guardar Plantilla</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL PARA EDITAR PLANTILLA DE CERTIFICADO
     ======================================================== -->
<div id="modal-editar-plantilla" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div id="modal-editar-backdrop" class="fixed inset-0 bg-brand-dark/70 backdrop-blur-sm transition-opacity opacity-0" onclick="cerrarModalEditarPlantilla()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 flex items-center justify-center min-h-full">
        <div id="modal-editar-panel" class="relative bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-line/80 overflow-hidden transform transition-all scale-95 opacity-0">
            
            <div class="bg-gradient-to-r from-brand-dark via-brand-deep to-brand-blue text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <div>
                        <h4 class="font-heading font-extrabold text-sm sm:text-base text-white">Editar Plantilla de Certificado</h4>
                        <p class="text-[11px] text-slate-200">Actualiza los datos institucionales, firmas o reemplaza la imagen base.</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalEditarPlantilla()" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-editar-plantilla" method="POST" action="" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-brand-dark mb-1">Nombre de la Plantilla <span class="text-rose-500">*</span></label>
                        <input type="text" id="edit-nombre" name="nombre" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>

                    <div>
                        <label class="block font-bold text-brand-dark mb-1">Área Asignada</label>
                        <select id="edit-area-id" name="area_id" class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                            <option value="">General (Todas las Áreas de la Empresa)</option>
                            @foreach($areas as $ar)
                                <option value="{{ $ar->id }}">{{ $ar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-brand-dark mb-1">Descripción / Notas internas</label>
                    <input type="text" id="edit-descripcion" name="descripcion"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                </div>

                <!-- Reemplazar Archivo de la Plantilla -->
                <div class="bg-brand-light/50 p-4 rounded-2xl border border-brand-blue/20 space-y-1.5">
                    <label class="block font-bold text-brand-dark">
                        <i class="fa-solid fa-image text-brand-blue mr-1"></i> Reemplazar Archivo de la Plantilla (Opcional)
                    </label>
                    <p class="text-[11px] text-muted">Deja en blanco si deseas conservar la imagen actual. Para cambiarla, sube un nuevo archivo PNG, JPG o WebP.</p>
                    <input type="file" name="archivo_plantilla" accept="image/png,image/jpeg,image/webp"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white hover:file:bg-brand-deep cursor-pointer">
                </div>

                <!-- SECCIÓN: JEFE INMEDIATO -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-line space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-brand-blue flex items-center justify-center text-xs font-bold">1</span>
                        <h5 class="font-heading font-extrabold text-xs text-brand-dark uppercase tracking-wide">Firma del Jefe Inmediato</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" id="edit-jefe-nombre" name="firma_1_nombre"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cargo Oficial</label>
                            <input type="text" id="edit-jefe-cargo" name="firma_1_cargo"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reemplazar Imagen de Firma (Opcional)</label>
                        <input type="file" name="firma_1_archivo" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-[11px] text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 cursor-pointer">
                    </div>
                </div>

                <!-- SECCIÓN: GERENTE -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-line space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-cyan-100 text-cyan-800 flex items-center justify-center text-xs font-bold">2</span>
                        <h5 class="font-heading font-extrabold text-xs text-brand-dark uppercase tracking-wide">Firma del Gerente</h5>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" id="edit-gerente-nombre" name="firma_2_nombre"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Cargo Oficial</label>
                            <input type="text" id="edit-gerente-cargo" name="firma_2_cargo"
                                   class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Reemplazar Imagen de Firma (Opcional)</label>
                        <input type="file" name="firma_2_archivo" accept="image/png,image/jpeg,image/webp"
                               class="w-full text-[11px] text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 cursor-pointer">
                    </div>
                </div>

                <!-- Textos y Organización -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nombre de la Organización</label>
                        <input type="text" id="edit-organizacion" name="nombre_organizacion"
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Texto de Certificación</label>
                        <input type="text" id="edit-texto" name="texto_certificado"
                               class="w-full px-3 py-2 rounded-xl border border-line bg-white text-xs text-brand-dark focus:outline-none focus:border-brand-blue">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="estado" id="edit-estado" value="1" class="rounded text-brand-blue focus:ring-brand-blue h-4 w-4">
                    <label for="edit-estado" class="text-xs text-slate-700 font-semibold cursor-pointer">
                        Plantilla activa para la emisión de nuevos certificados
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-line">
                    <button type="button" onclick="cerrarModalEditarPlantilla()"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-blue to-brand-deep hover:brightness-110 text-white font-heading font-bold shadow-xs transition-all cursor-pointer">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i>
                        <span>Actualizar Plantilla</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cambiarPestana(tipo) {
    const pnlInternos = document.getElementById('panel-internos');
    const pnlExternos = document.getElementById('panel-externos');
    const pnlPlantillas = document.getElementById('panel-plantillas');
    const btnInternos = document.getElementById('tab-btn-internos');
    const btnExternos = document.getElementById('tab-btn-externos');
    const btnPlantillas = document.getElementById('tab-btn-plantillas');

    const claseActivo = "px-4 py-2.5 rounded-2xl font-heading font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer bg-brand-blue text-white shadow-xs";
    const claseInactivo = "px-4 py-2.5 rounded-2xl font-heading font-extrabold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200";

    pnlInternos.classList.add('hidden');
    pnlExternos.classList.add('hidden');
    if (pnlPlantillas) pnlPlantillas.classList.add('hidden');

    btnInternos.className = claseInactivo;
    btnExternos.className = claseInactivo;
    if (btnPlantillas) btnPlantillas.className = claseInactivo;

    if (tipo === 'internos') {
        pnlInternos.classList.remove('hidden');
        btnInternos.className = claseActivo;
    } else if (tipo === 'externos') {
        pnlExternos.classList.remove('hidden');
        btnExternos.className = claseActivo;
    } else if (tipo === 'plantillas') {
        if (pnlPlantillas) pnlPlantillas.classList.remove('hidden');
        if (btnPlantillas) btnPlantillas.className = claseActivo;
    }
}

function abrirModalCrearPlantilla() {
    const modal = document.getElementById('modal-crear-plantilla');
    const backdrop = document.getElementById('modal-crear-backdrop');
    const panel = document.getElementById('modal-crear-panel');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    });
    document.body.classList.add('overflow-hidden');
}

function cerrarModalCrearPlantilla() {
    const modal = document.getElementById('modal-crear-plantilla');
    const backdrop = document.getElementById('modal-crear-backdrop');
    const panel = document.getElementById('modal-crear-panel');

    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

function abrirModalEditarPlantilla(datos) {
    const modal = document.getElementById('modal-editar-plantilla');
    const backdrop = document.getElementById('modal-editar-backdrop');
    const panel = document.getElementById('modal-editar-panel');
    const form = document.getElementById('form-editar-plantilla');

    form.action = datos.action || `/admin/certificados/plantillas/${datos.id}`;
    document.getElementById('edit-nombre').value = datos.nombre || '';
    document.getElementById('edit-area-id').value = datos.area_id || '';
    document.getElementById('edit-descripcion').value = datos.descripcion || '';
    document.getElementById('edit-organizacion').value = datos.organizacion || 'C.I. Piscícola New York S.A.S.';
    document.getElementById('edit-texto').value = datos.texto || '';
    document.getElementById('edit-jefe-nombre').value = datos.jefe_nombre || datos.firma_1_nombre || '';
    document.getElementById('edit-jefe-cargo').value = datos.jefe_cargo || datos.firma_1_cargo || '';
    document.getElementById('edit-gerente-nombre').value = datos.gerente_nombre || datos.firma_2_nombre || '';
    document.getElementById('edit-gerente-cargo').value = datos.gerente_cargo || datos.firma_2_cargo || '';
    document.getElementById('edit-estado').checked = String(datos.estado) === '1' || datos.estado === true;

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    });
    document.body.classList.add('overflow-hidden');
}

function cerrarModalEditarPlantilla() {
    const modal = document.getElementById('modal-editar-plantilla');
    const backdrop = document.getElementById('modal-editar-backdrop');
    const panel = document.getElementById('modal-editar-panel');

    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

document.addEventListener('DOMContentLoaded', () => {
    // Si viene en URL parametro tab=plantillas o tab=externos
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'plantillas' || tabParam === 'externos' || tabParam === 'internos') {
        cambiarPestana(tabParam);
    }

    // Modal Visor de Certificado
    const modal = document.getElementById('modal-visor-certificado');
    const backdrop = document.getElementById('modal-visor-backdrop');
    const panel = document.getElementById('modal-visor-panel');
    const btnCerrar = document.getElementById('visor-btn-cerrar');
    const btnDescargar = document.getElementById('visor-btn-descargar-pdf');

    const codigoTop = document.getElementById('visor-codigo');
    const codigoDip = document.getElementById('diploma-codigo');
    const empleadoDip = document.getElementById('diploma-empleado');
    const idDip = document.getElementById('diploma-identificacion');
    const areaDip = document.getElementById('diploma-area');
    const capDip = document.getElementById('diploma-capacitacion');
    const horasDip = document.getElementById('diploma-horas');
    const fechaRealizadaDip = document.getElementById('diploma-fecha-realizada');
    const notaDip = document.getElementById('diploma-nota');
    const fechaDip = document.getElementById('diploma-fecha');
    const jefeNombreDip = document.getElementById('diploma-jefe-nombre');
    const jefeCargoDip = document.getElementById('diploma-jefe-cargo');
    const gerenteNombreDip = document.getElementById('diploma-gerente-nombre');
    const gerenteCargoDip = document.getElementById('diploma-gerente-cargo');

    function abrirVisor(datos) {
        if (codigoTop) codigoTop.textContent = datos.codigo;
        if (codigoDip) codigoDip.textContent = datos.codigo;
        if (empleadoDip) empleadoDip.textContent = datos.empleado;
        if (idDip) idDip.textContent = datos.identificacion;
        if (areaDip) areaDip.textContent = datos.area;
        if (capDip) capDip.textContent = datos.capacitacion;
        if (horasDip) horasDip.textContent = `${datos.horas || 4} ${parseInt(datos.horas) === 1 ? 'hora' : 'horas'}`;
        if (fechaRealizadaDip) fechaRealizadaDip.textContent = datos.fecha;
        if (notaDip) notaDip.textContent = datos.nota;
        if (fechaDip) fechaDip.textContent = datos.fecha;
        if (btnDescargar) btnDescargar.href = datos.pdf;

        if (jefeNombreDip) jefeNombreDip.textContent = datos.jefeNombre || 'Ing. Carlos Mendoza';
        if (jefeCargoDip) jefeCargoDip.textContent = datos.jefeCargo || 'Jefe Inmediato';
        if (gerenteNombreDip) gerenteNombreDip.textContent = datos.gerenteNombre || 'Dra. Elena Ramos';
        if (gerenteCargoDip) gerenteCargoDip.textContent = datos.gerenteCargo || 'Gerente General';

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('scale-95', 'opacity-0');
            panel.classList.add('scale-100', 'opacity-100');
        });
        document.body.classList.add('overflow-hidden');
    }

    function cerrarVisor() {
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('scale-100', 'opacity-100');
        panel.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }

    document.querySelectorAll('.btn-ver-certificado').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirVisor({
                codigo: btn.getAttribute('data-codigo'),
                empleado: btn.getAttribute('data-empleado'),
                identificacion: btn.getAttribute('data-identificacion'),
                capacitacion: btn.getAttribute('data-capacitacion'),
                area: btn.getAttribute('data-area'),
                horas: btn.getAttribute('data-horas'),
                nota: btn.getAttribute('data-nota'),
                fecha: btn.getAttribute('data-fecha'),
                jefeNombre: btn.getAttribute('data-jefe-nombre'),
                jefeCargo: btn.getAttribute('data-jefe-cargo'),
                gerenteNombre: btn.getAttribute('data-gerente-nombre'),
                gerenteCargo: btn.getAttribute('data-gerente-cargo'),
                pdf: btn.getAttribute('data-pdf'),
            });
        });
    });

    document.querySelectorAll('.btn-editar-plantilla').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirModalEditarPlantilla({
                id: btn.getAttribute('data-id'),
                nombre: btn.getAttribute('data-nombre'),
                descripcion: btn.getAttribute('data-descripcion'),
                area_id: btn.getAttribute('data-area-id'),
                organizacion: btn.getAttribute('data-organizacion'),
                texto: btn.getAttribute('data-texto'),
                jefe_nombre: btn.getAttribute('data-jefe-nombre'),
                jefe_cargo: btn.getAttribute('data-jefe-cargo'),
                gerente_nombre: btn.getAttribute('data-gerente-nombre'),
                gerente_cargo: btn.getAttribute('data-gerente-cargo'),
                estado: btn.getAttribute('data-estado'),
                action: btn.getAttribute('data-action'),
            });
        });
    });

    btnCerrar?.addEventListener('click', cerrarVisor);
    backdrop?.addEventListener('click', cerrarVisor);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (!modal.classList.contains('hidden')) cerrarVisor();
            const modalCrear = document.getElementById('modal-crear-plantilla');
            if (modalCrear && !modalCrear.classList.contains('hidden')) cerrarModalCrearPlantilla();
            const modalEdit = document.getElementById('modal-editar-plantilla');
            if (modalEdit && !modalEdit.classList.contains('hidden')) cerrarModalEditarPlantilla();
        }
    });
});
</script>
@endsection
