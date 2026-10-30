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
     * Las 30 preguntas oficiales de la prueba VALANTI (Partes 1 y 2)
     * con el mapeo exacto de celdas de la plantilla oficial Excel de Octavio Escobar:
     * - Verdad (J4):       +B8+B10+B11+C12+C13+C14+B17+F6+G8+F10+G13+G15
     * - Rectitud (K4):     +C6+C7+C10+B16+C18+C11+B12+F11+G14+F16+F18+F19
     * - Paz (L4):          +C8+B9+C15+C17+B19+C20+G7+F9+G11+F14+F15+G16+G18
     * - Amor (M4):         +B6+B7+B13+B20+G6+F7+G9+G12+G17+G20
     * - No violencia (N4): +C9+B14+B15+C16+B18+C19+F8+G10+F12+F13+F17+G19+F20
     */
    public const PREGUNTAS = [
        // === PARTE 1: VALORES (Preguntas 1 a 9) ===
        1 => [
            'parte' => 1,
            'texto_a' => 'Muestro dedicacion a las personas que amo',
            'texto_b' => 'Actuo con perseverancia',
            'dim_a' => 'AMOR',       // B6
            'dim_b' => 'RECTITUD',   // C6
        ],
        2 => [
            'parte' => 1,
            'texto_a' => 'Soy tolerante',
            'texto_b' => 'Prefiero actuar con etica.',
            'dim_a' => 'AMOR',       // B7
            'dim_b' => 'RECTITUD',   // C7
        ],
        3 => [
            'parte' => 1,
            'texto_a' => 'Al pensar utilizo mi intuicion o sexto sentido',
            'texto_b' => 'Me siento una persona digna',
            'dim_a' => 'VERDAD',     // B8
            'dim_b' => 'PAZ',        // C8
        ],
        4 => [
            'parte' => 1,
            'texto_a' => 'Logro buena concentracion mental',
            'texto_b' => 'Perdono todas las ofensas de cualquier persona',
            'dim_a' => 'PAZ',          // B9
            'dim_b' => 'NO_VIOLENCIA', // C9
        ],
        5 => [
            'parte' => 1,
            'texto_a' => 'Normalmente razono mucho',
            'texto_b' => 'Me destaco por el liderazgo en mis acciones',
            'dim_a' => 'VERDAD',     // B10
            'dim_b' => 'RECTITUD',   // C10
        ],
        6 => [
            'parte' => 1,
            'texto_a' => 'Pienso con Integridad',
            'texto_b' => 'Me coloco objetivos y metas en mi vida personal',
            'dim_a' => 'VERDAD',     // B11
            'dim_b' => 'RECTITUD',   // C11
        ],
        7 => [
            'parte' => 1,
            'texto_a' => 'Soy una persona de iniciativa',
            'texto_b' => 'En mi trabajo normalmente soy curioso',
            'dim_a' => 'RECTITUD',   // B12
            'dim_b' => 'VERDAD',     // C12
        ],
        8 => [
            'parte' => 1,
            'texto_a' => 'Doy Amor',
            'texto_b' => 'Para pensar hago sintesis de las distintas ideas',
            'dim_a' => 'AMOR',       // B13
            'dim_b' => 'VERDAD',     // C13
        ],
        9 => [
            'parte' => 1,
            'texto_a' => 'Me siento en calma',
            'texto_b' => 'Pienso con veracidad',
            'dim_a' => 'NO_VIOLENCIA', // B14
            'dim_b' => 'VERDAD',       // C14
        ],

        // === PARTE 2: ANTIVALORES (Preguntas 10 a 30) ===
        10 => [
            'parte' => 2,
            'texto_a' => 'Irrespetar la propiedad',
            'texto_b' => 'Sentir inquietud',
            'dim_a' => 'NO_VIOLENCIA', // B15
            'dim_b' => 'PAZ',          // C15
        ],
        11 => [
            'parte' => 2,
            'texto_a' => 'Ser irresponsable',
            'texto_b' => 'ser desconsiderado hacia cualquier persona',
            'dim_a' => 'RECTITUD',     // B16
            'dim_b' => 'NO_VIOLENCIA', // C16
        ],
        12 => [
            'parte' => 2,
            'texto_a' => 'Caer en contradiccion al pensar',
            'texto_b' => 'Sentir Intolerancia',
            'dim_a' => 'VERDAD',     // B17
            'dim_b' => 'PAZ',        // C17
        ],
        13 => [
            'parte' => 2,
            'texto_a' => 'Ser violento',
            'texto_b' => 'Actuar con cobardia',
            'dim_a' => 'NO_VIOLENCIA', // B18
            'dim_b' => 'RECTITUD',     // C18
        ],
        14 => [
            'parte' => 2,
            'texto_a' => 'Sentirse Presumido',
            'texto_b' => 'Generar divisiones y discordia entre los seres humanos',
            'dim_a' => 'PAZ',          // B19
            'dim_b' => 'NO_VIOLENCIA', // C19
        ],
        15 => [
            'parte' => 2,
            'texto_a' => 'Ser Cruel',
            'texto_b' => 'Sentir ira',
            'dim_a' => 'AMOR',       // B20
            'dim_b' => 'PAZ',        // C20
        ],
        16 => [
            'parte' => 2,
            'texto_a' => 'Pensar con confusion',
            'texto_b' => 'Tener odio en el corazon',
            'dim_a' => 'VERDAD',     // F6
            'dim_b' => 'AMOR',       // G6
        ],
        17 => [
            'parte' => 2,
            'texto_a' => 'Decir blasfemias',
            'texto_b' => 'Ser Escandaloso',
            'dim_a' => 'AMOR',       // F7
            'dim_b' => 'PAZ',        // G7
        ],
        18 => [
            'parte' => 2,
            'texto_a' => 'Crear desigualdades entre los seres humanos',
            'texto_b' => 'Apasionarse por una idea',
            'dim_a' => 'NO_VIOLENCIA', // F8
            'dim_b' => 'VERDAD',       // G8
        ],
        19 => [
            'parte' => 2,
            'texto_a' => 'Sentirse inconstante',
            'texto_b' => 'Crear Rivalidad hacia otros',
            'dim_a' => 'PAZ',        // F9
            'dim_b' => 'AMOR',       // G9
        ],
        20 => [
            'parte' => 2,
            'texto_a' => 'Pensamientos Irracionales',
            'texto_b' => 'Traicionar a un desconocido',
            'dim_a' => 'VERDAD',       // F10
            'dim_b' => 'NO_VIOLENCIA', // G10
        ],
        21 => [
            'parte' => 2,
            'texto_a' => 'Ostentar riquezas materiales',
            'texto_b' => 'Sentirse Infeliz',
            'dim_a' => 'RECTITUD',   // F11
            'dim_b' => 'PAZ',        // G11
        ],
        22 => [
            'parte' => 2,
            'texto_a' => 'Entorpecer la comunicación entre seres humanos',
            'texto_b' => 'la maldad',
            'dim_a' => 'NO_VIOLENCIA', // F12
            'dim_b' => 'AMOR',         // G12
        ],
        23 => [
            'parte' => 2,
            'texto_a' => 'Odiar a cualquier ser de la naturaleza',
            'texto_b' => 'Hacer distinciones entre las personas',
            'dim_a' => 'NO_VIOLENCIA', // F13
            'dim_b' => 'VERDAD',       // G13
        ],
        24 => [
            'parte' => 2,
            'texto_a' => 'Sentirse intranquilo',
            'texto_b' => 'Ser Infiel',
            'dim_a' => 'PAZ',        // F14
            'dim_b' => 'RECTITUD',   // G14
        ],
        25 => [
            'parte' => 2,
            'texto_a' => 'Tener la mente dispersa',
            'texto_b' => 'Mostrar apatía al pensar',
            'dim_a' => 'PAZ',        // F15
            'dim_b' => 'VERDAD',     // G15
        ],
        26 => [
            'parte' => 2,
            'texto_a' => 'la injusticia',
            'texto_b' => 'Sentirse Angustiado',
            'dim_a' => 'RECTITUD',   // F16
            'dim_b' => 'PAZ',        // G16
        ],
        27 => [
            'parte' => 2,
            'texto_a' => 'vengarse de los que odian a todo el mundo',
            'texto_b' => 'Vengarse del que hace daño a un familiar',
            'dim_a' => 'NO_VIOLENCIA', // F17
            'dim_b' => 'AMOR',         // G17
        ],
        28 => [
            'parte' => 2,
            'texto_a' => 'Usar abusivamente el poder',
            'texto_b' => 'Distraerse',
            'dim_a' => 'RECTITUD',   // F18
            'dim_b' => 'PAZ',        // G18
        ],
        29 => [
            'parte' => 2,
            'texto_a' => 'Ser desagradecido con los que ayudan',
            'texto_b' => 'Ser Egoista con todos',
            'dim_a' => 'RECTITUD',     // F19
            'dim_b' => 'NO_VIOLENCIA', // G19
        ],
        30 => [
            'parte' => 2,
            'texto_a' => 'Cualquier forma de irrespeto',
            'texto_b' => 'odiar',
            'dim_a' => 'NO_VIOLENCIA', // F20
            'dim_b' => 'AMOR',         // G20
        ],
    ];

    /**
     * Matriz exacta de textos interpretativos de la hoja '.' (F2:K8) del Excel oficial VALANTI.
     * Escalas de corte (LOOKUP): 0, 24, 34, 44, 54, 64, 74.
     */
    public const TEXTOS_INTERPRETATIVOS_EXCEL = [
        'VERDAD' => [
            74 => 'Valora la verdad de manera extraordinaria. Resalta fuertemente la parte intelectual de sus valores, dándole total importancia a cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            64 => 'Valora la verdad mucho más que el resto de la gente. Resalta la parte intelectual de sus valores, dándole muy buena prioridad a cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            54 => 'Valora la verdad un poco más que el resto de la gente. Resalta la parte intelectual de sus valores, dándole buena prioridad a cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            44 => 'El valor verdad está dentro de la norma de la población. La parte intelectual de sus valores está equilibrada, dándole adecuada importancia a cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            34 => 'El predominio del valor verdad no es fundamental. La parte intelectual de sus valores es puesta en segundo plano, dándole un poco menos importancia a cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            24 => 'Bajo predominio del valor verdad. La parte intelectual de sus valores es ignorada, importándole poco cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
            0  => 'Muy bajo predominio del valor verdad. La parte intelectual de sus valores es completamente ignorada, sin importar cualidades como veracidad, raciocinio, curiosidad y honestidad intelectual.',
        ],
        'RECTITUD' => [
            74 => 'Le da importancia a la rectitud de manera extraordinaria. Destaca de manera extraordinaria el área física, dándole total importancia a cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad, y sacrificio.',
            64 => 'La importancia que le da a la rectitud es más alta que lo normal. Destaca el área física, dándole muy buena prioridad a cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad, y sacrificio.',
            54 => 'Le da un poco más de importancia a la rectitud que el resto de la población. Destaca el área física, dándole buena prioridad a cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad y sacrificio.',
            44 => 'El valor rectitud recibe una calificación dentro de lo esperado. La parte física de sus valores se muestra equilibrada, dándole adecuada importancia a cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad y sacrificio.',
            34 => 'La importancia del valor rectitud no es fundamental. La parte física de sus valores es colocada en segundo plano, dándole un poco menos importancia a cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad y sacrificio.',
            24 => 'Le otorga baja importancia al valor rectitud. La parte física de sus valores es negada, importándole poco cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad, y sacrificio.',
            0  => 'Le otorga muy baja importancia al valor rectitud. La parte física de sus valores es totalmente ignorada, sin importar cualidades como compromiso, confiabilidad, deber, cumplir metas, respeto, responsabilidad, y sacrificio.',
        ],
        'PAZ' => [
            74 => 'Valora la paz de manera extraordinaria. Resalta fuertemente la parte emocional de sus valores, dándole total importancia a cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            64 => 'La valoración de la paz es mucho más alta que lo normal. Resalta la parte emocional de sus valores, dándole muy buena prioridad a cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            54 => 'La valoración de la paz es más alta que lo normal. Resalta la parte emocional de sus valores, dándole buena prioridad a cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            44 => 'La valoración de la paz está dentro de la norma de la población. La parte emocional de sus valores está equilibrada, dándole adecuada importancia a cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            34 => 'La valoración de la paz no es tan prioritaria. La parte emocional de sus valores es puesta en segundo plano, dándole un poco menos importancia a cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            24 => 'Baja valoración de la paz. La parte emocional de sus valores es ignorada, importándole poco cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
            0  => 'Muy baja valoración de la paz. La parte emocional de sus valores es completamente ignorada, sin importar cualidades como calma, concentración, paciencia, reflexión, satisfacción, y silencio interior.',
        ],
        'AMOR' => [
            74 => 'Valora el amor de manera extraordinaria. Resalta fuertemente la parte intuitiva de sus valores, dándole total importancia a cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            64 => 'El amor impacta su vida mucho más que al resto de la gente. Resalta la parte intuitiva de sus valores, dándole muy buena prioridad a cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            54 => 'Impacta en su vida el amor un poco más que al resto de la gente. Resalta la parte intuitiva de sus valores, dándole buena prioridad a cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            44 => 'El valor amor le impacta dentro de la norma del grupo. La parte intuitiva de sus valores está equilibrada, dándole adecuada importancia a cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            34 => 'El impacto del valor amor no es fundamental. La parte intuitiva de sus valores es puesta en segundo plano, dándole un poco menos importancia a cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            24 => 'Bajo impacto en su vida del valor amor. La parte intuitiva de sus valores es ignorada, importándole poco cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
            0  => 'Muy bajo impacto en su vida del valor amor. La parte intuitiva de sus valores es completamente ignorada, sin importar cualidades como amabilidad, amistad, ayuda, apoyo, compartir, compasión, cuidado, perdón, simpatía y tolerancia.',
        ],
        'NO_VIOLENCIA' => [
            74 => 'Valora la no violencia de manera extraordinaria. Resalta fuertemente la parte espiritual de sus valores, dándole total importancia a cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            64 => 'Valora la no violencia mucho más que el resto de la gente. Resalta la parte espiritual de sus valores, dándole muy buena prioridad a cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            54 => 'Valora la no violencia un poco más que el resto de la gente. Resalta la parte espiritual de sus valores, dándole buena prioridad a cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            44 => 'El valor no violencia está dentro de lo normal. La parte espiritual de sus valores está equilibrada, dándole adecuada importancia a cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            34 => 'El valor no violencia no es básico. La parte espiritual de valores es puesta en segundo plano, dándole menos importancia a cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            24 => 'Bajo predominio del valor no violencia. La parte espiritual de sus valores es ignorada, importándole poco cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
            0  => 'Muy bajo predominio del valor no violencia. La parte espiritual de sus valores es completamente ignorada, sin importar cualidades como amor universal, aprecio por las demás culturas y creencias, hermandad, justicia social y unidad humana.',
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
     * Devuelve el escalón de corte según la tabla LOOKUP oficial de Excel ('.'!$A$2:$B$8):
     * Cortes: 0, 24, 34, 44, 54, 64, 74
     */
    public static function obtenerEscalonLookup(int|float $pe): int
    {
        if ($pe >= 74) {
            return 74;
        }
        if ($pe >= 64) {
            return 64;
        }
        if ($pe >= 54) {
            return 54;
        }
        if ($pe >= 44) {
            return 44;
        }
        if ($pe >= 34) {
            return 34;
        }
        if ($pe >= 24) {
            return 24;
        }

        return 0;
    }

    /**
     * Devuelve la interpretación cualitativa del Puntaje Estándar (PE)
     * idéntica a la fórmula =LOOKUP(PE, '.'!$A$2:$B$8) del Excel oficial VALANTI:
     * - >= 74: "Muy alto"
     * - 64 a 73: "Alto"
     * - 54 a 63: "Promedio Alto"
     * - 44 a 53: "Promedio"
     * - 34 a 43: "Promedio Bajo"
     * - 24 a 33: "Bajo"
     * - < 24: "Muy bajo"
     */
    public static function interpretarPuntajeEstandar(int|float $pe): string
    {
        return match (self::obtenerEscalonLookup($pe)) {
            74 => 'Muy alto',
            64 => 'Alto',
            54 => 'Promedio Alto',
            44 => 'Promedio',
            34 => 'Promedio Bajo',
            24 => 'Bajo',
            default => 'Muy bajo',
        };
    }

    /**
     * Devuelve los asteriscos de nivel (de * a *******) idénticos a =LOOKUP(PE, '.'!$D$2:$E$8) del Excel.
     */
    public static function asteriscosPuntajeEstandar(int|float $pe): string
    {
        return match (self::obtenerEscalonLookup($pe)) {
            74 => '*******',
            64 => '******',
            54 => '*****',
            44 => '****',
            34 => '***',
            24 => '**',
            default => '*',
        };
    }

    /**
     * Genera la interpretación narrativa completa del Cuestionario VALANTI
     * (resumen de área más/menos importante + los 5 párrafos descriptivos por dimensión).
     *
     * @param array<string, int|float> $pePorDimension ['VERDAD' => 55, 'RECTITUD' => 43, ...]
     * @return array{resumen: string, parrafos: array<string, string>}
     */
    public static function generarNarrativaCompleta(string $nombreCandidato, array $pePorDimension): array
    {
        // 1. Determinar área más importante y menos importante (MAX y MIN igual que en '.'!G10:K11)
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
            $pe = $pePorDimension[$dimKey] ?? 50;
            $parrafos[$dimKey] = self::generarParrafoDimension($dimKey, $pe, $info);
        }

        return [
            'resumen' => $resumen,
            'parrafos' => $parrafos,
        ];
    }

    /**
     * Devuelve el párrafo interpretativo oficial de una dimensión según su Puntaje Estándar (PE),
     * idéntico a =LOOKUP(PE, '.'!F2:F8, '.'!G2:K8) de la hoja '.' del Excel de VALANTI.
     */
    public static function generarParrafoDimension(string $dimKey, int|float $pe, array $info = []): string
    {
        $escalon = self::obtenerEscalonLookup($pe);
        return self::TEXTOS_INTERPRETATIVOS_EXCEL[$dimKey][$escalon]
            ?? self::TEXTOS_INTERPRETATIVOS_EXCEL[$dimKey][44];
    }
}
