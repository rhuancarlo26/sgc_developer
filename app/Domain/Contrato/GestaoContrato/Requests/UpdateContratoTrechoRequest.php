<?php

namespace App\Domain\Contrato\GestaoContrato\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratoTrechoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'contrato_id' => 'required',
            'tipo_contrato' => 'required',
            'uf_id' => 'required',
            'rodovia_id' => 'required',
            'km_inicial' => 'required',
            'km_final' => 'required',
            'tipo_trecho' => 'required',
            'coordenada' => 'required'
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
