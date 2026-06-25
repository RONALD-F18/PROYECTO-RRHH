<?php

namespace App\Support;

use App\Models\Empleado;

class EmpleadoPayload
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function paraCrear(array $data, int $codUsuario): array
    {
        $payload = self::soloFillable($data);
        $payload['cod_usuario'] = $codUsuario;
        $payload['estado_emp'] = strtoupper((string) ($payload['estado_emp'] ?? 'ACTIVO'));
        $payload['descripcion'] = (string) ($payload['descripcion'] ?? '');
        $payload['sexo'] = (string) ($payload['sexo'] ?? 'Masculino');

        if (isset($payload['cod_banco'])) {
            $payload['cod_banco'] = (int) $payload['cod_banco'];
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function paraActualizar(array $data): array
    {
        $payload = self::soloFillable($data);

        if (array_key_exists('estado_emp', $payload) && $payload['estado_emp'] !== null) {
            $payload['estado_emp'] = strtoupper((string) $payload['estado_emp']);
        }

        if (array_key_exists('cod_banco', $payload) && $payload['cod_banco'] !== null) {
            $payload['cod_banco'] = (int) $payload['cod_banco'];
        }

        if (array_key_exists('descripcion', $payload) && $payload['descripcion'] === null) {
            $payload['descripcion'] = '';
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function soloFillable(array $data): array
    {
        return array_intersect_key($data, array_flip((new Empleado)->getFillable()));
    }
}
