<?php

namespace App\Domain\Sgc\Contratada\Produtos\Pmqa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarPontosRequest extends FormRequest
{
    public function rules(): array
    {
        // dd('teste');
        return [
            'arquivo' => ['required', 'file', 'mimes:xlsx,xls'],
            'campanha_id' => ['required', 'integer', 'exists:sgc_pmqa_campanhas,id']
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
