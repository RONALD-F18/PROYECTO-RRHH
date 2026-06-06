<?php

namespace Tests\Soporte\Concerns;

use App\Models\Usuario;
use App\Services\AuthService;

/**
 * Cabecera Authorization Bearer con token Sanctum (SPA / API).
 */
trait ConCabeceraAutenticacionJwt
{
    protected function tokenJwtPara(Usuario $usuario): string
    {
        $token = $usuario->createToken('test')->plainTextToken;
        $this->assertNotEmpty($token, 'Sanctum: debe generarse un token en pruebas.');

        return $token;
    }

    protected function conJwt(Usuario $usuario): static
    {
        return $this->withHeader('Authorization', 'Bearer '.$this->tokenJwtPara($usuario));
    }
}
