<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BancoServices;
use App\Http\Requests\BancoRequest;

class BancoController extends Controller
{
    protected $bancoServices;

    public function __construct(BancoServices $bancoServices)
    {
        $this->bancoServices = $bancoServices;
    }

    public function index()
    {
        $data = $this->bancoServices->getAllBancos();

        return response()->json([
            'message' => 'Lista de bancos obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->bancoServices->getBancoById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Banco no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Banco obtenido exitosamente',
            'data' => $data
        ]);
    }

    public function store(BancoRequest $request)
    {
        $data = $this->bancoServices->createBanco($request->validated());

        return response()->json([
            'message' => 'Banco creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(BancoRequest $request, $id)
    {
        $data = $this->bancoServices->updateBanco($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Banco no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Banco actualizado exitosamente',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->bancoServices->deleteBanco($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Banco no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Banco eliminado exitosamente'
        ]);
    }

    

}
