<?php

namespace App\Domain\Licenca\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'nullable',
            'tipo_rel' => 'required',
            'numero_licenca' => 'required',
            'modal' => 'required',
            'data_emissao' => 'required',
            'vencimento' => 'required',
            'numero_sei' => 'required',
            'processo_dnit' => 'required',
            'inicio_subtrecho' => 'required',
            'fim_subtrecho' => 'required',
            'extensao' => 'nullable',
            'emissor' => 'required',
            'empreendimento' => 'required',
            'dias_renovacao' => 'required',
            'renovacao' => 'required',
            'requerimento' => 'required',
            'fiscal' => 'required',
            'data_publicacao' => 'required',
            'in_app' => 'nullable',
            'out_app' => 'nullable',
            'volume' => 'nullable',
            'sinaflor' => 'nullable',
            'total_app' => 'nullable',
            'geo_json' => 'nullable',
            'arquivado' => 'nullable',
            'obs_renovacao' => 'required',
            'status' => 'nullable',
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
