<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\ConsentimientoInformado;
use App\Services\Psicologia\ConsentimientoDocumentoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ConsentimientoController extends Controller
{
    /**
     * Vista de gestión y auditoría del consentimiento informado.
     */
    public function show(ConsentimientoInformado $consentimiento)
    {
        $consentimiento->load(['candidato', 'registrador', 'aplicaciones']);
        return view('psicologia.consentimiento.show', compact('consentimiento'));
    }

    /**
     * Descarga segura del archivo DOCX institucional con los datos completados.
     */
    public function descargarDocx(ConsentimientoInformado $consentimiento, ConsentimientoDocumentoService $documentoService): BinaryFileResponse
    {
        $candidato = $consentimiento->candidato;

        // Si el archivo no existe en disco, regenerarlo
        $rutaCompleta = storage_path("app/{$consentimiento->ruta_archivo_docx}");
        if (!$consentimiento->ruta_archivo_docx || !file_exists($rutaCompleta)) {
            $rutaCompleta = $documentoService->generarDocx($consentimiento);
        }

        // Trazabilidad: si aún estaba en 'GENERADO', marcarlo como 'DESCARGADO'
        if ($consentimiento->estado === 'GENERADO') {
            $consentimiento->estado = 'DESCARGADO';
            $consentimiento->save();
        }

        $nombreDescarga = "GH-FO-025_Consentimiento_{$candidato->numero_identificacion}.docx";

        return response()->download($rutaCompleta, $nombreDescarga, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Descarga / visualización segura del documento PDF oficial.
     * Soporta descarga directa o previsualización inline en nueva pestaña (?inline=1).
     */
    public function descargarPdf(Request $request, ConsentimientoInformado $consentimiento, ConsentimientoDocumentoService $documentoService): Response
    {
        $candidato = $consentimiento->candidato;

        $rutaCompleta = storage_path("app/{$consentimiento->ruta_archivo_pdf}");
        if (!$consentimiento->ruta_archivo_pdf || !file_exists($rutaCompleta)) {
            $rutaCompleta = $documentoService->generarPdf($consentimiento);
        }

        // Trazabilidad: si aún estaba en 'GENERADO', marcarlo como 'DESCARGADO'
        if ($consentimiento->estado === 'GENERADO') {
            $consentimiento->estado = 'DESCARGADO';
            $consentimiento->save();
        }

        $nombreDescarga = "GH-FO-025_Consentimiento_{$candidato->numero_identificacion}.pdf";

        if ($request->boolean('inline')) {
            return response()->file($rutaCompleta, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$nombreDescarga}\"",
            ]);
        }

        return response()->download($rutaCompleta, $nombreDescarga, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
