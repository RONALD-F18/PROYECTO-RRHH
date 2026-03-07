<?php

namespace App\Repositories\Eloquent;

use App\Models\PasswordResetToken;
use App\Repositories\Interfaces\PasswordResetInterface;

class PasswordResetRepository implements PasswordResetInterface
{
    public function deleteByEmail(string $email): void
    {
        PasswordResetToken::where('email_usuario', $email)->delete();
    }

    public function create(array $data): void
    {
        PasswordResetToken::create($data);
    }

    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken
    {
        return PasswordResetToken::where('email_usuario', $email)
            ->where('token', $token)
            ->first();
    }

    public function delete(PasswordResetToken $registro): void
    {
        $registro->delete();
    }
}