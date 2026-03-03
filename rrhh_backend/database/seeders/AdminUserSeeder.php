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

        DB::table('usuarios')->insert([
            'cod_rol'            => $rolAdmin->cod_rol,
            'nombre_usuario'     => 'admin',
            'email_usuario'      => 'admin@gmail.com',
            'contrasena_usuario' => Hash::make('admin123'),
            'estado_usuario'     => true,
            'fecha_registro'     => now(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }
}