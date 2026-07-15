<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnaliseIqaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
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
