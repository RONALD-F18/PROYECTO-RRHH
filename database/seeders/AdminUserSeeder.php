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

        DB::table('usuarios')->updateOrInsert(
            ['email_usuario' => 'ronaldacademy223@gmail.com'],
            [
                'cod_rol'            => $rolAdmin->cod_rol,
                'nombre_usuario'     => 'adminRonald',
                'contrasena_usuario' => Hash::make('Ronaltix@7'),
                'estado_usuario'     => true,
                'fecha_registro'     => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );

        DB::table('usuarios')->updateOrInsert(
            ['email_usuario' => 'tatisg234p@gmail.com'],
            [
                'cod_rol'            => $rolAdmin->cod_rol,
                'nombre_usuario'     => 'AdminAgela',
                'contrasena_usuario' => Hash::make('Angela@8'),
                'estado_usuario'     => true,
                'fecha_registro'     => now(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        );

        // Eliminar admin legacy si existía
        DB::table('usuarios')->where('email_usuario', 'admin2@gmail.com')->delete();
    }
}
