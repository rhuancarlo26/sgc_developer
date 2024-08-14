<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ACA\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnviarACARequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'acas' => ['required', 'array'],
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
