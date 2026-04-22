<?php

namespace App\Domain\Modulos\Importador\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportadorRequest extends FormRequest
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
            'modulo_id' => 'required',
            'mes_ano_referencia' => 'required',
            'campanha' => 'required',
            'contrato_id' => 'required',
            'arquivo' => 'required|mimes:xlsx,csv',

            'parecer_tecnico' => $this->input('enviar_analise') ? 'required' : 'nullable',
            'parecer_analise' => 'nullable',
            'fotos' => 'array',
            'anexos' => 'array',
            'enviar_analise' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'modulo_id.required' => 'O campo Módulo é obrigatório',
            'mes_ano_referencia.required' => 'O campo Referência (Mês/Ano) é obrigatório',
            'campanha.required' => 'O campo Campanha é obrigatório',
            'contrato_id.required' => 'O campo Contrato é obrigatório',

            'arquivo.required' => 'O campo planilha é obrigatório',
            'arquivo.mimes' => 'A planilha precisa ter as extensões .xlsx OU .csv',
        ];
    }
}
