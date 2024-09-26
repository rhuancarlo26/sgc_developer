<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Relatorio\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'id_resultado' => 'required|integer|exists:afugent_fauna_resultado,id',
            'sobre_relatorio' => 'required|string',
            'conclusao' => 'string|nullable|max:255',
        ];
    }
}
