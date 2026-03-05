<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('cod_usuario');
            $table->string('nombre_usuario')->required();
            $table->string('email_usuario')->required();
            $table->string('contrasena_usuario')->required();
            $table->foreignId('cod_rol')->constrained('roles', 'cod_rol');
            $table->boolean('estado_usuario')->default(true); // true = activo, false = inactivo
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
