<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BancoService;
use App\Http\Requests\BancoRequest;

class BancoController extends Controller
{
    protected $bancoService;

    public function __construct(BancoService $bancoService)
    {
        $this->bancoService = $bancoService;
    }

    public function index()
    {
        $data = $this->bancoService->getAllBancos();

        return response()->json([
            'message' => 'Lista de bancos obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->bancoService->getBancoById($id);

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
        $data = $this->bancoService->createBanco($request->validated());

        return response()->json([
            'message' => 'Banco creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(BancoRequest $request, $id)
    {
        $data = $this->bancoService->updateBanco($id, $request->validated());

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
        $deleted = $this->bancoService->deleteBanco($id);

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
