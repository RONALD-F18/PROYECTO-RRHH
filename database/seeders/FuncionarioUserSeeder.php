<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FuncionarioUserSeeder extends Seeder
{
    public function run(): void
    {
        $rolFuncionario = DB::table('roles')
            ->where('nombre_rol', 'funcionario')
            ->first();

        if (!$rolFuncionario) {
            return;
        }

        DB::table('usuarios')->updateOrInsert(
            ['email_usuario' => 'funcionario@empresa.com'],
            [
                'cod_rol'            => $rolFuncionario->cod_rol,
                'nombre_usuario'     => 'funcionario1',
                'contrasena_usuario' => Hash::make('Funcionario123'),
                'estado_usuario'     => true,
                'fecha_registro'     => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );
    }
}
