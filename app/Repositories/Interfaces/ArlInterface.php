<?php

namespace App\Repositories\Interfaces;

interface ArlInterface
{
    public function getAllArls();
    public function getArlById($id);
    public function createArl(array $data);
    public function updateArl($id, array $data);
    public function deleteArl($id);
}