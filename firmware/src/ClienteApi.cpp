#include "ClienteApi.h"

#include <ArduinoJson.h>
#include <HTTPClient.h>
#include <WiFi.h>

#include "Vaga.h"
#include "rede.h"

namespace {
constexpr uint32_t TIMEOUT_WIFI_MS = 15000;
constexpr uint16_t TIMEOUT_HTTP_MS = 1500;
}  // namespace

void ClienteApi::conectarWifi() {
    Serial.printf("Conectando ao Wi-Fi \"%s\"", WIFI_SSID);

    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_SENHA, WIFI_CANAL);

    const uint32_t inicio = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - inicio < TIMEOUT_WIFI_MS) {
        delay(250);
        Serial.print('.');
    }

    if (conectado()) {
        Serial.printf(" ok! IP: %s\n", WiFi.localIP().toString().c_str());
    } else {
        Serial.println(" falhou. Seguindo offline e tentando de novo em segundo plano.");
    }
}

bool ClienteApi::conectado() const {
    return WiFi.status() == WL_CONNECTED;
}

bool ClienteApi::enviarEstado(Vaga* const vagas[], size_t total, bool cancelaAberta) {
    if (!conectado()) {
        WiFi.reconnect();
        return false;
    }

    JsonDocument corpo;
    JsonArray lista = corpo["vagas"].to<JsonArray>();

    for (size_t i = 0; i < total; i++) {
        JsonObject item = lista.add<JsonObject>();
        item["numero"] = vagas[i]->numero();
        item["ocupada"] = vagas[i]->ocupada();

        if (vagas[i]->distanciaCm() == SensorUltrassonico::SEM_LEITURA) {
            item["distancia_cm"] = nullptr;
        } else {
            item["distancia_cm"] = vagas[i]->distanciaCm();
        }
    }
    corpo["cancela_aberta"] = cancelaAberta;

    String json;
    serializeJson(corpo, json);

    HTTPClient http;
    http.setTimeout(TIMEOUT_HTTP_MS);
    http.setConnectTimeout(TIMEOUT_HTTP_MS);
    http.begin(API_URL);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.addHeader("X-Device-Token", DEVICE_TOKEN);

    const int status = http.POST(json);
    http.end();

    if (status != 204) {
        Serial.printf("[api] POST falhou (status %d)\n", status);
        return false;
    }

    return true;
}
