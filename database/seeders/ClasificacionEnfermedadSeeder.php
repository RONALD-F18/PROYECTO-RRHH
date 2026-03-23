<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClasificacionEnfermedad;

class ClasificacionEnfermedadSeeder extends Seeder
{
    public function run(): void
    {
        $clasificaciones = [
            ['nombre_clasificacion' => 'Enfermedades respiratorias', 'codigo_cie10' => 'J00', 'descripcion' => 'Rinofaringitis aguda (resfriado común)'],
            ['nombre_clasificacion' => 'Enfermedades respiratorias', 'codigo_cie10' => 'J06', 'descripcion' => 'Infecciones agudas de vías respiratorias superiores'],
            ['nombre_clasificacion' => 'Enfermedades digestivas', 'codigo_cie10' => 'K21', 'descripcion' => 'Enfermedad del reflujo gastroesofágico'],
            ['nombre_clasificacion' => 'Trastornos musculoesqueléticos', 'codigo_cie10' => 'M54', 'descripcion' => 'Dorsalgia'],
            ['nombre_clasificacion' => 'Traumatismos y fracturas', 'codigo_cie10' => 'S82', 'descripcion' => 'Fractura de pierna, incluido tobillo'],
            ['nombre_clasificacion' => 'Trastornos genitourinarios', 'codigo_cie10' => 'N20', 'descripcion' => 'Cálculos renales y ureterales'],
            ['nombre_clasificacion' => 'Trastornos mentales y del comportamiento', 'codigo_cie10' => 'F32', 'descripcion' => 'Episodios depresivos'],
            ['nombre_clasificacion' => 'Trastornos neurológicos', 'codigo_cie10' => 'G43', 'descripcion' => 'Migraña'],
        ];

        foreach ($clasificaciones as $c) {
            ClasificacionEnfermedad::firstOrCreate(
                ['codigo_cie10' => $c['codigo_cie10']],
                $c
            );
        }
    }
}
