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
            'latitude_inicial' => 'nullable|decimal:2,10',
            'longitude_inicial' => 'nullable|decimal:2,10',
            'uf_final' => 'nullable',
            'km_final' => 'nullable',
            'latitude_final' => 'nullable|decimal:2,10',
            'longitude_final' => 'nullable|decimal:2,10',
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
