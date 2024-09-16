<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'fk_servico_licenca' => 'required',
            'uf_inicial' => 'required',
            'km_inicial' => 'required',
            'latitude_inicial' => 'nullable',
            'longitude_inicial' => 'nullable',
            'uf_final' => 'nullable',
            'km_final' => 'nullable',
            'latitude_final' => 'nullable',
            'longitude_final' => 'nullable',
            'data_inicial' => 'nullable',
            'data_final' => 'nullable',
            'observacao' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório'
        ];
    }
}
