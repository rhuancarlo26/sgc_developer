<?php

namespace App\Domain\Sgc\Contratada\Produtos\Malarigeno\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMalarigenoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrato_id' => 'required|integer|exists:contratos,id',
            'cod_emp' => 'required|string|max:255',
            'id_campanha' => 'required|integer|min:1',
            'sei_dnit' => 'nullable|string|max:255',
            'subproduto' => 'required|string',
            'modulo_id' => 'nullable|integer|exists:sgc_modulos,id|required_with:arquivo',
            'arquivo' => 'nullable|mimes:xlsx,csv',
            'fotos' => 'array',
            'fotos.*.arquivo' => 'nullable|image',
            'fotos.*.latitude' => 'nullable|numeric',
            'fotos.*.longitude' => 'nullable|numeric',
            'fotos.*.data_captura' => 'nullable|string',
            'fotos.*.descricao' => 'nullable|string',
            'anexos' => 'array',
            'anexos.*.arquivo' => 'nullable|file',
            'enviar_analise' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'contrato_id.required' => 'O contrato é obrigatório.',
            'cod_emp.required' => 'O empreendimento é obrigatório.',
            'id_campanha.required' => 'O ID da campanha é obrigatório.',
            'sei_dnit.max' => 'O campo SEI DNIT deve ter no máximo 255 caracteres.',
            'subproduto.required' => 'O subproduto é obrigatório.',
            'modulo_id.required_with' => 'Selecione um modelo de planilha ao enviar o arquivo.',
            'arquivo.mimes' => 'A planilha precisa ser .xlsx ou .csv.',
        ];
    }
}
