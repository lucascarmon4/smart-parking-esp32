<?php

use App\Models\Vaga;
use App\Support\Esp32;
use Database\Seeders\VagaSeeder;

beforeEach(function () {
    $this->seed(VagaSeeder::class);
});

it('mostra as vagas e quantas estão livres', function () {
    Vaga::firstWhere('numero', 3)->registrarLeitura(true, 6);

    $this->getJson('/api/estado')
        ->assertOk()
        ->assertJsonCount(4, 'vagas')
        ->assertJsonPath('vagas.2', ['numero' => 3, 'ocupada' => true, 'distancia_cm' => 6])
        ->assertJsonPath('livres', 3)
        ->assertJsonPath('total', 4)
        ->assertJsonPath('eventos.0.descricao', 'Vaga 3 ocupada');
});

it('mostra o esp32 offline antes do primeiro contato', function () {
    $this->getJson('/api/estado')
        ->assertJsonPath('esp32_online', false)
        ->assertJsonPath('ultimo_contato', null)
        ->assertJsonPath('cancela_aberta', false);
});

it('mostra o esp32 online por alguns segundos depois do último contato', function () {
    Esp32::registrarContato(cancelaAberta: true);

    $this->getJson('/api/estado')
        ->assertJsonPath('esp32_online', true)
        ->assertJsonPath('cancela_aberta', true);

    $this->travel(Esp32::SEGUNDOS_ATE_OFFLINE + 1)->seconds();

    $this->getJson('/api/estado')->assertJsonPath('esp32_online', false);
});
