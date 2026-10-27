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
4. Clique em um sensor de vaga e arraste a distância para menos de 10 cm → a vaga fica ocupada e o LED verde apaga
5. No sensor da entrada, menos de 15 cm abre a cancela (se houver vaga)

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
