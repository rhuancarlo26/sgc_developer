<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome_relatorio' => 'required',
            'fk_resultado' => 'required',
            'sobre_relatorio' => 'required',
        ];
    }

}
