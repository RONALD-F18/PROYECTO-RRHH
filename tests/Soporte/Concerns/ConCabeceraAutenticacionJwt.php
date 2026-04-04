<?php

namespace Tests\Soporte\Concerns;

use App\Models\Usuario;

/**
 * Genera cabecera Authorization Bearer para pruebas de API con guard JWT.
 */
trait ConCabeceraAutenticacionJwt
{
    protected function tokenJwtPara(Usuario $usuario): string
    {
        $token = auth('api')->login($usuario);
        $this->assertNotEmpty($token, 'JWT: el guard api debe devolver un token en pruebas.');

        return $token;
    }

    protected function conJwt(Usuario $usuario): static
    {
        return $this->withHeader('Authorization', 'Bearer '.$this->tokenJwtPara($usuario));
    }
}
