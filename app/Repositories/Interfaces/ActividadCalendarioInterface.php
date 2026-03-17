<?php

namespace App\Repositories\Interfaces;

interface ActividadCalendarioInterface
{
    public function getAllActividadesCalendario();
    public function getActividadCalendarioById($id);
    public function createActividadCalendario(array $data);
    public function updateActividadCalendario($id, array $data);
    public function deleteActividadCalendario($id);
}

