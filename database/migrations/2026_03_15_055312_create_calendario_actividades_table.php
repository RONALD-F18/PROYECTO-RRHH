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
                    Schema::create('calendario_actividades', function (Blueprint $table) {
                $table->bigIncrements('Cod_Actividad'); // Llave primaria
                
                $table->string('Titulo', 50);
                $table->string('Tipo', 20);
                
                $table->date('Fecha_Inicio');
                $table->date('Fecha_Fin')->nullable();
                
                $table->string('Estado', 20);
                $table->text('Descripcion')->nullable();
                
                $table->string('Prioridad', 20);
                $table->string('Color', 10)->nullable();
                
                $table->unsignedBigInteger('Cod_usuario'); // Llave foránea
                
                $table->date('Fecha_Creacion')->nullable();
                $table->date('Fecha_Recordatorio')->nullable();

                $table->foreign('Cod_usuario')
                    ->references('Cod_usuario')
                    ->on('usuarios')
                    ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendario_actividades');
    }
};
