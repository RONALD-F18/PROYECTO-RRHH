<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Arl;

class ArlSeeder extends Seeder
{
    public function run(): void
    {
        $arls = [
            ['nombre_arl' => 'ARL Sura', 'descripcion_arl' => 'Administradora de riesgos laborales del grupo SURA.'],
            ['nombre_arl' => 'ARL Colmena', 'descripcion_arl' => 'Administradora de riesgos laborales Colmena Seguros.'],
            ['nombre_arl' => 'ARL Positiva', 'descripcion_arl' => 'Administradora de riesgos laborales Positiva Compañía de Seguros.'],
            ['nombre_arl' => 'ARL AXA Colpatria', 'descripcion_arl' => 'Administradora de riesgos laborales AXA Colpatria.'],
            ['nombre_arl' => 'ARL Bolívar', 'descripcion_arl' => 'Administradora de riesgos laborales Seguros Bolívar.'],
        ];

        foreach ($arls as $arl) {
            Arl::updateOrCreate(
                ['nombre_arl' => $arl['nombre_arl']],
                ['descripcion_arl' => $arl['descripcion_arl']]
            );
        }
    }
}

