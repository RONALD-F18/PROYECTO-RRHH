<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ArlRequest;
use App\Services\ArlService;

class ArlController extends Controller
{
    protected $arlService;

    public function __construct(ArlService $arlService)
    {
        $this->arlService = $arlService;
    }

    public function index()
    {
        $data = $this->arlService->getAllArls();

        return response()->json([
            'message' => 'ARL listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->arlService->getArlById($id);

        if (!$data) {
            return response()->json([
                'message' => 'ARL no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'ARL obtenido exitosamente',
            'data' => $data
        ], 200);
    }

    public function store(ArlRequest $request)
    {
        $data = $this->arlService->createArl($request->validated());

        return response()->json([
            'message' => 'ARL creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(ArlRequest $request, $id)
    {
        $data = $this->arlService->updateArl($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'ARL no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'ARL actualizado exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $deleted = $this->arlService->deleteArl($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'ARL no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'ARL eliminado exitosamente'
        ], 200);
    }
}
