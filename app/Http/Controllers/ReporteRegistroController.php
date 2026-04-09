<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRegistroIndexRequest;
use App\Http\Requests\ReporteRegistroStoreRequest;
use App\Http\Resources\ReporteRegistroResource;
use App\Models\ReporteRegistro;
use App\Models\Usuario;
use App\Repositories\Interfaces\ReporteRegistroInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ReporteRegistroController extends Controller
{
    public function __construct(
        protected ReporteRegistroInterface $reporteRegistros
    ) {
    }

    public function index(ReporteRegistroIndexRequest $request): JsonResponse
    {
        $usuario = $this->usuarioAutenticado();
        $filtros = array_filter($request->validated(), fn ($v) => $v !== null && $v !== '');

        $coleccion = $this->reporteRegistros->listarParaUsuario(
            $usuario,
            $this->esAdministrador($usuario),
            $filtros
        );

        return response()->json([
            'data' => ReporteRegistroResource::collection($coleccion),
        ]);
    }

    public function store(ReporteRegistroStoreRequest $request): JsonResponse
    {
        $usuario = $this->usuarioAutenticado();
        $datos = $request->validated();

        $registro = $this->reporteRegistros->crear([
            'cod_usuario' => $usuario->cod_usuario,
            'modulo' => $datos['modulo'],
            'tipo' => $datos['tipo'],
            'estado' => $datos['estado'],
            'descripcion' => $datos['descripcion'] ?? null,
        ]);

        $registro->load('usuario');

        return response()->json([
            'data' => new ReporteRegistroResource($registro),
        ], Response::HTTP_CREATED);
    }

    public function destroy(ReporteRegistro $reporte_registro): JsonResponse|Response
    {
        $usuario = $this->usuarioAutenticado();

        if (! $this->puedeGestionarRegistro($usuario, $reporte_registro)) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar este registro.',
            ], Response::HTTP_FORBIDDEN);
        }

        $this->reporteRegistros->eliminar($reporte_registro);

        return response()->noContent();
    }

    private function usuarioAutenticado(): Usuario
    {
        /** @var Usuario $u */
        $u = auth('api')->user();

        return $u;
    }

    private function esAdministrador(Usuario $usuario): bool
    {
        return $usuario->roles && $usuario->roles->nombre_rol === 'administrador';
    }

    private function puedeGestionarRegistro(Usuario $usuario, ReporteRegistro $registro): bool
    {
        if ($this->esAdministrador($usuario)) {
            return true;
        }

        return (int) $registro->cod_usuario === (int) $usuario->cod_usuario;
    }
}
