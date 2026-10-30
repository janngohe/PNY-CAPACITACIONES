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
        if (Schema::hasColumn('plantillas_certificado', 'creado_por')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE plantillas_certificado MODIFY COLUMN creado_por BIGINT UNSIGNED NULL");
            }
        } else {
            Schema::table('plantillas_certificado', function (Blueprint $table) {
                $table->unsignedBigInteger('creado_por')->nullable()->after('area_id');
            });
        }

        if (!Schema::hasColumn('plantillas_certificado', 'firma_1_actualizada_el')) {
            Schema::table('plantillas_certificado', function (Blueprint $table) {
                $table->timestamp('firma_1_actualizada_el')->nullable()->after('firma_1_ruta');
            });
        }

        Schema::table('plantillas_certificado', function (Blueprint $table) {
            $table->foreign('creado_por')->references('id')->on('usuarios')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plantillas_certificado', function (Blueprint $table) {
            $table->dropForeign(['creado_por']);
            $table->dropColumn(['creado_por', 'firma_1_actualizada_el']);
        });
    }
};
