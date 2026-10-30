<?php

namespace Tests\Unit;

use App\Constants\PacaPreguntas;
use App\Services\Psicologia\CalificadorPacaService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CalificadorPacaServiceTest extends TestCase
{
    protected CalificadorPacaService $calificador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calificador = new CalificadorPacaService();
    }

    /**
     * Verifica que existan exactamente 40 afirmaciones del instrumento institucional.
     */
    public function test_cuenta_con_las_40_afirmaciones_exactas(): void
    {
        $this->assertCount(40, PacaPreguntas::AFIRMACIONES);
        $this->assertEquals(
            'Siento que no me alcanza el tiempo para hacer todo lo que tengo que hacer.',
            PacaPreguntas::AFIRMACIONES[1]
        );
        $this->assertEquals(
            'Juego mucho con los objetos que tengo en las manos.',
            PacaPreguntas::AFIRMACIONES[40]
        );
    }

    /**
     * Verifica la ponderación de cada opción de respuesta:
     * Siempre = 2, Frecuentemente = 1, Rara vez = 0.5, Nunca = 0
     */
    public function test_ponderacion_correcta_de_opciones(): void
    {
        $this->assertEquals(2.0, PacaPreguntas::puntajeDeOpcion('S'));
        $this->assertEquals(1.0, PacaPreguntas::puntajeDeOpcion('F'));
        $this->assertEquals(0.5, PacaPreguntas::puntajeDeOpcion('R'));
        $this->assertEquals(0.0, PacaPreguntas::puntajeDeOpcion('N'));
        $this->assertEquals(0.0, PacaPreguntas::puntajeDeOpcion('INVALIDA'));
    }

    /**
     * Verifica la correspondencia de preguntas para Tendencia Accidentógena (12 ítems).
     * B36, B39, B42, D35, D39, D41, F34, F37, F40, H36, H41, H43
     */
    public function test_preguntas_tendencia_accidentogena_exactas(): void
    {
        $esperadas = [3, 6, 9, 12, 16, 18, 21, 24, 27, 33, 38, 40];
        $this->assertEquals($esperadas, PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA);
        $this->assertCount(12, PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA);
    }

    /**
     * Verifica la correspondencia de preguntas para Patrón Tipo A (20 ítems).
     */
    public function test_preguntas_patron_tipo_a_exactas(): void
    {
        $esperadas = [1, 2, 4, 8, 11, 13, 14, 17, 19, 22, 23, 26, 28, 29, 31, 32, 34, 35, 37, 39];
        $this->assertEquals($esperadas, PacaPreguntas::PREGUNTAS_PATRON_TIPO_A);
        $this->assertCount(20, PacaPreguntas::PREGUNTAS_PATRON_TIPO_A);
    }

    /**
     * Caso benchmark manual 1: Respuestas todas en 'Nunca' (N).
     * Tendencia Accidentógena = 0, Tipo A = 0.
     */
    public function test_calculo_todas_nunca(): void
    {
        $respuestas = array_fill(1, 40, 'N');
        $resultado = $this->calificador->calificar($respuestas);

        $this->assertEquals(0.0, $resultado['puntaje_tendencia_accidentogena']);
        $this->assertEquals(0.0, $resultado['puntaje_patron_tipo_a']);
        $this->assertEquals('DEFINIDA', $resultado['estado_interpretacion_accidentogena']);
        $this->assertEquals('DEFINIDA', $resultado['estado_interpretacion_tipo_a']);
        $this->assertStringContainsString('No se evidencian rasgos', $resultado['interpretacion_accidentogena']);
        $this->assertStringContainsString('No se evidencian rasgos', $resultado['interpretacion_tipo_a']);
    }

    /**
     * Caso benchmark manual 2: Respuestas todas en 'Siempre' (S).
     * Tendencia Accidentógena = 12 * 2.0 = 24.0 puntos.
     * Patrón Tipo A = 20 * 2.0 = 40.0 puntos.
     */
    public function test_calculo_todas_siempre(): void
    {
        $respuestas = array_fill(1, 40, 'S');
        $resultado = $this->calificador->calificar($respuestas);

        $this->assertEquals(24.0, $resultado['puntaje_tendencia_accidentogena']);
        $this->assertEquals(40.0, $resultado['puntaje_patron_tipo_a']);
        $this->assertEquals('DEFINIDA', $resultado['estado_interpretacion_accidentogena']);
        $this->assertEquals('DEFINIDA', $resultado['estado_interpretacion_tipo_a']);
        $this->assertEquals(CalificadorPacaService::TXT_ACC_PRESENCIA, $resultado['interpretacion_accidentogena']);
        $this->assertEquals(CalificadorPacaService::TXT_TPOA_PRESENCIA, $resultado['interpretacion_tipo_a']);
    }

    /**
     * Caso benchmark manual 3: Cálculo mixto controlado.
     * - Para Tendencia Accidentógena: se responden con 'Frecuentemente' (1.0 pt c/u * 12 = 12.0 pts).
     * - Para Patrón Tipo A: 10 con 'S' (20 pts) y 10 con 'N' (0 pts) = 20.0 pts.
     * - Resto de preguntas (las 8 no asignadas): 'S' (no deben sumar a ninguno de los dos indicadores).
     */
    public function test_calculo_controlado_aisla_preguntas_no_participantes(): void
    {
        $respuestas = array_fill(1, 40, 'S'); // Las no participantes tendrán 'S', pero no deben sumar

        foreach (PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA as $num) {
            $respuestas[$num] = 'F'; // 1.0 cada una
        }

        foreach (PacaPreguntas::PREGUNTAS_PATRON_TIPO_A as $idx => $num) {
            $respuestas[$num] = ($idx < 10) ? 'S' : 'N'; // 10 con 2.0, 10 con 0.0
        }

        $resultado = $this->calificador->calificar($respuestas);

        $this->assertEquals(12.0, $resultado['puntaje_tendencia_accidentogena']);
        $this->assertEquals(20.0, $resultado['puntaje_patron_tipo_a']);
    }

    /**
     * Prueba el límite ambiguo documentado en Tendencia Accidentógena (8.5 puntos).
     * Debe conservar el puntaje y marcar AMBIGUA_VALIDACION_PENDIENTE.
     */
    public function test_rango_ambiguo_tendencia_accidentogena_8_5(): void
    {
        // 8 preguntas con 'Frecuentemente' (8.0 pts) y 1 con 'Rara vez' (0.5 pts) = 8.5 pts
        $respuestas = array_fill(1, 40, 'N');
        $preguntas = PacaPreguntas::PREGUNTAS_TENDENCIA_ACCIDENTOGENA;
        for ($i = 0; $i < 8; $i++) {
            $respuestas[$preguntas[$i]] = 'F'; // 1.0
        }
        $respuestas[$preguntas[8]] = 'R'; // 0.5 => Total 8.5

        $resultado = $this->calificador->calificar($respuestas);

        $this->assertEquals(8.5, $resultado['puntaje_tendencia_accidentogena']);
        $this->assertEquals('AMBIGUA_VALIDACION_PENDIENTE', $resultado['estado_interpretacion_accidentogena']);
        $this->assertTrue($resultado['tiene_ambiguedad']);
    }

    /**
     * Prueba el rango de superposición documentado en Patrón Tipo A (18.0 y 18.5 puntos).
     */
    public function test_rango_ambiguo_patron_tipo_a_18(): void
    {
        // 9 preguntas con 'Siempre' (18.0 pts) y resto con 'Nunca'
        $respuestas = array_fill(1, 40, 'N');
        $preguntas = PacaPreguntas::PREGUNTAS_PATRON_TIPO_A;
        for ($i = 0; $i < 9; $i++) {
            $respuestas[$preguntas[$i]] = 'S'; // 2.0 * 9 = 18.0
        }

        $resultado = $this->calificador->calificar($respuestas);

        $this->assertEquals(18.0, $resultado['puntaje_patron_tipo_a']);
        $this->assertEquals('AMBIGUA_VALIDACION_PENDIENTE', $resultado['estado_interpretacion_tipo_a']);
        $this->assertTrue($resultado['tiene_ambiguedad']);
    }

    /**
     * Debe lanzar excepción si falta alguna de las 40 preguntas.
     */
    public function test_lanza_excepcion_si_faltan_respuestas(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $respuestasIncompletas = array_fill(1, 39, 'S'); // Solo 39 preguntas
        $this->calificador->calificar($respuestasIncompletas);
    }
}
