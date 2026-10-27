#pragma once

#include <Arduino.h>

class SensorUltrassonico {
public:
    static constexpr uint16_t SEM_LEITURA = UINT16_MAX;

    SensorUltrassonico(uint8_t trig, uint8_t echo);

    void iniciar();

    // Distância em centímetros, ou SEM_LEITURA se nada respondeu (~4 m+).
    uint16_t medirCm();

private:
    uint8_t trig_;
    uint8_t echo_;
};
