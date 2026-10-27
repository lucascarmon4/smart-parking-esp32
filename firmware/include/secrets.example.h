#pragma once

// Copie este arquivo para include/secrets.h (que não vai pro Git) e preencha.

#define WIFI_SSID "nome-da-rede"
#define WIFI_SENHA "senha-da-rede"

// IP do computador rodando o Laravel na mesma rede (php artisan serve --host=0.0.0.0)
#define API_URL "http://192.168.0.10:8000/api/telemetria"

// Mesmo valor de GARAGEM_DEVICE_TOKEN no .env do backend
#define DEVICE_TOKEN "smart-parking-dev"
