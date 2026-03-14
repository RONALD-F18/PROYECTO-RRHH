<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // ID autoincremental
            $table->id();

            // Email del usuario que solicitó el reset
            // No lo hacemos foreign key para no depender del modelo Usuario directamente
            $table->string('email_usuario');

            // El token se guarda HASHEADO con SHA-256 (nunca en texto plano)
            $table->string('token')->unique();

            // Fecha y hora de expiración (se calcula al crear: now() + 30 minutos)
            $table->timestamp('expires_at');

            // created_at y updated_at automáticos de Laravel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};