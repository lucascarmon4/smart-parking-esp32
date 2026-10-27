# Smart Parking ESP32

Estacionamento inteligente com ESP32 que monitora as vagas e exibe a ocupação em tempo real em um dashboard web.

Projeto da disciplina de Sistemas Embarcados — Unisantos.

```
 ┌──────────────────────────┐   HTTP POST /api/telemetria   ┌──────────────────────┐   GET /api/garagem   ┌─────────────┐
 │ ESP32 (físico ou Wokwi)  │ ────────────────────────────▶ │  Laravel (backend)   │ ◀─────────────────── │  Dashboard  │
 │ 4 vagas + cancela + LCD  │       Wi-Fi, JSON + token     │  SQLite + histórico  │      polling 1 s     │  (navegador)│
 └──────────────────────────┘                               └──────────────────────┘                      └─────────────┘
```

## Estrutura

| Pasta       | O que tem                                                               |
| ----------- | ----------------------------------------------------------------------- |
| `firmware/` | Projeto PlatformIO do ESP32 + `diagram.json` e `wokwi.toml` do simulador |
| `backend/`  | Laravel: API de telemetria, histórico de eventos e dashboard            |

Abra o `smart-parking-esp32.code-workspace` no VS Code para ter as duas pastas lado a lado.

## Pré-requisitos

- PHP 8.3+, Composer e Node 20+
- VS Code com as extensões **PlatformIO IDE** e **Wokwi Simulator**
- Licença do Wokwi for VS Code: `F1 → Wokwi: Request a New License` (30 dias, renovável)

## Rodando

### 1. Backend + dashboard

```sh
cd backend
composer run setup   # instala dependências, cria o .env, o SQLite e as 4 vagas
composer run dev     # sobe o servidor em http://localhost:8000 + Vite
```

Sem simulador nem hardware, dá pra ver o dashboard funcionando com dados falsos:

```sh
php artisan garagem:simular
```

### 2. Simulação (Wokwi)

1. No VS Code, abra a pasta `firmware/`
2. Compile: `pio run -e wokwi` (ou o ✓ da barra do PlatformIO)
3. `F1 → Wokwi: Start Simulator`
4. Clique em um sensor de vaga e arraste a distância para menos de 10 cm → o LED fica vermelho e o dashboard atualiza
5. No sensor da entrada, menos de 15 cm abre a cancela (se houver vaga)

O ESP32 simulado conecta na rede `Wokwi-GUEST` e fala com o Laravel por `http://host.wokwi.internal:8000`.

### 3. ESP32 físico

1. `cp firmware/include/secrets.example.h firmware/include/secrets.h` e preencha Wi-Fi e o IP do computador
2. Rode o backend aceitando conexões da rede: `php artisan serve --host=0.0.0.0`
3. `pio run -e esp32 -t upload && pio device monitor`

## Ligações

| Componente           | Pinos do ESP32                          |
| -------------------- | --------------------------------------- |
| Vaga 1 (HC-SR04)     | TRIG 5 · ECHO 34 · LED verde 13 · vermelho 12 |
| Vaga 2 (HC-SR04)     | TRIG 18 · ECHO 35 · LED verde 14 · vermelho 27 |
| Vaga 3 (HC-SR04)     | TRIG 19 · ECHO 36 (VP) · LED verde 26 · vermelho 25 |
| Vaga 4 (HC-SR04)     | TRIG 23 · ECHO 39 (VN) · LED verde 33 · vermelho 32 |
| Sensor da entrada    | TRIG 17 · ECHO 16                       |
| Servo (cancela)      | PWM 4                                   |
| LCD 16x2 I2C (0x27)  | SDA 21 · SCL 22                         |

Montagem física:

- LEDs com resistor de 220 Ω em série
- O ECHO do HC-SR04 sai em 5 V e o ESP32 aceita 3,3 V: use divisor de tensão (1 kΩ + 2 kΩ) ou o modelo **HC-SR04P**
- Servo e sensores no 5 V de uma fonte externa, com GND em comum com o ESP32

## API

`POST /api/telemetria` — enviado pelo ESP32 a cada mudança e a cada 5 s (heartbeat). Header `X-Device-Token` igual a `GARAGEM_DEVICE_TOKEN` do `.env`.

```json
{
  "vagas": [
    { "numero": 1, "ocupada": true, "distancia_cm": 6 },
    { "numero": 2, "ocupada": false, "distancia_cm": 180 }
  ],
  "cancela_aberta": false
}
```

`GET /api/garagem` — estado atual (vagas, livres, cancela, ESP32 online).
`GET /api/garagem/eventos` — últimas movimentações.

## Testes

```sh
cd backend && composer test
```
