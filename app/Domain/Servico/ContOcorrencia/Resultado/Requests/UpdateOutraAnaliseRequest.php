<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutraAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'id_resultado' => ['required'],
            'nome' => ['required', 'string'],
            'arquivo' => ['required', 'file'],
            'caminho_arquivo' => ['required'],
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
