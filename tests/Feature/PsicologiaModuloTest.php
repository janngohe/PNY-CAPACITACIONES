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

class PsicologiaModuloTest extends TestCase
{
    use RefreshDatabase;

    protected Usuario $psicologa;
    protected Usuario $empleado;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\PsicologiaSeeder::class);

        // Usuario con rol PSICOLOGIA
        $this->psicologa = Usuario::updateOrCreate(
            ['identificacion' => 'TEST_PSICOLOGA_01'],
            [
                'nombre_completo' => 'Psicóloga de Pruebas',
                'password' => Hash::make('password123'),
                'rol' => 'PSICOLOGIA',
                'estado' => true,
                'usuario_nuevo' => false,
            ]
        );

        // Usuario con rol EMPLEADO
        $this->empleado = Usuario::updateOrCreate(
            ['identificacion' => 'TEST_EMPLEADO_01'],
            [
                'nombre_completo' => 'Empleado Sin Permisos',
                'password' => Hash::make('password123'),
                'rol' => 'EMPLEADO',
                'estado' => true,
                'usuario_nuevo' => false,
            ]
        );
    }

    /**
     * 1. Usuario con rol PSICOLOGIA puede acceder a /psicologia.
     */
    public function test_usuario_psicologia_accede_al_panel(): void
    {
        $response = $this->actingAs($this->psicologa)->get(route('psicologia.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Evaluación Psicotécnica Institucional');
    }

    /**
     * 2. Usuario no autorizado (rol EMPLEADO) no puede acceder a /psicologia.
     */
    public function test_usuario_no_autorizado_es_bloqueado(): void
    {
        $response = $this->actingAs($this->empleado)->get(route('psicologia.dashboard'));
        $response->assertRedirect(route('empleado.capacitaciones'));
        $response->assertSessionHas('error_acceso');
    }

    /**
     * 3. Acceso al historial de pruebas.
     */
    public function test_usuario_psicologia_accede_al_historial(): void
    {
        $response = $this->actingAs($this->psicologa)->get(route('psicologia.pruebas.historial'));
        $response->assertStatus(200);
        $response->assertSee('Historial de Aplicaciones');
    }

    /**
     * 4. Guardado de borrador parcial y continuidad sin pérdida de respuestas.
     */
    public function test_guardado_de_borrador_paca(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'ASPIRANTE BORRADOR',
            'numero_identificacion' => 'TEST_CC_BORRADOR_' . rand(1000, 9999),
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => 'Neiva',
            'fecha_consentimiento' => now()->toDateString(),
            'estado' => 'FIRMADO_DIGITAL',
        ]);

        $aplicacion = AplicacionPrueba::create([
            'candidato_id' => $candidato->id,
            'consentimiento_id' => $consentimiento->id,
            'prueba_codigo' => 'PAC-A',
            'edad_evaluado' => 25,
            'fecha_aplicacion' => now()->toDateString(),
            'estado' => 'EN_CURSO',
            'usuario_responsable_id' => $this->psicologa->id,
        ]);

        // Guardar 10 respuestas como borrador vía AJAX
        $respuestasParciales = [];
        for ($i = 1; $i <= 10; $i++) {
            $respuestasParciales[$i] = 'S';
        }

        $response = $this->actingAs($this->psicologa)->postJson(route('psicologia.paca.borrador', $aplicacion), [
            'respuestas' => $respuestasParciales,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'conteo' => 10, 'progreso' => 25.0]);

        $this->assertEquals(10, $aplicacion->respuestasPaca()->count());
    }

    /**
     * 5. Validación de 40 respuestas completas al finalizar y cálculo de resultados.
     */
    public function test_finalizacion_valida_y_califica_paca(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'ASPIRANTE FINALIZACION',
            'numero_identificacion' => 'TEST_CC_FIN_' . rand(1000, 9999),
            'lugar_expedicion' => 'Aipe',
            'ciudad_municipio' => 'Aipe',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => 'Aipe',
            'fecha_consentimiento' => now()->toDateString(),
            'estado' => 'FIRMADO_DIGITAL',
        ]);

        $aplicacion = AplicacionPrueba::create([
            'candidato_id' => $candidato->id,
            'consentimiento_id' => $consentimiento->id,
            'prueba_codigo' => 'PAC-A',
            'edad_evaluado' => 32,
            'fecha_aplicacion' => now()->toDateString(),
            'estado' => 'EN_CURSO',
            'usuario_responsable_id' => $this->psicologa->id,
        ]);

        // Intento de finalizar con respuestas incompletas (solo 38)
        $respuestasIncompletas = array_fill(1, 38, 'S');
        $responseIncompleta = $this->actingAs($this->psicologa)->post(route('psicologia.paca.finalizar', $aplicacion), [
            'respuestas' => $respuestasIncompletas,
        ]);
        $responseIncompleta->assertSessionHas('error');
        $this->assertFalse($aplicacion->fresh()->estaFinalizada());

        // Ahora enviar las 40 respuestas completas (todas Siempre => 24 y 40 pts)
        $respuestasCompletas = array_fill(1, 40, 'S');
        $responseFinal = $this->actingAs($this->psicologa)->post(route('psicologia.paca.finalizar', $aplicacion), [
            'respuestas' => $respuestasCompletas,
        ]);

        $responseFinal->assertRedirect(route('psicologia.paca.resultado', $aplicacion));

        $aplicacion->refresh();
        $this->assertTrue($aplicacion->estaFinalizada());
        $this->assertEquals(100.0, $aplicacion->progreso_porcentaje);

        // Verificar resultados calculados
        $resultado = $aplicacion->resultadoPaca;
        $this->assertNotNull($resultado);
        $this->assertEquals(24.0, $resultado->puntaje_tendencia_accidentogena);
        $this->assertEquals(40.0, $resultado->puntaje_patron_tipo_a);
        $this->assertStringContainsString('Presencia de rasgos', $resultado->interpretacion_accidentogena);
        $this->assertStringContainsString('Presencia de rasgos', $resultado->interpretacion_tipo_a);

        // Verificar que la vista de resultados carga con 200 OK
        $responseVistaResultados = $this->actingAs($this->psicologa)->get(route('psicologia.paca.resultado', $aplicacion));
        $responseVistaResultados->assertStatus(200);
        $responseVistaResultados->assertSee('24');
        $responseVistaResultados->assertSee('40');
    }

    /**
     * 6. Separación de aplicaciones históricas múltiples para una misma persona.
     */
    public function test_conservacion_de_aplicaciones_historicas(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'ASPIRANTE MULTIPLES EVALUACIONES',
            'numero_identificacion' => 'TEST_CC_MULTI_' . rand(1000, 9999),
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => 'Neiva',
            'fecha_consentimiento' => now()->subMonths(6)->toDateString(),
            'estado' => 'FIRMADO_DIGITAL',
        ]);

        // Primera aplicación histórica finalizada
        $app1 = AplicacionPrueba::create([
            'candidato_id' => $candidato->id,
            'consentimiento_id' => $consentimiento->id,
            'prueba_codigo' => 'PAC-A',
            'edad_evaluado' => 22,
            'fecha_aplicacion' => now()->subMonths(6)->toDateString(),
            'estado' => 'FINALIZADA',
            'progreso_porcentaje' => 100.0,
            'usuario_responsable_id' => $this->psicologa->id,
        ]);

        // Segunda aplicación actual
        $app2 = AplicacionPrueba::create([
            'candidato_id' => $candidato->id,
            'consentimiento_id' => $consentimiento->id,
            'prueba_codigo' => 'PAC-A',
            'edad_evaluado' => 23,
            'fecha_aplicacion' => now()->toDateString(),
            'estado' => 'EN_CURSO',
            'progreso_porcentaje' => 0.0,
            'usuario_responsable_id' => $this->psicologa->id,
        ]);

        $this->assertEquals(2, $candidato->aplicaciones()->count());
        $this->assertNotEquals($app1->id, $app2->id);
    }

    /**
     * 7. Descarga de consentimiento informado (PDF y DOCX) por parte de la psicóloga.
     */
    public function test_descarga_consentimiento_pdf_y_docx(): void
    {
        $candidato = Candidato::create([
            'nombre_completo' => 'CANDIDATO DESCARGA CONSENTIMIENTO',
            'numero_identificacion' => 'TEST_CC_DESC_' . rand(1000, 9999),
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
        ]);

        $consentimiento = $candidato->consentimientos()->create([
            'ciudad_municipio' => 'Neiva',
            'fecha_consentimiento' => now()->toDateString(),
            'estado' => 'FIRMADO_DIGITAL',
            'tipo_firma' => 'DIGITAL',
            'codigo_documental' => 'GH-FO-025',
            'version_documental' => '01',
        ]);

        // Descarga PDF
        $responsePdf = $this->actingAs($this->psicologa)->get(route('psicologia.consentimientos.pdf', $consentimiento));
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');

        // Previsualización inline PDF
        $responseInline = $this->actingAs($this->psicologa)->get(route('psicologia.consentimientos.pdf', $consentimiento) . '?inline=1');
        $responseInline->assertStatus(200);
        $responseInline->assertHeader('Content-Type', 'application/pdf');

        // Descarga DOCX
        $responseDocx = $this->actingAs($this->psicologa)->get(route('psicologia.consentimientos.docx', $consentimiento));
        $responseDocx->assertStatus(200);
        $this->assertStringContainsString('wordprocessingml.document', (string)$responseDocx->headers->get('Content-Type'));
    }

    /**
     * 8. Filtros avanzados del historial de pruebas (texto, estado, fechas, tendencia, orden).
     */
    public function test_filtros_avanzados_historial(): void
    {
        $candidato1 = Candidato::create([
            'nombre_completo' => 'JUAN PEREZ OPERARIO',
            'numero_identificacion' => '99887766',
            'lugar_expedicion' => 'Neiva',
            'ciudad_municipio' => 'Neiva',
            'cargo_postulado' => 'Operario de Piscicultura',
        ]);

        $cons1 = $candidato1->consentimientos()->create([
            'ciudad_municipio' => 'Neiva',
            'fecha_consentimiento' => now()->toDateString(),
            'estado' => 'FIRMADO_DIGITAL',
            'tipo_firma' => 'DIGITAL',
        ]);

        $app1 = AplicacionPrueba::create([
            'candidato_id' => $candidato1->id,
            'consentimiento_id' => $cons1->id,
            'prueba_codigo' => 'PAC-A',
            'fecha_aplicacion' => now()->toDateString(),
            'estado' => 'FINALIZADA',
            'progreso_porcentaje' => 100.0,
            'usuario_responsable_id' => $this->psicologa->id,
        ]);

        $app1->resultadoPaca()->create([
            'puntaje_tendencia_accidentogena' => 12.0,
            'puntaje_patron_tipo_a' => 22.0,
            'interpretacion_accidentogena' => 'Presencia de rasgos',
            'interpretacion_tipo_a' => 'Presencia de rasgos',
            'calculado_automaticamente' => true,
        ]);

        // Filtrar por texto
        $resBuscar = $this->actingAs($this->psicologa)->get(route('psicologia.pruebas.historial', ['buscar' => 'JUAN PEREZ']));
        $resBuscar->assertStatus(200);
        $resBuscar->assertSee('JUAN PEREZ OPERARIO');
        $resBuscar->assertSee('PDF'); // Verifica que se muestra el botón de consentimiento

        // Filtrar por estado FINALIZADA
        $resEstado = $this->actingAs($this->psicologa)->get(route('psicologia.pruebas.historial', ['estado' => 'FINALIZADA']));
        $resEstado->assertStatus(200);
        $resEstado->assertSee('JUAN PEREZ OPERARIO');

        // Filtrar por tendencia accidentógena PRESENCIA
        $resTendencia = $this->actingAs($this->psicologa)->get(route('psicologia.pruebas.historial', ['tendencia' => 'PRESENCIA']));
        $resTendencia->assertStatus(200);
        $resTendencia->assertSee('JUAN PEREZ OPERARIO');

        // Filtrar por tendencia AUSENCIA (no debe encontrarlo)
        $resAusencia = $this->actingAs($this->psicologa)->get(route('psicologia.pruebas.historial', ['tendencia' => 'AUSENCIA']));
        $resAusencia->assertStatus(200);
        $resAusencia->assertDontSee('JUAN PEREZ OPERARIO');
    }
}
