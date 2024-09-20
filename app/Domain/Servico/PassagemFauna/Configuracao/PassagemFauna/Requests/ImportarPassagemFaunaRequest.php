<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\PassagemFauna\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarPassagemFaunaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'arquivo' => ['required']
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
