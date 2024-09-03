<?php

namespace App\Domain\Contrato\GestaoContrato\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tipo_contrato' => 'required',
            'numero_contrato' => 'required',
            'cnpj' => 'required',
            'contratada' => 'required',
            'processo_sei' => 'required',
            'obj_contrato' => 'required',
            'data_inicio' => 'required',
            'data_termino' => 'required',
            'situacao' => 'required',
            'edital' => 'required',
            'tipo_licitacao' => 'required',
            'modalidade' => 'required',
            'unidade_gestora' => 'required',
            'fiscal_contrato' => 'required',
            'preco_inicial' => 'nullable',
            'total_aditivos' => 'nullable',
            'total_reajuste' => 'nullable',
            'total' => 'nullable'
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
