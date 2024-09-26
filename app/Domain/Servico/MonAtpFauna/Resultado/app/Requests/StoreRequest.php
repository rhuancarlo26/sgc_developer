<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fk_servico' => 'required',
            'nome_resultado' => 'required',
        ];
    }

}
