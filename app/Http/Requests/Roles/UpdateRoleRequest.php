<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => "required|max:256|unique:roles,name,{$this->request->get('id')}",
            'permissions' => 'required|array|min:1',
            'id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'É necessário selecionar ao menos uma permissão',
            'permissions.min' => 'É necessário selecionar ao menos uma permissão',
            'name.required' => 'O campo nome é obrigatório',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
