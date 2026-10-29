<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('EMPLEADO','JEFE_AREA','ADMINISTRADOR','PSICOLOGIA') NOT NULL DEFAULT 'EMPLEADO'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('EMPLEADO','JEFE_AREA','ADMINISTRADOR') NOT NULL DEFAULT 'EMPLEADO'");
    }
};

