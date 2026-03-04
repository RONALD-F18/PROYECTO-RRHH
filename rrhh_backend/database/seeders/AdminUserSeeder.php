<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
            $rolAdmin = DB::table('roles')
            ->where('nombre_rol', 'administrador')
            ->first();

        if (!$rolAdmin) {
            return;
        }

        // Admin 1
        DB::table('usuarios')->updateOrInsert(
            ['email_usuario' => 'admin@gmail.com'], // condición
            [
                'cod_rol'            => $rolAdmin->cod_rol,
                'nombre_usuario'     => 'admin',
                'contrasena_usuario' => Hash::make('admin123'),
                'estado_usuario'     => true,
                'fecha_registro'     => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );

        // Admin 2
        DB::table('usuarios')->updateOrInsert(
            ['email_usuario' => 'admin2@gmail.com'],
            [
                'cod_rol'            => $rolAdmin->cod_rol,
                'nombre_usuario'     => 'admin2',
                'contrasena_usuario' => Hash::make('admin456'),
                'estado_usuario'     => true,
                'fecha_registro'     => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );
    }
}

