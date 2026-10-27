<?php

namespace Database\Factories;

use App\Models\Vaga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vaga>
 */
class VagaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero' => fake()->unique()->numberBetween(1, 50),
            'ocupada' => false,
            'distancia_cm' => null,
        ];
    }

    public function ocupada(): static
    {
        return $this->state(['ocupada' => true, 'distancia_cm' => 5]);
    }
}
