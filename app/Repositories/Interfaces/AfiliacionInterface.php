<?php

namespace App\Repositories\Interfaces;

interface AfiliacionInterface
{
    public function getAllAfiliaciones();
    public function getAfiliacionById($id);
    public function createAfiliacion(array $data);
    public function updateAfiliacion($id, array $data);
    public function deleteAfiliacion($id);
}
