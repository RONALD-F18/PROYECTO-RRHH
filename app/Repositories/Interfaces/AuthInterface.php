<?php

namespace App\Repositories\Interfaces;

use App\Models\Usuario;

interface AuthInterface
{
    public function attemptLogin(array $credentials): bool;

    public function getUser(): ?Usuario;
}