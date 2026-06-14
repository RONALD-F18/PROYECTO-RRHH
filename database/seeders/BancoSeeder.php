<?php

namespace Database\Seeders;

use App\Models\Banco;
use Illuminate\Database\Seeder;

class BancoSeeder extends Seeder
{
    public function run(): void
    {
        $bancos = [
            ['nombre_banco' => 'Bancolombia', 'descripcion_banco' => 'Banco líder en Colombia con amplia red de oficinas y servicios digitales.'],
            ['nombre_banco' => 'BBVA Colombia', 'descripcion_banco' => 'Banca múltiple con presencia nacional e internacional.'],
            ['nombre_banco' => 'Banco de Bogotá', 'descripcion_banco' => 'Entidad bancaria tradicional del Grupo Aval.'],
            ['nombre_banco' => 'Davivienda', 'descripcion_banco' => 'Banco con fuerte presencia en pagos y crédito de consumo.'],
            ['nombre_banco' => 'Banco Popular', 'descripcion_banco' => 'Banco del Grupo Aval orientado a personas naturales.'],
            ['nombre_banco' => 'Banco AV Villas', 'descripcion_banco' => 'Banco del Grupo Aval con enfoque en vivienda y consumo.'],
            ['nombre_banco' => 'Banco Caja Social', 'descripcion_banco' => 'Banco del sector social con cobertura nacional.'],
            ['nombre_banco' => 'Scotiabank Colpatria', 'descripcion_banco' => 'Banco con soluciones corporativas y de consumo.'],
            ['nombre_banco' => 'Banco Agrario de Colombia', 'descripcion_banco' => 'Entidad estatal con enfoque rural y agropecuario.'],
            ['nombre_banco' => 'Banco Falabella', 'descripcion_banco' => 'Banco de consumo con integración retail.'],
            ['nombre_banco' => 'Nequi', 'descripcion_banco' => 'Billetera digital del Grupo Bancolombia.'],
            ['nombre_banco' => 'Daviplata', 'descripcion_banco' => 'Billetera digital de Davivienda.'],
        ];

        foreach ($bancos as $banco) {
            Banco::updateOrCreate(
                ['nombre_banco' => $banco['nombre_banco']],
                ['descripcion_banco' => $banco['descripcion_banco']]
            );
        }
    }
}
