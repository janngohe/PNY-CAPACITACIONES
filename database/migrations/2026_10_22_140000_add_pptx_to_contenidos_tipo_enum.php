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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contenidos MODIFY COLUMN tipo ENUM('TEXTO','VIDEO','IMAGEN','PDF','ENLACE','PPTX') NOT NULL DEFAULT 'TEXTO'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE contenidos MODIFY COLUMN tipo ENUM('TEXTO','VIDEO','IMAGEN','PDF','ENLACE') NOT NULL DEFAULT 'TEXTO'");
        }
    }
};
