<?php

namespace App\Domain\Modulos\Importador\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprovReprovImportadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parecer_analise' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'parecer_analise.required' => 'O campo Parecer de Análise é obrigatório',
        ];
    }
}
