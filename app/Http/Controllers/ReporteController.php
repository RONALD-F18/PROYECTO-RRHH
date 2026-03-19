<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRequest;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function __construct(
        protected ReporteService $reporteService
    ) {
    }

    /**
     * Genera un reporte en PDF por módulo y tipo.
     * Valida la estructura de entrada mediante ReporteRequest.
     */
    public function generar(ReporteRequest $request)
    {
        $validated = $request->validated();

        $reporte = $this->reporteService->generarReporte(
            $validated['modulo'],
            $validated['tipo'],
            $validated['params'] ?? []
        );

        $pdf = Pdf::loadView('reportes.general', [
            'reporte' => $reporte,
        ])->setPaper('letter', 'portrait');

        $filename = 'reporte-' . $validated['modulo'] . '-' . now()->format('YmdHis') . '.pdf';

        return $pdf->stream($filename);
    }
}

