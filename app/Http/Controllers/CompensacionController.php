<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CompensacionRequest;
use App\Services\CompensacionService;

class CompensacionController extends Controller
{
    protected $compensacionService;

    public function __construct(CompensacionService $compensacionService)
    {
        $this->compensacionService = $compensacionService;
    }

    public function index()
    {
        $data = $this->compensacionService->getAllCompensaciones();
        return response()->json([
            'message' => 'Lista de Compensaciones',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->compensacionService->getCompensacionById($id);
        if (!$data) {
            return response()->json([
                'message' => 'Compensacion no encontrada'
            ], 404);
        }
        return response()->json([
            'message' => 'Compensacion encontrada',
            'data' => $data
        ], 200);
    }

    public function store(CompensacionRequest $request)
    {
        $data = $this->compensacionService->createCompensacion($request->validated());
        return response()->json([
            'message' => 'Compensacion creada exitosamente',
            'data' => $data
        ], 201);
    }
}
