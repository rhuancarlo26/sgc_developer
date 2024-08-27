<?php

namespace App\Domain\Servico\PMQA\Execucao\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'fk_servico' => ['required'],
            'nome_campanha' => ['required'],
            'dt_inicio' => ['required'],
            'dt_fim' => ['required'],
            'pontos' => []
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
