<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModelUser;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            BancoSeeder::class,
            FuncionarioUserSeeder::class,
            EmpleadoSeeder::class,
            CargoSeeder::class,
            ContratoSeeder::class,
            EpsSeeder::class,
            ArlSeeder::class,
            PensionSeeder::class,
            CesantiaSeeder::class,
            CompensacionSeeder::class,
            RiesgoSeeder::class,
            AfiliacionSeeder::class,
        ]);
    }
}
