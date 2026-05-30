<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EtapaResultadosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'planilha_terrestre'   => 'nullable|file|mimes:xlsx,xls|max:10240',
            'planilha_aquatica'    => 'nullable|file|mimes:xlsx,xls|max:10240',
            'planilha_cavernicola' => 'nullable|file|mimes:xlsx,xls|max:10240',
            'consideracoes'              => 'nullable|string',
            'planilha_atropelamento'    => 'nullable|file|mimes:xlsx,xls|max:10240',
            'consideracoes_atropelamento' => 'nullable|string',
        ];
    }
}
