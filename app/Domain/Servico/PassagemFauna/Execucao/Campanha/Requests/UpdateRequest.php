<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'id_campanha' => ['required'],
            'obs' => ['required']
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
