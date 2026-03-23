<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('actividades_calendario', function (Blueprint $table) {
            // Actividades del calendario (tareas, reuniones, hitos) creadas por usuarios (funcionarios o admin)
            $table->id('cod_actividad');

            $table->string('titulo', 50);
            $table->string('tipo', 20);

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->string('estado', 20);
            $table->text('descripcion')->nullable();

            $table->string('prioridad', 20);
            $table->string('color', 10)->nullable();

            // Usuario (funcionario o admin) que creó la actividad
            $table->foreignId('cod_usuario')
                ->constrained('usuarios', 'cod_usuario')
                ->cascadeOnDelete();

            // Fechas de creación manual y recordatorio opcional
            $table->date('fecha_creacion')->nullable();
            $table->date('fecha_recordatorio')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades_calendario');
    }
};
