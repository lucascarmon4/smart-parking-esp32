<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventoResource;
use App\Http\Resources\VagaResource;
use App\Models\Evento;
use App\Models\Vaga;
use App\Support\Esp32;
use Illuminate\Http\JsonResponse;

/**
 * Estado atual do estacionamento, consultado pelo dashboard a cada 1 s.
 */
class EstadoController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $vagas = Vaga::orderBy('numero')->get();

        return response()->json([
            'vagas' => VagaResource::collection($vagas),
            'livres' => $vagas->where('ocupada', false)->count(),
            'total' => $vagas->count(),
            'cancela_aberta' => Esp32::cancelaAberta(),
            'esp32_online' => Esp32::online(),
            'ultimo_contato' => Esp32::ultimoContato()?->format('H:i:s'),
            'eventos' => EventoResource::collection(Evento::with('vaga')->latest('id')->limit(10)->get()),
        ]);
    }
}
