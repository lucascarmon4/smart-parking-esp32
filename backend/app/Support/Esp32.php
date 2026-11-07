<?php

namespace App\Support;

use App\Enums\TipoEvento;
use App\Models\Evento;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Estado que não pertence a nenhuma vaga: a cancela e o último contato do ESP32.
 * Fica no cache do Laravel, já que são só dois valores.
 */
class Esp32
{
    /** O ESP32 manda telemetria a cada 5 s; sem notícia por 15 s, consideramos offline. */
    public const SEGUNDOS_ATE_OFFLINE = 15;

    public static function registrarContato(bool $cancelaAberta): void
    {
        if (Cache::has('esp32.cancela_aberta') && $cancelaAberta !== self::cancelaAberta()) {
            Evento::create([
                'tipo' => $cancelaAberta ? TipoEvento::CancelaAberta : TipoEvento::CancelaFechada,
            ]);
        }

        Cache::forever('esp32.cancela_aberta', $cancelaAberta);
        Cache::forever('esp32.ultimo_contato', now()->timestamp);
    }

    public static function cancelaAberta(): bool
    {
        return Cache::get('esp32.cancela_aberta', false);
    }

    public static function ultimoContato(): ?Carbon
    {
        $timestamp = Cache::get('esp32.ultimo_contato');

        return $timestamp ? Carbon::createFromTimestamp($timestamp, config('app.timezone')) : null;
    }

    public static function online(): bool
    {
        return self::ultimoContato()?->gt(now()->subSeconds(self::SEGUNDOS_ATE_OFFLINE)) ?? false;
    }
}
