<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'periodo' => 'nullable',
            'dt_inicio' => 'nullable',
            'dt_final' => 'nullable',
            'mes' => 'nullable',
            'semestre' => 'nullable',
            'ano' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => 'O campo deve ser um número válido.',
            'required' => 'O campo é obrigatório.',
            'date' => 'O campo deve ser uma data válido.',
        ];
    }
}
