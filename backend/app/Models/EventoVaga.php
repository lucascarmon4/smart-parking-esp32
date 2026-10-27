<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoVaga extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'eventos_vaga';

    protected $fillable = ['vaga_id', 'ocupada'];

    protected function casts(): array
    {
        return [
            'ocupada' => 'boolean',
        ];
    }

    /** @return BelongsTo<Vaga, $this> */
    public function vaga(): BelongsTo
    {
        return $this->belongsTo(Vaga::class);
    }
}
