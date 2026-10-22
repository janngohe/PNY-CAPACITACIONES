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
        Schema::table('inducciones', function (Blueprint $table) {
            if (!Schema::hasColumn('inducciones', 'imagen_portada')) {
                $table->string('imagen_portada', 255)->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('inducciones', 'recursos')) {
                $table->json('recursos')->nullable()->after('contenido_diapositivas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inducciones', function (Blueprint $table) {
            if (Schema::hasColumn('inducciones', 'recursos')) {
                $table->dropColumn('recursos');
            }
            if (Schema::hasColumn('inducciones', 'imagen_portada')) {
                $table->dropColumn('imagen_portada');
            }
        });
    }
};
