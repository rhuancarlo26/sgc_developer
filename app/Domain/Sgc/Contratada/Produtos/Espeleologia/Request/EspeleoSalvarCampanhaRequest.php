<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Request;

use Illuminate\Foundation\Http\FormRequest;

class EspeleoSalvarCampanhaRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id_campanha' => 'required|string|max:50',
            'cod_emp' => 'required|string|exists:sgcvw_empreendimentos,cod_emp',
            'subproduto' => 'required|string|max:255',
            'subtrecho' => 'nullable|string|max:255',
            'segmento' => 'nullable|string|max:255',
            'extensao' => 'nullable|numeric',
            'tipo_de_intervencao' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
            'bioma' => 'nullable|string|max:255',
            'profissionais' => 'nullable|array',
            'profissionais.*.profissional_id' => 'required|exists:sgc_espeleo_profissionais,id',
            'justificativas' => 'nullable|array',
            'justificativas.*.justificativa' => 'nullable|string',
            'justificativas.*.tipo' => 'nullable|string|in:citacao,complementar',
            'justificativas.*.titulo' => 'nullable|string',
            'justificativas.*.codigo_sei' => 'nullable|string|max:255',
            'metodologia' => 'nullable|string|max:8000',
        ];
    }

    public function messages()
    {
        return [
            'id_campanha.required' => 'O ID da campanha é obrigatório.',
            'id_campanha.unique' => 'O ID da campanha já está em uso para este contrato.',
            'cod_emp.required' => 'O empreendimento é obrigatório.',
            'cod_emp.exists' => 'O empreendimento selecionado não existe.',
            'subproduto.required' => 'O subproduto é obrigatório.',
            'justificativas.*.tipo.in' => 'O tipo da justificativa deve ser "citacao" ou "complementar".',
        ];
    }
}
