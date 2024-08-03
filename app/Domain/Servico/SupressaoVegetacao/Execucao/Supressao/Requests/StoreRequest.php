<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'servico_id' => 'required',
            'licenca_id' => 'required',
            'estagio_sucessional_id' => 'required',
            'tipo_bioma_id' => 'required',
            'chave' => 'nullable',
            'dt_inicial' => 'required|date',
            'dt_final' => 'required|date',
            'fitofisionomia' => 'nullable',
            'area_em_app' => 'required|numeric',
            'area_fora_app' => 'required|numeric',
            'area_total' => 'required|numeric',
            'corte_especie' => 'nullable|boolean',
            'observacao' => 'nullable',
            'shapefile' => 'nullable',
            'local_shape' => 'nullable',
            'fotos' => 'nullable|array',
            'cortes' => 'nullable|array',
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
