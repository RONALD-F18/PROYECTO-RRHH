<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Eps;

class EpsSeeder extends Seeder
{
    public function run(): void
    {
        $epsList = [
            ['nombre_eps' => 'Nueva EPS', 'descripcion_eps' => 'Entidad promotora de salud del régimen contributivo y subsidiado.'],
            ['nombre_eps' => 'EPS Sura', 'descripcion_eps' => 'EPS del grupo SURA con amplia cobertura nacional.'],
            ['nombre_eps' => 'Sanitas EPS', 'descripcion_eps' => 'Entidad promotora de salud con énfasis en atención integral.'],
            ['nombre_eps' => 'Compensar EPS', 'descripcion_eps' => 'Caja de compensación con EPS propia para sus afiliados.'],
            ['nombre_eps' => 'Famisanar EPS', 'descripcion_eps' => 'EPS producto de la alianza entre Cafam y Colsubsidio.'],
        ];

        foreach ($epsList as $eps) {
            Eps::updateOrCreate(
                ['nombre_eps' => $eps['nombre_eps']],
                ['descripcion_eps' => $eps['descripcion_eps']]
            );
        }
    }
}

