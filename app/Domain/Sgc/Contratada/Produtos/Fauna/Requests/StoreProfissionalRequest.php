<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreProfissionalRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'profissional' => 'required|string|max:255',
            'formacao' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:255|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
            'email' => 'nullable|email|max:255',
            'curriculum_lattes' => 'nullable|string|max:255|url',
            'funcao' => 'nullable|string|max:255',
            'ctf' => 'nullable|string|max:255',
            'validade' => 'nullable|date',
            'conselho_de_classe' => 'required|string|in:Sim,Não',
            'numero_de_registro' => 'nullable|required_if:conselho_de_classe,Sim|integer',
            'status' => 'required|string|in:Ativo,Inativo',
            'observacao' => 'nullable|string',
        ];
    }
}