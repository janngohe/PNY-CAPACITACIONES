<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `usuarios` MODIFY COLUMN `rol` ENUM('EMPLEADO', 'JEFE_AREA', 'ADMINISTRADOR', 'PSICOLOGIA') NOT NULL DEFAULT 'EMPLEADO'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `usuarios` MODIFY COLUMN `rol` ENUM('EMPLEADO', 'JEFE_AREA', 'ADMINISTRADOR') NOT NULL DEFAULT 'EMPLEADO'");
        }
    }
};
