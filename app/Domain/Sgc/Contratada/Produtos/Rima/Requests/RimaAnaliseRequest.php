<?php

namespace App\Domain\Sgc\Contratada\Produtos\Rima\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RimaAnaliseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->perfis_id === 3;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:Aprovada,Rejeitada',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status da análise é obrigatório.',
            'status.in' => 'O status deve ser Aprovada ou Rejeitada.',
        ];
    }
}
