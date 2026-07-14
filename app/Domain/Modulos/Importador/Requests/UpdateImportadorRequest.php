<?php

namespace App\Domain\Modulos\Importador\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ModuloImportador;
use App\Models\Servicos;
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
            'fotos.*.id' => ['nullable'],
            'fotos.*.nome_arquivo' => ['nullable'],
            'fotos.*.caminho_arquivo' => ['nullable'],
            'fotos.*.nome_original' => ['nullable'],
            'fotos.*.latitude' => ['nullable'],
            'fotos.*.longitude' => ['nullable'],
            'fotos.*.descricao' => ['nullable'],
            'fotos.*.data_captura' => ['nullable'],
            'fotos.*.metadados' => ['nullable', 'array'],
            'fotos.*.compactada' => ['nullable'],
            'fotos.*.tentouExtrairCoordenadas' => ['nullable'],
            'fotos.*.possuiCoordenadasExif' => ['nullable'],
            'fotos.*.latitude_preenchida_automaticamente' => ['nullable'],
            'fotos.*.longitude_preenchida_automaticamente' => ['nullable'],
            'fotos.*.data_preenchida_automaticamente' => ['nullable'],
            'fotos.*.descricao_preenchida_automaticamente' => ['nullable'],
            'fotos.*.arquivo' => ['nullable', 'image', 'max:10240'],
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
            'fotos.*.arquivo.image' => 'As fotos precisam estar em um formato de imagem válido.',
            'fotos.*.arquivo.max' => 'Cada foto deve ter no máximo 10MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('servico_id') || !$this->filled('campanha')) {
                return;
            }

            $servicoAprovado = Servicos::query()
                ->where('id', $this->input('servico_id'))
                ->where('status_aprovacao', 3)
                ->exists();

            if (!$servicoAprovado) {
                $validator->errors()->add(
                    'servico_id',
                    'O importador só será habilitado após a aprovação do fiscal no cadastro do serviço.'
                );

                return;
            }

            $importadorAtual = $this->route('importador');

            if (
                $importadorAtual
                && !in_array((int) $importadorAtual->status, [ModuloImportador::RASCUNHO, ModuloImportador::REPROVADO], true)
                && ($this->hasFile('arquivo') || $this->boolean('update_modulo'))
            ) {
                $validator->errors()->add(
                    'arquivo',
                    'A planilha só pode ser alterada enquanto a importação estiver em rascunho ou reprovada pelo fiscal.'
                );

                return;
            }

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
