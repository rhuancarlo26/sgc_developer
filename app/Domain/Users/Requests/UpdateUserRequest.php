<?php

namespace App\Domain\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:256',
            'email' => "required|max:256|unique:users,email,{$this->request->get('id')}",
            'roles' => 'required|array|min:1',
            'id' => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'É necessário selecionar ao menos um perfil',
            'roles.min' => 'É necessário selecionar ao menos um perfil',
            'name.required' => 'O campo nome é obrigatório',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
