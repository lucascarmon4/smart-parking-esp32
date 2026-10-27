#include "SensorUltrassonico.h"

namespace {
// ~25 ms cobre o alcance máximo do HC-SR04 (~4 m ida e volta).
constexpr uint32_t TIMEOUT_ECO_US = 25000;
}

SensorUltrassonico::SensorUltrassonico(uint8_t trig, uint8_t echo) : trig_(trig), echo_(echo) {}

void SensorUltrassonico::iniciar() {
    pinMode(trig_, OUTPUT);
    pinMode(echo_, INPUT);
    digitalWrite(trig_, LOW);
}

uint16_t SensorUltrassonico::medirCm() {
    digitalWrite(trig_, LOW);
    delayMicroseconds(2);
    digitalWrite(trig_, HIGH);
    delayMicroseconds(10);
    digitalWrite(trig_, LOW);

    const unsigned long duracaoUs = pulseIn(echo_, HIGH, TIMEOUT_ECO_US);

    if (duracaoUs == 0) {
        return SEM_LEITURA;
    }

    // Som a ~343 m/s → 0,0343 cm/µs, dividido por 2 (ida e volta).
    return static_cast<uint16_t>(duracaoUs / 58);
}
