<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRelatorioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrato.id' => ['required', 'integer', 'min:1'],
            'data_inicio' => ['required', 'date_format:Y-m-d'],
            'data_fim' => ['required', 'date_format:Y-m-d', 'after_or_equal:data_inicio'],
        ];
    }

    public function attributes(): array
    {
        return [
            'contrato.id' => 'contrato',
            'data_inicio' => 'data inicial',
            'data_fim' => 'data final',
        ];
    }

    public function messages(): array
    {
        return [
            'data_inicio.required' => 'Informe a data inicial do relatório.',
            'data_fim.required' => 'Informe a data final do relatório.',
            'data_inicio.date_format' => 'Informe uma data inicial válida.',
            'data_fim.date_format' => 'Informe uma data final válida.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
