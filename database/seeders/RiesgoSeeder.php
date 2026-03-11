<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Riesgo;

class RiesgoSeeder extends Seeder
{
    public function run(): void
    {
        $riesgos = [
            ['nombre_riesgo' => 'Clase I - Riesgo mínimo', 'descripcion_riesgo' => 'Actividades administrativas y de oficina con exposición mínima.'],
            ['nombre_riesgo' => 'Clase II - Riesgo bajo', 'descripcion_riesgo' => 'Actividades comerciales y de servicios con riesgo controlado.'],
            ['nombre_riesgo' => 'Clase III - Riesgo medio', 'descripcion_riesgo' => 'Actividades industriales ligeras con maquinaria y equipos.'],
            ['nombre_riesgo' => 'Clase IV - Riesgo alto', 'descripcion_riesgo' => 'Actividades de construcción e industria pesada.'],
            ['nombre_riesgo' => 'Clase V - Riesgo máximo', 'descripcion_riesgo' => 'Actividades de alto riesgo como minería subterránea.'],
        ];

        foreach ($riesgos as $riesgo) {
            Riesgo::updateOrCreate(
                ['nombre_riesgo' => $riesgo['nombre_riesgo']],
                ['descripcion_riesgo' => $riesgo['descripcion_riesgo']]
            );
        }
    }
}

