<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fk_servico' => 'required',
            'nome_relatorio' => 'required',
            'fk_resultado' => 'required',
            'sobre_relatorio' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo é obrigatório.'
        ];
    }
}
