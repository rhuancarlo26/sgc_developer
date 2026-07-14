<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fk_servico' => ['required'],
            'nome' => ['required'],
            'fk_resultado' => ['required'],
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
