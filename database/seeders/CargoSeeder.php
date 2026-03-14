<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            ['nomb_cargo' => 'Auxiliar Administrativo', 'descripcion' => 'Apoyo en labores administrativas y de oficina.'],
            ['nomb_cargo' => 'Asistente Contable', 'descripcion' => 'Soporte en procesos contables y reportes financieros.'],
            ['nomb_cargo' => 'Analista', 'descripcion' => 'Análisis de procesos, datos o proyectos según el área.'],
            ['nomb_cargo' => 'Contador', 'descripcion' => 'Responsable de la contabilidad y estados financieros.'],
            ['nomb_cargo' => 'Coordinador', 'descripcion' => 'Coordina equipos o áreas operativas o administrativas.'],
            ['nomb_cargo' => 'Jefe de Área', 'descripcion' => 'Liderazgo y supervisión de un área o departamento.'],
            ['nomb_cargo' => 'Gerente', 'descripcion' => 'Dirección y toma de decisiones del área asignada.'],
            ['nomb_cargo' => 'Practicante', 'descripcion' => 'Estudiante en práctica laboral o pasantía.'],
        ];

        foreach ($cargos as $cargo) {
            DB::table('cargo')->insert(array_merge($cargo, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
