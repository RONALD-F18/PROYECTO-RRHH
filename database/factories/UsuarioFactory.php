<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre_usuario' => fake()->unique()->userName(),
            'email_usuario' => fake()->unique()->safeEmail(),
            'contrasena_usuario' => Hash::make('ClaveSegura1'),
            'cod_rol' => fn () => Rol::query()->where('nombre_rol', 'funcionario')->firstOrFail()->cod_rol,
            'estado_usuario' => true,
            'fecha_registro' => now(),
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn () => [
            'cod_rol' => Rol::query()->where('nombre_rol', 'administrador')->firstOrFail()->cod_rol,
        ]);
    }

    public function perfilFuncionario(): static
    {
        return $this->state(fn () => [
            'cod_rol' => Rol::query()->where('nombre_rol', 'funcionario')->firstOrFail()->cod_rol,
        ]);
    }
}
