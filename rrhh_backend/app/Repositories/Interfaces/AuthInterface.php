<?php

namespace App\Repositories\Interfaces;

interface AuthInterface
{
    public function attemptLogin(array $credentials);
    public function getUser();
}