<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnviarOcorrenciaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ocorrencias' => ['required'],
            'url' => ['required']
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
