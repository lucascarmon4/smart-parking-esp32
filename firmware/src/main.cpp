#include <Arduino.h>

#include "Cancela.h"
#include "ClienteApi.h"
#include "Painel.h"
#include "Vaga.h"
#include "config.h"

Vaga vaga1(PINOS_VAGAS[0]);
Vaga vaga2(PINOS_VAGAS[1]);
Vaga vaga3(PINOS_VAGAS[2]);
Vaga vaga4(PINOS_VAGAS[3]);
Vaga* const vagas[TOTAL_VAGAS] = {&vaga1, &vaga2, &vaga3, &vaga4};

Cancela cancela;
Painel painel;
ClienteApi api;

uint32_t ultimaLeituraMs = 0;
uint32_t ultimoEnvioMs = 0;
bool envioPendente = true;

size_t contarVagasLivres() {
    size_t livres = 0;
    for (Vaga* vaga : vagas) {
        if (!vaga->ocupada()) {
            livres++;
        }
    }
    return livres;
}

void setup() {
    Serial.begin(115200);
    Serial.println("\n== Smart Parking ESP32 ==");

    painel.iniciar();
    for (Vaga* vaga : vagas) {
        vaga->iniciar();
    }
    cancela.iniciar();

    api.conectarWifi();
    painel.mostrar(contarVagasLivres(), TOTAL_VAGAS, api.conectado());
}

void loop() {
    const uint32_t agora = millis();

    if (agora - ultimaLeituraMs >= INTERVALO_LEITURA_MS) {
        ultimaLeituraMs = agora;

        bool mudou = false;
        for (Vaga* vaga : vagas) {
            if (vaga->atualizar()) {
                Serial.printf("Vaga %u %s (%u cm)\n", vaga->numero(), vaga->ocupada() ? "ocupada" : "livre",
                              vaga->distanciaCm());
                mudou = true;
            }
        }

        const size_t livres = contarVagasLivres();

        if (cancela.atualizar(livres)) {
            Serial.printf("Cancela %s\n", cancela.aberta() ? "aberta" : "fechada");
            mudou = true;
        }

        if (mudou) {
            painel.mostrar(livres, TOTAL_VAGAS, api.conectado());
            envioPendente = true;
        }
    }

    // Envia quando algo muda e, mesmo sem mudanças, a cada heartbeat (mantém o "online" do dashboard).
    if (envioPendente || agora - ultimoEnvioMs >= INTERVALO_HEARTBEAT_MS) {
        ultimoEnvioMs = agora;
        envioPendente = false;  // se falhar, o próximo heartbeat reenvia o estado completo
        api.enviarEstado(vagas, TOTAL_VAGAS, cancela.aberta());
        painel.mostrar(contarVagasLivres(), TOTAL_VAGAS, api.conectado());
    }
}
