<?php

namespace Tests;

use App\Models\Rol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRolesPredeterminados();
    }

    /**
     * Roles requeridos por UserPolicy y factories de usuario en pruebas.
     */
    protected function sembrarRolesPredeterminados(): void
    {
        Rol::query()->firstOrCreate(
            ['nombre_rol' => 'administrador'],
            ['estado_rol' => true, 'descripcion' => 'Administrador del sistema']
        );
        Rol::query()->firstOrCreate(
            ['nombre_rol' => 'funcionario'],
            ['estado_rol' => true, 'descripcion' => 'Funcionario RRHH']
        );
    }
}
