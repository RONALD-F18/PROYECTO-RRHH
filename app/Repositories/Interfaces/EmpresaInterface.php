<?php

namespace App\Repositories\Interfaces;

interface EmpresaInterface
{
    public function getAllEmpresas();

    public function getEmpresaById($id);

    public function createEmpresa(array $data);

    public function updateEmpresa($id, array $data);

    public function deleteEmpresa($id);
}

