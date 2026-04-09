<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Inserta catálogos con IDs fijos alineados al front (rangos 50k–80k).
 * No duplica clasificación por codigo_cie10 si ya existe en BD.
 */
class CatalogosColombiaSuplementoSeeder extends Seeder
{
    public function run(): void
    {
        $data = require database_path('data/catalogos_colombia_suplemento.php');

        $now = now();

        foreach ($data['bancos'] as [$cod, $nombre, $desc]) {
            DB::table('bancos')->updateOrInsert(
                ['cod_banco' => $cod],
                [
                    'nombre_banco' => $nombre,
                    'descripcion_banco' => $desc,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        foreach ($data['eps'] as [$cod, $nombre, $desc]) {
            DB::table('eps')->updateOrInsert(
                ['cod_eps' => $cod],
                [
                    'nombre_eps' => $nombre,
                    'descripcion_eps' => $desc,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        foreach ($data['tipos_incapacidad'] as [$cod, $nombre, $descripcion, $clave]) {
            DB::table('tipo_incapacidad')->updateOrInsert(
                ['cod_tipo_incapacidad' => $cod],
                [
                    'nombre_tipo' => $nombre,
                    'descripcion' => $descripcion,
                    'clave_normativa' => $clave,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        foreach ($data['clasificaciones_cie10'] as [$cod, $nombre, $cie10]) {
            if (DB::table('clasificacion_enfermedad')->where('codigo_cie10', $cie10)->exists()) {
                continue;
            }
            DB::table('clasificacion_enfermedad')->updateOrInsert(
                ['cod_clasificacion_enfermedad' => $cod],
                [
                    'nombre_clasificacion' => $nombre,
                    'codigo_cie10' => $cie10,
                    'descripcion' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
