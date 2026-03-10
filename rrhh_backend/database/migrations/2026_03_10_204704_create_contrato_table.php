<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Contrato', function (Blueprint $table) {

    $table->id('Cod_Contrato');

    $table->string('tipo_contrato', 150);
    
    $table->unsignedBigInteger('Cod_empleado'); // FK
    
    $table->string('forma_de_pago', 150);
    $table->date('fecha_ingreso');
    $table->date('fecha_fin')->nullable();

    $table->integer('salario_base');

    $table->unsignedBigInteger('Cod_cargo'); // FK

    $table->string('modalidad_trabajo', 150);
    $table->string('horario_trabajo', 150);

    $table->boolean('auxilio_transporte');

    $table->text('descripcion')->nullable();

    $table->string('estado_contrato', 20);

    $table->timestamps();

    $table->foreign('Cod_empleado')
          ->references('Cod_empleado')
          ->on('empleados')
          ->onDelete('cascade');

    $table->foreign('Cod_cargo')
          ->references('Cod_cargo')
          ->on('Cargo')
          ->onDelete('cascade');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('Contrato');
    }
};
