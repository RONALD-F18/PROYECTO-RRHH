<?php

namespace App\Repositories\Interfaces;

interface BancoInterface
{
    public function getAllBancos();
    public function getBancoById($id);
    public function createBanco(array $data);
    public function updateBanco($id, array $data);
    public function deleteBanco($id);
}

