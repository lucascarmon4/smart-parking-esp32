<?php

namespace Database\Seeders;

use App\Models\Vaga;
use Illuminate\Database\Seeder;

class VagaSeeder extends Seeder
{
    /**
     * Cria as 4 vagas do estacionamento (mesma numeração do firmware).
     */
    public function run(): void
    {
        foreach (range(1, 4) as $numero) {
            Vaga::firstOrCreate(['numero' => $numero]);
        }
    }
}
