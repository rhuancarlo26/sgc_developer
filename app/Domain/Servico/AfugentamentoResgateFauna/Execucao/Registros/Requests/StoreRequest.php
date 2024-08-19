<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_frente' => 'required',
            'id_grupo_amostrado' => 'required',
            'data_registro' => 'required',
            'hora_registro' => 'required',
            'id_estado' => 'required',
            'km' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'sentido' => 'required',
            'margem' => 'required',
            'classe' => 'required',
            'ordem' => 'required',
            'familia' => 'required',
            'genero' => 'required',
            'especie' => 'required',
            'nome_comum' => 'required',
            'categoria' => 'required',
            'sexo' => 'required',
            'faixa_etaria' => 'required',
            'n_individuos' => 'required',
            'id_forma_registro' => 'required',
            'id_tipo_registro' => 'required',
            'id_destinacao_registro' => 'required',
            'latitude_soltura' => 'required',
            'longitude_soltura' => 'required',
            'nome_local' => 'required',
            'coletado' => 'required',
            'n_registro_tombamento' => 'required',
            'id_status_conservacao_federal' => 'required',
            'id_status_conservacao_iucn' => 'required',
        ];
    }
}