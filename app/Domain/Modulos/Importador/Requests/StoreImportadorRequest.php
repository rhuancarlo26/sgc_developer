<?php

namespace App\Domain\Modulos\Importador\Requests;

use App\Models\ModuloImportador;
use App\Models\Servicos;
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
            'fotos' => ['nullable', 'array'],
            'fotos.*.id' => ['nullable'],
            'fotos.*.arquivo' => ['nullable', 'file', 'image'],
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
