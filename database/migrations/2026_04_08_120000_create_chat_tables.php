<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_entradas_ayuda', function (Blueprint $table) {
            $table->id('cod_entrada_ayuda');
            $table->string('titulo', 150);
            $table->text('palabras_clave')->nullable()->comment('Separadas por coma; búsqueda insensible a mayúsculas');
            $table->text('contenido');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('chat_conversaciones', function (Blueprint $table) {
            $table->id('cod_chat_conversacion');
            $table->foreignId('cod_usuario')->constrained('usuarios', 'cod_usuario')->onDelete('cascade');
            $table->string('titulo', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('chat_mensajes', function (Blueprint $table) {
            $table->id('cod_chat_mensaje');
            $table->foreignId('cod_chat_conversacion')
                ->constrained('chat_conversaciones', 'cod_chat_conversacion')
                ->onDelete('cascade');
            $table->string('rol', 20)->comment('usuario | asistente');
            $table->text('contenido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_mensajes');
        Schema::dropIfExists('chat_conversaciones');
        Schema::dropIfExists('chat_entradas_ayuda');
    }
};
