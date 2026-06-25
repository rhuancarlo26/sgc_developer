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
            'subproduto' => 'required|string',
            'modulo_id' => 'required|integer|exists:sgc_modulos,id',
            'arquivo' => 'required|mimes:xlsx,csv',
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
            'subproduto.required' => 'O subproduto é obrigatório.',
            'modulo_id.required' => 'O módulo é obrigatório.',
            'arquivo.required' => 'O arquivo da planilha é obrigatório.',
            'arquivo.mimes' => 'A planilha precisa ser .xlsx ou .csv.',
        ];
    }
}
