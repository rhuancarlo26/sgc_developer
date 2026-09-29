<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntregaSimplificadaFaunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cod_emp' => 'required|string|max:255',
            'id_campanha' => 'required|integer|between:1,12',
            'sei_dnit' => 'nullable|string|max:255',
            'subproduto' => 'required|string',
            'planilhas' => 'nullable|array',
            'planilhas.terrestre' => 'nullable|array',
            'planilhas.aquatica' => 'nullable|array',
            'planilhas.cavernicola' => 'nullable|array',
            'planilhas.terrestre.modulo_id' => 'nullable|integer|exists:sgc_modulos,id|required_with:planilhas.terrestre.arquivo',
            'planilhas.aquatica.modulo_id' => 'nullable|integer|exists:sgc_modulos,id|required_with:planilhas.aquatica.arquivo',
            'planilhas.cavernicola.modulo_id' => 'nullable|integer|exists:sgc_modulos,id|required_with:planilhas.cavernicola.arquivo',
            'planilhas.*.arquivo' => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
            'planilhas.*.remover' => 'nullable|boolean',
            'fotos' => 'nullable|array|max:20',
            'fotos.*.arquivo' => 'nullable|image|max:5120',
            'fotos.*.latitude' => 'nullable|numeric',
            'fotos.*.longitude' => 'nullable|numeric',
            'fotos.*.data_captura' => 'nullable|string',
            'fotos.*.descricao' => 'nullable|string|max:500',
            'anexos' => 'nullable|array|max:10',
            'anexos.*.arquivo' => 'nullable|file|max:10240',
            'enviar_analise' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'contrato_id.required' => 'O contrato é obrigatório.',
            'cod_emp.required' => 'O empreendimento é obrigatório.',
            'id_campanha.required' => 'O ID da campanha é obrigatório.',
            'arquivo.mimes' => 'A planilha precisa ser .xlsx ou .csv.',
        ];
    }
}
