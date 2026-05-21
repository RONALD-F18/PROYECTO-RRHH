<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    /**
     * Resumen agregado para el panel (sustituye 7 listados completos en el front).
     */
    public function resumen()
    {
        return response()->json([
            'message' => 'Resumen del dashboard obtenido exitosamente',
            'data' => $this->dashboardService->getResumen(),
        ]);
    }
}
