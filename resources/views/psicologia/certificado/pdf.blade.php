<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Aptitud Psicológica GH-FO-028 - {{ $evaluacion->identificacion }}</title>
    <style>
        @page {
            margin: 18mm 20mm 16mm 20mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 10pt;
            line-height: 1.35;
        }

        /* ENCABEZADO INSTITUCIONAL GH-FO-028 (4 columnas x 2 filas) */
        .tabla-encabezado {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .tabla-encabezado td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
            text-align: center;
        }
        .enc-logo {
            width: 16%;
        }
        .enc-logo img {
            max-width: 75px;
            max-height: 55px;
        }
        .enc-titulo {
            width: 58%;
            font-weight: bold;
            font-size: 9.5pt;
        }
        .enc-meta {
            width: 13%;
            font-weight: bold;
            font-size: 8pt;
            line-height: 1.25;
        }

        /* TABLA DE DATOS DEL EVALUADO */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 10pt;
        }
        .tabla-datos td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        .label-dato {
            font-weight: bold;
        }
        .valor-dato {
            font-weight: normal;
            margin-left: 4px;
        }

        /* TEXTO DE CONSTANCIA */
        .parrafo-constancia {
            text-align: justify;
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.45;
            margin: 16px 0 18px 0;
        }

        /* SECCIÓN DE LOS 3 CUADRITOS Y CRITERIOS */
        .tabla-criterios {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .tabla-criterios td {
            vertical-align: middle;
            padding: 6px 0;
        }
        .col-espacio-izq {
            width: 18%;
        }
        .col-cuadrito {
            width: 9%;
            text-align: center;
            vertical-align: middle;
        }
        .cuadrito {
            width: 34px;
            height: 25px;
            border: 1.5px solid #2f5597;
            display: inline-block;
            text-align: center;
            line-height: 22px;
            background-color: #fff;
        }
        .flechita-check {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 16pt;
            font-weight: bold;
            color: #000;
            line-height: 22px;
        }
        .col-texto-criterio {
            width: 73%;
            text-align: justify;
            font-weight: bold;
            font-size: 8.5pt;
            line-height: 1.35;
            padding-left: 6px;
        }

        /* OBSERVACIONES */
        .seccion-observaciones {
            margin-top: 10px;
            margin-bottom: 26px;
            font-size: 9.5pt;
        }
        .obs-titulo {
            font-weight: bold;
            display: inline;
        }
        .obs-caja-texto {
            margin-top: 4px;
            line-height: 22px;
            font-size: 9.5pt;
            text-align: justify;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            min-height: 22px;
            word-wrap: break-word;
        }
        .linea-vacia {
            width: 100%;
            height: 21px;
            border-bottom: 1px solid #000;
        }

        /* FIRMA PSICÓLOGA */
        .seccion-firma {
            margin-top: 18px;
            margin-bottom: 16px;
            text-align: center;
        }
        .firma-contenedor-img {
            height: 48px;
            margin-bottom: 3px;
            text-align: center;
        }
        .firma-img {
            max-height: 48px;
            max-width: 210px;
            display: inline-block;
        }
        .linea-firma {
            width: 230px;
            border-top: 1.5px solid #000;
            margin: 0 auto 5px auto;
        }
        .texto-psicologa {
            font-weight: bold;
            font-size: 9.5pt;
            letter-spacing: 0.3px;
        }

        /* CONTROL DE CAMBIOS Y PIE DE PÁGINA */
        .bloque-inferior {
            margin-top: 14px;
        }
        .control-titulo {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .tabla-control {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .tabla-control th,
        .tabla-control td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            vertical-align: middle;
        }
        .tabla-control th {
            font-weight: bold;
        }
        .tabla-control td.col-desc {
            text-align: left;
        }
        .ultima-revision {
            margin-top: 12px;
            text-align: right;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    @php
        $ciudadFecha = $evaluacion->certificado_ciudad_fecha
            ?: ('Rivera-Huila, ' . ($evaluacion->certificado_emitido_at ?? now())->format('d/m/Y'));
        $concepto = $evaluacion->certificado_concepto;
        $observaciones = mb_strtoupper(trim((string) ($evaluacion->certificado_observaciones ?? '')), 'UTF-8');
    @endphp

    <!-- ENCABEZADO INSTITUCIONAL GH-FO-028 -->
    <table class="tabla-encabezado">
        <tr>
            <td class="enc-logo" rowspan="2">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo C.I. Piscícola New York">
                @else
                    <strong>PISCÍCOLA NEW YORK</strong>
                @endif
            </td>
            <td class="enc-titulo">
                GESTION HUMANA
            </td>
            <td class="enc-meta">
                <div>CODIGO:</div>
                <div>GH- FO-028</div>
            </td>
            <td class="enc-meta">
                <div>VERSION:</div>
                <div>02</div>
            </td>
        </tr>
        <tr>
            <td class="enc-titulo">
                CERTIFICADO DE APTITUD PSICOLÓGICA
            </td>
            <td class="enc-meta">
                <div>FECHA:</div>
                <div>27/07/2023</div>
            </td>
            <td class="enc-meta">
                <div>PAGINA</div>
                <div>1 de 1</div>
            </td>
        </tr>
    </table>

    <!-- TABLA 1: DATOS DEL EVALUADO -->
    <table class="tabla-datos">
        <tr>
            <td colspan="2">
                <span class="label-dato">Ciudad/Municipio/ Fecha:</span>
                <span class="valor-dato">{{ $ciudadFecha }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label-dato">Nombre del Evaluado:</span>
                <span class="valor-dato">{{ $evaluacion->nombre_completo }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label-dato">Número de cedula:</span>
                <span class="valor-dato">{{ $evaluacion->identificacion }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 55%;">
                <span class="label-dato">Fecha de Nacimiento:</span>
                <span class="valor-dato">{{ $evaluacion->fecha_nacimiento?->format('d/m/Y') }}</span>
            </td>
            <td style="width: 45%;">
                <span class="label-dato">Edad:</span>
                <span class="valor-dato">{{ $evaluacion->edad }} años</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label-dato">Cargo al que aspira:</span>
                <span class="valor-dato">{{ $evaluacion->cargo_aspira }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="label-dato">Pruebas Psicológicas:</span>
                <span class="valor-dato" style="margin-left: 55px; font-weight: bold;">VALANTI / PAC-A</span>
            </td>
        </tr>
    </table>

    <!-- PÁRRAFO DE CONSTANCIA -->
    <div class="parrafo-constancia">
        Hacemos Constar que evaluamos a la persona en mención de acuerdo con los criterios dados por la empresa C.I. Piscícola New York S.A. en relación con: Batería de Pruebas.
    </div>

    <!-- 3 OPCIONES DE CONCEPTO CON SUS CUADRITOS Y FLECHITA -->
    <table class="tabla-criterios">
        <tr>
            <td class="col-espacio-izq"></td>
            <td class="col-cuadrito">
                <div class="cuadrito">
                    @if($concepto === 'CUMPLE_SIN_INDICE')
                        <span class="flechita-check">&#10003;</span>
                    @endif
                </div>
            </td>
            <td class="col-texto-criterio">
                CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCÍCOLA NEW YORK S.A. (EN TÉRMINOS DE: RASGOS DE PERSONALIDAD, TENDENCIA DE VALORES Y SIN INDICE DE ACCIDENTALIDAD.
            </td>
        </tr>
        <tr>
            <td class="col-espacio-izq"></td>
            <td class="col-cuadrito">
                <div class="cuadrito">
                    @if($concepto === 'CUMPLE_CON_RECOMENDACIONES')
                        <span class="flechita-check">&#10003;</span>
                    @endif
                </div>
            </td>
            <td class="col-texto-criterio">
                CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCÍCOLA NEW YORK S.A. (EN TÉRMINOS DE: RASGOS DE PERSONALIDAD, TENDENCIA DE VALORES Y SE SUGIEREN RECOMENDACIONES A NIVEL ACCIDENTÓGENO.
            </td>
        </tr>
        <tr>
            <td class="col-espacio-izq"></td>
            <td class="col-cuadrito">
                <div class="cuadrito">
                    @if($concepto === 'NO_CUMPLE')
                        <span class="flechita-check">&#10003;</span>
                    @endif
                </div>
            </td>
            <td class="col-texto-criterio">
                NO CUMPLE CON EL PERFIL LABORAL ESPERADO POR LA C.I. PISCICOLA NEW YORK S.A.
            </td>
        </tr>
    </table>

    <!-- OBSERVACIONES -->
    <div class="seccion-observaciones">
        @if($observaciones !== '')
            <div class="obs-caja-texto">
                <span class="obs-titulo">OBSERVACIONES:</span>
                <span>{{ $observaciones }}</span>
            </div>
            <div class="linea-vacia"></div>
            <div class="linea-vacia"></div>
        @else
            <div style="border-bottom: 1px solid #000; height: 20px;">
                <span class="obs-titulo" style="background: #fff; padding-right: 6px;">OBSERVACIONES:</span>
            </div>
            <div class="linea-vacia"></div>
            <div class="linea-vacia"></div>
            <div class="linea-vacia"></div>
            <div class="linea-vacia"></div>
        @endif
    </div>

    <!-- FIRMA PSICÓLOGA -->
    <div class="seccion-firma">
        @if(!empty($firmaPsicologaBase64))
            <div class="firma-contenedor-img">
                <img src="{{ $firmaPsicologaBase64 }}" alt="Firma Psicóloga" class="firma-img">
            </div>
        @else
            <div style="height: 30px;"></div>
        @endif
        <div class="linea-firma"></div>
        <div class="texto-psicologa">PSICOLOGA</div>
    </div>

    <!-- CONTROL DE CAMBIOS -->
    <div class="bloque-inferior">
        <div class="control-titulo">CONTROL DE CAMBIOS</div>
        <table class="tabla-control">
            <thead>
                <tr>
                    <th style="width: 9%;">PAG</th>
                    <th style="width: 35%;">DESCRIPCION DEL CAMBIO</th>
                    <th style="width: 9%;">VERSION</th>
                    <th style="width: 12%;">VIGENCIA</th>
                    <th style="width: 11%;">ELABORO</th>
                    <th style="width: 12%;">REVISO</th>
                    <th style="width: 12%;">APROBO</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1de 1</td>
                    <td class="col-desc">Emisión del documento</td>
                    <td>01</td>
                    <td>07/03/2022</td>
                    <td>psicóloga</td>
                    <td>Jefe Gestión Humana</td>
                    <td>Jefe Gestión Humana</td>
                </tr>
                <tr>
                    <td>1 de 1</td>
                    <td class="col-desc">Se adiciona cargo al que aspira y se ajusta el encabezado de acuerdo con el procedimiento de control de documentos.</td>
                    <td>02</td>
                    <td>25/07/2023</td>
                    <td>psicóloga</td>
                    <td>Jefe de gestión Humana</td>
                    <td>Jefe de gestión Humana</td>
                </tr>
                <tr>
                    <td style="height: 14px;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <div class="ultima-revision">
            Ultima Revisión: 11/03/2026
        </div>
    </div>

</body>
</html>

