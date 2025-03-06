<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'fk_campanha_ponto' => ['required'],
            'medido' => ['nullable'],
            'iqa' => ['nullable'],
            'parametros' => ['nullable'],
            'observacao' => ['nullable'],
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
