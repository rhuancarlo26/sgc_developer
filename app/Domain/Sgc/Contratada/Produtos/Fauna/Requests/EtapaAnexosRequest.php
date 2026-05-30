<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EtapaAnexosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'anexos.anuencia_proprietarios'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.registro_fotografico'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.dados_secundarios'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.art'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ret'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.cr'                      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ctf'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.anuencia_colecoes'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.oficio_atividades_campo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.rfaef'                   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.cartas_anuencia'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
