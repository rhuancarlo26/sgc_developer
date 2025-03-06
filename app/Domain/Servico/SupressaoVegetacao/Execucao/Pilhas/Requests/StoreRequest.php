<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'licenca_id' => 'required',
            'area_supressao_id' => 'required',
            'patio_estocagem_id' => 'required',
            'tipo_produto_id' => 'required',
            'corte_especie_id' => 'nullable',
            'chave' => 'nullable',
            'tipo_pilha' => 'required',
            'volume' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'fotos' => 'nullable|array',
            'observacao' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => 'O campo deve ser um número válido.',
            'required' => 'O campo é obrigatório.',
            'date' => 'O campo deve ser uma data válido.',
        ];
    }
}
