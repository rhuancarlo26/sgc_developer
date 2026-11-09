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
            'profissional'        => 'required|string|max:255',
            'formacao'            => 'required|string|max:255',
            'cpf'                 => 'required|string|max:255',
            'telefone'            => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'curriculum_lattes'   => 'nullable|string|max:255',
            'funcao'              => 'nullable|string|max:255',
            'ctf'                 => 'nullable|string|max:255',
            'validade'            => 'nullable|date',
            'conselho_de_classe'  => 'nullable|string|in:Sim,Não',
            'numero_de_registro'  => 'nullable|integer',
            'status'              => 'nullable|string|in:Ativo,Inativo',
            'observacao'          => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'profissional.required' => 'O nome do profissional é obrigatório.',
            'formacao.required'     => 'A formação é obrigatória.',
            'cpf.required'          => 'O CPF é obrigatório.',
        ];
    }
}