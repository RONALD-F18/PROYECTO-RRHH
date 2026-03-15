<?php

namespace App\Repositories\Eloquent;

use App\Models\Contrato;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ContratoInterface;

class ContratoRepository implements ContratoInterface
{
    public function GetAllContratos(): Collection
    {
        $Contratos = Contrato::all();
        return $Contratos;
    }

    public function GetContratoById($id): ?Contrato
    {
        $Contrato = Contrato::with('empleado', 'cargo')->find($id);
        return !$Contrato ? null : $Contrato;
    }

    public function GetContratosVigentes(): Collection
    {
        return Contrato::with('empleado', 'cargo')
            ->whereIn('estado_contrato', ['ACTIVO', 'Vigente'])
            ->orderBy('fecha_ingreso', 'desc')
            ->get();
    }

    public function CreateContrato(array $data): Contrato
    {
        $Contrato = Contrato::create($data);
        return $Contrato;
    }

    public function UpdateContrato($id, array $data): ?Contrato
    {
        $Contrato = Contrato::find($id);
        if (!$Contrato) {
            return null;
        }
        $Contrato->update($data);
        return $Contrato;
    }

    public function DeleteContrato($id): bool
    {
        $Contrato = Contrato::find($id);
        if (!$Contrato) {
            return false;
        }
        $Contrato->delete();
        return true;
    }
}
