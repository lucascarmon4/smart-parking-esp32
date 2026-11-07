<?php

namespace App\Http\Resources;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Evento */
class EventoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tipo' => $this->tipo,
            'descricao' => $this->descricao(),
            'horario' => $this->created_at->format('H:i:s'),
        ];
    }
}
