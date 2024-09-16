<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'arquivo' => 'nullable|file',
            'fk_servico' => 'required',
            'fk_campanha' => 'nullable',
            'fk_grupo_amostrado' => 'required',
            'data_registro' => 'nullable',
            'fk_estado' => 'nullable',
            'km' => 'nullable',
            'hora_registro' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'sentido' => 'nullable',
            'margem' => 'nullable',
            'classe' => 'nullable',
            'ordem' => 'nullable',
            'familia' => 'nullable',
            'genero' => 'nullable',
            'especie' => 'nullable',
            'nome_comum' => 'nullable',
            'sexo' => 'nullable',
            'faixa_etaria' => 'nullable',
            'coletado' => 'nullable',
            'n_registro_tombamento' => 'nullable',
            'carcaca_removida' => 'nullable',
            'reducao_biologica' => 'nullable',
            'n_individuos' => 'nullable',
            'estadual' => 'nullable',
            'federal' => 'nullable',
            'iucn' => 'nullable',
        ];
    }
}
