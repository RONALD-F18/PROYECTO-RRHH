<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRequest;
use App\Models\Empresa;
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
            $validated['params'] ?? [],
            auth()->user()?->cod_usuario
        );

        $empresa = Empresa::query()->orderBy('id_empresa')->first();

        $pdf = Pdf::loadView('reportes.general', [
            'reporte' => $reporte,
            'empresa' => $empresa,
        ])->setPaper('letter', 'portrait');

        $filename = 'reporte-' . $validated['modulo'] . '-' . now()->format('YmdHis') . '.pdf';

        return $pdf->stream($filename);
    }
}

