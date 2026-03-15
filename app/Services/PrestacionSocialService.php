<?php

namespace App\Services;

use App\Repositories\Interfaces\ContratoInterface;
use App\Repositories\Interfaces\PrestacionSocialInterface;

class PrestacionSocialService
{
    /** Días del año laboral (Colombia, efectos laborales). */
    private const DIAS_ANIO_LABORAL = 360;

    /** Tasa anual intereses sobre cesantías (Ley 52 de 1975): 12%. */
    private const TASA_INTERESES_CESANTIAS_ANUAL = 0.12;

    /** Auxilio de transporte mensual (actualizar según decreto vigente). Solo aplica hasta 2 SMMLV. */
    private const AUXILIO_TRANSPORTE_MENSUAL = 200000;

    /** Salario mínimo mensual legal vigente (SMMLV) - tope auxilio. */
    private const SALARIO_MINIMO_MENSUAL = 1300000;

    /** Quien gane más de este número de SMMLV no tiene derecho a auxilio. */
    private const TOPE_SMMLV_AUXILIO_TRANSPORTE = 2;

    protected $prestacionSocialRepository;
    protected $contratoRepository;

    public function __construct(
        PrestacionSocialInterface $prestacionSocialRepository,
        ContratoInterface $contratoRepository
    ) {
        $this->prestacionSocialRepository = $prestacionSocialRepository;
        $this->contratoRepository = $contratoRepository;
    }

    public function getTotalesPendientes(): array
    {
        return $this->prestacionSocialRepository->getTotalesPendientes();
    }

    public function getContratosVigentesParaLiquidacion()
    {
        return $this->contratoRepository->GetContratosVigentes();
    }

    public function getPrestacionesByContratoId($cod_contrato)
    {
        return $this->prestacionSocialRepository->getByContratoId($cod_contrato);
    }

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
     * Regla: desde (último periodo_fin + 1 día) o desde fecha_ingreso hasta hoy.
     * Fórmulas legales Colombia: cesantías, intereses 12%, prima semestral, vacaciones 15 días/año.
     */
    public function calcularPrestaciones($cod_contrato): array
    {
        $contrato = $this->contratoRepository->GetContratoById($cod_contrato);
        if (!$contrato) {
            throw new \InvalidArgumentException('Contrato no encontrado.');
        }

        if (!in_array($contrato->estado_contrato, ['ACTIVO', 'Vigente'])) {
            throw new \InvalidArgumentException('Solo se pueden calcular prestaciones para contratos vigentes.');
        }

        $topeAuxilio = self::SALARIO_MINIMO_MENSUAL * self::TOPE_SMMLV_AUXILIO_TRANSPORTE;

        $fechaIngreso = $contrato->fecha_ingreso instanceof \DateTimeInterface
            ? $contrato->fecha_ingreso
            : \Carbon\Carbon::parse($contrato->fecha_ingreso);
        $hoy = now()->startOfDay();

        $ultimoPeriodo = $this->prestacionSocialRepository->getUltimoPeriodoByContratoId($cod_contrato);
        if ($ultimoPeriodo) {
            $inicioCalculo = \Carbon\Carbon::parse($ultimoPeriodo->fecha_periodo_fin)->addDay();
        } else {
            $inicioCalculo = $fechaIngreso->copy();
        }

        if ($inicioCalculo->gt($hoy)) {
            throw new \InvalidArgumentException('No hay días nuevos para calcular.');
        }

        $diasTrabajados = $inicioCalculo->diffInDays($hoy);
        if ($diasTrabajados < 1) {
            throw new \InvalidArgumentException('No hay días nuevos para calcular.');
        }

        $salarioBase = (float) $contrato->salario_base;
        $auxilioTransporte = 0;
        if ($contrato->auxilio_transporte && $salarioBase < $topeAuxilio) {
            $auxilioTransporte = self::AUXILIO_TRANSPORTE_MENSUAL;
        }
        $baseCesantias = $salarioBase + $auxilioTransporte;

        // Cesantías: (salario + auxilio) * días / 360
        $cesantiasValor = ($baseCesantias * $diasTrabajados) / self::DIAS_ANIO_LABORAL;

        // Intereses cesantías: 12% anual proporcional
        $interesesCesantiasValor = $cesantiasValor * self::TASA_INTERESES_CESANTIAS_ANUAL * ($diasTrabajados / self::DIAS_ANIO_LABORAL);

        // Prima de servicios: por semestre (1 ene - 30 jun, 1 jul - 31 dic). Solo días del semestre en curso.
        $inicioSemestre = $hoy->month <= 6
            ? $hoy->copy()->startOfYear()
            : $hoy->copy()->month(7)->startOfMonth();
        $inicioPrima = $inicioSemestre->gte($inicioCalculo) ? $inicioSemestre : $inicioCalculo->copy();
        if ($inicioPrima->lt($fechaIngreso)) {
            $inicioPrima = $fechaIngreso->copy();
        }
        $diasPrima = $inicioPrima->diffInDays($hoy);
        $primaValor = ($baseCesantias * $diasPrima) / self::DIAS_ANIO_LABORAL;

        // Vacaciones: 15 días por año = salario * días / 720 (no incluye auxilio)
        $vacacionesValor = ($salarioBase * $diasTrabajados) / 720;

        $data = [
            'cod_contrato' => $cod_contrato,
            'fecha_periodo_inicio' => $inicioCalculo->toDateString(),
            'fecha_periodo_fin' => $hoy->toDateString(),
            'dias_trabajados' => $diasTrabajados,
            'salario_base' => round($salarioBase, 2),
            'auxilio_transporte' => round($auxilioTransporte, 2),
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

    public function getAllPrestacionesSociales()
    {
        return $this->prestacionSocialRepository->getAllPrestacionesSociales();
    }

    public function getPrestacionSocialById($cod_prestacion_social_periodo)
    {
        return $this->prestacionSocialRepository->getPrestacionSocialById($cod_prestacion_social_periodo);
    }
}
