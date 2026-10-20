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
        // 1. Agregar columna puntos a capacitaciones (conservando incentivo para compatibilidad)
        if (!Schema::hasColumn('capacitaciones', 'puntos')) {
            Schema::table('capacitaciones', function (Blueprint $table) {
                $table->unsignedInteger('puntos')->default(20)->after('duracion_estimada');
            });
        }

        // 2. Agregar saldo de puntos al usuario
        if (!Schema::hasColumn('usuarios', 'puntos_saldo')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->integer('puntos_saldo')->default(0)->after('rol');
            });
        }

        // 3. Tabla del Catálogo de Beneficios
        if (!Schema::hasTable('beneficios')) {
            Schema::create('beneficios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 255);
                $table->text('descripcion');
                $table->unsignedInteger('puntos_requeridos');
                $table->boolean('estado')->default(true);
                $table->string('icono', 100)->nullable()->default('fa-gift');
                $table->timestamps();
            });
        }

        // 4. Tabla de Movimientos / Transacciones de Puntos
        if (!Schema::hasTable('movimientos_puntos')) {
            Schema::create('movimientos_puntos', function (Blueprint $table) {
                $table->id();
                $table->integer('usuario_id');
                $table->integer('capacitacion_id')->nullable();
                $table->foreignId('beneficio_id')->nullable()->constrained('beneficios')->nullOnDelete();
                $table->string('tipo', 30); // ACREDITACION, CANJE, AJUSTE
                $table->integer('puntos'); // positivo para suma, negativo para resta
                $table->integer('saldo_anterior')->default(0);
                $table->integer('saldo_nuevo')->default(0);
                $table->string('motivo', 255);
                $table->timestamps();

                $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
                $table->foreign('capacitacion_id')->references('id')->on('capacitaciones')->nullOnDelete();
                $table->index(['usuario_id', 'tipo']);
                $table->index(['usuario_id', 'capacitacion_id']);
            });
        }

        // 5. Tabla de Canjes de Beneficios
        if (!Schema::hasTable('canjes_beneficios')) {
            Schema::create('canjes_beneficios', function (Blueprint $table) {
                $table->id();
                $table->integer('usuario_id');
                $table->foreignId('beneficio_id')->constrained('beneficios')->cascadeOnDelete();
                $table->unsignedInteger('puntos_utilizados');
                $table->string('estado', 30)->default('CANJEADO'); // CANJEADO, ENTREGADO, CANCELADO
                $table->string('codigo_canje', 30)->unique();
                $table->dateTime('fecha_canje');
                $table->text('notas')->nullable();
                $table->timestamps();

                $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
                $table->index(['usuario_id', 'estado']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canjes_beneficios');
        Schema::dropIfExists('movimientos_puntos');
        Schema::dropIfExists('beneficios');

        if (Schema::hasColumn('usuarios', 'puntos_saldo')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('puntos_saldo');
            });
        }

        if (Schema::hasColumn('capacitaciones', 'puntos')) {
            Schema::table('capacitaciones', function (Blueprint $table) {
                $table->dropColumn('puntos');
            });
        }
    }
};
