<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutraAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fk_resultado' => ['required'],
            'nome' => ['required'],
            'arquivo' => ['required'],
            'analise' => ['required'],
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
