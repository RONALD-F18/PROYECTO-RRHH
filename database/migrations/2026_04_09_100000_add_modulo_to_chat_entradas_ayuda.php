<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_entradas_ayuda', function (Blueprint $table) {
            $table->string('modulo', 50)
                ->nullable()
                ->after('titulo')
                ->comment('Clave para el front: filtrar sugerencias por pantalla (ej. empleados, contratos)');
        });
    }

    public function down(): void
    {
        Schema::table('chat_entradas_ayuda', function (Blueprint $table) {
            $table->dropColumn('modulo');
        });
    }
};
