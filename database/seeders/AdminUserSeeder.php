<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** @deprecated Usar UsuarioSeeder */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UsuarioSeeder::class);
    }
}
