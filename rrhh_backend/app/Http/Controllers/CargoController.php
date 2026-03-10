<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CargoService;
use App\Http\Requests\CargoRequest;

class CargoController extends Controller
{
    protected $cargoService;
    
    public function __construct(CargoService $cargoService)
    {
        $this->cargoService = $cargoService;
    }

    public function index()
    {
        $data = $this->cargoService->getAllCargos();

        return response()->json([
            'message' => 'Lista de cargos obtenida exitosamente',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = $this->cargoService->getCargoById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Cargo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cargo obtenido exitosamente',
            'data' => $data
        ]);
    }

    public function store(CargoRequest $request)
    {
        $data = $this->cargoService->createCargo($request->validated());

        return response()->json([
            'message' => 'Cargo creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(CargoRequest $request, $id)
    {
        $data = $this->cargoService->updateCargo($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Cargo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cargo actualizado exitosamente',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->cargoService->deleteCargo($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Cargo no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cargo eliminado exitosamente'
        ]);
    }   
}
