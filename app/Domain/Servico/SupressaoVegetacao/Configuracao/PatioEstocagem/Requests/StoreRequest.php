<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'licenca_id' => 'required',
            'tipo_patio_id' => 'required',
            'shapefile' => 'nullable',
            'observacao' => 'nullable',
            'fotos' => 'array|nullable',
        ];
    }

    public function messages()
    {
        return [
            'numeric' => 'O campo deve ser um número válido.',
            'required' => 'O campo é obrigatório.',
        ];
    }
}
