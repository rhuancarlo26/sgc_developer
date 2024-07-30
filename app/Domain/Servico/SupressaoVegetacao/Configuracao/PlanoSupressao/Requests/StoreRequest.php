<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'area_em_app' => 'numeric|required',
            'area_fora_app' => 'numeric|required',
            'dt_final' => 'date|nullable',
            'dt_inicial' => 'date|nullable',
            'local_shape_em_app' => 'file|nullable',
            'local_shape_fora_app' => 'file|nullable',
            'doc' => 'file|required',
        ];
    }

    public function messages()
    {
        return [
            'numeric' => 'O campo deve ser um número válido.',
            'required' => 'O campo é obrigatório.',
        ];
    }
}
