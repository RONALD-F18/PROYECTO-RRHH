<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthInterface;

class AuthService
{
    public const TOKEN_NAME = 'spa-github-pages';

    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $credentials): array
    {
        if (! $this->authRepository->attemptLogin($credentials)) {
            throw new \Exception('Credenciales inválidas');
        }

        $user = $this->authRepository->getUser();
        $plainTextToken = $user->createToken(self::TOKEN_NAME)->plainTextToken;

        return [
            'access_token' => $plainTextToken,
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ];
    }
}
