<?php

namespace Tests\Soporte\Concerns;

use App\Models\Arl;
use App\Models\Banco;
use App\Models\Cargo;
use App\Models\Cesantia;
use App\Models\Compensacion;
use App\Models\Contrato;
use App\Models\Empleado;
use App\Models\Eps;
use App\Models\Pension;
use App\Models\Riesgo;
use App\Models\TipoIncapacidad;
use App\Models\Usuario;

trait ConDatosPruebaRrhh
{
    protected function sembrarTiposIncapacidad(): void
    {
        $tipos = [
            ['clave_normativa' => 'origen_comun', 'nombre_tipo' => 'Enfermedad común', 'descripcion' => 'Prueba'],
            ['clave_normativa' => 'laboral', 'nombre_tipo' => 'Accidente laboral', 'descripcion' => 'Prueba'],
            ['clave_normativa' => 'maternidad', 'nombre_tipo' => 'Licencia maternidad', 'descripcion' => 'Prueba'],
            ['clave_normativa' => 'paternidad', 'nombre_tipo' => 'Licencia paternidad', 'descripcion' => 'Prueba'],
        ];

        foreach ($tipos as $tipo) {
            TipoIncapacidad::query()->firstOrCreate(
                ['clave_normativa' => $tipo['clave_normativa']],
                ['nombre_tipo' => $tipo['nombre_tipo'], 'descripcion' => $tipo['descripcion']]
            );
        }
    }

    protected function crearBanco(array $overrides = []): Banco
    {
        return Banco::create(array_merge([
            'nombre_banco' => 'Banco Test '.uniqid(),
            'descripcion_banco' => 'Banco de prueba automatizada',
        ], $overrides));
    }

    protected function crearCargo(array $overrides = []): Cargo
    {
        return Cargo::create(array_merge([
            'nomb_cargo' => 'Cargo Test '.uniqid(),
            'descripcion' => 'Cargo de prueba',
        ], $overrides));
    }

    protected function crearEmpleadoModelo(Usuario $usuario, Banco $banco, array $overrides = []): Empleado
    {
        return Empleado::create(array_merge([
            'nombre_empleado' => 'Juan',
            'apellidos_empleado' => 'Perez',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'tipo_documento' => 'CC',
            'fecha_nac' => '1990-05-15',
            'direccion' => 'Calle 123 Bogota DC',
            'numero_telefono' => '3'.random_int(100000000, 999999999),
            'correo_empleado' => 'emp_'.uniqid('', true).'@example.com',
            'numero_cuenta' => (string) random_int(1000000000, 9999999999),
            'tipo_cuenta' => 'AHORROS',
            'cod_banco' => $banco->cod_banco,
            'estado_emp' => 'ACTIVO',
            'discapacidad' => 'NINGUNA',
            'nacionalidad' => 'COLOMBIANA',
            'estado_civil' => 'SOLTERO',
            'grupo_sanguineo' => 'O+',
            'profesion' => 'Analista',
            'fec_exp_doc' => '2010-06-01',
            'descripcion' => 'Empleado de prueba',
            'cod_usuario' => $usuario->cod_usuario,
        ], $overrides));
    }

    protected function crearContratoModelo(Empleado $empleado, Cargo $cargo, array $overrides = []): Contrato
    {
        return Contrato::create(array_merge([
            'tipo_contrato' => 'Termino indefinido',
            'cod_empleado' => $empleado->cod_empleado,
            'forma_de_pago' => 'Mensual',
            'fecha_ingreso' => '2020-01-15',
            'fecha_fin' => null,
            'salario_base' => 2500000,
            'cod_cargo' => $cargo->cod_cargo,
            'modalidad_trabajo' => 'Presencial',
            'horario_trabajo' => 'L-V',
            'auxilio_transporte' => true,
            'descripcion' => 'Contrato de prueba',
            'estado_contrato' => 'ACTIVO',
        ], $overrides));
    }

    /** @return array{usuario: Usuario, banco: Banco, cargo: Cargo, empleado: Empleado, contrato: Contrato} */
    protected function crearEmpleadoConContrato(?Usuario $usuario = null): array
    {
        $usuario ??= Usuario::factory()->create();
        $banco = $this->crearBanco();
        $cargo = $this->crearCargo();
        $empleado = $this->crearEmpleadoModelo($usuario, $banco);
        $contrato = $this->crearContratoModelo($empleado, $cargo);

        return compact('usuario', 'banco', 'cargo', 'empleado', 'contrato');
    }

    /** @return array<string, int> */
    protected function crearCatalogosAfiliacion(): array
    {
        $eps = Eps::create(['nombre_eps' => 'EPS T '.uniqid(), 'descripcion_eps' => 'Prueba']);
        $arl = Arl::create(['nombre_arl' => 'ARL T '.uniqid(), 'descripcion_arl' => 'Prueba']);
        $riesgo = Riesgo::create(['nombre_riesgo' => 'Riesgo T '.uniqid(), 'descripcion_riesgo' => 'Prueba']);
        $pension = Pension::create(['nombre_fondo_pension' => 'Pension T '.uniqid(), 'descripcion_fondo_pension' => 'Prueba']);
        $cesantia = Cesantia::create(['nombre_fondo_cesantia' => 'Cesantia T '.uniqid(), 'descripcion_fondo_cesantia' => 'Prueba']);
        $caja = Compensacion::create([
            'nombre_caja_compensacion' => 'Caja T '.uniqid(),
            'descripcion_caja_compensacion' => 'Prueba',
        ]);

        return [
            'cod_eps' => (int) $eps->cod_eps,
            'cod_arl' => (int) $arl->cod_arl,
            'cod_riesgo' => (int) $riesgo->cod_riesgo,
            'cod_fondo_pensiones' => (int) $pension->cod_fondo_pensiones,
            'cod_fondo_cesantias' => (int) $cesantia->cod_fondo_cesantias,
            'cod_caja_compensacion' => (int) $caja->cod_caja_compensacion,
        ];
    }

    protected function payloadEmpleadoApi(int $codBanco, array $overrides = []): array
    {
        return array_merge([
            'nombre_empleado' => 'Maria',
            'apellidos_empleado' => 'Lopez',
            'tipo_documento' => 'CC',
            'doc_iden' => (string) random_int(10000000, 99999999),
            'fecha_nac' => '1992-03-10',
            'direccion' => 'Carrera 45 Numero 12 Bogota',
            'numero_telefono' => '3'.random_int(100000000, 999999999),
            'correo_empleado' => 'api_emp_'.uniqid('', true).'@gmail.com',
            'numero_cuenta' => (string) random_int(1000000000, 9999999999),
            'tipo_cuenta' => 'AHORROS',
            'cod_banco' => $codBanco,
            'estado_emp' => 'ACTIVO',
            'discapacidad' => 'NINGUNA',
            'nacionalidad' => 'COLOMBIANA',
            'estado_civil' => 'SOLTERO',
            'grupo_sanguineo' => 'A+',
            'profesion' => 'Contador',
            'fec_exp_doc' => '2012-03-10',
            'descripcion' => 'Empleado creado por API test',
        ], $overrides);
    }

    protected function payloadContratoApi(int $codEmpleado, int $codCargo, array $overrides = []): array
    {
        return array_merge([
            'tipo_contrato' => 'Termino fijo',
            'cod_empleado' => $codEmpleado,
            'forma_de_pago' => 'Quincenal',
            'fecha_ingreso' => '2021-02-01',
            'fecha_fin' => null,
            'salario_base' => 1800000,
            'cod_cargo' => $codCargo,
            'modalidad_trabajo' => 'Hibrido',
            'horario_trabajo' => 'L-V 8-5',
            'auxilio_transporte' => false,
            'descripcion' => 'Contrato API test',
            'estado_contrato' => 'ACTIVO',
        ], $overrides);
    }

    protected function payloadAfiliacionApi(int $codEmpleado, array $catalogos, array $overrides = []): array
    {
        return array_merge([
            'fecha_afiliacion_eps' => '2021-03-01',
            'fecha_afiliacion_arl' => '2021-03-01',
            'fecha_afiliacion_caja' => '2021-03-01',
            'fecha_afiliacion_fondo_pensiones' => '2021-03-01',
            'fecha_afiliacion_fondo_cesantias' => '2021-03-01',
            'estado_afiliacion' => 'ACTIVA',
            'cod_eps' => $catalogos['cod_eps'],
            'cod_riesgo' => $catalogos['cod_riesgo'],
            'cod_arl' => $catalogos['cod_arl'],
            'cod_fondo_pensiones' => $catalogos['cod_fondo_pensiones'],
            'cod_fondo_cesantias' => $catalogos['cod_fondo_cesantias'],
            'cod_caja_compensacion' => $catalogos['cod_caja_compensacion'],
            'cod_empleado' => $codEmpleado,
            'descripcion' => 'Afiliacion de prueba',
            'tipo_regimen' => 'Contributivo',
        ], $overrides);
    }
}
