<?php

namespace App\Services\Psicologia;

use App\Models\EvaluacionCandidato;
use Illuminate\Support\Collection;
use RuntimeException;
use ZipArchive;

class ReporteExcelPsicologiaService
{
    /**
     * Genera un archivo Excel (.xlsx) nativo OpenXML con la estructura y estilos
     * idénticos al formato institucional PRUEBAS APLICADAS {AÑO}.xlsx.
     *
     * @param Collection<int, EvaluacionCandidato> $evaluaciones
     * @param int $anio
     * @return string Ruta temporal del archivo .xlsx generado
     */
    public function generarArchivoXlsx(Collection $evaluaciones, int $anio): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pruebas_psicologia_') . '.xlsx';

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No fue posible crear el archivo Excel temporal.');
        }

        $nombreHoja = (string) $anio;

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->buildRootRelsXml());
        $zip->addFromString('docProps/app.xml', $this->buildAppXml($nombreHoja));
        $zip->addFromString('docProps/core.xml', $this->buildCoreXml($anio));
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbookXml($nombreHoja));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->buildStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($evaluaciones));

        $zip->close();

        return $tempFile;
    }

    private function xmlEscape(string $valor): string
    {
        // Eliminar caracteres de control inválidos en XML 1.0
        $limpio = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $valor) ?? $valor;

        return htmlspecialchars($limpio, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function buildContentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
    }

    private function buildRootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
    }

    private function buildAppXml(string $nombreHoja): string
    {
        $hojaEsc = $this->xmlEscape($nombreHoja);

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>C.I. Piscícola New York S.A. - Psicología</Application>
    <HeadingPairs>
        <vt:vector size="2" baseType="variant">
            <vt:variant><vt:lpstr>Hojas de cálculo</vt:lpstr></vt:variant>
            <vt:variant><vt:i4>1</vt:i4></vt:variant>
        </vt:vector>
    </HeadingPairs>
    <TitlesOfParts>
        <vt:vector size="1" baseType="lpstr">
            <vt:lpstr>{$hojaEsc}</vt:lpstr>
        </vt:vector>
    </TitlesOfParts>
</Properties>
XML;
    }

    private function buildCoreXml(int $anio): string
    {
        $nowIso = gmdate('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:title>PRUEBAS APLICADAS {$anio}</dc:title>
    <dc:creator>C.I. Piscícola New York S.A.</dc:creator>
    <dcterms:created xsi:type="dcterms:W3CDTF">{$nowIso}</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">{$nowIso}</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function buildWorkbookXml(string $nombreHoja): string
    {
        $hojaEsc = $this->xmlEscape($nombreHoja);

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="{$hojaEsc}" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
    }

    private function buildWorkbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
XML;
    }

    /**
     * Estilos del reporte:
     * - s="0": Por defecto
     * - s="1": Encabezado verde institucional (#A8D08D), Calibri 10pt Negrita, centrado con ajuste de texto y bordes finos
     * - s="2": Encabezado verde institucional (#A8D08D), Calibri 9pt Negrita, centrado con ajuste de texto y bordes finos (para columnas de CUMPLE)
     * - s="3": Celda de datos centrada (Calibri 10pt, bordes finos)
     * - s="4": Celda de datos centrada en negrita (para la 'X' de CUMPLE, Calibri 11pt, bordes finos)
     * - s="5": Celda de observación (Calibri 8.5pt, centrado vertical, wrapText=1, bordes finos)
     */
    private function buildStylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="5">
        <font><sz val="10"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>
        <font><b/><sz val="10"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>
        <font><b/><sz val="9"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>
        <font><b/><sz val="11"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>
        <font><sz val="8"/><color rgb="FF000000"/><name val="Calibri"/><family val="2"/></font>
    </fonts>
    <fills count="3">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill>
            <patternFill patternType="solid">
                <fgColor rgb="FFA8D08D"/>
                <bgColor indexed="64"/>
            </patternFill>
        </fill>
    </fills>
    <borders count="2">
        <border><left/><right/><top/><bottom/><diagonal/></border>
        <border>
            <left style="thin"><color rgb="FF000000"/></left>
            <right style="thin"><color rgb="FF000000"/></right>
            <top style="thin"><color rgb="FF000000"/></top>
            <bottom style="thin"><color rgb="FF000000"/></bottom>
            <diagonal/>
        </border>
    </borders>
    <cellStyleXfs count="1">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    </cellStyleXfs>
    <cellXfs count="6">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center" wrapText="1"/>
        </xf>
        <xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center" wrapText="1"/>
        </xf>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center" wrapText="1"/>
        </xf>
        <xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center"/>
        </xf>
        <xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
            <alignment horizontal="center" vertical="center" wrapText="1"/>
        </xf>
    </cellXfs>
</styleSheet>
XML;
    }

    /**
     * Construye la hoja con las 10 columnas exactas del Excel PRUEBAS APLICADAS 2026.xlsx:
     * A: N°
     * B: NOMBRE COMPLETO
     * C: NÚMERO DE DOCUMENTO
     * D: CARGO PARA DESEMPEÑAR
     * E: FECHA DE APLICACIÓN
     * F: CUMPLE
     * G: CUMPLE SUGIEREN RECOMENDACIONES
     * H: NO CUMPLE CON EL PERFIL
     * I: OBSERVACION
     * J: SEGUIMIENTO
     *
     * @param Collection<int, EvaluacionCandidato> $evaluaciones
     */
    private function buildSheetXml(Collection $evaluaciones): string
    {
        $rowsXml = '';

        // Fila 1: Encabezados exactos del archivo Excel cargado por el usuario
        $headers = [
            'A' => ['texto' => 'N°', 'style' => 1],
            'B' => ['texto' => 'NOMBRE COMPLETO', 'style' => 1],
            'C' => ['texto' => 'NÚMERO DE DOCUMENTO', 'style' => 1],
            'D' => ['texto' => 'CARGO PARA DESEMPEÑAR', 'style' => 1],
            'E' => ['texto' => 'FECHA DE APLICACIÓN', 'style' => 1],
            'F' => ['texto' => 'CUMPLE', 'style' => 2],
            'G' => ['texto' => 'CUMPLE SUGIEREN RECOMENDACIONES', 'style' => 2],
            'H' => ['texto' => 'NO CUMPLE CON EL PERFIL ', 'style' => 2],
            'I' => ['texto' => 'OBSERVACION', 'style' => 1],
            'J' => ['texto' => 'SEGUIMIENTO ', 'style' => 1],
        ];

        $rowsXml .= '<row r="1" ht="34" customHeight="1">';
        foreach ($headers as $col => $info) {
            $txt = $this->xmlEscape($info['texto']);
            $st = $info['style'];
            $rowsXml .= "<c r=\"{$col}1\" s=\"{$st}\" t=\"inlineStr\"><is><t>{$txt}</t></is></c>";
        }
        $rowsXml .= '</row>';

        // Filas de datos (2 .. N+1)
        $consecutivo = 1;
        foreach ($evaluaciones as $ev) {
            $rowNum = $consecutivo + 1;

            $nombre = $this->xmlEscape(mb_strtoupper(trim((string) $ev->nombre_completo), 'UTF-8'));
            $documento = $this->xmlEscape(trim((string) $ev->identificacion));
            $cargo = $this->xmlEscape(mb_strtoupper(trim((string) ($ev->cargo_aspira ?? '')), 'UTF-8'));
            $fecha = ($ev->fecha_hora_aceptacion ?? $ev->created_at)?->format('d/m/Y') ?? '';
            $fechaEsc = $this->xmlEscape($fecha);

            $concepto = $ev->conceptoAptitudinalActual();
            $marcaCumple = $concepto === 'CUMPLE_SIN_INDICE' ? 'X' : '';
            $marcaRecomendaciones = $concepto === 'CUMPLE_CON_RECOMENDACIONES' ? 'X' : '';
            $marcaNoCumple = $concepto === 'NO_CUMPLE' ? 'X' : '';

            $observacion = $this->xmlEscape($ev->observacionReporteExcel());

            $rowsXml .= "<row r=\"{$rowNum}\" ht=\"24\" customHeight=\"1\">";
            $rowsXml .= "<c r=\"A{$rowNum}\" s=\"3\"><v>{$consecutivo}</v></c>";
            $rowsXml .= "<c r=\"B{$rowNum}\" s=\"3\" t=\"inlineStr\"><is><t>{$nombre}</t></is></c>";
            $rowsXml .= "<c r=\"C{$rowNum}\" s=\"3\" t=\"inlineStr\"><is><t>{$documento}</t></is></c>";
            $rowsXml .= "<c r=\"D{$rowNum}\" s=\"3\" t=\"inlineStr\"><is><t>{$cargo}</t></is></c>";
            $rowsXml .= "<c r=\"E{$rowNum}\" s=\"3\" t=\"inlineStr\"><is><t>{$fechaEsc}</t></is></c>";
            $rowsXml .= "<c r=\"F{$rowNum}\" s=\"4\" t=\"inlineStr\"><is><t>{$marcaCumple}</t></is></c>";
            $rowsXml .= "<c r=\"G{$rowNum}\" s=\"4\" t=\"inlineStr\"><is><t>{$marcaRecomendaciones}</t></is></c>";
            $rowsXml .= "<c r=\"H{$rowNum}\" s=\"4\" t=\"inlineStr\"><is><t>{$marcaNoCumple}</t></is></c>";
            $rowsXml .= "<c r=\"I{$rowNum}\" s=\"5\" t=\"inlineStr\"><is><t>{$observacion}</t></is></c>";
            $rowsXml .= "<c r=\"J{$rowNum}\" s=\"3\" t=\"inlineStr\"><is><t></t></is></c>";
            $rowsXml .= '</row>';

            $consecutivo++;
        }

        $ultimaFila = max(1, $evaluaciones->count() + 1);

        // Anchos de columna idénticos a la hoja 2026 del Excel cargado
        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <cols>
        <col min="1" max="1" width="6" customWidth="1"/>
        <col min="2" max="2" width="34" customWidth="1"/>
        <col min="3" max="3" width="16" customWidth="1"/>
        <col min="4" max="4" width="30" customWidth="1"/>
        <col min="5" max="5" width="13" customWidth="1"/>
        <col min="6" max="6" width="9" customWidth="1"/>
        <col min="7" max="7" width="13" customWidth="1"/>
        <col min="8" max="8" width="11" customWidth="1"/>
        <col min="9" max="9" width="46" customWidth="1"/>
        <col min="10" max="10" width="19" customWidth="1"/>
    </cols>
    <sheetData>
        {$rowsXml}
    </sheetData>
    <autoFilter ref="A1:J{$ultimaFila}"/>
</worksheet>
XML;
    }
}

