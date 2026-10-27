#pragma once

#include <Arduino.h>

class Vaga;

class ClienteApi {
public:
    void conectarWifi();
    bool conectado() const;

    // Envia o estado completo da garagem. Retorna true se o backend aceitou.
    bool enviarEstado(Vaga* const vagas[], size_t total, bool cancelaAberta);
};
