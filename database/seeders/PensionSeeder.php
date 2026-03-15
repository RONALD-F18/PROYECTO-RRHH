<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pension;

class PensionSeeder extends Seeder
{
    public function run(): void
    {
        $fondos = [
            ['nombre_fondo_pension' => 'Colpensiones', 'descripcion_fondo_pension' => 'Administradora colombiana de pensiones del régimen público.'],
            ['nombre_fondo_pension' => 'Protección', 'descripcion_fondo_pension' => 'Fondo privado de pensiones y cesantías.'],
            ['nombre_fondo_pension' => 'Porvenir', 'descripcion_fondo_pension' => 'Fondo privado de pensiones y cesantías del Grupo Aval.'],
            ['nombre_fondo_pension' => 'Colfondos', 'descripcion_fondo_pension' => 'Fondo privado de pensiones y cesantías.'],
            ['nombre_fondo_pension' => 'Skandia', 'descripcion_fondo_pension' => 'Fondo privado con portafolios de inversión para pensiones.'],
        ];

        foreach ($fondos as $fondo) {
            Pension::updateOrCreate(
                ['nombre_fondo_pension' => $fondo['nombre_fondo_pension']],
                ['descripcion_fondo_pension' => $fondo['descripcion_fondo_pension']]
            );
        }
    }
}

