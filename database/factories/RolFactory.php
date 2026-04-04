<?php

namespace Database\Factories;

use App\Models\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rol>
 */
class RolFactory extends Factory
{
    protected $model = Rol::class;

    public function definition(): array
    {
        return [
            'nombre_rol' => fake()->unique()->slug(2),
            'estado_rol' => true,
            'descripcion' => null,
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn () => [
            'nombre_rol' => 'administrador',
            'descripcion' => 'Rol administrador',
        ]);
    }

    public function funcionario(): static
    {
        return $this->state(fn () => [
            'nombre_rol' => 'funcionario',
            'descripcion' => 'Rol funcionario',
        ]);
    }
}
