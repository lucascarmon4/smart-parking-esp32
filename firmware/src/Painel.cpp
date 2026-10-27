#include "Painel.h"

#include "config.h"

Painel::Painel() : lcd_(ENDERECO_LCD, 16, 2) {}

void Painel::iniciar() {
    lcd_.init();
    lcd_.backlight();
    lcd_.setCursor(0, 0);
    lcd_.print("Smart Parking");
    lcd_.setCursor(0, 1);
    lcd_.print("Iniciando...");
}

void Painel::mostrar(size_t vagasLivres, size_t totalVagas, bool online) {
    char linha[17];

    lcd_.setCursor(0, 0);
    if (vagasLivres == 0) {
        snprintf(linha, sizeof(linha), "%-16s", "LOTADO");
    } else {
        snprintf(linha, sizeof(linha), "Vagas: %u/%-7u", static_cast<unsigned>(vagasLivres),
                 static_cast<unsigned>(totalVagas));
    }
    lcd_.print(linha);

    lcd_.setCursor(0, 1);
    snprintf(linha, sizeof(linha), "%-16s", online ? "Wi-Fi: online" : "Wi-Fi: offline");
    lcd_.print(linha);
}
