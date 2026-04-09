<?php

namespace App\Services;

use App\Repositories\Interfaces\ContratoInterface;
use App\Repositories\Interfaces\PrestacionSocialInterface;

/**
 * Lógica de negocio de Prestaciones Sociales (Colombia): cesantías, intereses, prima, vacaciones.
 * Usa constantes legales (SMMLV, auxilio, 360 días, 12% intereses) y delega persistencia al repository.
 */
class PrestacionSocialService
{
    /** Días del año laboral (Colombia, efectos laborales). */
    private const DIAS_ANIO_LABORAL = 360;

    /** Tasa anual intereses sobre cesantías (Ley 52 de 1975): 12%. */
    private const TASA_INTERESES_CESANTIAS_ANUAL = 0.12;

    /** Tope legal histórico de auxilio: hasta 2 SMMLV. */
    private const TOPE_SMMLV_AUXILIO_TRANSPORTE = 2.0;

    /**
     * Histórico anual Colombia (COP/mes): salario mínimo y auxilio de transporte.
     * Fuente de referencia: compilados históricos (Gerencie / salario mínimo histórico).
     */
    private const PARAMETROS_LABORALES_POR_ANIO = [
        1990 => ['smmlv' => 41025.0, 'auxilio' => 3798.0],
        1991 => ['smmlv' => 51716.0, 'auxilio' => 4787.0],
        1992 => ['smmlv' => 65190.0, 'auxilio' => 6033.0],
        1993 => ['smmlv' => 81510.0, 'auxilio' => 7542.0],
        1994 => ['smmlv' => 98700.0, 'auxilio' => 8705.0],
        1995 => ['smmlv' => 118934.0, 'auxilio' => 10815.0],
        1996 => ['smmlv' => 142125.0, 'auxilio' => 13567.0],
        1997 => ['smmlv' => 172005.0, 'auxilio' => 17250.0],
        1998 => ['smmlv' => 203826.0, 'auxilio' => 20700.0],
        1999 => ['smmlv' => 236460.0, 'auxilio' => 24012.0],
        2000 => ['smmlv' => 260100.0, 'auxilio' => 26413.0],
        2001 => ['smmlv' => 286000.0, 'auxilio' => 30000.0],
        2002 => ['smmlv' => 309000.0, 'auxilio' => 34000.0],
        2003 => ['smmlv' => 332000.0, 'auxilio' => 37500.0],
        2004 => ['smmlv' => 358000.0, 'auxilio' => 41600.0],
        2005 => ['smmlv' => 381500.0, 'auxilio' => 44500.0],
        2006 => ['smmlv' => 408000.0, 'auxilio' => 47700.0],
        2007 => ['smmlv' => 433700.0, 'auxilio' => 50800.0],
        2008 => ['smmlv' => 461500.0, 'auxilio' => 55000.0],
        2009 => ['smmlv' => 496900.0, 'auxilio' => 59300.0],
        2010 => ['smmlv' => 515000.0, 'auxilio' => 61500.0],
        2011 => ['smmlv' => 535600.0, 'auxilio' => 63600.0],
        2012 => ['smmlv' => 566700.0, 'auxilio' => 67800.0],
        2013 => ['smmlv' => 589500.0, 'auxilio' => 70500.0],
        2014 => ['smmlv' => 616000.0, 'auxilio' => 72000.0],
        2015 => ['smmlv' => 644350.0, 'auxilio' => 74000.0],
        2016 => ['smmlv' => 689455.0, 'auxilio' => 77700.0],
        2017 => ['smmlv' => 737717.0, 'auxilio' => 83140.0],
        2018 => ['smmlv' => 781242.0, 'auxilio' => 88211.0],
        2019 => ['smmlv' => 828116.0, 'auxilio' => 97032.0],
        2020 => ['smmlv' => 877803.0, 'auxilio' => 102854.0],
        2021 => ['smmlv' => 908526.0, 'auxilio' => 106454.0],
        2022 => ['smmlv' => 1000000.0, 'auxilio' => 117172.0],
        2023 => ['smmlv' => 1160000.0, 'auxilio' => 140606.0],
        2024 => ['smmlv' => 1300000.0, 'auxilio' => 162000.0],
        2025 => ['smmlv' => 1423500.0, 'auxilio' => 200000.0],
        2026 => ['smmlv' => 1750905.0, 'auxilio' => 249095.0],
    ];

    protected $prestacionSocialRepository;
    protected $contratoRepository;

    public function __construct(
        PrestacionSocialInterface $prestacionSocialRepository,
        ContratoInterface $contratoRepository
    ) {
        $this->prestacionSocialRepository = $prestacionSocialRepository;
        $this->contratoRepository = $contratoRepository;
    }

    /** Suma de cesantías, intereses, prima y vacaciones donde estado_pago = Pendiente. */
    public function getTotalesPendientes(): array
    {
        return $this->prestacionSocialRepository->getTotalesPendientes();
    }

    /** Contratos en estado ACTIVO (vigentes; equivalente front “Vigente”). */
    public function getContratosVigentesParaLiquidacion()
    {
        return $this->contratoRepository->GetContratosVigentes();
    }

    /** Todos los períodos de prestaciones de un contrato (ordenados por fecha_periodo_fin desc). */
    public function getPrestacionesByContratoId($cod_contrato)
    {
        return $this->prestacionSocialRepository->getByContratoId($cod_contrato);
    }

    /** Contrato (con empleado y cargo) más la colección de períodos de prestaciones de ese contrato. */
    public function getContratoConPrestaciones($cod_contrato)
    {
        $contrato = $this->contratoRepository->GetContratoById($cod_contrato);
        if (!$contrato) {
            return null;
        }
        $prestaciones = $this->prestacionSocialRepository->getByContratoId($cod_contrato);
        return [
            'contrato' => $contrato,
            'prestaciones' => $prestaciones,
        ];
    }

    /**
     * Calcula y persiste un nuevo período de prestaciones para el contrato.
     * Rango: desde (último periodo_fin + 1 día) o desde fecha_ingreso hasta hoy.
     * Fórmulas: cesantías (base*días/360), intereses 12%, prima semestral, vacaciones (salario*días/720).
     */
    public function calcularPrestaciones($cod_contrato): array
    {
        $contrato = $this->contratoRepository->GetContratoById($cod_contrato);
        if (!$contrato) {
            throw new \InvalidArgumentException('Contrato no encontrado.');
        }

        if ($contrato->estado_contrato !== 'ACTIVO') {
            throw new \InvalidArgumentException('Solo se pueden calcular prestaciones para contratos en estado ACTIVO.');
        }

        $fechaIngreso = $contrato->fecha_ingreso instanceof \DateTimeInterface
            ? $contrato->fecha_ingreso
            : \Carbon\Carbon::parse($contrato->fecha_ingreso);
        $hoy = now()->startOfDay();
        $fechaFinContrato = null;
        if (!empty($contrato->fecha_fin)) {
            $fechaFinContrato = \Carbon\Carbon::parse($contrato->fecha_fin)->startOfDay();
        }
        $fechaCorte = $fechaFinContrato && $fechaFinContrato->lt($hoy) ? $fechaFinContrato : $hoy;

        // Desde cuándo calcular: día siguiente al último periodo guardado, o fecha de ingreso
        $ultimoPeriodo = $this->prestacionSocialRepository->getUltimoPeriodoByContratoId($cod_contrato);
        if ($ultimoPeriodo) {
            $inicioCalculo = \Carbon\Carbon::parse($ultimoPeriodo->fecha_periodo_fin)->addDay();
        } else {
            $inicioCalculo = $fechaIngreso->copy();
        }

        if ($inicioCalculo->gt($fechaCorte)) {
            throw new \InvalidArgumentException('No hay días nuevos para calcular.');
        }

        $diasTrabajados = $inicioCalculo->diffInDays($fechaCorte) + 1;
        if ($diasTrabajados < 1) {
            throw new \InvalidArgumentException('No hay días nuevos para calcular.');
        }

        $salarioBase = (float) $contrato->salario_base;
        $acumuladoAuxilio = 0.0;
        $cesantiasValor = 0.0;
        $interesesCesantiasValor = 0.0;
        $primaValor = 0.0;
        $vacacionesValor = 0.0;

        foreach ($this->segmentosPorAnio($inicioCalculo, $fechaCorte) as [$inicioTramo, $finTramo, $anio]) {
            $diasTramo = $inicioTramo->diffInDays($finTramo) + 1;
            $parametros = $this->parametrosLaboralesPorAnio($anio);
            $topeAuxilioTramo = $parametros['smmlv'] * self::TOPE_SMMLV_AUXILIO_TRANSPORTE;
            $auxilioTramo = ($contrato->auxilio_transporte && $salarioBase <= $topeAuxilioTramo)
                ? $parametros['auxilio']
                : 0.0;

            $baseCesantiasTramo = $salarioBase + $auxilioTramo;
            $cesantiasTramo = ($baseCesantiasTramo * $diasTramo) / self::DIAS_ANIO_LABORAL;
            $interesesTramo = $cesantiasTramo * self::TASA_INTERESES_CESANTIAS_ANUAL * ($diasTramo / self::DIAS_ANIO_LABORAL);
            $primaTramo = ($baseCesantiasTramo * $diasTramo) / self::DIAS_ANIO_LABORAL;
            $vacacionesTramo = ($salarioBase * $diasTramo) / 720;

            $acumuladoAuxilio += ($auxilioTramo * $diasTramo) / 30;
            $cesantiasValor += $cesantiasTramo;
            $interesesCesantiasValor += $interesesTramo;
            $primaValor += $primaTramo;
            $vacacionesValor += $vacacionesTramo;
        }

        $data = [
            'cod_contrato' => $cod_contrato,
            'fecha_periodo_inicio' => $inicioCalculo->toDateString(),
            'fecha_periodo_fin' => $fechaCorte->toDateString(),
            'dias_trabajados' => $diasTrabajados,
            'salario_base' => round($salarioBase, 2),
            'auxilio_transporte' => round($acumuladoAuxilio, 2),
            'cesantias_valor' => round($cesantiasValor, 2),
            'intereses_cesantias_valor' => round($interesesCesantiasValor, 2),
            'prima_valor' => round($primaValor, 2),
            'vacaciones_valor' => round($vacacionesValor, 2),
            'estado_pago' => 'Pendiente',
            'fecha_calculo' => now()->toDateString(),
        ];

        $prestacion = $this->prestacionSocialRepository->createPrestacionSocial($data);
        return $prestacion->toArray();
    }

    /**
     * Retorna tramos [inicio, fin, anio] para liquidar año a año.
     *
     * @return array<int, array{0:\Carbon\Carbon,1:\Carbon\Carbon,2:int}>
     */
    private function segmentosPorAnio(\Carbon\Carbon $inicio, \Carbon\Carbon $fin): array
    {
        $cursor = $inicio->copy()->startOfDay();
        $fin = $fin->copy()->startOfDay();
        $segmentos = [];

        while ($cursor->lte($fin)) {
            $finAnio = $cursor->copy()->endOfYear()->startOfDay();
            if ($finAnio->gt($fin)) {
                $finAnio = $fin->copy();
            }
            $segmentos[] = [$cursor->copy(), $finAnio->copy(), (int) $cursor->year];
            $cursor = $finAnio->copy()->addDay();
        }

        return $segmentos;
    }

    /**
     * Obtiene SMMLV/Auxilio por año. Si falta el año, usa el último disponible y avisa.
     *
     * @return array{smmlv:float,auxilio:float}
     */
    private function parametrosLaboralesPorAnio(int $anio): array
    {
        if (isset(self::PARAMETROS_LABORALES_POR_ANIO[$anio])) {
            return self::PARAMETROS_LABORALES_POR_ANIO[$anio];
        }

        $anios = array_keys(self::PARAMETROS_LABORALES_POR_ANIO);
        sort($anios);
        $ultimo = end($anios);
        $primero = reset($anios);

        if ($anio < $primero) {
            throw new \InvalidArgumentException("No hay parámetros laborales configurados para el año {$anio}.");
        }

        if ($anio > $ultimo) {
            return self::PARAMETROS_LABORALES_POR_ANIO[$ultimo];
        }

        throw new \InvalidArgumentException("No hay parámetros laborales configurados para el año {$anio}.");
    }

    /** Marca el período como Pagado o Trasladado; solo si está Pendiente. Registra fecha_pago_cancelacion. */
    public function actualizarEstado($cod_prestacion_social_periodo, string $estado_pago)
    {
        if (!in_array($estado_pago, ['Pagado', 'Trasladado'], true)) {
            throw new \InvalidArgumentException('Estado de pago inválido. Use Pagado o Trasladado.');
        }
        $prestacion = $this->prestacionSocialRepository->getPrestacionSocialById($cod_prestacion_social_periodo);
        if (!$prestacion) {
            return null;
        }
        if ($prestacion->estado_pago !== 'Pendiente') {
            throw new \InvalidArgumentException('Solo se puede cambiar el estado de períodos en estado Pendiente.');
        }
        return $this->prestacionSocialRepository->actualizarEstado($cod_prestacion_social_periodo, $estado_pago);
    }

    /** Elimina un período; solo si estado_pago = Pendiente (no se borran pagos ya registrados). */
    public function eliminarPrestacion($cod_prestacion_social_periodo): bool
    {
        $prestacion = $this->prestacionSocialRepository->getPrestacionSocialById($cod_prestacion_social_periodo);
        if (!$prestacion) {
            return false;
        }
        if ($prestacion->estado_pago !== 'Pendiente') {
            throw new \InvalidArgumentException('Solo se pueden eliminar períodos en estado Pendiente.');
        }
        return $this->prestacionSocialRepository->deletePrestacionSocial($cod_prestacion_social_periodo);
    }

    /** Todos los períodos de todos los contratos (historial global), con contrato.empleado y contrato.cargo. */
    public function getAllPrestacionesSociales()
    {
        return $this->prestacionSocialRepository->getAllPrestacionesSociales();
    }

    /** Un período por ID (con contrato, empleado, cargo). */
    public function getPrestacionSocialById($cod_prestacion_social_periodo)
    {
        return $this->prestacionSocialRepository->getPrestacionSocialById($cod_prestacion_social_periodo);
    }
}
