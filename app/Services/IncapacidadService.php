<?php

namespace App\Services;

use App\Models\Incapacidad;
use App\Repositories\Interfaces\IncapacidadInterface;
use App\Repositories\Interfaces\ContratoInterface;
use Carbon\Carbon;

/**
 * Lógica de negocio del módulo Incapacidades (solo entidad Incapacidad).
 * Normativa colombiana: distribución de pago según tipo (clave_normativa) y días.
 * - Origen común: días 1-2 Empresa 100%, 3-90 EPS 66.67%, 91-180 EPS 50%, 181+ Pensiones 50%.
 * - Laboral: ARL 100% desde día 1. Maternidad: EPS 100% (máx 126 días). Paternidad: EPS 100% (máx 14 días).
 */
class IncapacidadService
{
    /** Enfermedad general: días 1-2 Empresa 100%. */
    private const ENFERMEDAD_GENERAL_DIAS_EMPRESA = 2;
    /** Enfermedad general: días 3 a 90 (88 días) EPS 66.67%. */
    private const ENFERMEDAD_GENERAL_EPS_DIAS_3_90 = 88;
    private const ENFERMEDAD_GENERAL_EPS_PORC_3_90 = 0.6667;
    /** Enfermedad general: días 91 a 180 (90 días) EPS 50%. */
    private const ENFERMEDAD_GENERAL_EPS_DIAS_91_180 = 90;
    private const ENFERMEDAD_GENERAL_EPS_PORC_91_180 = 0.50;
    /** Días por mes para salario diario (normativa colombiana). */
    private const DIAS_MES = 30;

    protected $incapacidadRepository;
    protected $contratoRepository;

    public function __construct(
        IncapacidadInterface $incapacidadRepository,
        ContratoInterface $contratoRepository
    ) {
        $this->incapacidadRepository = $incapacidadRepository;
        $this->contratoRepository = $contratoRepository;
    }

    public function getAllIncapacidades()
    {
        return $this->incapacidadRepository->getAllIncapacidades();
    }

    public function getIncapacidadById($cod_incapacidad)
    {
        return $this->incapacidadRepository->getIncapacidadById($cod_incapacidad);
    }

    public function getByEmpleadoId($cod_empleado)
    {
        return $this->incapacidadRepository->getByEmpleadoId($cod_empleado);
    }

    /**
     * Calcula días laborales entre fecha_inicio y fecha_fin (inclusive).
     */
    public function calcularDiasIncapacidad(string $fechIni, string $fechFin): int
    {
        $inicio = Carbon::parse($fechIni)->startOfDay();
        $fin = Carbon::parse($fechFin)->startOfDay();
        if ($fin->lt($inicio)) {
            return 0;
        }
        return $inicio->diffInDays($fin) + 1;
    }

    /**
     * Distribución de pago según normativa colombiana por tipo y días.
     * clave_normativa del tipo: origen_comun, laboral, maternidad, paternidad.
     */
    public function calcularDistribucionPagos(Incapacidad $incapacidad): array
    {
        $dias = $this->calcularDiasIncapacidad($incapacidad->fecha_inicio->toDateString(), $incapacidad->fecha_fin->toDateString());
        $contrato = $this->contratoRepository->GetContratoVigenteByEmpleadoId($incapacidad->cod_empleado);
        $salarioBase = $contrato ? (float) $contrato->salario_base : 0;
        $salarioDiario = $salarioBase > 0 ? round($salarioBase / self::DIAS_MES, 2) : 0;

        $diasEmpresa = 0;
        $diasEps = 0;
        $diasArl = 0;
        $diasPensiones = 0;
        $montoEmpresa = 0.0;
        $montoEps = 0.0;
        $montoArl = 0.0;
        $montoPensiones = 0.0;
        $entidadResponsable = '';

        $tipo = $incapacidad->tipoIncapacidad;
        $clave = $tipo ? strtolower($tipo->clave_normativa ?? '') : '';

        if ($clave === 'laboral') {
            // Accidente/Enfermedad laboral: ARL 100% desde día 1 (hasta 180 prorrogables).
            $diasArl = $dias;
            $montoArl = round($diasArl * $salarioDiario, 2);
            $entidadResponsable = 'ARL';
        } elseif ($clave === 'maternidad') {
            // Licencia maternidad: 18 semanas (126 días), EPS 100%.
            $diasEps = min($dias, 126);
            $montoEps = round($diasEps * $salarioDiario, 2);
            $entidadResponsable = 'EPS';
        } elseif ($clave === 'paternidad') {
            // Licencia paternidad: 2 semanas (14 días), EPS 100%.
            $diasEps = min($dias, 14);
            $montoEps = round($diasEps * $salarioDiario, 2);
            $entidadResponsable = 'EPS';
        } else {
            // Enfermedad general (origen común): 1-2 Empresa 100%, 3-90 EPS 66.67%, 91-180 EPS 50%, 181+ Pensiones 50%.
            $resto = $dias;
            if ($resto > 0) {
                $diasEmpresa = min($resto, self::ENFERMEDAD_GENERAL_DIAS_EMPRESA);
                $montoEmpresa = round($diasEmpresa * $salarioDiario, 2);
                $resto -= $diasEmpresa;
            }
            if ($resto > 0) {
                $dias3_90 = min($resto, self::ENFERMEDAD_GENERAL_EPS_DIAS_3_90);
                $montoEps += round($dias3_90 * $salarioDiario * self::ENFERMEDAD_GENERAL_EPS_PORC_3_90, 2);
                $diasEps += $dias3_90;
                $resto -= $dias3_90;
            }
            if ($resto > 0) {
                $dias91_180 = min($resto, self::ENFERMEDAD_GENERAL_EPS_DIAS_91_180);
                $montoEps += round($dias91_180 * $salarioDiario * self::ENFERMEDAD_GENERAL_EPS_PORC_91_180, 2);
                $diasEps += $dias91_180;
                $resto -= $dias91_180;
            }
            if ($resto > 0) {
                $diasPensiones = $resto;
                $montoPensiones = round($diasPensiones * $salarioDiario * 0.50, 2);
            }
            $entidades = array_filter([
                $diasEmpresa > 0 ? 'Empresa' : null,
                $diasEps > 0 ? 'EPS' : null,
                $diasArl > 0 ? 'ARL' : null,
                $diasPensiones > 0 ? 'Pensiones' : null,
            ]);
            $entidadResponsable = implode(', ', $entidades) ?: 'EPS';
        }

        $totalPagado = round($montoEmpresa + $montoEps + $montoArl + $montoPensiones, 2);

        return [
            'dias_totales' => $dias,
            'dias_empresa' => $diasEmpresa,
            'dias_eps' => $diasEps,
            'dias_arl' => $diasArl,
            'dias_pensiones' => $diasPensiones,
            'monto_empresa' => $montoEmpresa,
            'monto_eps' => $montoEps,
            'monto_arl' => $montoArl,
            'monto_pensiones' => $montoPensiones,
            'total_pagado' => $totalPagado,
            'entidad_responsable' => $entidadResponsable,
            'salario_base' => $salarioBase,
            'salario_diario' => $salarioDiario,
        ];
    }

    /**
     * Crea incapacidad y asigna entidad_responsable según normativa.
     */
    public function createIncapacidad(array $data): Incapacidad
    {
        if (empty($data['fecha_radicacion'])) {
            $data['fecha_radicacion'] = now()->toDateString();
        }
        $incapacidad = $this->incapacidadRepository->createIncapacidad($data);
        $dist = $this->calcularDistribucionPagos($incapacidad->fresh(['tipoIncapacidad']));
        $incapacidad->update(['entidad_responsable' => $dist['entidad_responsable']]);
        return $incapacidad->fresh(['tipoIncapacidad', 'empleado', 'clasificacionEnfermedad']);
    }

    public function updateIncapacidad($cod_incapacidad, array $data): ?Incapacidad
    {
        $incapacidad = $this->incapacidadRepository->updateIncapacidad($cod_incapacidad, $data);
        if (!$incapacidad) {
            return null;
        }
        $dist = $this->calcularDistribucionPagos($incapacidad->fresh(['tipoIncapacidad']));
        $incapacidad->update(['entidad_responsable' => $dist['entidad_responsable']]);
        return $incapacidad->fresh(['tipoIncapacidad', 'empleado', 'clasificacionEnfermedad']);
    }

    public function deleteIncapacidad($cod_incapacidad): bool
    {
        return $this->incapacidadRepository->deleteIncapacidad($cod_incapacidad);
    }

    /**
     * Resumen para dashboard: totales por estado, por tipo, costo total.
     */
    public function getResumen(): array
    {
        $todas = $this->incapacidadRepository->getAllIncapacidades();
        $activas = $todas->where('estado_incapacidad', 'Activa');
        $totalDias = 0;
        $costoTotal = 0.0;
        foreach ($activas as $inc) {
            $d = $this->calcularDistribucionPagos($inc);
            $totalDias += $d['dias_totales'];
            $costoTotal += $d['total_pagado'];
        }
        $origenComun = $todas->filter(function ($i) {
            $t = $i->tipoIncapacidad;
            return $t && strtolower($t->clave_normativa ?? '') === 'origen_comun';
        })->count();
        $laboral = $todas->filter(function ($i) {
            $t = $i->tipoIncapacidad;
            return $t && strtolower($t->clave_normativa ?? '') === 'laboral';
        })->count();

        return [
            'total' => $todas->count(),
            'activas' => $activas->count(),
            'origen_comun' => $origenComun,
            'laboral' => $laboral,
            'total_dias' => $totalDias,
            'costo_total' => round($costoTotal, 2),
        ];
    }
}
