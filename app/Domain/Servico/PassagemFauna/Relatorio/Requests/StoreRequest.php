<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_servico' => ['required'],
            'id_resultado' => ['required'],
            'fk_status' => ['required'],
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
