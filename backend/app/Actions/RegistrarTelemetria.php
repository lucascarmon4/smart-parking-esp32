<?php

namespace App\Actions;

use App\Models\Vaga;
use App\Support\EstadoDispositivo;
use Illuminate\Support\Facades\DB;

class RegistrarTelemetria
{
    public function __construct(private EstadoDispositivo $dispositivo) {}

    /**
     * @param  array<int, array{numero: int, ocupada: bool, distancia_cm?: int|null}>  $vagas
     */
    public function handle(array $vagas, bool $cancelaAberta): void
    {
        DB::transaction(function () use ($vagas) {
            foreach ($vagas as $leitura) {
                $vaga = Vaga::updateOrCreate(
                    ['numero' => $leitura['numero']],
                    [
                        'ocupada' => $leitura['ocupada'],
                        'distancia_cm' => $leitura['distancia_cm'] ?? null,
                    ],
                );

                if ($vaga->wasRecentlyCreated || $vaga->wasChanged('ocupada')) {
                    $vaga->eventos()->create(['ocupada' => $vaga->ocupada]);
                }
            }
        });

        $this->dispositivo->registrarContato($cancelaAberta);
    }
}
