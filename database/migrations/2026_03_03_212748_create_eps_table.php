<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eps', function (Blueprint $table) {
            $table->id('cod_eps');
            $table->string('nombre_eps', 50)->unique();
            $table->string('descripcion_eps', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eps');
    }
};