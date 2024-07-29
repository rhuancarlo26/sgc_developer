<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'area_em_app' => 'numeric|nullable',
            'area_fora_app' => 'numeric|nullable',
            'dt_final' => 'date|nullable',
            'dt_inicial' => 'date|nullable',
            'local_shape_em_app' => 'file|nullable',
            'local_shape_fora_app' => 'file|nullable',
            'doc' => 'file|nullable',
        ];
    }
}
