<?php

namespace App\Domain\Sgc\Contratada\Produtos\Malarigeno\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'subproduto' => 'nullable|string',
            'modulo_id' => [
                'required',
                'integer',
                Rule::exists('sgc_modulos', 'id')->where('produto_slug', $this->route('produto')),
            ],
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
            'modulo_id.required' => 'O módulo é obrigatório.',
            'modulo_id.exists' => 'O módulo selecionado não pertence a este produto.',
            'arquivo.required' => 'O arquivo da planilha é obrigatório.',
            'arquivo.mimes' => 'A planilha precisa ser .xlsx ou .csv.',
        ];
    }
}
