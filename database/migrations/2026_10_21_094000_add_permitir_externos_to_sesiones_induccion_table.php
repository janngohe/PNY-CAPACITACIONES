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
        if (Schema::hasTable('sesiones_induccion') && !Schema::hasColumn('sesiones_induccion', 'permitir_externos')) {
            Schema::table('sesiones_induccion', function (Blueprint $table) {
                $table->boolean('permitir_externos')->default(true)->after('areas_convocadas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sesiones_induccion') && Schema::hasColumn('sesiones_induccion', 'permitir_externos')) {
            Schema::table('sesiones_induccion', function (Blueprint $table) {
                $table->dropColumn('permitir_externos');
            });
        }
    }
};
