<?php

namespace App\Models;

use App\Enums\TipoEvento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaga extends Model
{
    protected $fillable = ['numero', 'ocupada', 'distancia_cm'];

    protected function casts(): array
    {
        return [
            'ocupada' => 'boolean',
        ];
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    /**
     * Grava a leitura mais recente do sensor e, se a vaga mudou de estado,
     * registra o evento no histórico.
     */
    public function registrarLeitura(bool $ocupada, ?int $distanciaCm): void
    {
        if ($ocupada !== $this->ocupada) {
            $this->eventos()->create([
                'tipo' => $ocupada ? TipoEvento::VagaOcupada : TipoEvento::VagaLiberada,
            ]);
        }

        $this->update(['ocupada' => $ocupada, 'distancia_cm' => $distanciaCm]);
    }
}
