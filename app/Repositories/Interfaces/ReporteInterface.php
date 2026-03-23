<?php

namespace App\Repositories\Interfaces;

use App\Models\Reporte;
use Illuminate\Database\Eloquent\Collection;

interface ReporteInterface
{
    public function getAllReportes(): Collection;

    public function getReporteById($cod_reporte): ?Reporte;

    public function createReporte(array $data): Reporte;

    public function updateReporte($cod_reporte, array $data): ?Reporte;

    public function deleteReporte($cod_reporte): bool;
}

