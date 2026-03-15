<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestacion_social_periodo', function (Blueprint $table) {
            $table->id('cod_prestacion_social_periodo');
            $table->foreignId('cod_contrato')->constrained('contrato', 'cod_contrato')->onDelete('cascade');
            $table->date('fecha_periodo_inicio');
            $table->date('fecha_periodo_fin');
            $table->unsignedInteger('dias_trabajados');
            $table->decimal('salario_base', 12, 2);
            $table->decimal('auxilio_transporte', 12, 2)->default(0);
            $table->decimal('cesantias_valor', 12, 2)->default(0);
            $table->decimal('intereses_cesantias_valor', 12, 2)->default(0);
            $table->decimal('prima_valor', 12, 2)->default(0);
            $table->decimal('vacaciones_valor', 12, 2)->default(0);
            $table->string('estado_pago', 20)->default('Pendiente');
            $table->date('fecha_pago_cancelacion')->nullable();
            $table->date('fecha_calculo')->nullable();
            $table->string('observaciones', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestacion_social_periodo');
    }
};
