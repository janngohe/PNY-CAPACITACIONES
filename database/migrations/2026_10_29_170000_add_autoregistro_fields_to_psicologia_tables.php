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
        Schema::table('candidatos', function (Blueprint $table) {
            $table->string('token_acceso', 64)->nullable()->index()->after('cargo_postulado');
            $table->string('origen_registro', 30)->default('MANUAL_PSICOLOGA')->after('token_acceso');
        });

        Schema::table('consentimientos_informados', function (Blueprint $table) {
            $table->string('tipo_firma', 20)->default('FISICA')->after('estado');
            $table->string('firma_digital_ruta', 255)->nullable()->after('tipo_firma');
            $table->string('ip_firma', 45)->nullable()->after('firma_digital_ruta');
            $table->dateTime('fecha_firma_digital')->nullable()->after('ip_firma');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE consentimientos_informados MODIFY COLUMN estado ENUM('GENERADO','DESCARGADO','FIRMADO_FISICO','FIRMADO_DIGITAL','REVOCADO') NOT NULL DEFAULT 'GENERADO'");
        }

        Schema::table('aplicaciones_pruebas', function (Blueprint $table) {
            $table->string('token_sesion', 64)->nullable()->index()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aplicaciones_pruebas', function (Blueprint $table) {
            $table->dropColumn('token_sesion');
        });

        Schema::table('consentimientos_informados', function (Blueprint $table) {
            $table->dropColumn(['tipo_firma', 'firma_digital_ruta', 'ip_firma', 'fecha_firma_digital']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE consentimientos_informados MODIFY COLUMN estado ENUM('GENERADO','DESCARGADO','FIRMADO_FISICO','REVOCADO') NOT NULL DEFAULT 'GENERADO'");
        }

        Schema::table('candidatos', function (Blueprint $table) {
            $table->dropColumn(['token_acceso', 'origen_registro']);
        });
    }
};
