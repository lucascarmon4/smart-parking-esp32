#include "Vaga.h"

Vaga::Vaga(const PinosVaga& pinos)
    : numero_(pinos.numero),
      ledVerde_(pinos.ledVerde),
      ledVermelho_(pinos.ledVermelho),
      sensor_(pinos.trig, pinos.echo) {}

void Vaga::iniciar() {
    sensor_.iniciar();
    pinMode(ledVerde_, OUTPUT);
    pinMode(ledVermelho_, OUTPUT);
    acenderLeds();
}

bool Vaga::atualizar() {
    distanciaCm_ = sensor_.medirCm();
    const bool leituraOcupada = distanciaCm_ < DISTANCIA_OCUPADA_CM;

    if (leituraOcupada == ocupada_) {
        leiturasDivergentes_ = 0;
        return false;
    }

    if (++leiturasDivergentes_ < LEITURAS_PARA_CONFIRMAR) {
        return false;
    }

    ocupada_ = leituraOcupada;
    leiturasDivergentes_ = 0;
    acenderLeds();

    return true;
}

void Vaga::acenderLeds() {
    digitalWrite(ledVerde_, ocupada_ ? LOW : HIGH);
    digitalWrite(ledVermelho_, ocupada_ ? HIGH : LOW);
}
