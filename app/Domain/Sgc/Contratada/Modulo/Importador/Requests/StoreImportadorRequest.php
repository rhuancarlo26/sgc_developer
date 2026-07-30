<?php

namespace App\Domain\Modulos\Importador\Requests;

use App\Models\ModuloImportador;
use Illuminate\Validation\Validator;
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
            'servico_id' => ['nullable', 'exists:servicos,id'],
            'arquivo' => 'required|mimes:xlsx,csv',
            'parecer_tecnico' => $this->input('enviar_analise') ? 'required' : 'nullable',
            'parecer_analise' => 'nullable',
            'fotos' => 'array',
            'anexos' => 'array',
            'enviar_analise' => ['required', 'boolean'],
            'continuar_formulario' => ['nullable', 'boolean']
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('servico_id') || !$this->filled('campanha')) {
                return;
            }

            $jaExiste = ModuloImportador::query()
                ->where('servico_id', $this->input('servico_id'))
                ->where('campanha', $this->input('campanha'))
                ->exists();

            if ($jaExiste) {
                $validator->errors()->add(
                    'campanha',
                    'Essa campanha já foi cadastrada para este serviço. Selecione outra campanha.'
                );
            }
        });
    }
}
