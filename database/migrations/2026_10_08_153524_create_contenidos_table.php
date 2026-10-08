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
        Schema::create('contenidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('titulo', 255);
            $table->enum('tipo', ['TEXTO', 'VIDEO', 'IMAGEN', 'PDF', 'ENLACE']);
            $table->text('contenido')->nullable();
            $table->string('ruta_archivo', 255)->nullable();
            $table->unsignedInteger('orden')->default(1);
            $table->boolean('obligatorio')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenidos');
    }
};
