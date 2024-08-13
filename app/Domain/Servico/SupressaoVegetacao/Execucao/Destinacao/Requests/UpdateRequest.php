<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'chave' => 'nullable',
            'dt_envio' => 'nullable|date',
            'uso_da_madeira' => 'nullable|string',
            'destinatario' => 'nullable|string',
            'observacao' => 'nullable|string',
            'pilhas' => 'required|array',
            'arquivos' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'date' => 'O campo deve ser uma data válido.',
            'pilhas.required' => 'Adicione pelo menos um controle de pilhas.'
        ];
    }
}
