<?php

namespace App\Domain\Sgc\Contratada\Produtos\Indigena\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndigenaSimplificadaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'cod_emp' => 'required|string|max:255',
            'id_campanha' => 'required|integer|min:1',
            'sei_dnit' => 'nullable|string|max:255',
            'subproduto' => 'required|string|max:255',
            'modulo_id' => 'nullable|integer|exists:sgc_modulos,id|required_with:arquivo',
            'arquivo' => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
            'fotos' => 'nullable|array',
            'fotos.*.arquivo' => 'nullable|image|max:10240',
            'fotos.*.latitude' => 'nullable|numeric',
            'fotos.*.longitude' => 'nullable|numeric',
            'fotos.*.data_captura' => 'nullable|date',
            'fotos.*.descricao' => 'nullable|string|max:1000',
            'anexos' => 'nullable|array',
            'anexos.*.arquivo' => 'nullable|file|max:10240',
            'fotos_remover' => 'nullable|array',
            'fotos_remover.*' => 'integer',
            'anexos_remover' => 'nullable|array',
            'anexos_remover.*' => 'integer',
            'enviar_analise' => 'required|boolean',
        ];
    }
}


