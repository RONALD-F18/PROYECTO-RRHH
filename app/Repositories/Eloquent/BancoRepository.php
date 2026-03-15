<?php   

namespace App\Repositories\Eloquent;

use App\Models\Banco;
use App\Repositories\Interfaces\BancoInterface;


class BancoRepository implements BancoInterface
{
    public function getAllBancos()
    {
        $bancos = Banco::all();
        return $bancos;
    }

    public function getBancoById($id)
    {
        $banco = Banco::find($id);
        return !$banco ? null : $banco;
    }

    public function createBanco(array $data)
    {
        $banco = Banco::create($data);
        return $banco;
    }

    public function updateBanco($id, array $data)
    {
        $banco = Banco::find($id);
        if (!$banco) {
            return null;
        }
        $banco->update($data);
        return $banco;
    }
    
    public function deleteBanco($id)
    {
        $banco = Banco::find($id);
        if (!$banco) {
            return false;
        }
        $banco->delete();
        return true;
    }
}
