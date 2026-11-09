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
        $somenteTerrestre = str_contains(mb_strtolower((string) $this->input('subproduto', '')), 'atropelamento');

        return [
            'cod_emp' => 'required|string|max:255',
            'id_campanha' => 'required|integer|between:1,12',
            'data_ini' => 'nullable|date_format:Y-m-d|required_with:data_fim',
            'data_fim' => 'nullable|date_format:Y-m-d|required_with:data_ini|after_or_equal:data_ini',
            'sei_dnit' => 'nullable|string|max:255',
            'subproduto' => 'required|string',
            'planilhas' => $somenteTerrestre ? 'nullable|array:terrestre' : 'nullable|array',
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
            'anexos' => 'nullable|array|max:200',
            'anexos.*.id' => 'nullable|integer|distinct',
            'anexos.*.arquivo' => 'required_without_all:anexos.*.id,anexos.*.upload_token|nullable|file|max:20480',
            'anexos.*.upload_token' => 'nullable|string|max:4096|distinct',
            'anexos.*.classe' => ['nullable', 'string', 'max:100', 'regex:/^(abio|anuencia_proprietarios|registro_fotografico|dados_secundarios|art|ret_cr_ctf|anuencia_colecoes|oficios|outros|outros_[a-zA-Z0-9_-]+)$/'],
            'anexos.*.titulo_bloco' => 'nullable|string|max:100',
            'anexos.*.remover' => 'nullable|boolean',
            'enviar_analise' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'contrato_id.required' => 'O contrato é obrigatório.',
            'data_ini.required_with' => 'Informe a data inicial da campanha.',
            'data_fim.required_with' => 'Informe a data final da campanha.',
            'data_ini.date_format' => 'Informe uma data inicial válida.',
            'data_fim.date_format' => 'Informe uma data final válida.',
            'data_fim.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'cod_emp.required' => 'O empreendimento é obrigatório.',
            'id_campanha.required' => 'O ID da campanha é obrigatório.',
            'planilhas.array' => 'Campanhas de atropelamento permitem somente a planilha de Fauna Terrestre.',
            'arquivo.mimes' => 'A planilha precisa ser .xlsx ou .csv.',
            'anexos.*.arquivo.max' => 'Cada anexo deve ter no máximo 20 MB.',
            'anexos.*.arquivo.uploaded' => 'Não foi possível receber o anexo. Confira o tamanho do arquivo e os limites de upload do servidor.',
            'anexos.*.arquivo.required_without_all' => 'Este anexo não chegou ao servidor. Tente enviar o arquivo novamente.',
        ];
    }
}
