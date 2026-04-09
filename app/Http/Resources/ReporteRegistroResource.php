<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ReporteRegistro
 */
class ReporteRegistroResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'modulo' => $this->modulo,
            'tipo' => $this->tipo,
            'estado' => $this->estado,
            'descripcion' => $this->descripcion,
            'created_at' => $this->created_at?->toIso8601String(),
            'nombre_usuario' => $this->usuario?->nombre_usuario,
            'usuario' => $this->when(
                $this->relationLoaded('usuario'),
                fn () => $this->usuario ? ['nombre_usuario' => $this->usuario->nombre_usuario] : null
            ),
        ];
    }
}
