<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelemetriaRequest;
use App\Models\Vaga;
use App\Support\Esp32;
use Illuminate\Http\Response;

/**
 * Recebe o estado enviado pelo ESP32 (a cada mudança e a cada 5 s).
 */
class TelemetriaController extends Controller
{
    public function __invoke(TelemetriaRequest $request): Response
    {
        foreach ($request->validated('vagas') as $leitura) {
            Vaga::where('numero', $leitura['numero'])
                ->firstOrFail()
                ->registrarLeitura($leitura['ocupada'], $leitura['distancia_cm']);
        }

        Esp32::registrarContato($request->boolean('cancela_aberta'));

        return response()->noContent();
    }
}
