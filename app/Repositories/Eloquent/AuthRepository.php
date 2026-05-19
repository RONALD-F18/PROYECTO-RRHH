<?php

namespace App\Repositories\Eloquent;

use App\Models\Usuario;
use App\Repositories\Interfaces\AuthInterface;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    protected ?Usuario $user = null;

    public function attemptLogin(array $credentials): bool
    {
        $this->user = Usuario::where('email_usuario', $credentials['email_usuario'])->first();

        if (! $this->user || ! Hash::check($credentials['contrasena_usuario'], $this->user->contrasena_usuario)) {
            $this->user = null;

            return false;
        }

        return true;
    }

    public function getUser(): ?Usuario
    {
        return $this->user;
    }
}
