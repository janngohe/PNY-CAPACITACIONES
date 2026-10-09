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

    <!-- BUSCADOR DE CERTIFICADOS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <span class="text-xs font-semibold text-slate-700">
            Total de certificados emitidos: <strong class="text-brand-dark">{{ $totalEmitidos }}</strong>
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
                                    <a href="{{ route('empleado.certificados.ver', $cert) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-brand-light hover:text-brand-blue text-slate-700 font-heading font-bold text-[11px] transition-colors"
                                       title="Ver certificado oficial">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </a>
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
                                No se encontraron certificados con los criterios de búsqueda.
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
@endsection
