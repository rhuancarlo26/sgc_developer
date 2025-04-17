<?php

namespace App\Domain\Licenca\LicencaSegmento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaSegmentoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'licenca_id'  => 'required',
            'rodovia_id'     => 'required',
            'uf_id'  => 'required',
            'km_inicial'   => 'required',
            'km_final'      => 'required',
            'extensao' => 'required',
            'tipo_trecho' => 'required',
            'cod_tipo_trecho' => 'nullable',
            'versao_snv' => 'nullable',
            'coordenada' => 'required',
            'uf_final'    => 'nullable',
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
