<?php

namespace App\Domain\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:256',
            'email' => 'required|max:256|unique:users,email',
            'roles' => 'required|array|min:1',
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
