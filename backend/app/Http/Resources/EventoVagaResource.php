<?php

namespace App\Http\Resources;

use App\Models\EventoVaga;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EventoVaga */
class EventoVagaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vaga' => $this->vaga->numero,
            'ocupada' => $this->ocupada,
            'ocorreu_em' => $this->created_at->toIso8601String(),
        ];
    }
}
