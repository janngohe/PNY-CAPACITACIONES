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
        Schema::create('aplicaciones_pruebas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_id')->constrained('candidatos')->onDelete('cascade');
            $table->foreignId('consentimiento_id')->constrained('consentimientos_informados')->onDelete('restrict');
            $table->string('prueba_codigo', 30);
            $table->unsignedSmallInteger('edad_evaluado')->nullable();
            $table->date('fecha_aplicacion');
            $table->dateTime('hora_inicio')->nullable();
            $table->dateTime('hora_finalizacion')->nullable();
            $table->enum('estado', ['BORRADOR', 'EN_CURSO', 'FINALIZADA', 'ANULADA'])->default('BORRADOR');
            $table->decimal('progreso_porcentaje', 5, 2)->default(0);
            $table->unsignedBigInteger('usuario_responsable_id')->nullable();
            $table->foreign('usuario_responsable_id')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('prueba_codigo')->references('codigo')->on('pruebas_psicotecnicas')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aplicaciones_pruebas');
    }
};
