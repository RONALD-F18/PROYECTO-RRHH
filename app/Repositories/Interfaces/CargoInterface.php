<?php

namespace App\Repositories\Interfaces;


interface CargoInterface
{
    public function GetAllCargos();
    public function GetCargoById($id);
    public function CreateCargo(array $data);
    public function UpdateCargo($id, array $data);
    public function DeleteCargo($id);
}