<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventoVagaResource;
use App\Models\EventoVaga;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventoVagaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $eventos = EventoVaga::with('vaga')
            ->latest('id')
            ->limit(15)
            ->get();

        return EventoVagaResource::collection($eventos);
    }
}
