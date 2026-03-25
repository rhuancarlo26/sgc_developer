<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'id_resultado' => ['required'],
            'nome_relatorio' => ['required'],
            'sobre_relatorio' => ['required'],

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
