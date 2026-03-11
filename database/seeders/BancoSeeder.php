<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BancoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bancos')->insert([
            [
                'nombre_banco'     => 'Bancolombia',
                'descripcion_banco'=> 'Banco líder en Colombia con amplia red de oficinas y servicios digitales.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'nombre_banco'     => 'BBVA',
                'descripcion_banco'=> 'BBVA Colombia, banca múltiple con presencia nacional e internacional.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
