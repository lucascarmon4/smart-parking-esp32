#pragma once

#include "SensorUltrassonico.h"
#include "config.h"

class Vaga {
public:
    explicit Vaga(const PinosVaga& pinos);

    void iniciar();

    // Faz uma leitura e retorna true se o estado (ocupada/livre) mudou.
    bool atualizar();

    uint8_t numero() const { return numero_; }
    bool ocupada() const { return ocupada_; }
    uint16_t distanciaCm() const { return distanciaCm_; }

private:
    void acenderLeds();

    uint8_t numero_;
    uint8_t ledVerde_;
    uint8_t ledVermelho_;
    SensorUltrassonico sensor_;

    bool ocupada_ = false;
    uint16_t distanciaCm_ = SensorUltrassonico::SEM_LEITURA;
    uint8_t leiturasDivergentes_ = 0;
};
