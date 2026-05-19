<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre_rol' => 'administrador',
                'descripcion' => 'Rol con todos los permisos',
            ],
            [
                'nombre_rol' => 'funcionario',
                'descripcion' => 'Usuario comun en el sistema',
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre_rol' => $rol['nombre_rol']],
                [
                    'descripcion' => $rol['descripcion'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
