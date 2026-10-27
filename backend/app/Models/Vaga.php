<?php

namespace App\Models;

use Database\Factories\VagaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vaga extends Model
{
    /** @use HasFactory<VagaFactory> */
    use HasFactory;

    protected $table = 'vagas';

    protected $fillable = ['numero', 'ocupada', 'distancia_cm'];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'ocupada' => 'boolean',
            'distancia_cm' => 'integer',
        ];
    }

    /** @return HasMany<EventoVaga, $this> */
    public function eventos(): HasMany
    {
        return $this->hasMany(EventoVaga::class);
    }

    /** @param Builder<Vaga> $query */
    public function scopeLivres(Builder $query): void
    {
        $query->where('ocupada', false);
    }
}
