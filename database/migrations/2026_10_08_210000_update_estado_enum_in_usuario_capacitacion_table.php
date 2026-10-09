<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE usuario_capacitacion MODIFY COLUMN estado ENUM('PENDIENTE', 'EN_PROGRESO', 'MODULOS_COMPLETOS', 'COMPLETADA', 'NO_APROBADA') NOT NULL DEFAULT 'PENDIENTE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE usuario_capacitacion MODIFY COLUMN estado ENUM('PENDIENTE', 'EN_PROGRESO', 'COMPLETADA', 'NO_APROBADA') NOT NULL DEFAULT 'PENDIENTE'");
    }
};
