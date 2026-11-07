<?php

namespace App\Models;

use App\Enums\TipoEvento;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evento extends Model
{
    protected $fillable = ['tipo', 'vaga_id'];

    protected function casts(): array
    {
        return [
            'tipo' => TipoEvento::class,
        ];
    }

    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }

    /**
     * Texto mostrado no dashboard, ex.: "Vaga 2 ocupada".
     */
    public function descricao(): string
    {
        return match ($this->tipo) {
            TipoEvento::VagaOcupada => "Vaga {$this->vaga->numero} ocupada",
            TipoEvento::VagaLiberada => "Vaga {$this->vaga->numero} liberada",
            TipoEvento::CancelaAberta => 'Cancela aberta',
            TipoEvento::CancelaFechada => 'Cancela fechada',
        };
    }
}
