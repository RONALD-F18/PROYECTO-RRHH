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
        Schema::create('certificaciones', function (Blueprint $table) {
            $table->id('cod_certificacion');

            $table->unsignedBigInteger('id_empresa');
            $table->unsignedBigInteger('cod_empleado');
            $table->unsignedBigInteger('cod_contrato')->nullable();

            $table->string('tipo_certificacion', 30);
            $table->boolean('incluye_salario')->default(false);
            $table->decimal('salario_certificado', 15, 2)->nullable();

            $table->unsignedBigInteger('cod_eps')->nullable();
            $table->unsignedBigInteger('cod_arl')->nullable();
            $table->unsignedBigInteger('cod_pension')->nullable();
            $table->unsignedBigInteger('cod_caja')->nullable();
            $table->unsignedBigInteger('cod_cesantias')->nullable();

            $table->date('fecha_emision');
            $table->string('ciudad_emision', 100)->default('Medellín');
            $table->string('descripcion', 150)->nullable();

            $table->timestamps();

            $table->foreign('id_empresa')->references('id_empresa')->on('empresas');
            $table->foreign('cod_empleado')->references('cod_empleado')->on('empleados');
            $table->foreign('cod_contrato')->references('cod_contrato')->on('contrato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificaciones');
    }
};

