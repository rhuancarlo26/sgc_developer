<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EtapaMetodologiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'metodologias'                     => 'nullable|array',
            'metodologias.*.grupo_faunistico'  => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos,Quelônios e Crocodilianos,Fauna Cavernícola,Invertebrados',
            'metodologias.*.metodologia'       => 'required_with:metodologias|string',
        ];
    }
}
