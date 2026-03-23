<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incapacidad', function (Blueprint $table) {
            $table->id('cod_incapacidad');
            $table->string('descripcion', 200)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_radicacion')->nullable();
            $table->unsignedBigInteger('cod_tipo_incapacidad');
            $table->unsignedBigInteger('cod_empleado');
            $table->unsignedBigInteger('cod_clasificacion_enfermedad')->nullable();
            $table->string('estado_incapacidad', 25)->default('Activa');
            $table->string('entidad_responsable', 150)->nullable();
            $table->timestamps();

            $table->foreign('cod_tipo_incapacidad')->references('cod_tipo_incapacidad')->on('tipo_incapacidad')->onDelete('restrict');
            $table->foreign('cod_empleado')->references('cod_empleado')->on('empleados')->onDelete('cascade');
            $table->foreign('cod_clasificacion_enfermedad')->references('cod_clasificacion_enfermedad')->on('clasificacion_enfermedad')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incapacidad');
    }
};
