<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('empresas')->updateOrInsert(
            ['nit' => '901347562'],
            [
                'dv'                      => '1',
                'razon_social'            => 'TALENT SPHERE S.A.S.',
                'nombre_comercial'        => 'Talent Sphere',
                'tipo_empresa'            => 'Privada',
                'estado_empresa'          => 'Activa',
                'fecha_constitucion'      => '2012-06-01',
                'direccion'               => 'Carrera 7 # 71-21, Torre Empresarial',
                'ciudad'                  => 'Bogotá D.C.',
                'departamento'            => 'Cundinamarca',
                'pais'                    => 'Colombia',
                'telefono'                => '6011234567',
                'correo'                  => 'contacto@talentsphere.cloud',
                'pagina_web'              => 'https://talentsphere.cloud',
                'nombre_representante'    => 'Ronaldo Franco',
                'documento_representante' => '1129244160',
                'fecha_creacion'          => now(),
                'fecha_actualizacion'     => now(),
                'updated_at'              => now(),
            ]
        );
    }
}
