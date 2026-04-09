<?php

namespace Database\Seeders;

use App\Models\ChatEntradaAyuda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatEntradaAyudaSeeder extends Seeder
{
    /**
     * Sincroniza el diccionario con database/data/chat_entradas_ayuda_seed.php
     * (elimina entradas previas para evitar duplicados si cambian los títulos).
     */
    public function run(): void
    {
        $filas = require database_path('data/chat_entradas_ayuda_seed.php');

        DB::transaction(function () use ($filas): void {
            ChatEntradaAyuda::query()->delete();
            foreach ($filas as $fila) {
                ChatEntradaAyuda::query()->create(array_merge($fila, ['activo' => true]));
            }
        });
    }
}
