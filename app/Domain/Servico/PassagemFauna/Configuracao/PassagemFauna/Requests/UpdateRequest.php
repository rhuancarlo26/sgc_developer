<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'rodovia' => ['required'],
            'uf' => ['required'],
            'km' => ['required'],
            'tipo_de_estrutura' => ['required'],
            'classificacao' => ['required'],
            'nome' => ['required'],
            'dimensoes' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'observacao' => ['required'],

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
