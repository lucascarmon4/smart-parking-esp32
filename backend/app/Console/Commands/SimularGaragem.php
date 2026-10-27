<?php

namespace App\Console\Commands;

use App\Actions\RegistrarTelemetria;
use App\Models\Vaga;
use Illuminate\Console\Command;

/**
 * Gera telemetria falsa para desenvolver o dashboard sem ESP32 nem Wokwi.
 */
class SimularGaragem extends Command
{
    protected $signature = 'garagem:simular {--intervalo=2 : Segundos entre cada mudança}';

    protected $description = 'Simula carros entrando e saindo das vagas';

    public function handle(RegistrarTelemetria $registrar): int
    {
        $intervalo = max(1, (int) $this->option('intervalo'));

        $this->info('Simulando a garagem. Ctrl+C para parar.');

        while (true) {
            $vagas = Vaga::orderBy('numero')->get();

            if ($vagas->isEmpty()) {
                $this->error('Nenhuma vaga cadastrada. Rode: php artisan db:seed');

                return self::FAILURE;
            }

            $sorteada = $vagas->random();

            $leituras = $vagas->map(function (Vaga $vaga) use ($sorteada) {
                $ocupada = $vaga->is($sorteada) ? ! $vaga->ocupada : $vaga->ocupada;

                return [
                    'numero' => $vaga->numero,
                    'ocupada' => $ocupada,
                    'distancia_cm' => $ocupada ? random_int(3, 9) : random_int(80, 200),
                ];
            })->all();

            $registrar->handle($leituras, cancelaAberta: (bool) random_int(0, 1));

            $this->line(sprintf(
                'Vaga %d %s',
                $sorteada->numero,
                $sorteada->ocupada ? 'liberada' : 'ocupada',
            ));

            sleep($intervalo);
        }
    }
}
