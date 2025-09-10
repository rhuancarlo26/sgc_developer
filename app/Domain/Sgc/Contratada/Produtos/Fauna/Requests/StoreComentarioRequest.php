<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreComentarioRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'analise_id' => 'required|integer|exists:sgc_fauna_analise_etapas,id',
            'etapa' => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
            'comentario' => 'required|string|max:1000',
            'id_modulo' => 'nullable|integer',
        ];
    }
}