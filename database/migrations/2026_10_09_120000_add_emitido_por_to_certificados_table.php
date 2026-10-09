<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * El administrador puede emitir certificados manualmente: se registra quién lo hizo.
     * Los certificados emitidos automáticamente por el sistema quedan con emitido_por = NULL.
     */
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->unsignedBigInteger('emitido_por')->nullable()->after('ruta_archivo');
            $table->foreign('emitido_por')->references('id')->on('usuarios')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropForeign(['emitido_por']);
            $table->dropColumn('emitido_por');
        });
    }
};
