<?php   

namespace App\Repositories\Interfaces;


interface InasistenciaInterface
{
    public function getAllInasistencias();
    public function getInasistenciaById($cod_inasistencias);
    public function createInasistencia(array $data);
    public function updateInasistencia($cod_inasistencias, array $data);
    public function deleteInasistencia($cod_inasistencias);
}
