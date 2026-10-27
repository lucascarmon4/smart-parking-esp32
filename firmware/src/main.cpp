// Teste de bancada do circuito: confirma que cada peça do diagram.json responde.
//   - sensor da vaga < 10 cm  → LED vermelho; senão LED verde
//   - sensor da entrada < 15 cm e há vaga livre → cancela (servo) abre
//   - LCD mostra vagas livres e o estado da cancela

#include <Arduino.h>
#include <ESP32Servo.h>
#include <LiquidCrystal_I2C.h>

struct Vaga {
    uint8_t trig;
    uint8_t echo;
    uint8_t ledVerde;
    uint8_t ledVermelho;
};

// Os ECHO usam 34, 35, 36 (VP) e 39 (VN): pinos só de entrada, que sobram no ESP32.
const Vaga VAGAS[] = {
    {5, 34, 13, 12},
    {18, 35, 14, 27},
    {19, 36, 26, 25},
    {23, 39, 33, 32},
};
const size_t TOTAL_VAGAS = sizeof(VAGAS) / sizeof(VAGAS[0]);

const uint8_t ENTRADA_TRIG = 17;
const uint8_t ENTRADA_ECHO = 16;
const uint8_t SERVO_PWM = 4;

const int VAGA_OCUPADA_CM = 10;
const int CARRO_NA_ENTRADA_CM = 15;
const int CANCELA_FECHADA = 0;
const int CANCELA_ABERTA = 90;

LiquidCrystal_I2C lcd(0x27, 16, 2);  // SDA 21, SCL 22 (padrão do ESP32)
Servo cancela;

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

void setup() {
    Serial.begin(115200);

    for (const Vaga& vaga : VAGAS) {
        pinMode(vaga.trig, OUTPUT);
        pinMode(vaga.echo, INPUT);
        pinMode(vaga.ledVerde, OUTPUT);
        pinMode(vaga.ledVermelho, OUTPUT);
    }
    pinMode(ENTRADA_TRIG, OUTPUT);
    pinMode(ENTRADA_ECHO, INPUT);

    cancela.attach(SERVO_PWM);
    cancela.write(CANCELA_FECHADA);

    lcd.init();
    lcd.backlight();

    Serial.println("Smart Parking - teste de bancada");
}

void loop() {
    size_t livres = 0;

    for (size_t i = 0; i < TOTAL_VAGAS; i++) {
        const Vaga& vaga = VAGAS[i];
        int distancia = medirCm(vaga.trig, vaga.echo);
        bool ocupada = distancia >= 0 && distancia < VAGA_OCUPADA_CM;

        digitalWrite(vaga.ledVerde, ocupada ? LOW : HIGH);
        digitalWrite(vaga.ledVermelho, ocupada ? HIGH : LOW);
        if (!ocupada) {
            livres++;
        }

        Serial.printf("V%u: %4d cm %s | ", (unsigned)(i + 1), distancia, ocupada ? "X" : "-");
    }

    int entrada = medirCm(ENTRADA_TRIG, ENTRADA_ECHO);
    bool abrir = entrada >= 0 && entrada < CARRO_NA_ENTRADA_CM && livres > 0;
    cancela.write(abrir ? CANCELA_ABERTA : CANCELA_FECHADA);

    Serial.printf("entrada: %4d cm | livres: %u\n", entrada, (unsigned)livres);

    // Cada linha do LCD tem 16 colunas; os espaços no fim apagam o texto anterior.
    lcd.setCursor(0, 0);
    if (livres == 0) {
        lcd.print("LOTADO          ");
    } else {
        lcd.printf("Livres: %u de %u  ", (unsigned)livres, (unsigned)TOTAL_VAGAS);
    }
    lcd.setCursor(0, 1);
    lcd.print(abrir ? "Cancela: ABERTA " : "Cancela: fechada");

    delay(200);
}
