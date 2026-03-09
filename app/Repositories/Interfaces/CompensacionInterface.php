<?php

namespace App\Repositories\Interfaces;

interface CompensacionInterface
{
    public function getAllCompensaciones();
    public function getCompensacionById($id);
    public function createCompensacion(array $data);
    public function updateCompensacion($id, array $data);
    public function deleteCompensacion($id);
}