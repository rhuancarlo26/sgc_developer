<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sgc_resultado_id' => ['required'],
            'parametro_id' => ['required'],
            'analises' => ['required'],
            'graf_analise_parametro' => ['required'],
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
