<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Requests;

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

    protected function prepareForValidation(): void
    {
        $pmqa = filter_var($this->input('pmqa'), FILTER_VALIDATE_BOOLEAN);

        $this->merge([
            'pmqa' => $pmqa ? 1 : 0,
            'contrato_id' => $pmqa ? $this->input('contrato_id') : null,
        ]);
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
            'pmqa' => 'nullable|boolean',
            'contrato_id' => 'nullable|required_if:pmqa,1|exists:contratos,id',
            'planilha_modelo' => 'nullable|mimes:xlsx,csv',
            'campos' => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'O campo é obrigatório',
            'contrato_id.required_if' => 'O contrato é obrigatório quando PMQA estiver marcado.',
            'contrato_id.exists' => 'O contrato selecionado é inválido.',
            'planilha_modelo.mimes' => 'A planilha modelo precisa ter as extensões .xlsx OU .csv',
        ];
    }
}
