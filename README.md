# Smart Parking ESP32

Estacionamento inteligente com ESP32 que monitora as vagas e controla a cancela da entrada.

Projeto da disciplina de Sistemas Embarcados — Unisantos.

## Estrutura

| Pasta       | O que tem                                                               |
| ----------- | ----------------------------------------------------------------------- |
| `firmware/` | Projeto PlatformIO do ESP32 + `diagram.json` e `wokwi.toml` do simulador |

## Pré-requisitos

- VS Code com as extensões **PlatformIO IDE** e **Wokwi Simulator**
- Licença do Wokwi for VS Code: `F1 → Wokwi: Request a New License` (vale por máquina e é temporária)

## Simulação (Wokwi)

1. No VS Code, abra a pasta `firmware/`
2. Compile pelo ✓ da barra do PlatformIO (o primeiro build baixa o toolchain e demora alguns minutos)
3. `F1 → Wokwi: Start Simulator`
4. Clique em um sensor de vaga e arraste a distância para menos de 10 cm → a vaga fica ocupada
5. No sensor da entrada, menos de 15 cm abre a cancela (se houver vaga)

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
