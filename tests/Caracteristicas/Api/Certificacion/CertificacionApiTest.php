<?php

namespace Tests\Caracteristicas\Api\Certificacion;

use App\Models\Empresa;
use App\Models\Usuario;
use Tests\Soporte\Concerns\ConCabeceraAutenticacionJwt;
use Tests\Soporte\Concerns\ConDatosPruebaRrhh;
use Tests\Soporte\Concerns\ConPruebasModuloApi;
use Tests\TestCase;

class CertificacionApiTest extends TestCase
{
    use ConCabeceraAutenticacionJwt;
    use ConDatosPruebaRrhh;
    use ConPruebasModuloApi;

    public function test_certificaciones_crud_y_pdf_api(): void
    {
        $base = $this->crearEmpleadoConContrato();
        $empresa = Empresa::create([
            'nit' => (string) random_int(900000000, 999999999),
            'dv' => '1',
            'razon_social' => 'Empresa Cert SAS',
            'nombre_comercial' => 'Empresa Cert',
            'pais' => 'Colombia',
        ]);

        $payload = [
            'id_empresa' => $empresa->id_empresa,
            'cod_empleado' => $base['empleado']->cod_empleado,
            'cod_contrato' => $base['contrato']->cod_contrato,
            'tipo_certificacion' => 'Laboral',
            'incluye_salario' => true,
            'salario_certificado' => 2500000,
            'fecha_emision' => '2024-08-01',
            'ciudad_emision' => 'Bogota',
            'descripcion' => 'Certificacion laboral test',
        ];

        $this->getJson('/api/v1/certificaciones')->assertUnauthorized();

        $respuesta = $this->conJwt($base['usuario'])
            ->postJson('/api/v1/certificaciones', $payload)
            ->assertCreated();

        $id = data_get($respuesta->json(), 'data.cod_certificacion');
        $this->assertNotNull($id);

        $this->conJwt($base['usuario'])
            ->getJson('/api/v1/certificaciones/'.$id)
            ->assertOk();

        $this->conJwt($base['usuario'])
            ->getJson('/api/v1/certificaciones/'.$id.'/pdf-laboral')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->conJwt($base['usuario'])
            ->putJson('/api/v1/certificaciones/'.$id, [
                'descripcion' => 'Certificacion actualizada',
            ])
            ->assertOk();

        $this->conJwt($base['usuario'])
            ->deleteJson('/api/v1/certificaciones/'.$id)
            ->assertOk();
    }
}
