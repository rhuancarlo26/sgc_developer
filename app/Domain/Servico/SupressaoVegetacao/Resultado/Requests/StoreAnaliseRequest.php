<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'analise_destinacao_pilhas' => 'nullable',
            'analise_pilhas_cadastradas' => 'nullable',
            'analise_supressao_vegetacao' => 'nullable',
            'analise_pilhas_especie_protetigas' => 'nullable',
            'analise_supressao_especies_protegida' => 'nullable',
        ];
    }
}
