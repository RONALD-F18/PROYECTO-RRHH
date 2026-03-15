<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ContratoService;
use App\Http\Requests\ContratoRequest;

class ContratoController extends Controller
{
    protected $contratoService;

    public function __construct(ContratoService $contratoService)
    {
        $this->contratoService = $contratoService;
    }

    public function index()
    {
        $data = $this->contratoService->getAllContratos();

        return response()->json([
            'message' => 'Contratos listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $Contrato = $this->contratoService->getContratoById($id);
        if (!$Contrato) {
            return response()->json(['message' => 'Contrato no encontrado'], 404);
        }
        return response()->json($Contrato);
    }

    public function store(ContratoRequest $request)
    {
        $data = $request->validated();
        $Contrato = $this->contratoService->createContrato($data);
        return response()->json($Contrato, 201);
    }

    public function update(ContratoRequest $request, $id)
    {
        $data = $request->validated();
        $Contrato = $this->contratoService->updateContrato($id, $data);
        if (!$Contrato) {
            return response()->json(['message' => 'Contrato no encontrado'], 404);
        }
        return response()->json($Contrato);
    }

    public function destroy($id)
    {
        $deleted = $this->contratoService->deleteContrato($id);
        if (!$deleted) {
            return response()->json(['message' => 'Contrato no encontrado'], 404);
        }
        return response()->json(['message' => 'Contrato eliminado exitosamente']);
    }
}
