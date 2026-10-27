<?php

namespace Database\Seeders;

use App\Models\Vaga;
use Illuminate\Database\Seeder;

class VagaSeeder extends Seeder
{
    public const TOTAL_VAGAS = 4;

    public function run(): void
    {
        foreach (range(1, self::TOTAL_VAGAS) as $numero) {
            Vaga::firstOrCreate(['numero' => $numero]);
        }
    }
}
