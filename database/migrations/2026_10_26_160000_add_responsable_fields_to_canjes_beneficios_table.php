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
            if (!Schema::hasColumn('canjes_beneficios', 'responsable_usuario_id')) {
                $table->integer('responsable_usuario_id')->nullable()->after('entregado_por');
            }
            if (!Schema::hasColumn('canjes_beneficios', 'responsable_cc')) {
                $table->string('responsable_cc', 30)->nullable()->after('responsable_usuario_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canjes_beneficios', function (Blueprint $table) {
            if (Schema::hasColumn('canjes_beneficios', 'responsable_cc')) {
                $table->dropColumn('responsable_cc');
            }
            if (Schema::hasColumn('canjes_beneficios', 'responsable_usuario_id')) {
                $table->dropColumn('responsable_usuario_id');
            }
        });
    }
};
