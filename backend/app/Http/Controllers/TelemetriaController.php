<?php

namespace App\Http\Controllers;

use App\Actions\RegistrarTelemetria;
use App\Http\Requests\RegistrarTelemetriaRequest;
use Illuminate\Http\Response;

class TelemetriaController extends Controller
{
    public function __invoke(RegistrarTelemetriaRequest $request, RegistrarTelemetria $registrar): Response
    {
        $registrar->handle(
            vagas: $request->validated('vagas'),
            cancelaAberta: $request->boolean('cancela_aberta'),
        );

        return response()->noContent();
    }
}
