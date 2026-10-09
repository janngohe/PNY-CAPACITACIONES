<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra qué Jefe de Área publicó la capacitación.
     * La visibilidad para empleados se controla con la tabla area_capacitacion.
     */
    public function up(): void
    {
        if (Schema::hasColumn('capacitaciones', 'creado_por')) {
            return;
        }

        Schema::table('capacitaciones', function (Blueprint $table) {
            $table->integer('creado_por')->nullable()->after('plantilla_certificado_id');
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('capacitaciones', 'creado_por')) {
            return;
        }

        Schema::table('capacitaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creado_por');
        });
    }
};
