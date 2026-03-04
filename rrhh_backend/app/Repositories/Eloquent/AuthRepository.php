<?php 

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\AuthInterface;

// Facade de JWTAuth para trabajar con JWT (login, usuario, token)
use Tymon\JWTAuth\Facades\JWTAuth;

// Excepción que se lanza cuando ocurre un error con JWT
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthRepository implements AuthInterface
{
public function attemptLogin(array $credentials)
{
    try {
        // mapeo: la columna en BD se llama contrasena_usuario, pero Auth::attempt espera 'password'
        $credencialesJWT = [
            'email_usuario' => $credentials['email_usuario'],
            'password' => $credentials['contrasena_usuario'], // <- map aquí
        ];

        $token = JWTAuth::attempt($credencialesJWT);

        return $token ?: false;
    } catch (JWTException $e) {
        return false;
    }
}

    public function getUser()
    {
        try{
            return JWTAuth::user();
        }
        catch (JWTException $e)
        {
            return null;
        }
    }
}