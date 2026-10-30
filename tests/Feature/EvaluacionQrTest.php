<?php

namespace Tests\Feature;

use App\Constants\PacaPreguntas;
use App\Models\AplicacionPrueba;
use App\Models\Candidato;
use App\Models\ConsentimientoInformado;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EvaluacionQrTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $psicologa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->psicologa = Usuario::create([
            'identificacion' => '1075250001',
            'nombre_completo' => 'Psicologa Test',
            'password' => Hash::make('1075250001'),
            'rol' => 'PSICOLOGIA',
            'cargo' => 'Psicóloga',
            'usuario_nuevo' => false,
            'estado' => true,
        ]);
    }

    /**
     * 1. La psicóloga puede acceder a la pantalla del código QR para evaluados.
     */
    public function test_psicologa_accede_a_pantalla_qr(): void
    {
        $response = $this->actingAs($this->psicologa)->get(route('psicologia.qr.index'));
        $response->assertStatus(200);
        $response->assertSee('Auto-registro de Evaluados');
        $response->assertSee('contenedorQrVisual');
    }

    /**
     * 2. Un aspirante escanea el QR, ingresa sus datos y se genera el consentimiento GH-FO-025.
     */
    public function test_aspirante_se_registra_via_qr(): void
    {
        $cedula = '88' . rand(100000, 999999);

        $response = $this->post(route('evaluacion.registrar'), [
            'nombre_completo' => 'JUAN PÉREZ ASPIRANTE',
            'numero_identificacion' => $cedula,
            'lugar_expedicion' => 'Neiva (Huila)',
            'ciudad_municipio' => 'Betania - Palermo',
            'telefono' => '3123456789',
            'email' => 'juan.perez@aspirante.com',
            'cargo_postulado' => 'Operario de Piscicultura',
        ]);

        $candidato = Candidato::where('numero_identificacion', $cedula)->first();
        $this->assertNotNull($candidato);
        $this->assertEquals('QR_AUTOREGISTRO', $candidato->origen_registro);
        $this->assertNotEmpty($candidato->token_acceso);

        // Consentimiento creado automáticamente
        $consentimiento = $candidato->ultimoConsentimiento();
        $this->assertNotNull($consentimiento);
        $this->assertEquals('GH-FO-025', $consentimiento->codigo_documental);
        $this->assertEquals('GENERADO', $consentimiento->estado);

        $response->assertRedirect(route('evaluacion.consentimiento', ['token' => $candidato->token_acceso]));
    }

    /**
     * 3. El aspirante no puede acceder a las pruebas sin firmar el consentimiento.
     */
    public function test_aspirante_bloqueado_sin_firma_digital(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'ASPIRANTE SIN FIRMA',
            'numero_identificacion' => '77' . rand(100000, 999999),
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
            'token_acceso' => bin2hex(random_bytes(24)),
            'origen_registro' => 'QR_AUTOREGISTRO',
        ]);

        $response = $this->get(route('evaluacion.pruebas', ['token' => $candidato->token_acceso]));
        $response->assertRedirect(route('evaluacion.consentimiento', ['token' => $candidato->token_acceso]));
    }

    /**
     * 4. El aspirante firma digitalmente el consentimiento y se desbloquea PAC-A.
     */
    public function test_aspirante_firma_consentimiento_digitalmente(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'ASPIRANTE FIRMA DIGITAL',
            'numero_identificacion' => '66' . rand(100000, 999999),
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
            'token_acceso' => bin2hex(random_bytes(24)),
            'origen_registro' => 'QR_AUTOREGISTRO',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => $candidato->ciudad_municipio,
            'fecha_consentimiento' => now()->toDateString(),
            'codigo_documental' => 'GH-FO-025',
            'version_documental' => '01',
            'estado' => 'GENERADO',
        ]);

        // Simular dibujo canvas PNG en base64
        $imagenFalsaPng = 'data:image/png;base64,' . base64_encode(str_repeat('x', 300));

        $response = $this->post(route('evaluacion.consentimiento.firmar'), [
            'token' => $candidato->token_acceso,
            'firma_canvas' => $imagenFalsaPng,
            'acepto_terminos' => '1',
        ]);

        $consentimiento->refresh();
        $this->assertEquals('FIRMADO_DIGITAL', $consentimiento->estado);
        $this->assertEquals('DIGITAL', $consentimiento->tipo_firma);
        $this->assertNotNull($consentimiento->firma_digital_ruta);
        $this->assertTrue($consentimiento->estaFirmado());

        $response->assertRedirect(route('evaluacion.pruebas', ['token' => $candidato->token_acceso]));
    }

    /**
     * 5. Flujo completo: aspirante inicia PAC-A, guarda borrador y finaliza con calificación automática.
     */
    public function test_flujo_completo_paca_aspirante(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'CARLOS ASPIRANTE COMPLETO',
            'numero_identificacion' => '55' . rand(100000, 999999),
            'lugar_expedicion' => 'Campoalegre',
            'ciudad_municipio' => 'Campoalegre',
            'token_acceso' => bin2hex(random_bytes(24)),
            'origen_registro' => 'QR_AUTOREGISTRO',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => $candidato->ciudad_municipio,
            'fecha_consentimiento' => now()->toDateString(),
            'codigo_documental' => 'GH-FO-025',
            'version_documental' => '01',
            'estado' => 'FIRMADO_DIGITAL',
            'tipo_firma' => 'DIGITAL',
        ]);

        // 5.1 Iniciar PAC-A
        $responseIniciar = $this->post(route('evaluacion.paca.iniciar'), [
            'token' => $candidato->token_acceso,
        ]);

        $aplicacion = $candidato->aplicaciones()->where('prueba_codigo', 'PAC-A')->first();
        $this->assertNotNull($aplicacion);
        $this->assertEquals('EN_CURSO', $aplicacion->estado);
        $this->assertNotEmpty($aplicacion->token_sesion);

        // 5.1.1 Cargar la vista HTML de la prueba PAC-A (verificar que no haya errores de renderizado de Blade)
        $responseVista = $this->get(route('evaluacion.paca.aplicar', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]));
        $responseVista->assertStatus(200);
        $responseVista->assertSee('Cuestionario PAC-A');
        $responseVista->assertSee('Siento que no me alcanza el tiempo');
        $responseVista->assertSee('Siempre');
        $responseVista->assertSee('Frecuentemente');

        // 5.2 Guardar borrador parcial (preguntas 1 a 10 con 'S')
        $borrador = [];
        for ($i = 1; $i <= 10; $i++) {
            $borrador[$i] = 'S';
        }

        $responseBorrador = $this->postJson(route('evaluacion.paca.borrador', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]), [
            'respuestas' => $borrador
        ]);

        $responseBorrador->assertStatus(200);
        $responseBorrador->assertJson(['ok' => true, 'respondidas' => 10]);

        // 5.3 Intentar finalizar sin completar las 40 (debe bloquear)
        $responseIncompleta = $this->post(route('evaluacion.paca.finalizar', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]), [
            'respuestas' => $borrador
        ]);

        $responseIncompleta->assertSessionHas('error_validacion');
        $aplicacion->refresh();
        $this->assertFalse($aplicacion->estaFinalizada());

        // 5.4 Completar las 40 preguntas (todas con 'F' = 1 pt)
        $respuestasCompletas = [];
        for ($i = 1; $i <= 40; $i++) {
            $respuestasCompletas[$i] = 'F';
        }

        $responseFinal = $this->post(route('evaluacion.paca.finalizar', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]), [
            'respuestas' => $respuestasCompletas
        ]);

        $aplicacion->refresh();
        $this->assertTrue($aplicacion->estaFinalizada());
        $this->assertEquals(100, $aplicacion->progreso_porcentaje);

        $resultado = $aplicacion->resultadoPaca;
        $this->assertNotNull($resultado);

        // En Frecuentemente (1 pt por ítem):
        // Tendencia accidentógena tiene 12 ítems * 1 pt = 12 pts
        // Patrón Tipo A tiene 20 ítems * 1 pt = 20 pts
        $this->assertEquals(12.0, (float)$resultado->puntaje_tendencia_accidentogena);
        $this->assertEquals(20.0, (float)$resultado->puntaje_patron_tipo_a);

        $responseFinal->assertRedirect(route('evaluacion.paca.completada', [
            'token' => $candidato->token_acceso,
            'appToken' => $aplicacion->token_sesion,
        ]));

        // 5.5 La psicóloga puede consultar este resultado desde su módulo
        $responsePsicologa = $this->actingAs($this->psicologa)->get(route('psicologia.paca.resultado', $aplicacion));
        $responsePsicologa->assertStatus(200);
        $responsePsicologa->assertSee('12');
        $responsePsicologa->assertSee('20');
    }
}
