<?php

namespace App\Shared\Base\Role\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:256|string|unique:roles,name',
            'permissions' => 'required|array|min:1'
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
