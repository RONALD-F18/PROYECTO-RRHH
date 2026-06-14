<?php

namespace App\Repositories\Eloquent;

use App\Models\PrestacionSocialPeriodo;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\PrestacionSocialInterface;
use Illuminate\Support\Facades\DB;

class PrestacionSocialRepository implements PrestacionSocialInterface
{
    public function getAllPrestacionesSociales(): Collection
    {
        return PrestacionSocialPeriodo::with('contrato.empleado', 'contrato.cargo')->orderBy('fecha_periodo_fin', 'desc')->get();
    }

    public function getPrestacionSocialById($cod_prestacion_social_periodo): ?PrestacionSocialPeriodo
    {
        $prestacion = PrestacionSocialPeriodo::with('contrato.empleado', 'contrato.cargo')->find($cod_prestacion_social_periodo);
        return $prestacion ?: null;
    }

    public function createPrestacionSocial(array $data): PrestacionSocialPeriodo
    {
        return PrestacionSocialPeriodo::create($data);
    }

    public function getTotalesPendientes(): array
    {
        $pendientes = DB::table('prestacion_social_periodo')
            ->where('estado_pago', 'Pendiente')
            ->selectRaw('
                COUNT(*) AS cantidad_pendientes,
                COALESCE(SUM(cesantias_valor), 0) AS total_cesantias,
                COALESCE(SUM(intereses_cesantias_valor), 0) AS total_intereses,
                COALESCE(SUM(prima_valor), 0) AS total_prima,
                COALESCE(SUM(vacaciones_valor), 0) AS total_vacaciones
            ')
            ->first();

        $pagados = DB::table('prestacion_social_periodo')
            ->whereIn('estado_pago', ['Pagado', 'Trasladado'])
            ->selectRaw('
                COUNT(*) AS cantidad_pagados,
                COALESCE(SUM(cesantias_valor), 0) AS total_cesantias,
                COALESCE(SUM(intereses_cesantias_valor), 0) AS total_intereses,
                COALESCE(SUM(prima_valor), 0) AS total_prima,
                COALESCE(SUM(vacaciones_valor), 0) AS total_vacaciones
            ')
            ->first();

        $totalCesantias = (float) ($pendientes->total_cesantias ?? 0);
        $totalIntereses = (float) ($pendientes->total_intereses ?? 0);
        $totalPrima = (float) ($pendientes->total_prima ?? 0);
        $totalVacaciones = (float) ($pendientes->total_vacaciones ?? 0);

        return [
            'cantidad_periodos_pendientes' => (int) ($pendientes->cantidad_pendientes ?? 0),
            'cantidad_periodos_pagados' => (int) ($pagados->cantidad_pagados ?? 0),
            'total_cesantias' => round($totalCesantias, 2),
            'total_intereses' => round($totalIntereses, 2),
            'total_prima' => round($totalPrima, 2),
            'total_vacaciones' => round($totalVacaciones, 2),
            'total_general_pendiente' => round($totalCesantias + $totalIntereses + $totalPrima + $totalVacaciones, 2),
            'totales_pagados' => [
                'total_cesantias' => round((float) ($pagados->total_cesantias ?? 0), 2),
                'total_intereses' => round((float) ($pagados->total_intereses ?? 0), 2),
                'total_prima' => round((float) ($pagados->total_prima ?? 0), 2),
                'total_vacaciones' => round((float) ($pagados->total_vacaciones ?? 0), 2),
            ],
        ];
    }

    public function getByContratoId($cod_contrato): Collection
    {
        return PrestacionSocialPeriodo::where('cod_contrato', $cod_contrato)
            ->orderBy('fecha_periodo_fin', 'desc')
            ->get();
    }

    public function getUltimoPeriodoByContratoId($cod_contrato): ?PrestacionSocialPeriodo
    {
        return PrestacionSocialPeriodo::where('cod_contrato', $cod_contrato)
            ->orderBy('fecha_periodo_fin', 'desc')
            ->first();
    }

    public function actualizarEstado($cod_prestacion_social_periodo, string $estado_pago): ?PrestacionSocialPeriodo
    {
        $prestacion = PrestacionSocialPeriodo::find($cod_prestacion_social_periodo);
        if (!$prestacion) {
            return null;
        }
        $prestacion->estado_pago = $estado_pago;
        $prestacion->fecha_pago_cancelacion = in_array($estado_pago, ['Pagado', 'Trasladado']) ? now()->toDateString() : null;
        $prestacion->save();
        return $prestacion;
    }

    public function deletePrestacionSocial($cod_prestacion_social_periodo): bool
    {
        $prestacion = PrestacionSocialPeriodo::find($cod_prestacion_social_periodo);
        if (!$prestacion) {
            return false;
        }
        $prestacion->delete();
        return true;
    }
}
