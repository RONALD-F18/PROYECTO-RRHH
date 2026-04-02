<?php

namespace App\Repositories\Eloquent;

use App\Models\Reporte;
use App\Repositories\Interfaces\ReporteInterface;
use Illuminate\Database\Eloquent\Collection;

class ReporteRepository implements ReporteInterface
{
    public function getAllReportes(): Collection
    {
        return Reporte::with(['empleado', 'contrato', 'usuario'])->orderByDesc('fecha_emision')->get();
    }

    public function getReporteById($cod_reporte): ?Reporte
    {
        $reporte = Reporte::with(['empleado', 'contrato', 'usuario'])->find($cod_reporte);

        return $reporte ?: null;
    }

    public function createReporte(array $data): Reporte
    {
        return Reporte::create($data);
    }

    public function updateReporte($cod_reporte, array $data): ?Reporte
    {
        $reporte = Reporte::find($cod_reporte);

        if (!$reporte) {
            return null;
        }

        $reporte->update($data);

        return $reporte;
    }

    public function deleteReporte($cod_reporte): bool
    {
        $reporte = Reporte::find($cod_reporte);

        if (!$reporte) {
            return false;
        }

        $reporte->delete();

        return true;
    }
}

