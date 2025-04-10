<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRHRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id_campanha' => ['required'],
            'id_servico_rh' => ['required'],
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
