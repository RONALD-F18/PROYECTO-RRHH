<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoIncapacidad;

class TipoIncapacidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre_tipo' => 'Enfermedad General (Origen Común)',
                'descripcion' => 'Incapacidad por enfermedad no laboral. Días 1-2 Empresa, 3-90 EPS 66.67%, 91-180 EPS 50%, 181+ Pensiones 50%.',
                'clave_normativa' => 'origen_comun',
            ],
            [
                'nombre_tipo' => 'Accidente Laboral',
                'descripcion' => 'Accidente de trabajo. ARL paga 100% desde el día 1.',
                'clave_normativa' => 'laboral',
            ],
            [
                'nombre_tipo' => 'Enfermedad Laboral',
                'descripcion' => 'Enfermedad profesional. ARL paga 100% desde el día 1.',
                'clave_normativa' => 'laboral',
            ],
            [
                'nombre_tipo' => 'Licencia de Maternidad',
                'descripcion' => '18 semanas (126 días). EPS paga 100%.',
                'clave_normativa' => 'maternidad',
            ],
            [
                'nombre_tipo' => 'Licencia de Paternidad',
                'descripcion' => '2 semanas (14 días). EPS paga 100%.',
                'clave_normativa' => 'paternidad',
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoIncapacidad::firstOrCreate(
                ['nombre_tipo' => $tipo['nombre_tipo']],
                $tipo
            );
        }
    }
}
