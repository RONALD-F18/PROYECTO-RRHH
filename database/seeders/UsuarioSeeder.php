<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('usuarios')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $ahora = now();

        $usuarios = [
            [
                'cod_usuario' => 1,
                'nombre_usuario' => 'AdminRonald',
                'email_usuario' => 'ronaldacademy223@gmail.com',
                'contrasena_usuario' => Hash::make('Ronaltix@7'),
                'cod_rol' => 1,
                'estado_usuario' => 1,
                'fecha_registro' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'cod_usuario' => 2,
                'nombre_usuario' => 'AdminAngela',
                'email_usuario' => 'tatisg234p@gmail.com',
                'contrasena_usuario' => Hash::make('Angela@8'),
                'cod_rol' => 1,
                'estado_usuario' => 1,
                'fecha_registro' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'cod_usuario' => 3,
                'nombre_usuario' => 'Funcionario1',
                'email_usuario' => 'ronalcrack222@gmail.com',
                'contrasena_usuario' => Hash::make('Ronald1234'),
                'cod_rol' => 2,
                'estado_usuario' => 1,
                'fecha_registro' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'cod_usuario' => 8,
                'nombre_usuario' => 'AdminWilli',
                'email_usuario' => 'willingtonguzmana@gmail.com',
                'contrasena_usuario' => '$2y$12$Z0pE3197Ym79l8tqfBfveO7/34xNWh9f2qRAn15S.4b.8w2nI4FzG',
                'cod_rol' => 1,
                'estado_usuario' => 1,
                'fecha_registro' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'cod_usuario' => 9,
                'nombre_usuario' => 'AdminTatiana',
                'email_usuario' => 'tatis.cruzmolina@gmail.com',
                'contrasena_usuario' => '$2y$12$uK9Y2b27Z6bA6oM7R0YxeODeZp5pE/G2GzM9Y98v7F6pE5e7X2xDG',
                'cod_rol' => 1,
                'estado_usuario' => 1,
                'fecha_registro' => $ahora,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        DB::table('usuarios')->insert($usuarios);
    }
}
