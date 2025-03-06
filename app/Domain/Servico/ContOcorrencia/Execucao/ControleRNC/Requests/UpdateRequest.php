<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ControleRNC\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ocorrencias' => ['required', 'array']
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
