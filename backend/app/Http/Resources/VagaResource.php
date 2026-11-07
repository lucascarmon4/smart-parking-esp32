<?php

namespace App\Http\Resources;

use App\Models\Vaga;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Vaga */
class VagaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'numero' => $this->numero,
            'ocupada' => $this->ocupada,
            'distancia_cm' => $this->distancia_cm,
        ];
    }
}
