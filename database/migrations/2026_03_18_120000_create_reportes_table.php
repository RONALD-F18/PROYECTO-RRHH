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
        Schema::create('reportes', function (Blueprint $table) {
            $table->id('cod_reporte');

            $table->unsignedBigInteger('cod_empleado')->nullable();
            $table->unsignedBigInteger('cod_contrato')->nullable();
            $table->unsignedBigInteger('cod_usuario')->nullable();

            $table->string('tipo_certificacion', 30)->nullable();

            $table->date('fecha_emision');
            $table->string('descripcion', 150)->nullable();

            // Campos propios del módulo de reportes
            $table->string('modulo', 30);
            $table->string('tipo_reporte', 50);
            $table->string('estado', 20)->default('Generado');

            $table->timestamps();

            $table->foreign('cod_empleado')->references('cod_empleado')->on('empleados');
            $table->foreign('cod_contrato')->references('cod_contrato')->on('contrato');
            $table->foreign('cod_usuario')->references('cod_usuario')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};

