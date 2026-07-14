<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipamentoRecursoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_contrato'       => 'required',
            'nome'              => 'required',
            'modelo'            => 'required',
            'ultima_calibracao' => 'required'
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
