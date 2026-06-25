<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Bancos con cod_banco fijos (rango 80k) alineados al front.
 * Fuente única: database/data/catalogos_colombia_suplemento.php
 */
class BancoSeeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('data/catalogos_colombia_suplemento.php');
        $now = now();

        foreach ($data['bancos'] as [$cod, $nombre, $desc]) {
            DB::table('bancos')->updateOrInsert(
                ['cod_banco' => $cod],
                [
                    'nombre_banco' => $nombre,
                    'descripcion_banco' => $desc,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $extras = [
            [80023, 'Bancolombia', 'Banco líder en Colombia con amplia red de oficinas y servicios digitales.'],
            [80024, 'BBVA Colombia', 'Banca múltiple con presencia nacional e internacional.'],
            [80025, 'Davivienda', 'Banco con fuerte presencia en pagos y crédito de consumo.'],
            [80026, 'Nequi', 'Billetera digital del Grupo Bancolombia.'],
            [80027, 'Daviplata', 'Billetera digital de Davivienda.'],
        ];

        foreach ($extras as [$cod, $nombre, $desc]) {
            DB::table('bancos')->updateOrInsert(
                ['cod_banco' => $cod],
                [
                    'nombre_banco' => $nombre,
                    'descripcion_banco' => $desc,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
