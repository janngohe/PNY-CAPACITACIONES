<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('valanti_respuestas')) {
            Schema::create('valanti_respuestas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_candidato_id')
                    ->constrained('evaluaciones_candidatos')
                    ->cascadeOnDelete();
                $table->unsignedTinyInteger('numero_pregunta'); // 1 a 30
                $table->unsignedTinyInteger('puntos_a'); // 0, 1, 2 o 3
                $table->unsignedTinyInteger('puntos_b'); // 0, 1, 2 o 3
                $table->timestamps();

                $table->unique(['evaluacion_candidato_id', 'numero_pregunta'], 'valanti_resp_eval_preg_unique');
            });
        }

        if (!Schema::hasTable('valanti_resultados')) {
            Schema::create('valanti_resultados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_candidato_id')
                    ->unique()
                    ->constrained('evaluaciones_candidatos')
                    ->cascadeOnDelete();

                // Puntajes Directos (PD) - Suma total = 90
                $table->unsignedSmallInteger('pd_verdad')->default(0);
                $table->unsignedSmallInteger('pd_rectitud')->default(0);
                $table->unsignedSmallInteger('pd_paz')->default(0);
                $table->unsignedSmallInteger('pd_amor')->default(0);
                $table->unsignedSmallInteger('pd_no_violencia')->default(0);
                $table->unsignedSmallInteger('pd_total')->default(90);

                // Puntajes Estándar (PE) - Baremos 1997 (n=730)
                $table->integer('pe_verdad')->default(50);
                $table->integer('pe_rectitud')->default(50);
                $table->integer('pe_paz')->default(50);
                $table->integer('pe_amor')->default(50);
                $table->integer('pe_no_violencia')->default(50);

                // Interpretaciones Cualitativas
                $table->string('nivel_verdad', 40);
                $table->string('nivel_rectitud', 40);
                $table->string('nivel_paz', 40);
                $table->string('nivel_amor', 40);
                $table->string('nivel_no_violencia', 40);

                // Perfil Esperado por la Empresa (por defecto 50 en cada dimensión)
                $table->integer('perfil_empresa_verdad')->default(50);
                $table->integer('perfil_empresa_rectitud')->default(50);
                $table->integer('perfil_empresa_paz')->default(50);
                $table->integer('perfil_empresa_amor')->default(50);
                $table->integer('perfil_empresa_no_violencia')->default(50);

                // Distancia con la Organización (PE - Perfil_Esperado_Empresa)
                $table->integer('distancia_verdad')->default(0);
                $table->integer('distancia_rectitud')->default(0);
                $table->integer('distancia_paz')->default(0);
                $table->integer('distancia_amor')->default(0);
                $table->integer('distancia_no_violencia')->default(0);

                $table->timestamp('finalizada_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('valanti_resultados');
        Schema::dropIfExists('valanti_respuestas');
    }
};

