<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVeiculoRecursoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required',
            'id_contrato'    => 'required',
            'cod_veiculos'   => 'required',
            'obs'            => 'required',
            'placa'          => 'required',
            'ultima_revisao' => 'required',
            'km_inicial'     => 'required'
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
