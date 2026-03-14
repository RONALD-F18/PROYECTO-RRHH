<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $table    = 'password_reset_tokens';
    protected $fillable = ['email_usuario', 'token', 'expires_at'];
    protected $casts    = ['expires_at' => 'datetime'];

    // Retorna true si el token ya venció
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }
}