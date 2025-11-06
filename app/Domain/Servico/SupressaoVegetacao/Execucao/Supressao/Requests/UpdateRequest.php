<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
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
            'corte_especies' => 'nullable|array',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'geometry' =>  'nullable',
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
