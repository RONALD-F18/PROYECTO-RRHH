<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = [
            ['nomb_cargo' => 'Auxiliar Administrativo', 'descripcion' => 'Apoyo en labores administrativas y de oficina.'],
            ['nomb_cargo' => 'Asistente Contable', 'descripcion' => 'Soporte en procesos contables y reportes financieros.'],
            ['nomb_cargo' => 'Analista', 'descripcion' => 'Análisis de procesos, datos o proyectos según el área.'],
            ['nomb_cargo' => 'Analista de Recursos Humanos', 'descripcion' => 'Gestión de procesos de selección, nómina y bienestar.'],
            ['nomb_cargo' => 'Contador', 'descripcion' => 'Responsable de la contabilidad y estados financieros.'],
            ['nomb_cargo' => 'Coordinador', 'descripcion' => 'Coordina equipos o áreas operativas o administrativas.'],
            ['nomb_cargo' => 'Coordinador de RRHH', 'descripcion' => 'Coordina procesos de talento humano y clima laboral.'],
            ['nomb_cargo' => 'Jefe de Área', 'descripcion' => 'Liderazgo y supervisión de un área o departamento.'],
            ['nomb_cargo' => 'Gerente', 'descripcion' => 'Dirección y toma de decisiones del área asignada.'],
            ['nomb_cargo' => 'Gerente de RRHH', 'descripcion' => 'Dirección estratégica del área de talento humano.'],
            ['nomb_cargo' => 'Practicante', 'descripcion' => 'Estudiante en práctica laboral o pasantía.'],
            ['nomb_cargo' => 'Técnico en Sistemas', 'descripcion' => 'Soporte técnico y mantenimiento de sistemas de información.'],
            ['nomb_cargo' => 'Asistente de Nómina', 'descripcion' => 'Apoyo en liquidación y pagos de nómina.'],
            ['nomb_cargo' => 'Recepcionista', 'descripcion' => 'Atención al público y apoyo administrativo en recepción.'],
            ['nomb_cargo' => 'Operario', 'descripcion' => 'Ejecución de labores operativas en planta o campo.'],
        ];

        foreach ($cargos as $cargo) {
            Cargo::updateOrCreate(
                ['nomb_cargo' => $cargo['nomb_cargo']],
                ['descripcion' => $cargo['descripcion']]
            );
        }
    }
}
