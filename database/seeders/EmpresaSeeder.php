<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('empresas')->insert([
            [
                'nit'                     => '900456789',
                'dv'                      => '3',
                'razon_social'            => 'DISTRIAGRO DG S.A.S.',
                'nombre_comercial'        => 'Distriagro DG',
                'tipo_empresa'            => 'Privada',
                'estado_empresa'          => 'Activa',
                'fecha_constitucion'      => '2012-06-01',
                'direccion'               => 'Km 5 Vía Apartadó – Turbo',
                'ciudad'                  => 'Apartadó',
                'departamento'            => 'Antioquia',
                'pais'                    => 'Colombia',
                'telefono'                => '6048123456',
                'correo'                  => 'contacto@distriagrodg.com',
                'pagina_web'              => 'https://www.distriagrodg.com',
                'nombre_representante'    => 'Diana Gómez',
                'documento_representante' => '1032456789',
                'fecha_creacion'          => now(),
                'fecha_actualizacion'     => now(),
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
        ]);
    }
}

