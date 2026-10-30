<?php

namespace App\Constants;

class ValantiPreguntas
{
    public const TOTAL_PREGUNTAS = 30;
    public const PUNTOS_POR_PREGUNTA = 3;
    public const SUMA_TOTAL_ESPERADA = 90;

    /**
     * Combinaciones válidas para distribuir los 3 puntos entre A y B.
     */
    public const COMBINACIONES_VALIDAS = [
        '3-0' => ['a' => 3, 'b' => 0, 'etiqueta' => '3 - 0'],
        '2-1' => ['a' => 2, 'b' => 1, 'etiqueta' => '2 - 1'],
        '1-2' => ['a' => 1, 'b' => 2, 'etiqueta' => '1 - 2'],
        '0-3' => ['a' => 0, 'b' => 3, 'etiqueta' => '0 - 3'],
    ];

    /**
     * Parámetros estadísticos y normas nacionales (Baremos 1997, n=730)
     * y Perfil Ideal estático: Verdad=50, Rectitud=65, Paz=50, Amor=65, No violencia=65.
     */
    public const BAREMOS = [
        'VERDAD' => [
            'clave' => 'verdad',
            'nombre' => 'Verdad',
            'nombre_mayus' => 'VERDAD',
            'area_corta' => 'intelectual',
            'area' => 'Área Intelectual',
            'cualidades' => 'veracidad, raciocinio, curiosidad y honestidad intelectual',
            'media' => 15.65,
            'desviacion' => 4.70,
            'perfil_esperado_default' => 50,
        ],
        'RECTITUD' => [
            'clave' => 'rectitud',
            'nombre' => 'Rectitud',
            'nombre_mayus' => 'RECTITUD',
            'area_corta' => 'física',
            'area' => 'Área Física / Deber',
            'cualidades' => 'compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad y sacrificio',
            'media' => 21.05,
            'desviacion' => 4.44,
            'perfil_esperado_default' => 65,
        ],
        'PAZ' => [
            'clave' => 'paz',
            'nombre' => 'Paz',
            'nombre_mayus' => 'PAZ',
            'area_corta' => 'emocional',
            'area' => 'Área Emocional',
            'cualidades' => 'calma, concentración, paciencia, reflexión, satisfacción, y silencio interior',
            'media' => 17.35,
            'desviacion' => 6.61,
            'perfil_esperado_default' => 50,
        ],
        'AMOR' => [
            'clave' => 'amor',
            'nombre' => 'Amor',
            'nombre_mayus' => 'AMOR',
            'area_corta' => 'intuitiva',
            'area' => 'Área Intuitiva / Afectiva',
            'cualidades' => 'amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia',
            'media' => 16.68,
            'desviacion' => 5.41,
            'perfil_esperado_default' => 65,
        ],
        'NO_VIOLENCIA' => [
            'clave' => 'no_violencia',
            'nombre' => 'No violencia',
            'nombre_mayus' => 'NO VIOLENCIA',
            'area_corta' => 'espiritual',
            'area' => 'Área Espiritual',
            'cualidades' => 'amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana',
            'media' => 21.22,
            'desviacion' => 7.19,
            'perfil_esperado_default' => 65,
        ],
    ];

    /**
     * Las 30 preguntas oficiales de la prueba VALANTI (Partes 1 y 2).
     */
    public const PREGUNTAS = [
        // === PARTE 1: VALORES (Preguntas 1 a 9) ===
        1 => [
            'parte' => 1,
            'texto_a' => 'Muestro dedicacion a las personas que amo',
            'texto_b' => 'Actuo con perseverancia',
            'dim_a' => 'AMOR',
            'dim_b' => 'RECTITUD',
        ],
        2 => [
            'parte' => 1,
            'texto_a' => 'Soy tolerante',
            'texto_b' => 'Prefiero actuar con etica.',
            'dim_a' => 'PAZ',
            'dim_b' => 'RECTITUD',
        ],
        3 => [
            'parte' => 1,
            'texto_a' => 'Al pensar utilizo mi intuicion o sexto sentido',
            'texto_b' => 'Me siento una persona digna',
            'dim_a' => 'VERDAD',
            'dim_b' => 'NO_VIOLENCIA',
        ],
        4 => [
            'parte' => 1,
            'texto_a' => 'Logro buena concentracion mental',
            'texto_b' => 'Perdono todas las ofensas de cualquier persona',
            'dim_a' => 'PAZ',
            'dim_b' => 'AMOR',
        ],
        5 => [
            'parte' => 1,
            'texto_a' => 'Normalmente razono mucho',
            'texto_b' => 'Me destaco por el liderazgo en mis acciones',
            'dim_a' => 'VERDAD',
            'dim_b' => 'RECTITUD',
        ],
        6 => [
            'parte' => 1,
            'texto_a' => 'Pienso con Integridad',
            'texto_b' => 'Me coloco objetivos y metas en mi vida personal',
            'dim_a' => 'VERDAD',
            'dim_b' => 'RECTITUD',
        ],
        7 => [
            'parte' => 1,
            'texto_a' => 'Soy una persona de iniciativa',
            'texto_b' => 'En mi trabajo normalmente soy curioso',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'VERDAD',
        ],
        8 => [
            'parte' => 1,
            'texto_a' => 'Doy Amor',
            'texto_b' => 'Para pensar hago sintesis de las distintas ideas',
            'dim_a' => 'AMOR',
            'dim_b' => 'VERDAD',
        ],
        9 => [
            'parte' => 1,
            'texto_a' => 'Me siento en calma',
            'texto_b' => 'Pienso con veracidad',
            'dim_a' => 'PAZ',
            'dim_b' => 'VERDAD',
        ],

        // === PARTE 2: ANTIVALORES (Preguntas 10 a 30) ===
        10 => [
            'parte' => 2,
            'texto_a' => 'Irrespetar la propiedad',
            'texto_b' => 'Sentir inquietud',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'PAZ',
        ],
        11 => [
            'parte' => 2,
            'texto_a' => 'Ser irresponsable',
            'texto_b' => 'ser desconsiderado hacia cualquier persona',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'AMOR',
        ],
        12 => [
            'parte' => 2,
            'texto_a' => 'Caer en contradiccion al pensar',
            'texto_b' => 'Sentir Intolerancia',
            'dim_a' => 'VERDAD',
            'dim_b' => 'PAZ',
        ],
        13 => [
            'parte' => 2,
            'texto_a' => 'Ser violento',
            'texto_b' => 'Actuar con cobardia',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'RECTITUD',
        ],
        14 => [
            'parte' => 2,
            'texto_a' => 'Sentirse Presumido',
            'texto_b' => 'Generar divisiones y discordia entre los seres humanos',
            'dim_a' => 'PAZ',
            'dim_b' => 'NO_VIOLENCIA',
        ],
        15 => [
            'parte' => 2,
            'texto_a' => 'Ser Cruel',
            'texto_b' => 'Sentir ira',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'PAZ',
        ],
        16 => [
            'parte' => 2,
            'texto_a' => 'Pensar con confusion',
            'texto_b' => 'Tener odio en el corazon',
            'dim_a' => 'VERDAD',
            'dim_b' => 'AMOR',
        ],
        17 => [
            'parte' => 2,
            'texto_a' => 'Decir blasfemias',
            'texto_b' => 'Ser Escandaloso',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'PAZ',
        ],
        18 => [
            'parte' => 2,
            'texto_a' => 'Crear desigualdades entre los seres humanos',
            'texto_b' => 'Apasionarse por una idea',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'VERDAD',
        ],
        19 => [
            'parte' => 2,
            'texto_a' => 'Sentirse inconstante',
            'texto_b' => 'Crear Rivalidad hacia otros',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'NO_VIOLENCIA',
        ],
        20 => [
            'parte' => 2,
            'texto_a' => 'Pensamientos Irracionales',
            'texto_b' => 'Traicionar a un desconocido',
            'dim_a' => 'VERDAD',
            'dim_b' => 'AMOR',
        ],
        21 => [
            'parte' => 2,
            'texto_a' => 'Ostentar riquezas materiales',
            'texto_b' => 'Sentirse Infeliz',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'PAZ',
        ],
        22 => [
            'parte' => 2,
            'texto_a' => 'Entorpecer la comunicación entre seres humanos',
            'texto_b' => 'la maldad',
            'dim_a' => 'AMOR',
            'dim_b' => 'NO_VIOLENCIA',
        ],
        23 => [
            'parte' => 2,
            'texto_a' => 'Odiar a cualquier ser de la naturaleza',
            'texto_b' => 'Hacer distinciones entre las personas',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'AMOR',
        ],
        24 => [
            'parte' => 2,
            'texto_a' => 'Sentirse intranquilo',
            'texto_b' => 'Ser Infiel',
            'dim_a' => 'PAZ',
            'dim_b' => 'AMOR',
        ],
        25 => [
            'parte' => 2,
            'texto_a' => 'Tener la mente dispersa',
            'texto_b' => 'Mostrar apatía al pensar',
            'dim_a' => 'PAZ',
            'dim_b' => 'VERDAD',
        ],
        26 => [
            'parte' => 2,
            'texto_a' => 'la injusticia',
            'texto_b' => 'Sentirse Angustiado',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'PAZ',
        ],
        27 => [
            'parte' => 2,
            'texto_a' => 'vengarse de los que odian a todo el mundo',
            'texto_b' => 'Vengarse del que hace daño a un familiar',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'AMOR',
        ],
        28 => [
            'parte' => 2,
            'texto_a' => 'Usar abusivamente el poder',
            'texto_b' => 'Distraerse',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'VERDAD',
        ],
        29 => [
            'parte' => 2,
            'texto_a' => 'Ser desagradecido con los que ayudan',
            'texto_b' => 'Ser Egoista con todos',
            'dim_a' => 'RECTITUD',
            'dim_b' => 'AMOR',
        ],
        30 => [
            'parte' => 2,
            'texto_a' => 'Cualquier forma de irrespeto',
            'texto_b' => 'odiar',
            'dim_a' => 'NO_VIOLENCIA',
            'dim_b' => 'AMOR',
        ],
    ];

    /**
     * Valida si un par de puntos (A, B) es una combinación permitida ([3,0], [0,3], [2,1], [1,2]).
     */
    public static function esCombinacionValida(int $puntosA, int $puntosB): bool
    {
        return ($puntosA + $puntosB === 3)
            && $puntosA >= 0 && $puntosA <= 3
            && $puntosB >= 0 && $puntosB <= 3;
    }

    /**
     * Calcula el Puntaje Estándar (PE) según el baremo nacional (1997, n=730):
     * PE = round( 50 + 10 * ((PD - Media) / Desviación) )
     */
    public static function calcularPuntajeEstandar(int $pd, float $media, float $desviacion): int
    {
        return (int) round(50 + 10 * (($pd - $media) / $desviacion));
    }

    /**
     * Devuelve la interpretación cualitativa del Puntaje Estándar (PE):
     * - PE > 60: "Muy Alto"
     * - PE entre 55 y 60: "Promedio Alto"
     * - PE entre 45 y 54: "Promedio"
     * - PE entre 40 y 44: "Promedio Bajo"
     * - PE < 40: "Bajo"
     */
    public static function interpretarPuntajeEstandar(int $pe): string
    {
        if ($pe > 60) {
            return 'Muy Alto';
        }
        if ($pe >= 55) {
            return 'Promedio Alto';
        }
        if ($pe >= 45) {
            return 'Promedio';
        }
        if ($pe >= 40) {
            return 'Promedio Bajo';
        }

        return 'Bajo';
    }

    /**
     * Genera la interpretación narrativa completa del Cuestionario VALANTI
     * (resumen de área más/menos importante + los 5 párrafos descriptivos por dimensión).
     *
     * @param array<string, int> $pePorDimension ['VERDAD' => 55, 'RECTITUD' => 43, ...]
     * @return array{resumen: string, parrafos: array<string, string>}
     */
    public static function generarNarrativaCompleta(string $nombreCandidato, array $pePorDimension): array
    {
        // 1. Determinar área más importante y menos importante
        $ordenadoMayor = $pePorDimension;
        arsort($ordenadoMayor);
        $dimMayorKey = array_key_first($ordenadoMayor);

        $ordenadoMenor = $pePorDimension;
        asort($ordenadoMenor);
        $dimMenorKey = array_key_first($ordenadoMenor);

        $infoMayor = self::BAREMOS[$dimMayorKey];
        $infoMenor = self::BAREMOS[$dimMenorKey];

        $resumen = sprintf(
            'El área %s (Valor %s) es la más importante para %s. El área menos importante es la %s (Valor %s).',
            $infoMayor['area_corta'],
            $infoMayor['nombre_mayus'],
            $nombreCandidato,
            $infoMenor['area_corta'],
            $infoMenor['nombre_mayus']
        );

        // 2. Generar el párrafo narrativo exacto para cada una de las 5 dimensiones
        $parrafos = [];
        foreach (self::BAREMOS as $dimKey => $info) {
            $pe = (int) ($pePorDimension[$dimKey] ?? 50);
            $parrafos[$dimKey] = self::generarParrafoDimension($dimKey, $pe, $info);
        }

        return [
            'resumen' => $resumen,
            'parrafos' => $parrafos,
        ];
    }

    /**
     * Construye el párrafo interpretativo oficial de una dimensión según su Puntaje Estándar (PE).
     */
    public static function generarParrafoDimension(string $dimKey, int $pe, array $info): string
    {
        $valorMinus = mb_strtolower($info['nombre'], 'UTF-8');
        $parteTexto = $dimKey === 'NO_VIOLENCIA'
            ? 'La parte espiritual de valores'
            : "La parte {$info['area_corta']} de sus valores";
        $cualidades = $info['cualidades'];

        // Muy Alto (PE > 60) o Promedio Alto (PE 55 - 60)
        if ($pe >= 55) {
            if ($dimKey === 'PAZ') {
                $fraseInicial = $pe > 60
                    ? 'La valoración de la paz es mucho más alta que lo normal.'
                    : 'La valoración de la paz es más alta que lo normal.';
            } else {
                $articulo = in_array($dimKey, ['VERDAD', 'RECTITUD', 'PAZ', 'NO_VIOLENCIA'], true) ? 'la ' : 'el ';
                $intensidad = $pe > 60 ? 'mucho más' : 'un poco más';
                $fraseInicial = "Valora {$articulo}{$valorMinus} {$intensidad} que el resto de la gente.";
            }

            $prioridad = $pe > 60 ? 'muy alta prioridad' : 'buena prioridad';

            return "{$fraseInicial} Resalta la parte {$info['area_corta']} de sus valores, dándole {$prioridad} a cualidades como {$cualidades}.";
        }

        // Promedio / Dentro de la norma del grupo (PE 45 - 54)
        if ($pe >= 45) {
            return "El valor {$valorMinus} le impacta dentro de la norma del grupo. {$parteTexto} está equilibrada, dándole adecuada importancia a cualidades como {$cualidades}.";
        }

        // Promedio Bajo (PE 42 - 44): "La importancia del valor X no es fundamental..."
        if ($pe >= 42) {
            return "La importancia del valor {$valorMinus} no es fundamental. {$parteTexto} es colocada en segundo plano, dándole un poco menos importancia a cualidades como {$cualidades}.";
        }

        // Bajo (PE <= 41): "El valor X no es básico..."
        return "El valor {$valorMinus} no es básico. {$parteTexto} es puesta en segundo plano, dándole menos importancia a cualidades como {$cualidades}.";
    }
}
