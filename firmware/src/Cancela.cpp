#include "Cancela.h"

#include "config.h"

Cancela::Cancela() : sensorEntrada_(PINO_ENTRADA_TRIG, PINO_ENTRADA_ECHO) {}

void Cancela::iniciar() {
    sensorEntrada_.iniciar();
    servo_.attach(PINO_SERVO);
    servo_.write(ANGULO_CANCELA_FECHADA);
}

bool Cancela::atualizar(size_t vagasLivres) {
    const bool carroNaEntrada = sensorEntrada_.medirCm() < DISTANCIA_CARRO_NA_ENTRADA_CM;
    const uint32_t agora = millis();

    if (carroNaEntrada) {
        ultimoCarroNaEntradaMs_ = agora;
    }

    if (!aberta_ && carroNaEntrada && vagasLivres > 0) {
        mover(true);
        return true;
    }

    if (aberta_ && !carroNaEntrada && agora - ultimoCarroNaEntradaMs_ >= FECHAR_CANCELA_APOS_MS) {
        mover(false);
        return true;
    }

    return false;
}

void Cancela::mover(bool abrir) {
    aberta_ = abrir;
    servo_.write(abrir ? ANGULO_CANCELA_ABERTA : ANGULO_CANCELA_FECHADA);
}
