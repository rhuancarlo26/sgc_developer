<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Request;

use Illuminate\Foundation\Http\FormRequest;

class PatrimonioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tipo' => 'required|string|in:paipa',
            'paipa_id' => 'nullable|integer|exists:sgc_patrimonio_paipa,id',
            'empreendimento_id' => 'required|integer',

            'justificativa_sei' => 'nullable|string|max:255',
            'justificativa_titulo' => 'nullable|string|max:255',
            'justificativa_citacao' => 'nullable|string',
            'justificativa_complementar' => 'nullable|string',

            'equipe' => 'nullable|array',
            'equipe.*.id' => 'nullable|integer|exists:sgc_patrimonio_equipe,id',
            'equipe.*.nome' => 'required_with:equipe|string|max:255',
            'equipe.*.cpf' => 'nullable|string|max:20',
            'equipe.*.cnpj' => 'nullable|string|max:20',
            'equipe.*.email' => 'nullable|email|max:255',
            'equipe.*.profissao' => 'nullable|string|max:255',
            'equipe.*.funcao' => 'nullable|string|max:255',
            'equipe.*.tipo_participacao' => 'nullable|string|max:255',
            'equipe.*.conselho_classe' => 'nullable|string|max:255',
            'equipe.*.numero_registro' => 'nullable|string|max:255',
            'equipe.*.carteira_profissional' => 'nullable|string|max:255',
            'equipe.*.ct' => 'nullable|string|max:255',
            'equipe.*.obs' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'O tipo do subproduto é obrigatório',
            'tipo.in' => 'O tipo informado é inválido',
            'empreendimento_id.required' => 'O empreendimento deve ser selecionado',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
