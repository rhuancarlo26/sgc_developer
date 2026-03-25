<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pmqa_id' => ['required'],
            'nome' => ['required'],
            'pmqa_resultado_id' => ['required'],
            'observacao' => ['required'],
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
