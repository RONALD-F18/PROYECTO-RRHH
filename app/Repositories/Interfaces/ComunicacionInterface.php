<?php

namespace App\Repositories\Interfaces;

interface ComunicacionInterface
{
    public function GetAllComunicaciones();
    public function GetComunicacionById($id);
    public function CreateComunicacion(array $data);
    public function UpdateComunicacion($id, array $data);
    public function DeleteComunicacion($id);
}