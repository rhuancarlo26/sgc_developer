<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'periodo' => ['required'],
            'data_inicial' => ['required'],
            'data_final' => ['required'],
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
