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
        Schema::create('inasistencias', function (Blueprint $table) {
            $table->id('cod_inasistencias');
            $table->string('motivo_inasistencia', 50);
            $table->date('fecha_inasistencia');
            $table->foreignId('cod_empleado')->nullable();
            $table->string('observaciones', 80)->nullable();
            $table->string('justificado',2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inasistencias');
    }
};
