<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RolRequest;
use App\Services\RolService;

class RolController extends Controller
{
    protected $rolService;

    public function __construct(RolService $rolService)
    {
        $this->rolService = $rolService;
    }

    public function index()
    {
        $data = $this->rolService->getAllRoles();

        return response()->json([
            'message' => 'Roles listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $data = $this->rolService->getRoleById($id);

        if (!$data) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Rol obtenido exitosamente',
            'data' => $data
        ], 200);
    }

    public function store(RolRequest $request)
    {
        $data = $this->rolService->createRole($request->validated());

        return response()->json([
            'message' => 'Rol creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update(RolRequest $request, $id)
    {
        $data = $this->rolService->updateRole($id, $request->validated());

        if (!$data) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Rol actualizado exitosamente',
            'data' => $data
        ], 200);
    }

    public function destroy($id)
    {
        $data = $this->rolService->deleteRole($id);

        if (!$data) {
            return response()->json([
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Rol eliminado exitosamente',
            'data' => $data
        ], 200);
    } 
}
