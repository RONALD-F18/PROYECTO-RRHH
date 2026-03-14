<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nombre_rol' => 'administrador',
                'descripcion' => 'Rol con todos los permisos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre_rol' => 'funcionario',
                'descripcion' => 'Usuario comun en el sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}