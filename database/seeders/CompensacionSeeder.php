<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compensacion;

class CompensacionSeeder extends Seeder
{
    public function run(): void
    {
        $cajas = [
            [
                'nombre_caja_compensacion' => 'Compensar',
                'descripcion_caja_compensacion' => 'Caja de compensación familiar con presencia en Bogotá y Cundinamarca.',
            ],
            [
                'nombre_caja_compensacion' => 'Colsubsidio',
                'descripcion_caja_compensacion' => 'Caja de compensación familiar con amplia red de servicios en Colombia.',
            ],
            [
                'nombre_caja_compensacion' => 'Cafam',
                'descripcion_caja_compensacion' => 'Caja de compensación familiar de la Asociación Nacional de Industriales.',
            ],
            [
                'nombre_caja_compensacion' => 'Comfenalco Antioquia',
                'descripcion_caja_compensacion' => 'Caja de compensación familiar con sede en Antioquia.',
            ],
            [
                'nombre_caja_compensacion' => 'Comfama',
                'descripcion_caja_compensacion' => 'Caja de compensación familiar de Antioquia, enfocada en bienestar integral.',
            ],
        ];

        foreach ($cajas as $caja) {
            Compensacion::updateOrCreate(
                ['nombre_caja_compensacion' => $caja['nombre_caja_compensacion']],
                ['descripcion_caja_compensacion' => $caja['descripcion_caja_compensacion']]
            );
        }
    }
}

