<?php

namespace App\Repositories\Interfaces;

interface PensionInterface
{
    public function getAll();
    public function getPensionById($id);
    public function createPension(array $data);
    public function updatePension($id, array $data);
    public function deletePension($id);
}