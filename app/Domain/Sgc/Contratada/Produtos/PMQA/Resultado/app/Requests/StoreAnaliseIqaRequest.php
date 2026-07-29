<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseIqaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sgc_resultado_id' => ['required'],
            'analise_iqa' => ['required'],
            'graf_analise_iqa' => ['required'],
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
