<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Vaga */
class VagaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'numero' => $this->numero,
            'ocupada' => $this->ocupada,
            'distancia_cm' => $this->distancia_cm,
            'atualizada_em' => $this->updated_at?->toIso8601String(),
        ];
    }
}
