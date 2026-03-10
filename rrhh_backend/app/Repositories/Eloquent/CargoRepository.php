<?php

namespace App\Repositories\Eloquent;

use App\Models\Cargo;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\CargoInterface;

class CargoRepository implements CargoInterface
{
    public function GetAllCargos(): Collection
    {
        $Cargos = Cargo::all();
        return $Cargos;
    }

    public function GetCargoById($id): ?Cargo
    {
        $Cargo = Cargo::find($id);
        return !$Cargo ? null : $Cargo;
    }

    public function CreateCargo(array $data): Cargo
    {
        $Cargo = Cargo::create($data);
        return $Cargo;
    }

    public function UpdateCargo($id, array $data): ?Cargo
    {
        $Cargo = Cargo::find($id);
        if (!$Cargo) {
            return null;
        }
        $Cargo->update($data);
        return $Cargo;
    }

    public function DeleteCargo($id): bool
    {
        $Cargo = Cargo::find($id);
        if (!$Cargo) {
            return false;
        }
        $Cargo->delete();
        return true;
    }
}