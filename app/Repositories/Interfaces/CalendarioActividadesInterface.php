<?php

namespace App\Repositories\Interfaces;

interface CalendarioActividadesInterface
{
    public function GetAllCalendarioActividades();
    public function GetCalendarioActividadById($id);
    public function CreateCalendarioActividad(array $data);
    public function UpdateCalendarioActividad($id, array $data);
    public function DeleteCalendarioActividad($id);
}