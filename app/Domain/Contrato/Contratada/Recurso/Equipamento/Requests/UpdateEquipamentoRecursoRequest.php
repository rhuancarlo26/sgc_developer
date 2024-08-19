<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipamentoRecursoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'                => 'required|exists:equipamentos,id',
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
