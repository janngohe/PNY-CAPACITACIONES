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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('firma_digital', 255)->nullable()->after('area_id');
            $table->string('cargo', 150)->nullable()->after('firma_digital');
            $table->timestamp('firma_actualizada_el')->nullable()->after('cargo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['firma_digital', 'cargo', 'firma_actualizada_el']);
        });
    }
};
