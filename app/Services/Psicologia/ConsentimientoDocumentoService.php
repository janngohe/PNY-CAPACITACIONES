<?php

namespace App\Services\Psicologia;

use App\Models\ConsentimientoInformado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RuntimeException;

class ConsentimientoDocumentoService
{
    /**
     * Ruta de la plantilla maestra DOCX institucional.
     */
    protected string $plantillaDocxPath;

    /**
     * Ruta del logo oficial para PDF.
     */
    protected string $logoPath;

    public function __construct()
    {
        $this->plantillaDocxPath = storage_path('app/templates/GH-FO-025 CONSENTIMIENTO INFORMADO.docx');
        $this->logoPath = storage_path('app/templates/logo_consentimiento.jpeg');
    }

    /**
     * Genera el archivo DOCX personalizado a partir de la plantilla institucional.
     */
    public function generarDocx(ConsentimientoInformado $consentimiento): string
    {
        if (!file_exists($this->plantillaDocxPath)) {
            throw new RuntimeException("No se encontró la plantilla institucional en: {$this->plantillaDocxPath}");
        }

        $candidato = $consentimiento->candidato;
        $dirDestino = storage_path("app/consentimientos/{$consentimiento->id}");
        File::ensureDirectoryExists($dirDestino);

        $nombreArchivo = "consentimiento_GH-FO-025_{$candidato->numero_identificacion}.docx";
        $rutaCompletaDocx = "{$dirDestino}/{$nombreArchivo}";
        $rutaRelativa = "consentimientos/{$consentimiento->id}/{$nombreArchivo}";

        // Copiar plantilla a la ruta destino
        copy($this->plantillaDocxPath, $rutaCompletaDocx);

        $zip = new ZipArchive();
        if ($zip->open($rutaCompletaDocx) !== true) {
            throw new RuntimeException("No fue posible abrir el archivo DOCX generado.");
        }

        $xml = $zip->getFromName('word/document.xml');
        if (!$xml) {
            $zip->close();
            throw new RuntimeException("No se encontró word/document.xml dentro del archivo DOCX.");
        }

        // Reemplazo de campos en el documento XML
        $ciudad = htmlspecialchars($consentimiento->ciudad_municipio, ENT_XML1, 'UTF-8');
        $fecha = htmlspecialchars($consentimiento->fecha_consentimiento->format('d/m/Y'), ENT_XML1, 'UTF-8');
        $nombre = htmlspecialchars($candidato->nombre_completo, ENT_XML1, 'UTF-8');
        $cedula = htmlspecialchars($candidato->numero_identificacion, ENT_XML1, 'UTF-8');
        $lugarExp = htmlspecialchars($candidato->lugar_expedicion, ENT_XML1, 'UTF-8');

        // 1. Ciudad/Municipio
        $xml = preg_replace(
            '/(<w:t>Ciudad<\/w:t>.*?<w:t>\/Municipio<\/w:t>.*?<w:t>: )_+(<\/w:t>)/s',
            '${1}' . $ciudad . '${2}',
            $xml
        );
        // Respaldo en caso de variación de runs de guiones
        $xml = str_replace(
            ': __________________________',
            ': ' . $ciudad,
            $xml
        );

        // 2. Fecha
        $xml = preg_replace(
            '/(<w:t>Fecha: )_+(<\/w:t>)/s',
            '${1}' . $fecha . '${2}',
            $xml
        );
        $xml = str_replace(
            'Fecha: ____________________________________',
            'Fecha: ' . $fecha,
            $xml
        );

        // 3. Yo [Nombre]
        $xml = str_replace(
            '<w:t xml:space="preserve">_____________________________________________________________, </w:t>',
            '<w:t xml:space="preserve"> ' . $nombre . ', </w:t>',
            $xml
        );

        // 4. No. [Cédula]
        $xml = str_replace(
            '<w:t xml:space="preserve"> __________________________</w:t>',
            '<w:t xml:space="preserve"> ' . $cedula . '</w:t>',
            $xml
        );

        // 5. expedida en [Lugar]
        $xml = str_replace(
            '<w:t xml:space="preserve">_____________________, </w:t>',
            '<w:t xml:space="preserve">' . $lugarExp . ', </w:t>',
            $xml
        );

        // 6. Cedula en firma
        $xml = str_replace(
            '<w:t xml:space="preserve">edula: </w:t>',
            '<w:t xml:space="preserve">edula: ' . $cedula . '</w:t>',
            $xml
        );

        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        // Actualizar modelo
        $consentimiento->ruta_archivo_docx = $rutaRelativa;
        $consentimiento->save();

        return $rutaCompletaDocx;
    }

    /**
     * Genera el archivo PDF idéntico al formato institucional GH-FO-025.
     */
    public function generarPdf(ConsentimientoInformado $consentimiento): string
    {
        $candidato = $consentimiento->candidato;
        $dirDestino = storage_path("app/consentimientos/{$consentimiento->id}");
        File::ensureDirectoryExists($dirDestino);

        $nombreArchivo = "consentimiento_GH-FO-025_{$candidato->numero_identificacion}.pdf";
        $rutaCompletaPdf = "{$dirDestino}/{$nombreArchivo}";
        $rutaRelativa = "consentimientos/{$consentimiento->id}/{$nombreArchivo}";

        // Cargar logo en base64 para evitar bloqueos de renderizado local
        $logoBase64 = null;
        if (file_exists($this->logoPath)) {
            $tipo = pathinfo($this->logoPath, PATHINFO_EXTENSION);
            $datos = file_get_contents($this->logoPath);
            $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($datos);
        }

        $pdf = Pdf::loadView('psicologia.consentimiento.pdf', [
            'consentimiento' => $consentimiento,
            'candidato' => $candidato,
            'logoBase64' => $logoBase64,
        ])->setPaper('letter', 'portrait');

        $pdf->save($rutaCompletaPdf);

        // Actualizar modelo
        $consentimiento->ruta_archivo_pdf = $rutaRelativa;
        $consentimiento->save();

        return $rutaCompletaPdf;
    }

    /**
     * Genera ambos formatos (DOCX y PDF).
     */
    public function generarDocumentosCompletos(ConsentimientoInformado $consentimiento): array
    {
        $docx = $this->generarDocx($consentimiento);
        $pdf = $this->generarPdf($consentimiento);

        return [
            'docx' => $docx,
            'pdf' => $pdf,
        ];
    }
}
