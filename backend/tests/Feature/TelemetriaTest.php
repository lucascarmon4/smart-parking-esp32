<?php

namespace Tests\Feature;

use App\Models\Vaga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelemetriaTest extends TestCase
{
    use RefreshDatabase;

    private function payload(bool $vaga1Ocupada = true): array
    {
        return [
            'vagas' => [
                ['numero' => 1, 'ocupada' => $vaga1Ocupada, 'distancia_cm' => 5],
                ['numero' => 2, 'ocupada' => false, 'distancia_cm' => 120],
            ],
            'cancela_aberta' => false,
        ];
    }

    public function test_rejeita_requisicao_sem_token(): void
    {
        $this->postJson(route('telemetria.store'), $this->payload())
            ->assertForbidden();
    }

    public function test_registra_estado_das_vagas(): void
    {
        $this->withHeader('X-Device-Token', 'token-de-teste')
            ->postJson(route('telemetria.store'), $this->payload())
            ->assertNoContent();

        $this->assertDatabaseHas('vagas', ['numero' => 1, 'ocupada' => true]);
        $this->assertDatabaseHas('vagas', ['numero' => 2, 'ocupada' => false]);
    }

    public function test_so_gera_evento_quando_a_ocupacao_muda(): void
    {
        Vaga::factory()->create(['numero' => 1, 'ocupada' => false]);
        Vaga::factory()->create(['numero' => 2, 'ocupada' => false]);

        $this->withHeader('X-Device-Token', 'token-de-teste');

        $this->postJson(route('telemetria.store'), $this->payload())->assertNoContent();
        $this->postJson(route('telemetria.store'), $this->payload())->assertNoContent();

        $this->assertDatabaseCount('eventos_vaga', 1);
    }

    public function test_garagem_retorna_resumo_das_vagas(): void
    {
        Vaga::factory()->ocupada()->create(['numero' => 1]);
        Vaga::factory()->create(['numero' => 2]);

        $this->getJson(route('garagem.show'))
            ->assertOk()
            ->assertJson(['total' => 2, 'livres' => 1])
            ->assertJsonCount(2, 'vagas');
    }
}
