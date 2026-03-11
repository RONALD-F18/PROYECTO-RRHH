<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmpleadoRequest;
use App\Services\EmpleadoService;



class EmpleadoController extends Controller
{
    protected $empleadoService;

    public function __construct(EmpleadoService $empleadoService)
    {
        $this->empleadoService = $empleadoService;
    }

    public function index()
    {
       
        $data = $this->empleadoService->getAllEmpleados();

        return response()->json([
            'message' => 'Empleados listados exitosamente',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $Empleado = $this->empleadoService->getEmpleadoById($id);
        if (!$Empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json($Empleado);
    }

    public function store(EmpleadoRequest $request)
    {
        $data = array_merge($request->validated(), [
            'cod_usuario' => auth()->user()->cod_usuario,
            'estado_emp'  => $request->input('estado_emp', 'ACTIVO'),
        ]);
        $Empleado = $this->empleadoService->createEmpleado($data);
        return response()->json($Empleado, 201);
    }

    public function update(EmpleadoRequest $request, $id)
    {
        $data = $request->validated();
        $Empleado = $this->empleadoService->updateEmpleado($id, $data);
        if (!$Empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json($Empleado);
    }

    public function destroy($id)
    {
        $deleted = $this->empleadoService->deleteEmpleado($id);
        if (!$deleted) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json(['message' => 'Empleado eliminado']);
    }
}
