<?php

namespace Tests\Caracteristicas\Api\Empleado;

use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class EmpleadoApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_empleados_crud_api(): void
    {
        $usuario = Usuario::factory()->create();
        $banco = $this->crearBanco();

        $payload = $this->payloadEmpleadoApi((int) $banco->cod_banco);
        $payloadUpdate = array_merge($payload, [
            'nombre_empleado' => 'Pedro',
            'apellidos_empleado' => 'Ramirez',
            'numero_telefono' => '3'.random_int(100000000, 999999999),
        ]);

        $this->probarCrudModuloApiPlano(
            $usuario,
            '/api/v1/empleados',
            $payload,
            $payloadUpdate,
            'cod_empleado'
        );
    }
}
