<?php

namespace App\Repositories\Interfaces;

interface RiesgoInterface
{
    public function getAllRiesgos();
    public function getRiesgoById($id);
    public function createRiesgo(array $data);
    public function updateRiesgo($id, array $data);
    public function deleteRiesgo($id);
}