<?php

use App\Enums\TipoEvento;
use App\Models\Evento;
use App\Models\Vaga;
use Database\Seeders\VagaSeeder;

beforeEach(function () {
    config(['services.esp32.token' => 'token-de-teste']);
    $this->seed(VagaSeeder::class);
});

function telemetria(array $ocupadas = [], bool $cancelaAberta = false): array
{
    return [
        'vagas' => collect(range(1, 4))->map(fn (int $numero) => [
            'numero' => $numero,
            'ocupada' => in_array($numero, $ocupadas),
            'distancia_cm' => in_array($numero, $ocupadas) ? 5 : 200,
        ])->all(),
        'cancela_aberta' => $cancelaAberta,
    ];
}

function enviarTelemetria(array $dados, string $token = 'token-de-teste')
{
    return test()->postJson('/api/telemetria', $dados, ['X-Device-Token' => $token]);
}

it('recusa telemetria sem o token certo', function () {
    test()->postJson('/api/telemetria', telemetria())->assertUnauthorized();
    enviarTelemetria(telemetria(), 'outro-token')->assertUnauthorized();
});

it('recusa tudo quando o token não está configurado', function () {
    config(['services.esp32.token' => null]);

    enviarTelemetria(telemetria(), '')->assertUnauthorized();
});

it('atualiza o estado das vagas', function () {
    enviarTelemetria(telemetria(ocupadas: [2]))->assertNoContent();

    $vaga = Vaga::firstWhere('numero', 2);
    expect($vaga->ocupada)->toBeTrue()
        ->and($vaga->distancia_cm)->toBe(5)
        ->and(Vaga::where('ocupada', true)->count())->toBe(1);
});

it('registra evento só quando a vaga muda de estado', function () {
    enviarTelemetria(telemetria(ocupadas: [2]));
    enviarTelemetria(telemetria(ocupadas: [2]));
    enviarTelemetria(telemetria());

    expect(Evento::pluck('tipo')->all())->toBe([TipoEvento::VagaOcupada, TipoEvento::VagaLiberada]);
});

it('registra quando a cancela abre e fecha', function () {
    enviarTelemetria(telemetria());
    enviarTelemetria(telemetria(cancelaAberta: true));
    enviarTelemetria(telemetria(cancelaAberta: true));
    enviarTelemetria(telemetria());

    expect(Evento::pluck('tipo')->all())->toBe([TipoEvento::CancelaAberta, TipoEvento::CancelaFechada]);
});

it('aceita distância nula quando o sensor não responde', function () {
    $dados = telemetria();
    $dados['vagas'][0]['distancia_cm'] = null;

    enviarTelemetria($dados)->assertNoContent();

    expect(Vaga::firstWhere('numero', 1)->distancia_cm)->toBeNull();
});

it('valida o formato enviado', function () {
    enviarTelemetria(['vagas' => [['numero' => 9, 'ocupada' => 'talvez']]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['vagas.0.numero', 'vagas.0.ocupada', 'vagas.0.distancia_cm', 'cancela_aberta']);
});
