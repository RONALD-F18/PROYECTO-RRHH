<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clasificacion_enfermedad', function (Blueprint $table) {
            $table->id('cod_clasificacion_enfermedad');
            $table->string('nombre_clasificacion', 150)->comment('Nombre legible de la clasificación');
            $table->string('codigo_cie10', 20)->nullable()->comment('Código CIE-10 (Clasificación Internacional de Enfermedades, 10.ª revisión)');
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clasificacion_enfermedad');
    }
};
