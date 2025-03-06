<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_campanha' => ['required'],
            'id_abio' => ['required'],
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
