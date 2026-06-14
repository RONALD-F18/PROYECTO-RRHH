<?php   

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface InasistenciaInterface
{
    public function getAllInasistencias();

    public function buscarInasistencias(?int $codEmpleado = null, ?int $mes = null, ?int $anio = null): Collection;

    public function getInasistenciaById($cod_inasistencias);

    public function createInasistencia(array $data);

    public function updateInasistencia($cod_inasistencias, array $data);

    public function deleteInasistencia($cod_inasistencias);

    public function deleteByEmpleadoId(int $codEmpleado): int;
}
