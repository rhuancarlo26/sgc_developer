<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRascunhoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'campanha_id'  => 'nullable|integer',
            'cod_emp'      => 'required|string|max:255',
            'subproduto'   => 'required|string|max:255',
            'id_campanha'  => 'required|integer|between:1,12', 
        ];
    }

    public function messages(): array
    {
        return [
            'id_campanha.required' => 'A campanha é obrigatória.',
            'id_campanha.integer'  => 'A campanha deve ser um número inteiro.',
            'id_campanha.between'  => 'A campanha deve estar entre 1 e 12.',
            'cod_emp.required'     => 'O empreendimento é obrigatório.',
            'subproduto.required'  => 'O subproduto é obrigatório.',
        ];
    }
}
