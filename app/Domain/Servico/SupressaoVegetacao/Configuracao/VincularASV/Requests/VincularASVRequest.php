<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VincularASVRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'licenca_id' => [
                'required',
                Rule::unique('servico_licenca', 'licenca_id')->where('servico_id', $this->input('servico_id'))
            ],
            'servico_id' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'unique' => 'A licença já está vinculada ao serviço.'
        ];
    }
}
