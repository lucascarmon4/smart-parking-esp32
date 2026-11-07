# Smart Parking ESP32

Estacionamento inteligente com ESP32 que monitora as vagas, controla a cancela da entrada e mostra tudo em tempo real num dashboard web.

Projeto da disciplina de Sistemas Embarcados — Unisantos.

```
ESP32 (Wokwi) ──Wi-Fi──▶ POST /api/telemetria ──▶ Laravel + SQLite ◀── GET /api/estado (a cada 1 s) ── Dashboard
```

## Estrutura

| Pasta       | O que tem                                                               |
| ----------- | ----------------------------------------------------------------------- |
| `firmware/` | Projeto PlatformIO do ESP32 + `diagram.json` e `wokwi.toml` do simulador |
| `backend/`  | Laravel: recebe a telemetria do ESP32 e serve o dashboard               |

## Pré-requisitos

- VS Code com as extensões **PlatformIO IDE** e **Wokwi Simulator**
- Licença do Wokwi for VS Code: `F1 → Wokwi: Request a New License` (vale por máquina e é temporária)
- PHP 8.3+ e Composer (no Windows e no Mac, o jeito mais fácil é o [Laravel Herd](https://herd.laravel.com))

## Rodando

### 1. Backend + dashboard

Na primeira vez, dentro de `backend/`:

```sh
composer run setup   # instala dependências, cria o .env, o banco SQLite e as 4 vagas
```

Depois, sempre que for usar:

```sh
composer run dev     # sobe o servidor em http://localhost:8000
```

Abra http://localhost:8000 no navegador. Enquanto o ESP32 não mandar nada, ele aparece como offline.

### 2. Simulação (Wokwi)

Com o backend rodando:

1. No VS Code, abra a pasta `firmware/`
2. Compile pelo ✓ da barra do PlatformIO (o primeiro build baixa o toolchain e demora alguns minutos)
3. `F1 → Wokwi: Start Simulator`
4. No terminal do Wokwi devem aparecer `Conectando ao Wi-Fi... conectado!` e, a cada 5 s, `telemetria: ... -> HTTP 204`
5. Clique em um sensor de vaga e arraste a distância para menos de 10 cm → a vaga fica ocupada, o LED verde apaga e o dashboard atualiza
6. No sensor da entrada, menos de 15 cm abre a cancela (se houver vaga)

Mudou o `main.cpp`? Compile (✓) e reinicie o simulador. Mudou só o `diagram.json`? Basta reiniciar o simulador.

Dentro do simulador, `host.wokwi.internal` aponta para o `localhost` do computador, por isso o firmware funciona igual no Windows e no Mac.

Para zerar o histórico do dashboard: `php artisan migrate:fresh --seed` dentro de `backend/`.

## API

`POST /api/telemetria` — enviado pelo ESP32 a cada mudança e a cada 5 s. O header `X-Device-Token` precisa ser igual ao `ESP32_TOKEN` do `backend/.env` (o mesmo valor de `TOKEN` no `firmware/src/main.cpp`).

```json
{
  "vagas": [
    { "numero": 1, "ocupada": true, "distancia_cm": 6 },
    { "numero": 2, "ocupada": false, "distancia_cm": null }
  ],
  "cancela_aberta": false
}
```

`GET /api/estado` — estado atual usado pelo dashboard: vagas, livres, cancela, ESP32 online e últimas movimentações.

Testes do backend: `composer test` dentro de `backend/`.

## Ligações

| Componente           | Pinos do ESP32                 |
| -------------------- | ------------------------------ |
| Vaga 1 (HC-SR04)     | TRIG 26 · ECHO 25 · LED 27     |
| Vaga 2 (HC-SR04)     | TRIG 32 · ECHO 35 · LED 33     |
| Vaga 3 (HC-SR04)     | TRIG 18 · ECHO 5 · LED 19      |
| Vaga 4 (HC-SR04)     | TRIG 16 · ECHO 4 · LED 17      |
| Sensor da entrada    | TRIG 13 · ECHO 12              |
| Servo (cancela)      | PWM 14                         |
| LCD 16x2 I2C (0x27)  | SDA 21 · SCL 22                |

Vagas 1 e 2 ficam no lado esquerdo do ESP32 e vagas 3 e 4 no direito, para os fios não se cruzarem. O LED verde aceso indica vaga livre.

Montagem física:

- Cada LED com resistor de 220 Ω em série (no diagrama, entre o catodo e o GND)
- O ECHO do HC-SR04 sai em 5 V e o ESP32 aceita 3,3 V: use divisor de tensão (1 kΩ + 2 kΩ) ou o modelo **HC-SR04P**
- Servo e sensores no 5 V de uma fonte externa, com GND em comum com o ESP32
