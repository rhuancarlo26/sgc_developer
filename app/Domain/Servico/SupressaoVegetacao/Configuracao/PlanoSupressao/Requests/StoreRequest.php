<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'nullable|exists:plano_supressao,id',
            'servico_id' => $this->isMethod('post') ? 'required|exists:servicos,id' : 'nullable',
            'area_em_app' => 'nullable|numeric',
            'area_fora_app' => 'nullable|numeric',
            'dt_final' => 'date|nullable',
            'dt_inicial' => 'date|nullable',
            'local_shape_em_app' => 'file|nullable',
            'local_shape_fora_app' => 'file|nullable',
            'doc' => $this->isMethod('post') && !$this->id ? 'required|file|mimes:pdf' : 'nullable|file|mimes:pdf',
        ];
    }

    public function messages()
    {
        return [
            'numeric' => 'O campo deve ser um número válido.',
            'required' => 'O campo é obrigatório.',
        ];
    }
}
