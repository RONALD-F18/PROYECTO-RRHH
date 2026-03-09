<?php

namespace App\Repositories\Interfaces;

interface EpsInterface
{
    public function getAllEps();
    public function getEpsById($id);
    public function createEps(array $data);
    public function updateEps($id, array $data);
    public function deleteEps($id);
}   
