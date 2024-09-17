<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_servico' => ['required'],
            'nome' => ['required'],
            'campanhas_selecionadas' => ['required'],
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
