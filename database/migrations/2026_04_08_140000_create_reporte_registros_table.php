<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_registros', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cod_usuario');

            $table->string('modulo', 30);
            $table->string('tipo', 50);
            $table->string('estado', 100);
            $table->string('descripcion', 150)->nullable();

            $table->timestamps();

            $table->foreign('cod_usuario')->references('cod_usuario')->on('usuarios')->cascadeOnDelete();

            $table->index('modulo');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_registros');
    }
};
