<?php

return [

    /*
    | Token compartilhado com o firmware. O ESP32 envia esse valor no header
    | X-Device-Token em toda requisição de telemetria.
    */
    'device_token' => env('GARAGEM_DEVICE_TOKEN'),

    /*
    | Depois de quantos segundos sem telemetria o dispositivo é considerado offline.
    */
    'dispositivo_offline_apos' => (int) env('GARAGEM_DISPOSITIVO_OFFLINE_APOS', 10),

];
