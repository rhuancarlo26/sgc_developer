<?php

namespace App\Domain\Servico\PassagemFauna\Configuracao\VincularABIO\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRETRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_abio' => ['required'],
            'arquivo' => ['required', 'file', 'mimes:pdf'],
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
