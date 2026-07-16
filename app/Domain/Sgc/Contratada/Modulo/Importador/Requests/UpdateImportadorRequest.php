<?php

namespace App\Domain\Modulos\Importador\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ModuloImportador;
use Illuminate\Validation\Validator;

class UpdateImportadorRequest extends FormRequest
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
        $rules = [
            'modulo_id' => 'required',
            'mes_ano_referencia' => 'required',
            'campanha' => 'required',
            'contrato_id' => 'required',
            'servico_id' => ['nullable', 'exists:servicos,id'],
            'arquivo' => 'nullable|mimes:xlsx,csv',
            'parecer_tecnico' => $this->boolean('enviar_analise') ? 'required' : 'nullable',
            'parecer_analise' => 'nullable',
            'fotos' => ['nullable', 'array'],
            'anexos' => ['nullable', 'array'],
            'enviar_analise' => ['required', 'boolean'],
            'update_modulo' => ['nullable', 'boolean'],
        ];

        if ($this->input('update_modulo')) {
            $rules['arquivo'] = 'required|mimes:xlsx,csv';
        }

        return $rules;
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
            'parecer_tecnico.required' => 'O campo Parecer Técnico é obrigatório para enviar para análise.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('servico_id') || !$this->filled('campanha')) {
                return;
            }

            $importadorAtual = $this->route('importador');

            $jaExiste = ModuloImportador::query()
                ->where('servico_id', $this->input('servico_id'))
                ->where('campanha', $this->input('campanha'))
                ->when($importadorAtual, function ($query) use ($importadorAtual) {
                    $query->where('id', '!=', $importadorAtual->id);
                })
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
