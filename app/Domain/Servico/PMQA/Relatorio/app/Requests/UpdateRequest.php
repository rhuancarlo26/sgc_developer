<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
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
