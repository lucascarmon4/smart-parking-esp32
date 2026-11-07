// Estacionamento inteligente: lê os sensores, acende os LEDs, controla a cancela,
// mostra o resumo no LCD e envia o estado para o backend Laravel por Wi-Fi.
//   - sensor da vaga < 10 cm  → vaga ocupada, LED verde apaga; senão fica aceso
//   - sensor da entrada < 15 cm e há vaga livre → cancela (servo) abre
//   - a cada mudança (e a cada 5 s, para o dashboard saber que está online) → POST /api/telemetria

#include <Arduino.h>
#include <ArduinoJson.h>
#include <ESP32Servo.h>
#include <HTTPClient.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>

// Rede aberta do simulador Wokwi. "host.wokwi.internal" é o localhost do computador
// que está rodando o simulador, onde o `php artisan serve` atende na porta 8000.
const char* WIFI_REDE = "Wokwi-GUEST";
const char* WIFI_SENHA = "";
const char* URL_TELEMETRIA = "http://host.wokwi.internal:8000/api/telemetria";
const char* TOKEN = "smart-parking-dev";  // igual ao ESP32_TOKEN do backend/.env

const unsigned long ENVIO_PERIODICO_MS = 5000;

struct Vaga {
    uint8_t trig;
    uint8_t echo;
    uint8_t led;
};

// Vagas 1 e 2 ligam no lado esquerdo do ESP32; vagas 3 e 4, no lado direito.
const Vaga VAGAS[] = {
    {26, 25, 27},
    {32, 35, 33},
    {18, 5, 19},
    {16, 4, 17},
};
const size_t TOTAL_VAGAS = sizeof(VAGAS) / sizeof(VAGAS[0]);

const uint8_t ENTRADA_TRIG = 13;
const uint8_t ENTRADA_ECHO = 12;  // pino de boot: precisa estar em LOW ao ligar, e o ECHO parado fica em LOW
const uint8_t SERVO_PWM = 14;

const int VAGA_OCUPADA_CM = 10;
const int CARRO_NA_ENTRADA_CM = 15;
const int CANCELA_FECHADA = 0;
const int CANCELA_ABERTA = 90;

LiquidCrystal_I2C lcd(0x27, 16, 2);  // SDA 21, SCL 22 (padrão do ESP32)
Servo cancela;

// Última leitura de cada vaga e da cancela: é o que vai para o backend.
int distancias[TOTAL_VAGAS];
bool ocupadas[TOTAL_VAGAS];
bool cancelaAberta = false;
unsigned long ultimoEnvio = 0;

// Dispara o HC-SR04 e converte o tempo do eco em cm. Retorna -1 se não houve eco.
int medirCm(uint8_t trig, uint8_t echo) {
    digitalWrite(trig, LOW);
    delayMicroseconds(2);
    digitalWrite(trig, HIGH);
    delayMicroseconds(10);
    digitalWrite(trig, LOW);

    unsigned long duracaoUs = pulseIn(echo, HIGH, 25000);  // ~4 m ida e volta
    if (duracaoUs == 0) {
        return -1;
    }
    return duracaoUs / 58;  // som a 343 m/s, ida e volta
}

// Tenta conectar por até 10 s. Se não der, o estacionamento funciona igual, só sem enviar dados.
void conectarWifi() {
    lcd.setCursor(0, 0);
    lcd.print("Conectando WiFi ");
    Serial.print("Conectando ao Wi-Fi");

    WiFi.begin(WIFI_REDE, WIFI_SENHA, 6);  // canal 6 acelera a conexão no Wokwi
    unsigned long inicio = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - inicio < 10000) {
        delay(250);
        Serial.print(".");
    }

    Serial.println(WiFi.status() == WL_CONNECTED ? " conectado!" : " falhou (seguindo sem rede)");
    lcd.clear();
}

// Monta o JSON com o estado atual e faz o POST para o backend.
//   {"vagas":[{"numero":1,"ocupada":false,"distancia_cm":200}, ...],"cancela_aberta":false}
void enviarTelemetria() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("telemetria: sem Wi-Fi");
        return;
    }

    JsonDocument json;
    JsonArray vagas = json["vagas"].to<JsonArray>();
    for (size_t i = 0; i < TOTAL_VAGAS; i++) {
        JsonObject vaga = vagas.add<JsonObject>();
        vaga["numero"] = i + 1;
        vaga["ocupada"] = ocupadas[i];
        if (distancias[i] >= 0) {
            vaga["distancia_cm"] = distancias[i];
        } else {
            vaga["distancia_cm"] = nullptr;  // sensor não respondeu
        }
    }
    json["cancela_aberta"] = cancelaAberta;

    String corpo;
    serializeJson(json, corpo);

    // Timeouts curtos: se o backend estiver fora do ar, o loop não trava os sensores.
    HTTPClient http;
    http.setConnectTimeout(1000);
    http.setTimeout(1000);
    http.begin(URL_TELEMETRIA);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    http.addHeader("X-Device-Token", TOKEN);
    int status = http.POST(corpo);
    http.end();

    Serial.printf("telemetria: %s -> HTTP %d\n", corpo.c_str(), status);
}

void setup() {
    Serial.begin(115200);

    for (const Vaga& vaga : VAGAS) {
        pinMode(vaga.trig, OUTPUT);
        pinMode(vaga.echo, INPUT);
        pinMode(vaga.led, OUTPUT);
    }
    pinMode(ENTRADA_TRIG, OUTPUT);
    pinMode(ENTRADA_ECHO, INPUT);

    cancela.attach(SERVO_PWM);
    cancela.write(CANCELA_FECHADA);

    lcd.init();
    lcd.backlight();

    Serial.println("Smart Parking");
    conectarWifi();
}

void loop() {
    size_t livres = 0;
    bool mudou = false;

    for (size_t i = 0; i < TOTAL_VAGAS; i++) {
        const Vaga& vaga = VAGAS[i];
        int distancia = medirCm(vaga.trig, vaga.echo);
        bool ocupada = distancia >= 0 && distancia < VAGA_OCUPADA_CM;

        digitalWrite(vaga.led, ocupada ? LOW : HIGH);
        if (!ocupada) {
            livres++;
        }

        mudou = mudou || ocupada != ocupadas[i];
        ocupadas[i] = ocupada;
        distancias[i] = distancia;
    }

    int entrada = medirCm(ENTRADA_TRIG, ENTRADA_ECHO);
    bool abrir = entrada >= 0 && entrada < CARRO_NA_ENTRADA_CM && livres > 0;
    cancela.write(abrir ? CANCELA_ABERTA : CANCELA_FECHADA);

    mudou = mudou || abrir != cancelaAberta;
    cancelaAberta = abrir;

    // Cada linha do LCD tem 16 colunas; os espaços no fim apagam o texto anterior.
    lcd.setCursor(0, 0);
    if (livres == 0) {
        lcd.print("LOTADO          ");
    } else {
        lcd.printf("Livres: %u de %u  ", (unsigned)livres, (unsigned)TOTAL_VAGAS);
    }
    lcd.setCursor(0, 1);
    lcd.print(abrir ? "Cancela: ABERTA " : "Cancela: fechada");

    if (mudou || millis() - ultimoEnvio >= ENVIO_PERIODICO_MS) {
        enviarTelemetria();
        ultimoEnvio = millis();
    }

    delay(200);
}
