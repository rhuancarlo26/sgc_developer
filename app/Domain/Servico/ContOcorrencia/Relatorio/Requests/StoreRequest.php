<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_servico' => ['required'],
            'nome_relatorio' => ['required'],
            'id_resultado' => ['required'],
            'sobre_relatorio' => ['required']
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
