<?php

namespace App\Services\Psicologia;

use App\Constants\ValantiPreguntas;
use App\Models\EvaluacionCandidato;
use App\Models\ResultadoValanti;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class CalificadorValantiService
{
    /**
     * Califica las 30 respuestas del cuestionario VALANTI con el Perfil Ideal estático
     * (Verdad=50, Rectitud=65, Paz=50, Amor=65, No Violencia=65), persiste el resultado
     * y genera automáticamente el archivo PDF institucional con el Gráfico Radial.
     */
    public function calificarYAplicar(EvaluacionCandidato $evaluacion): ResultadoValanti
    {
        $respuestas = $evaluacion->respuestasValanti()->get()->keyBy('numero_pregunta');

        if ($respuestas->count() < ValantiPreguntas::TOTAL_PREGUNTAS) {
            throw new InvalidArgumentException(
                "No se puede calificar la prueba VALANTI: solo se han respondido {$respuestas->count()} de 30 preguntas."
            );
        }

        // 1. Inicializar Puntajes Directos (PD) por dimensión
        $pd = [
            'VERDAD' => 0,
            'RECTITUD' => 0,
            'PAZ' => 0,
            'AMOR' => 0,
            'NO_VIOLENCIA' => 0,
        ];

        $sumaTotalPuntos = 0;

        foreach (ValantiPreguntas::PREGUNTAS as $numero => $pregunta) {
            $resp = $respuestas->get($numero);
            if (!$resp) {
                throw new InvalidArgumentException("Falta respuesta para la pregunta #{$numero} de VALANTI.");
            }

            $ptsA = (int) $resp->puntos_a;
            $ptsB = (int) $resp->puntos_b;

            if (!ValantiPreguntas::esCombinacionValida($ptsA, $ptsB)) {
                throw new InvalidArgumentException(
                    "Combinación inválida en la pregunta #{$numero} ({$ptsA}-{$ptsB}). La suma debe ser estrictamente 3."
                );
            }

            $pd[$pregunta['dim_a']] += $ptsA;
            $pd[$pregunta['dim_b']] += $ptsB;
            $sumaTotalPuntos += ($ptsA + $ptsB);
        }

        if ($sumaTotalPuntos !== ValantiPreguntas::SUMA_TOTAL_ESPERADA) {
            throw new InvalidArgumentException(
                "La sumatoria total de los puntajes directos debe ser exactamente 90 puntos (obtenido: {$sumaTotalPuntos})."
            );
        }

        $resultadoExistente = $evaluacion->resultadoValanti;

        $datosResultado = [
            'pd_total' => $sumaTotalPuntos,
            'finalizada_at' => $resultadoExistente?->finalizada_at ?? now(),
        ];

        // 2. Calcular Puntaje Estándar (PE), Interpretación y Distancia con el Perfil Ideal Fijo
        // Perfil Ideal Fijo: Verdad=50, Rectitud=65, Paz=50, Amor=65, No Violencia=65
        foreach (ValantiPreguntas::BAREMOS as $dimKey => $baremo) {
            $clave = $baremo['clave']; // verdad, rectitud, paz, amor, no_violencia
            $puntajeDirecto = $pd[$dimKey];

            // PE = round( 50 + 10 * ((PD - Media) / Desviación) )
            $puntajeEstandar = ValantiPreguntas::calcularPuntajeEstandar(
                $puntajeDirecto,
                $baremo['media'],
                $baremo['desviacion']
            );

            $interpretacion = ValantiPreguntas::interpretarPuntajeEstandar($puntajeEstandar);

            // Perfil Ideal estático definido por la organización
            $perfilEsperado = (int) $baremo['perfil_esperado_default'];

            // Distancia = PE - Perfil_Esperado_Empresa
            $distancia = $puntajeEstandar - $perfilEsperado;

            $datosResultado["pd_{$clave}"] = $puntajeDirecto;
            $datosResultado["pe_{$clave}"] = $puntajeEstandar;
            $datosResultado["nivel_{$clave}"] = $interpretacion;
            $datosResultado["perfil_empresa_{$clave}"] = $perfilEsperado;
            $datosResultado["distancia_{$clave}"] = $distancia;
        }

        $resultado = ResultadoValanti::updateOrCreate(
            ['evaluacion_candidato_id' => $evaluacion->id],
            $datosResultado
        );

        // 3. Generar y guardar automáticamente el PDF en disco apenas finaliza la prueba
        try {
            $this->generarYGuardarPdfReporte($evaluacion, $resultado);
        } catch (\Throwable $e) {
            report($e);
        }

        return $resultado;
    }

    /**
     * Genera en alta resolución (PNG base64) el gráfico radial de 5 ejes con marcas de graduación
     * perpendiculares (estilo estrella de 5 ejes de VALANTI) y tipografía TrueType legible.
     */
    public function generarRadarPngBase64(ResultadoValanti $resultado): string
    {
        $width = 1040;
        $height = 680;
        $cx = 520;
        $cy = 355;
        $maxRadius = 245;
        $maxVal = 70;

        $im = imagecreatetruecolor($width, $height);
        imageantialias($im, true);

        // Colores idénticos a la imagen de referencia de VALANTI
        $blanco = imagecolorallocate($im, 255, 255, 255);
        $negroEje = imagecolorallocate($im, 20, 20, 20);
        $negro = imagecolorallocate($im, 0, 0, 0);
        $azulIdeal = imagecolorallocate($im, 0, 28, 155); // Azul oscuro corporativo #001c9b

        imagefill($im, 0, 0, $blanco);

        $fontRegular = 'C:/Windows/Fonts/arial.ttf';
        $fontBold = 'C:/Windows/Fonts/arialbd.ttf';
        $usaTtf = file_exists($fontRegular) && file_exists($fontBold) && function_exists('imagettftext');

        // Ángulos de los 5 ejes en sentido horario iniciando a las 12:00 (-90°):
        // 0: Verdad (-90°), 1: Rectitud (-18°), 2: Paz (54°), 3: Amor (126°), 4: No violencia (198°)
        $ejes = [
            ['nombre' => 'Verdad',       'angulo' => -90, 'pe' => (int) $resultado->pe_verdad,       'ideal' => 50],
            ['nombre' => 'Rectitud',     'angulo' => -18, 'pe' => (int) $resultado->pe_rectitud,     'ideal' => 65],
            ['nombre' => 'Paz',          'angulo' => 54,  'pe' => (int) $resultado->pe_paz,          'ideal' => 50],
            ['nombre' => 'Amor',         'angulo' => 126, 'pe' => (int) $resultado->pe_amor,         'ideal' => 65],
            ['nombre' => 'No violencia', 'angulo' => 198, 'pe' => (int) $resultado->pe_no_violencia, 'ideal' => 65],
        ];

        $puntoPolar = function (float $valor, float $anguloGrados) use ($cx, $cy, $maxRadius, $maxVal): array {
            $valorClamped = max(0.0, min((float) $maxVal, $valor));
            $r = ($valorClamped / $maxVal) * $maxRadius;
            $rad = deg2rad($anguloGrados);
            return [
                (int) round($cx + $r * cos($rad)),
                (int) round($cy + $r * sin($rad)),
            ];
        };

        // 1. Dibujar los 5 ejes radiales desde el centro hasta 70 + marcas perpendiculares cada 10 unidades
        imagesetthickness($im, 2);
        foreach ($ejes as $eje) {
            $ang = $eje['angulo'];
            [$vx, $vy] = $puntoPolar(70, $ang);
            imageline($im, $cx, $cy, $vx, $vy, $negroEje);

            // Vector unitario perpendicular al eje para dibujar las rayitas (ticks) en 10, 20, 30, 40, 50, 60, 70
            $perpRad = deg2rad($ang + 90);
            $tickHalfLen = 6;
            $dx = $tickHalfLen * cos($perpRad);
            $dy = $tickHalfLen * sin($perpRad);

            for ($step = 10; $step <= 70; $step += 10) {
                [$tx, $ty] = $puntoPolar($step, $ang);
                imageline(
                    $im,
                    (int) round($tx - $dx),
                    (int) round($ty - $dy),
                    (int) round($tx + $dx),
                    (int) round($ty + $dy),
                    $negroEje
                );
            }
        }

        // 2. Números de escala (0, 10, 20, 30, 40, 50, 60, 70) a la izquierda del eje vertical superior (Verdad)
        for ($step = 0; $step <= 70; $step += 10) {
            [, $ty] = $puntoPolar($step, -90);
            $txt = (string) $step;
            if ($usaTtf) {
                $bbox = imagettfbbox(12, 0, $fontRegular, $txt);
                $tw = abs($bbox[4] - $bbox[0]);
                $offsetY = ($step === 0) ? 16 : 5;
                imagettftext($im, 12, 0, $cx - $tw - 14, $ty + $offsetY, $negro, $fontRegular, $txt);
            } else {
                $tw = imagefontwidth(4) * strlen($txt);
                imagestring($im, 4, $cx - $tw - 12, $ty - 7, $txt, $negro);
            }
        }

        // 3. Etiquetas grandes en negrita de los 5 ejes (Verdad, Rectitud, Paz, Amor, No violencia)
        if ($usaTtf) {
            $fontSizeLabel = 21;
            foreach ($ejes as $idx => $eje) {
                [$tipX, $tipY] = $puntoPolar(70, $eje['angulo']);
                $bbox = imagettfbbox($fontSizeLabel, 0, $fontBold, $eje['nombre']);
                $tw = abs($bbox[4] - $bbox[0]);
                $th = abs($bbox[5] - $bbox[1]);

                switch ($idx) {
                    case 0: // Verdad (Arriba centrado)
                        $lx = (int) round($cx - ($tw / 2));
                        $ly = $tipY - 18;
                        break;
                    case 1: // Rectitud (Derecha)
                        $lx = $tipX + 18;
                        $ly = (int) round($tipY + ($th / 3));
                        break;
                    case 2: // Paz (Abajo derecha)
                        $lx = $tipX + 14;
                        $ly = $tipY + $th + 2;
                        break;
                    case 3: // Amor (Abajo izquierda)
                        $lx = $tipX - $tw - 14;
                        $ly = $tipY + $th + 2;
                        break;
                    default: // No violencia (Izquierda)
                        $lx = $tipX - $tw - 18;
                        $ly = (int) round($tipY + ($th / 3));
                        break;
                }

                imagettftext($im, $fontSizeLabel, 0, $lx, $ly, $negro, $fontBold, $eje['nombre']);
            }
        } else {
            $etiquetasPos = [
                0 => [$cx - 28, $cy - $maxRadius - 32],
                1 => [$cx + $maxRadius + 16, $cy - 82],
                2 => [$cx + 155, $cy + $maxRadius - 35],
                3 => [$cx - 195, $cy + $maxRadius - 35],
                4 => [$cx - $maxRadius - 125, $cy - 82],
            ];
            foreach ($ejes as $idx => $eje) {
                [$lx, $ly] = $etiquetasPos[$idx];
                imagestring($im, 5, $lx, $ly, $eje['nombre'], $negro);
            }
        }

        // 4. Serie Perfil Ideal (Azul punteado + Rombos azules en [50, 65, 50, 65, 65])
        $coordsIdeal = [];
        foreach ($ejes as $eje) {
            $coordsIdeal[] = $puntoPolar($eje['ideal'], $eje['angulo']);
        }

        $estiloPunteado = array_merge(
            array_fill(0, 9, $azulIdeal),
            array_fill(0, 6, IMG_COLOR_TRANSPARENT)
        );
        imagesetstyle($im, $estiloPunteado);
        imagesetthickness($im, 3);

        for ($i = 0; $i < 5; $i++) {
            [$x1, $y1] = $coordsIdeal[$i];
            [$x2, $y2] = $coordsIdeal[($i + 1) % 5];
            imageline($im, $x1, $y1, $x2, $y2, IMG_COLOR_STYLED);
        }

        // Marcadores Rombo azules
        imagesetthickness($im, 1);
        foreach ($coordsIdeal as [$ix, $iy]) {
            $d = 9;
            $rombo = [
                $ix, $iy - $d,
                $ix + $d, $iy,
                $ix, $iy + $d,
                $ix - $d, $iy,
            ];
            imagefilledpolygon($im, $rombo, $azulIdeal);
        }

        // 5. Serie Candidato (Línea negra continua + Cuadrados negros sólidos)
        $coordsCandidato = [];
        foreach ($ejes as $eje) {
            $coordsCandidato[] = $puntoPolar($eje['pe'], $eje['angulo']);
        }

        imagesetthickness($im, 3);
        for ($i = 0; $i < 5; $i++) {
            [$x1, $y1] = $coordsCandidato[$i];
            [$x2, $y2] = $coordsCandidato[($i + 1) % 5];
            imageline($im, $x1, $y1, $x2, $y2, $negro);
        }

        // Marcadores Cuadrados negros
        imagesetthickness($im, 1);
        foreach ($coordsCandidato as [$kx, $ky]) {
            $s = 7;
            imagefilledrectangle($im, $kx - $s, $ky - $s, $kx + $s, $ky + $s, $negro);
        }

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);

        return 'data:image/png;base64,' . base64_encode($pngData);
    }

    /**
     * Construye el archivo PDF oficial del Cuestionario VALANTI con el gráfico radial
     * y los párrafos interpretativos, almacenándolo automáticamente en storage/app/public/valanti_pdfs/.
     */
    public function generarYGuardarPdfReporte(EvaluacionCandidato $evaluacion, ?ResultadoValanti $resultado = null): string
    {
        $resultado = $resultado ?? $evaluacion->resultadoValanti;

        $logoBase64 = null;
        $rutaLogo = public_path('images/Logo.png');
        if (file_exists($rutaLogo)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($rutaLogo));
        }

        $radarPngBase64 = $this->generarRadarPngBase64($resultado);
        $baremos = ValantiPreguntas::BAREMOS;

        $narrativa = ValantiPreguntas::generarNarrativaCompleta($evaluacion->nombre_completo, [
            'VERDAD' => (int) $resultado->pe_verdad,
            'RECTITUD' => (int) $resultado->pe_rectitud,
            'PAZ' => (int) $resultado->pe_paz,
            'AMOR' => (int) $resultado->pe_amor,
            'NO_VIOLENCIA' => (int) $resultado->pe_no_violencia,
        ]);

        $pdf = Pdf::loadView('psicologia.valanti.pdf', compact(
            'evaluacion',
            'resultado',
            'logoBase64',
            'radarPngBase64',
            'baremos',
            'narrativa'
        ))->setPaper('letter', 'portrait');

        $rutaRelativa = $this->rutaArchivoPdf($evaluacion);
        Storage::disk('public')->put($rutaRelativa, $pdf->output());

        return $rutaRelativa;
    }

    /**
     * Ruta relativa determinística donde queda guardado el PDF automático de VALANTI (v2 con nuevo diseño).
     */
    public function rutaArchivoPdf(EvaluacionCandidato $evaluacion): string
    {
        $identLimpia = preg_replace('/[^A-Za-z0-9_-]/', '', $evaluacion->identificacion);
        return "valanti_pdfs/Cuestionario_VALANTI_{$identLimpia}_{$evaluacion->id}.pdf";
    }
}
