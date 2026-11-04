<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluaciones_candidatos', function (Blueprint $table) {
            $table->string('certificado_ciudad_fecha', 180)->nullable()->after('estado');
            $table->string('certificado_concepto', 60)->nullable()->after('certificado_ciudad_fecha');
            $table->text('certificado_observaciones')->nullable()->after('certificado_concepto');
            $table->timestamp('certificado_emitido_at')->nullable()->after('certificado_observaciones');
            $table->unsignedBigInteger('certificado_emitido_por')->nullable()->after('certificado_emitido_at');
        });
    }

    public function down(): void
    {
        Schema::table('evaluaciones_candidatos', function (Blueprint $table) {
            $table->dropColumn([
                'certificado_ciudad_fecha',
                'certificado_concepto',
                'certificado_observaciones',
                'certificado_emitido_at',
                'certificado_emitido_por',
            ]);
        });
    }
};

