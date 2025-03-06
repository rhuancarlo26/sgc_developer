<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rodovia' => 'required',
            'uf_inicial' => 'required',
            'km_inicial' => 'required',
            'latitude_inicial' => 'required',
            'longitude_inicial' => 'required',
            'uf_final' => 'required',
            'km_final' => 'required',
            'latitude_final' => 'required',
            'longitude_final' => 'required',
            'data_inicial' => 'required',
            'data_final' => 'required',
        ];
    }
}
