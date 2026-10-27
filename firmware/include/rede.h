#pragma once

// Configuração de rede por ambiente (ver platformio.ini).

#if defined(AMBIENTE_WOKWI)

// Rede virtual do simulador. host.wokwi.internal aponta para o computador
// que está rodando a simulação, onde o Laravel escuta na porta 8000.
#define WIFI_SSID "Wokwi-GUEST"
#define WIFI_SENHA ""
#define WIFI_CANAL 6
#define API_URL "http://host.wokwi.internal:8000/api/telemetria"
#define DEVICE_TOKEN "smart-parking-dev"

#else

// ESP32 físico: copie secrets.example.h para secrets.h e preencha.
#include "secrets.h"
#define WIFI_CANAL 0

#endif
