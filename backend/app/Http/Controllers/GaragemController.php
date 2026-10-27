<?php

namespace App\Http\Controllers;

use App\Http\Resources\VagaResource;
use App\Models\Vaga;
use App\Support\EstadoDispositivo;
use Illuminate\Http\JsonResponse;

class GaragemController extends Controller
{
    public function show(EstadoDispositivo $dispositivo): JsonResponse
    {
        $vagas = Vaga::orderBy('numero')->get();

        return response()->json([
            'total' => $vagas->count(),
            'livres' => $vagas->where('ocupada', false)->count(),
            'vagas' => VagaResource::collection($vagas),
            'cancela_aberta' => $dispositivo->cancelaAberta(),
            'dispositivo_online' => $dispositivo->online(),
            'ultimo_contato' => $dispositivo->ultimoContato()?->toIso8601String(),
        ]);
    }
}
