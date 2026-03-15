<?php

namespace App\Repositories\Interfaces;

interface CesantiaInterface
{
    public function getAllCesantias();
    public function getCesantiaById($id);
    public function createCesantia(array $data);
    public function updateCesantia($id, array $data);
    public function deleteCesantia($id);
}