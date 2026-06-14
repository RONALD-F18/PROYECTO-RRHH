<?php
namespace App\Services;

use App\Repositories\Interfaces\InasistenciaInterface;

class InasistenciaService
{
    protected $inasistenciaRepository;

    public function __construct(InasistenciaInterface $inasistenciaRepository)
    {
        $this->inasistenciaRepository = $inasistenciaRepository;
    }

    public function getAllInasistencias()
    {
        return $this->inasistenciaRepository->getAllInasistencias();
    }

    public function buscarInasistencias(?int $codEmpleado = null, ?int $mes = null, ?int $anio = null)
    {
        return $this->inasistenciaRepository->buscarInasistencias($codEmpleado, $mes, $anio);
    }

    public function getInasistenciaById($cod_inasistencias)
    {
        return $this->inasistenciaRepository->getInasistenciaById($cod_inasistencias);
    }

    public function createInasistencia(array $data)
    {
        return $this->inasistenciaRepository->createInasistencia($data);
    }

    public function updateInasistencia($cod_inasistencias, array $data)
    {
        return $this->inasistenciaRepository->updateInasistencia($cod_inasistencias, $data);
    }

    public function deleteInasistencia($cod_inasistencias)
    {
        return $this->inasistenciaRepository->deleteInasistencia($cod_inasistencias);
    }

    public function deleteByEmpleadoId(int $codEmpleado): int
    {
        return $this->inasistenciaRepository->deleteByEmpleadoId($codEmpleado);
    }
}
