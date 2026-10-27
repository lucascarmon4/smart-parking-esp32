#pragma once

#include <LiquidCrystal_I2C.h>

class Painel {
public:
    Painel();

    void iniciar();
    void mostrar(size_t vagasLivres, size_t totalVagas, bool online);

private:
    LiquidCrystal_I2C lcd_;
};
