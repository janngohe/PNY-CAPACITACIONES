<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Crear tabla de sedes
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('ciudad', 100)->default('Rivera - Huila');
            $table->string('direccion', 255)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // 2. Agregar sede_id a tabla de areas
        Schema::table('areas', function (Blueprint $table) {
            $table->foreignId('sede_id')->nullable()->after('id')->constrained('sedes')->nullOnDelete();
        });

        // 3. Agregar sede_id a tabla de usuarios
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('sede_id')->nullable()->after('area_id')->constrained('sedes')->nullOnDelete();
        });

        // 4. Sembrar sedes iniciales oficiales de C.I. Piscícola New York
        $ahora = now();
        $sedes = [
            [
                'nombre' => 'Sede Principal - Rivera',
                'ciudad' => 'Rivera - Huila',
                'direccion' => 'Km 4 Vía Rivera',
                'descripcion' => 'Sede administrativa central y centro de operaciones principales.',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Sede Represa de Betania',
                'ciudad' => 'Yaguará - Huila',
                'direccion' => 'Embalse de Betania',
                'descripcion' => 'Zona de producción acuícola, jaulas flotantes y centros de cultivo.',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Sede Planta de Procesamiento',
                'ciudad' => 'Rivera - Huila',
                'direccion' => 'Parque Industrial / Vía Neiva',
                'descripcion' => 'Planta de eviscerado, fileteado, empaque y despacho de exportación.',
                'estado' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        DB::table('sedes')->insert($sedes);

        // Obtener el ID de la Sede Principal
        $sedePrincipalId = DB::table('sedes')->where('nombre', 'Sede Principal - Rivera')->value('id');

        if ($sedePrincipalId) {
            // Asignar todas las áreas existentes a la Sede Principal por defecto
            DB::table('areas')->whereNull('sede_id')->update(['sede_id' => $sedePrincipalId]);

            // Asignar todos los usuarios a la Sede Principal por defecto
            DB::table('usuarios')->whereNull('sede_id')->update(['sede_id' => $sedePrincipalId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id');
        });

        Schema::dropIfExists('sedes');
    }
};
