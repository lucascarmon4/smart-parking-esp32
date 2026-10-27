#pragma once

#include <Arduino.h>

// ─── Vagas ──────────────────────────────────────────────────────────────────
// Cada vaga tem um HC-SR04 (trig/echo) e um par de LEDs (verde/vermelho).
// Os ECHO das vagas usam os GPIOs 34, 35, 36 (VP) e 39 (VN), que são só de entrada.

struct PinosVaga {
    uint8_t numero;
    uint8_t trig;
    uint8_t echo;
    uint8_t ledVerde;
    uint8_t ledVermelho;
};

constexpr PinosVaga PINOS_VAGAS[] = {
    {1, 5, 34, 13, 12},
    {2, 18, 35, 14, 27},
    {3, 19, 36, 26, 25},
    {4, 23, 39, 33, 32},
};

constexpr size_t TOTAL_VAGAS = sizeof(PINOS_VAGAS) / sizeof(PINOS_VAGAS[0]);

// Abaixo dessa distância a vaga é considerada ocupada.
constexpr uint16_t DISTANCIA_OCUPADA_CM = 10;

// Leituras consecutivas iguais antes de mudar o estado (filtra ruído do sensor).
constexpr uint8_t LEITURAS_PARA_CONFIRMAR = 3;

// ─── Cancela ────────────────────────────────────────────────────────────────
constexpr uint8_t PINO_ENTRADA_TRIG = 17;
constexpr uint8_t PINO_ENTRADA_ECHO = 16;
constexpr uint8_t PINO_SERVO = 4;

constexpr uint16_t DISTANCIA_CARRO_NA_ENTRADA_CM = 15;
constexpr uint8_t ANGULO_CANCELA_FECHADA = 0;
constexpr uint8_t ANGULO_CANCELA_ABERTA = 90;
constexpr uint32_t FECHAR_CANCELA_APOS_MS = 2000;

// ─── Display LCD 16x2 I2C (SDA 21, SCL 22) ──────────────────────────────────
constexpr uint8_t ENDERECO_LCD = 0x27;

// ─── Tempos ─────────────────────────────────────────────────────────────────
constexpr uint32_t INTERVALO_LEITURA_MS = 100;
constexpr uint32_t INTERVALO_HEARTBEAT_MS = 5000;
