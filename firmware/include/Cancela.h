#pragma once

#include <ESP32Servo.h>

#include "SensorUltrassonico.h"

class Cancela {
public:
    Cancela();

    void iniciar();

    // Abre quando há carro na entrada e vaga livre; fecha depois que o carro passa.
    // Retorna true se a cancela mudou de estado.
    bool atualizar(size_t vagasLivres);

    bool aberta() const { return aberta_; }

private:
    void mover(bool abrir);

    Servo servo_;
    SensorUltrassonico sensorEntrada_;
    bool aberta_ = false;
    uint32_t ultimoCarroNaEntradaMs_ = 0;
};
