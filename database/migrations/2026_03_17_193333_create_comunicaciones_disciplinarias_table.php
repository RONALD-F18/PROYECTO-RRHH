<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicaciones_disciplinarias', function (Blueprint $table) {
            $table->id('cod_disciplinario');
            $table->string('tipo_comunicacion', 50);
            $table->date('fecha_emision');
            $table->date('fecha_inicio_suspension')->nullable();
            $table->date('fecha_fin_suspension')->nullable();
            $table->string('estado_comunicacion', 20);
            $table->string('motivo_comunicacion', 20);
            $table->text('descripcion')->nullable();
            $table->integer('dias_suspension')->nullable();

            $table->foreignId('cod_empleado')
                  ->constrained('empleados', 'cod_empleado')
                  ->cascadeOnDelete();

            $table->foreignId('cod_usuario')
                  ->constrained('usuarios', 'cod_usuario')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicaciones_disciplinarias');
    }
};