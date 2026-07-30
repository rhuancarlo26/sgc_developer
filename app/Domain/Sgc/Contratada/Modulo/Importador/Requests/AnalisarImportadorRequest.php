<?php

namespace App\Domain\Modulos\Importador\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalisarImportadorRequest extends FormRequest
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
            'parecer_tecnico' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'parecer_tecnico.required' => 'O campo Parecer Técnico é obrigatório',
        ];
    }
}
