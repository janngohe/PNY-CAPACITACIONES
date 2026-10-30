@extends('layouts.evaluacion')

@section('title', 'Ingreso de Datos Personales')
@section('paso_activo', '1')

@section('content')
<div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-8 shadow-xs">
    <div class="text-center max-w-md mx-auto mb-6">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-50 text-brand-blue mb-3 border border-blue-100">
            <i class="fa-solid fa-id-card text-xl"></i>
        </div>
        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 font-serif">
            Bienvenido al Proceso de Selección
        </h2>
        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            Ingresa tus datos personales a continuación. Esta información se utilizará para generar tu <strong>Consentimiento Informado (GH-FO-025)</strong> e iniciar la prueba psicotécnica <strong>PAC-A</strong>.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <div class="font-bold flex items-center gap-1.5 mb-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                <span>Por favor corrige los siguientes campos:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('evaluacion.registrar') }}" method="POST" class="space-y-4 sm:space-y-5">
        @csrf

        <!-- Nombre Completo -->
        <div>
            <label for="nombre_completo" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Nombre Completo <span class="text-rose-600">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-user text-xs"></i>
                </span>
                <input type="text"
                       name="nombre_completo"
                       id="nombre_completo"
                       required
                       value="{{ old('nombre_completo', $candidato?->nombre_completo) }}"
                       placeholder="Ej. MARÍA FERNANDA RAMÍREZ"
                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 uppercase transition-all">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Cédula de Ciudadanía -->
            <div>
                <label for="numero_identificacion" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Cédula de Ciudadanía <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-address-card text-xs"></i>
                    </span>
                    <input type="text"
                           name="numero_identificacion"
                           id="numero_identificacion"
                           required
                           value="{{ old('numero_identificacion', $candidato?->numero_identificacion) }}"
                           placeholder="Ej. 1075250000"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Si ya te habías registrado, recuperaremos tu proceso.</p>
            </div>

            <!-- Lugar de Expedición -->
            <div>
                <label for="lugar_expedicion" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Lugar de Expedición <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </span>
                    <input type="text"
                           name="lugar_expedicion"
                           id="lugar_expedicion"
                           required
                           value="{{ old('lugar_expedicion', $candidato?->lugar_expedicion) }}"
                           placeholder="Ej. Neiva (Huila)"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Ciudad / Municipio de Residencia -->
            <div>
                <label for="ciudad_municipio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Ciudad o Municipio Actual <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-city text-xs"></i>
                    </span>
                    <input type="text"
                           name="ciudad_municipio"
                           id="ciudad_municipio"
                           required
                           value="{{ old('ciudad_municipio', $candidato?->ciudad_municipio) }}"
                           placeholder="Ej. Betania - Palermo"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
            </div>

            <!-- Cargo al que postula -->
            <div>
                <label for="cargo_postulado" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Cargo al que te Postulas
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-briefcase text-xs"></i>
                    </span>
                    <input type="text"
                           name="cargo_postulado"
                           id="cargo_postulado"
                           value="{{ old('cargo_postulado', $candidato?->cargo_postulado) }}"
                           placeholder="Ej. Operario de Cultivo / Piscicultura"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Teléfono / Celular -->
            <div>
                <label for="telefono" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Teléfono / Celular
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-phone text-xs"></i>
                    </span>
                    <input type="tel"
                           name="telefono"
                           id="telefono"
                           value="{{ old('telefono', $candidato?->telefono) }}"
                           placeholder="Ej. 310 123 4567"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </span>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email', $candidato?->email) }}"
                           placeholder="Ej. aspirante@gmail.com"
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 transition-all">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <button type="submit"
                    class="w-full py-3.5 px-6 rounded-2xl bg-brand-blue hover:bg-brand-dark text-white font-bold text-xs sm:text-sm uppercase tracking-wider transition-all shadow-md active:scale-98 flex items-center justify-center gap-2 cursor-pointer">
                <span>Continuar a Consentimiento Informado</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </form>
</div>
@endsection
