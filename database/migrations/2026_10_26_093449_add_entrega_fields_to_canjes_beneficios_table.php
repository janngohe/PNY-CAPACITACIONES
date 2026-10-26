<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('canjes_beneficios', function (Blueprint $table) {
            if (!Schema::hasColumn('canjes_beneficios', 'fecha_entrega')) {
                $table->dateTime('fecha_entrega')->nullable()->after('fecha_canje');
            }
            if (!Schema::hasColumn('canjes_beneficios', 'entregado_por')) {
                $table->string('entregado_por', 255)->nullable()->after('fecha_entrega');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canjes_beneficios', function (Blueprint $table) {
            if (Schema::hasColumn('canjes_beneficios', 'entregado_por')) {
                $table->dropColumn('entregado_por');
            }
            if (Schema::hasColumn('canjes_beneficios', 'fecha_entrega')) {
                $table->dropColumn('fecha_entrega');
            }
        });
    }
};
