<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fk_resultado' => ['required'],
            'fk_parametro' => ['required'],
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
