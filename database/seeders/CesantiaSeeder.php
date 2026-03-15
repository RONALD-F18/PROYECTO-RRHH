<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cesantia;

class CesantiaSeeder extends Seeder
{
    public function run(): void
    {
        $fondos = [
            ['nombre_fondo_cesantia' => 'Protección Cesantías', 'descripcion_fondo_cesantia' => 'Fondo de cesantías administrado por Protección.'],
            ['nombre_fondo_cesantia' => 'Porvenir Cesantías', 'descripcion_fondo_cesantia' => 'Fondo de cesantías administrado por Porvenir.'],
            ['nombre_fondo_cesantia' => 'Colfondos Cesantías', 'descripcion_fondo_cesantia' => 'Fondo de cesantías administrado por Colfondos.'],
            ['nombre_fondo_cesantia' => 'FNA Cesantías', 'descripcion_fondo_cesantia' => 'Fondo Nacional del Ahorro - Cesantías.'],
            ['nombre_fondo_cesantia' => 'Skandia Cesantías', 'descripcion_fondo_cesantia' => 'Fondo de cesantías administrado por Skandia.'],
        ];

        foreach ($fondos as $fondo) {
            Cesantia::updateOrCreate(
                ['nombre_fondo_cesantia' => $fondo['nombre_fondo_cesantia']],
                ['descripcion_fondo_cesantia' => $fondo['descripcion_fondo_cesantia']]
            );
        }
    }
}

