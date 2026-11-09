<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigModuloRequest extends FormRequest
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
            'nome' => 'required',
            'planilha_modelo' => 'nullable|mimes:xlsx,csv',
            'campos' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'O campo é obrigatório',
            'planilha_modelo.mimes' => 'A planilha modelo precisa ter as extensões .xlsx OU .csv',
        ];
    }
}
