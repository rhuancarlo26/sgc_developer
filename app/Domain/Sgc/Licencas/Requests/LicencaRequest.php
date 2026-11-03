<?php

namespace App\Domain\Sgc\Licencas\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LicencaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_id' => ['required', 'exists:sgc_licencas_tipos,id'],
            'numero' => ['required', 'string', 'max:100'],
            'orgao_emissor' => ['required', 'string', 'max:100'],
            'uf_id' => ['nullable', 'exists:estados,id'],
            'empreendimento' => ['nullable', 'string', 'max:255'],
            'processo_sei' => ['nullable', 'string', 'max:50'],
            'sei_documento' => ['nullable', 'string', 'max:50'],
            'data_emissao' => ['required', 'date'],
            'data_validade' => ['nullable', 'date', 'after_or_equal:data_emissao'],
            'antecedencia_renovacao_dias' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'licenca_anterior_id' => ['nullable', 'exists:sgc_licencas,id'],
            'motivo_vinculo' => ['nullable', 'required_with:licenca_anterior_id', Rule::in(['retificacao', 'renovacao'])],
            'copiar_condicionantes' => ['boolean'],
            'contrato_id' => ['nullable', 'exists:contratos,id'],
            'observacoes' => ['nullable', 'string'],
            'arquivo' => ['nullable', 'file', 'max:51200'],
            'responsaveis' => ['array'],
            'responsaveis.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_id' => 'tipo',
            'numero' => 'número',
            'orgao_emissor' => 'órgão emissor',
            'uf_id' => 'UF',
            'data_emissao' => 'data de emissão',
            'data_validade' => 'data de validade',
            'antecedencia_renovacao_dias' => 'antecedência para renovação',
            'motivo_vinculo' => 'motivo do vínculo',
        ];
    }
}
