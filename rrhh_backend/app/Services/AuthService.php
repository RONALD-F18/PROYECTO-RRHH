<?php 

namespace App\Services;

use App\Repositories\Interfaces\AuthInterface;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService {

    protected $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $credentials)
    {

        $token = $this->authRepository->attemptLogin($credentials);
        
        if (!$token) {
            throw new \Exception('Creenciales Invalidas');
        }

        $user = $this->authRepository->getUser();


        return [
            'token' => $token,
            'user' => $user
        ];
    }
}

