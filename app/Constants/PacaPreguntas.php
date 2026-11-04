<?php

namespace App\Constants;

class PacaPreguntas
{
    /**
     * Opciones válidas de respuesta con su descripción y ponderación oficial.
     */
    public const OPCIONES = [
        'S' => ['codigo' => 'S', 'nombre' => 'Siempre', 'puntaje' => 2.0],
        'F' => ['codigo' => 'F', 'nombre' => 'Frecuentemente', 'puntaje' => 1.0],
        'R' => ['codigo' => 'R', 'nombre' => 'Rara vez', 'puntaje' => 0.5],
        'N' => ['codigo' => 'N', 'nombre' => 'Nunca', 'puntaje' => 0.0],
    ];

    /**
     * Las 40 afirmaciones transcritas fielmente del formato institucional PAC-A (GH-FO).
     */
    public const AFIRMACIONES = [
        1 => 'Siento que no me alcanza el tiempo para hacer todo lo que tengo que hacer.',
        2 => 'Olvido felicitar a otras personas por sus logros.',
        3 => 'Prefiero trabajar más solo que en grupo.',
        4 => 'Soy una persona perfeccionista.',
        5 => 'Me preocupo cuando no logro algo que me había propuesto.',
        6 => 'Me aburro fácilmente.',
        7 => 'Interrumpo a otros cuando están hablando.',
        8 => 'Me siento muy orgulloso de mis logros.',
        9 => 'Busco en mi trabajo tener una relación amistosa con mi jefe.',
        10 => 'Puedo estar tranquilo cuando me quedo quieto y sentado dispuesto a descansar.',
        11 => 'Pienso que mi trabajo es lo más importante en mi vida.',
        12 => 'Me siento culpable por los problemas que hay en mi familia.',
        13 => 'Hago la mayoría de mis actividades, lo más rápido posible.',
        14 => 'Fácilmente me molesta que las cosas no salgan como yo quiero.',
        15 => 'Tengo todo lo que necesito a nivel personal, para hacer todo lo que se me pida.',
        16 => 'Me molesta que me acosen y apuren.',
        17 => 'Le dedico todo el tiempo necesario a mis actividades laborales.',
        18 => 'Tiendo a preocuparme al sentir algún dolor y sentirme enfermo.',
        19 => 'Me molestan eventos poco importantes.',
        20 => 'Aprovecho mi hora de almuerzo para relajarme y desconectarme del trabajo.',
        21 => 'Logro que las personas hagan lo que yo quiero.',
        22 => 'En todas las actividades que realizo, siento la necesidad de ganar y triunfar.',
        23 => 'Si estoy descansando, sin hacer nada, siento que estoy perdiendo mi tiempo.',
        24 => 'Cuando me he ausentado de mi trabajo, ha sido por causa de salud.',
        25 => 'Soy de temperamento calmado.',
        26 => 'Pienso que la mayoría de las personas no pueden hacer las cosas perfectas.',
        27 => 'En el trabajo se deben tener compañeros, pero no amigos.',
        28 => 'Soy una persona ambiciosa.',
        29 => 'Cuando me hablan, intento escuchar, pero realmente pienso en otra cosa.',
        30 => 'Me interesa dedicar más tiempo a actividades extra laborales.',
        31 => 'Me fijo metas generalmente muy altas a nivel laboral.',
        32 => 'Camino rápidamente.',
        33 => 'Con facilidad me golpeo con diferentes objetos.',
        34 => 'Me molesta perder el control sobre las cosas, las situaciones o las personas.',
        35 => 'Le cuento a otros acerca de mis logros y triunfos.',
        36 => 'Deseo superar a los demás, soy competitivo.',
        37 => 'Tiendo a terminar las frases de los otros, cuando están hablando.',
        38 => 'Me interesa establecer vínculos afectivos con mis compañeros de trabajo.',
        39 => 'En las reuniones tiendo a hablar rápido y fuerte.',
        40 => 'Juego mucho con los objetos que tengo en las manos.',
    ];

    /**
     * Preguntas que participan en el Indicador de Tendencia Accidentógena (12 ítems).
     * Correspondencia con Excel: B36, B39, B42, D35, D39, D41, F34, F37, F40, H36, H41, H43
     */
    public const PREGUNTAS_TENDENCIA_ACCIDENTOGENA = [
        3, 6, 9, 12, 16, 18, 21, 24, 27, 33, 38, 40
    ];

    /**
     * Preguntas que participan en el Indicador de Patrón de Comportamiento Tipo A (20 ítems).
     * Correspondencia con Excel:
     * B34, B35, B37, B41 (1, 2, 4, 8)
     * D34, D36:D37, D40, D42 (11, 13, 14, 17, 19)
     * F35, F36, F39, F41, F42 (22, 23, 26, 28, 29)
     * H34, H35, H37, H38, H40, H42 (31, 32, 34, 35, 37, 39)
     */
    public const PREGUNTAS_PATRON_TIPO_A = [
        1, 2, 4, 8, 11, 13, 14, 17, 19, 22, 23, 26, 28, 29, 31, 32, 34, 35, 37, 39
    ];

    /**
     * Preguntas de Control / Complementarias (8 ítems).
     * Ítems institucionales del instrumento que no computan puntaje de riesgo directo
     * pero complementan el perfil de respuesta del evaluado (ítems de amortiguación / validez).
     */
    public const PREGUNTAS_CONTROL = [
        5, 7, 10, 15, 20, 25, 30, 36
    ];

    /**
     * Retorna los metadatos de categoría / indicador para una pregunta (1 a 40).
     */
    public static function categoriaDePregunta(int $numero): array
    {
        if (in_array($numero, self::PREGUNTAS_TENDENCIA_ACCIDENTOGENA, true)) {
            return [
                'clave' => 'accidentogena',
                'nombre' => 'Tendencia Accidentógena',
                'indicador' => 'Indicador I',
                'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200',
                'pill_border' => 'border-rose-300',
                'dot_color' => 'bg-rose-500',
                'icono' => 'fa-solid fa-triangle-exclamation',
                'puntua' => true,
                'max_pts' => 2.0,
            ];
        }

        if (in_array($numero, self::PREGUNTAS_PATRON_TIPO_A, true)) {
            return [
                'clave' => 'tipo_a',
                'nombre' => 'Patrón Tipo A',
                'indicador' => 'Indicador II',
                'badge_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'pill_border' => 'border-indigo-300',
                'dot_color' => 'bg-indigo-500',
                'icono' => 'fa-solid fa-bolt',
                'puntua' => true,
                'max_pts' => 2.0,
            ];
        }

        return [
            'clave' => 'control',
            'nombre' => 'Complementaria / Control',
            'indicador' => 'Control',
            'badge_class' => 'bg-slate-100 text-slate-700 border-slate-200',
            'pill_border' => 'border-slate-300',
            'dot_color' => 'bg-slate-400',
            'icono' => 'fa-solid fa-shield-halved',
            'puntua' => false,
            'max_pts' => 0.0,
        ];
    }

    /**
     * Obtiene el valor numérico para una opción de respuesta.
     */
    public static function puntajeDeOpcion(string $opcion): float
    {
        $op = strtoupper(trim($opcion));
        return self::OPCIONES[$op]['puntaje'] ?? 0.0;
    }
}
