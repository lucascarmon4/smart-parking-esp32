<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TelemetriaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vagas' => ['required', 'array'],
            'vagas.*.numero' => ['required', 'integer', 'exists:vagas,numero'],
            'vagas.*.ocupada' => ['required', 'boolean'],
            'vagas.*.distancia_cm' => ['present', 'nullable', 'integer', 'min:0'],
            'cancela_aberta' => ['required', 'boolean'],
        ];
    }
}
