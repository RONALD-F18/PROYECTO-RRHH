<?php

namespace App\Repositories\Interfaces;

interface PasswordResetInterface
{
    public function deleteByEmail(string $email): void;
    public function create(array $data): void;
    public function findByEmailAndToken(string $email, string $token): ?\App\Models\PasswordResetToken;
    public function delete(\App\Models\PasswordResetToken $registro): void;
}