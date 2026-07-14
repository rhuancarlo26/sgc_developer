<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\ACA\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data_aca' => ['required', 'date'],
            'lote' => ['required'],
            'id_lote' => ['required', 'integer'],
            'nome_lote' => ['required', 'string'],
            'empresa' => ['required', 'string'],
            'num_contrato' => ['required', 'string'],
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
