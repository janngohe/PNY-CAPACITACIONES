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
        Schema::create('consentimientos_informados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_id')->constrained('candidatos')->onDelete('cascade');
            $table->string('ciudad_municipio', 150);
            $table->date('fecha_consentimiento');
            $table->string('codigo_documental', 50)->default('GH-FO-025');
            $table->string('version_documental', 10)->default('01');
            $table->enum('estado', ['GENERADO', 'DESCARGADO', 'FIRMADO_FISICO', 'FIRMADO_DIGITAL', 'REVOCADO'])->default('GENERADO');
            $table->string('ruta_archivo_docx', 255)->nullable();
            $table->string('ruta_archivo_pdf', 255)->nullable();
            $table->date('fecha_firma_fisica')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->foreign('registrado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consentimientos_informados');
    }
};
