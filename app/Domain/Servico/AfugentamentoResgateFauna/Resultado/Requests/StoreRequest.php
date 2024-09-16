<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nome' => 'required',
            'periodo' => 'required',
        ];

        switch ($this->input('periodo')) {
            case 'M':
                $rules['mes'] = 'required|date_format:Y-m';
                break;
            case 'S':
                $rules['semestre'] = 'required|in:1,2';
                $rules['ano'] = 'required|digits:4';
                break;
            case 'A':
                $rules['ano'] = 'required|digits:4';
                break;
            case 'P':
                $rules['dt_inicio'] = 'required|date';
                $rules['dt_final'] = 'required|date|after_or_equal:dt_inicio';
                break;
        }

        return $rules;
    }
}
