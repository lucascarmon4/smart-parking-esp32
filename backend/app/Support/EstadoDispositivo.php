<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Estado volátil do ESP32 (cancela e último contato), guardado em cache.
 */
class EstadoDispositivo
{
    private const CHAVE_CANCELA = 'garagem.cancela_aberta';

    private const CHAVE_ULTIMO_CONTATO = 'garagem.ultimo_contato';

    public function registrarContato(bool $cancelaAberta): void
    {
        Cache::forever(self::CHAVE_CANCELA, $cancelaAberta);
        Cache::forever(self::CHAVE_ULTIMO_CONTATO, now()->toIso8601String());
    }

    public function cancelaAberta(): bool
    {
        return (bool) Cache::get(self::CHAVE_CANCELA, false);
    }

    public function ultimoContato(): ?Carbon
    {
        $valor = Cache::get(self::CHAVE_ULTIMO_CONTATO);

        return $valor ? Carbon::parse($valor) : null;
    }

    public function online(): bool
    {
        $ultimoContato = $this->ultimoContato();

        return $ultimoContato !== null
            && $ultimoContato->diffInSeconds(now()) <= config('garagem.dispositivo_offline_apos');
    }
}
