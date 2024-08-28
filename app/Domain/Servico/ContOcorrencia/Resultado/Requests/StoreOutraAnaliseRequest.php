<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutraAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_resultado' => ['required'],
            'nome' => ['required', 'string'],
            'arquivo' => ['required', 'file'],
            'analise' => ['required', 'string']
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
