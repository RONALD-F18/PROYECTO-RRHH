<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrato', function (Blueprint $table) {
            $table->id('cod_contrato');
            $table->string('tipo_contrato', 150);
            $table->foreignId('cod_empleado')->constrained('empleados', 'cod_empleado')->onDelete('cascade');
            $table->string('forma_de_pago', 150);
            $table->date('fecha_ingreso');
            $table->date('fecha_fin')->nullable();
            $table->integer('salario_base');
            $table->foreignId('cod_cargo')->constrained('cargo', 'cod_cargo')->onDelete('cascade');
            $table->string('modalidad_trabajo', 150);
            $table->string('horario_trabajo', 150);
            $table->boolean('auxilio_transporte');
            $table->text('descripcion')->nullable();
            $table->string('estado_contrato', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato');
    }
};
