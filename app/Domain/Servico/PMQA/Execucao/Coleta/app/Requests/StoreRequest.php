<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        $request = (object)request()->all();
        $regras = [];

        $regras['fk_campanha_ponto'] = 'required';
        $regras['dt_coleta'] = 'required';
        $regras['coleta'] = 'required';

        if ($request->coleta === true) {
            $regras['observacao'] = 'required';
        } else {
            $regras['numero_amostra'] = 'required';
            $regras['preservacao_amostra'] = 'required';
            $regras['acondicionamento_amostra'] = 'required';
            $regras['transporte_amostra'] = 'required';
        }

        return $regras;
    }

    public function messages(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
