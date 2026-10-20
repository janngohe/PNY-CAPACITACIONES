<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropUnique('areas_nombre_unique');
            $table->unique(['sede_id', 'nombre'], 'areas_sede_id_nombre_unique');
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropUnique('areas_sede_id_nombre_unique');
            $table->unique('nombre', 'areas_nombre_unique');
        });
    }
};
