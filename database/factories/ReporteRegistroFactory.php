<?php

namespace Database\Factories;

use App\Models\ReporteRegistro;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReporteRegistro>
 */
class ReporteRegistroFactory extends Factory
{
    protected $model = ReporteRegistro::class;

    public function definition(): array
    {
        return [
            'cod_usuario' => Usuario::factory(),
            'modulo' => 'empleados',
            'tipo' => 'resumen_general',
            'estado' => 'Generado',
            'descripcion' => null,
        ];
    }
}
