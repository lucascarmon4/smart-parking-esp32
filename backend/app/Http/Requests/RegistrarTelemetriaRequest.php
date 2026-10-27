<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarTelemetriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $token = config('garagem.device_token');

        return filled($token) && hash_equals($token, (string) $this->header('X-Device-Token'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vagas' => ['required', 'array', 'min:1'],
            'vagas.*.numero' => ['required', 'integer', 'min:1', 'distinct'],
            'vagas.*.ocupada' => ['required', 'boolean'],
            'vagas.*.distancia_cm' => ['nullable', 'integer', 'min:0'],
            'cancela_aberta' => ['required', 'boolean'],
        ];
    }
}
