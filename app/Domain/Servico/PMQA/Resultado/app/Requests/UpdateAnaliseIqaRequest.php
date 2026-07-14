<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnaliseIqaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'fk_resultado' => ['required'],
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
