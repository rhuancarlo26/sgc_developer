<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        $request = request()->all();
        $rules = [];

        if ($request['form'] === 1) {
            $rules = [
                ...$rules,
                'id_servico' => ['required'],
                'num_contrato' => ['nullable'],
                'nome' => ['required'],
                'rodovia' => ['required'],
                'empresa' => ['required'],
                'situacao_contrato' => ['required'],
                'obj_contrato' => ['required'],
                'fiscal_contrato' => ['required'],
                'km_inicial' => ['required'],
                'km_final' => ['required'],
                'estaca_inicial' => ['required'],
                'estaca_final' => ['required'],
                'lat_inical' => ['required'],
                'lat_final' => ['required'],
                'long_inical' => ['required'],
                'long_final' => ['required'],
            ];
        } elseif ($request['form'] === 2) {
            $rules = [
                ...$rules,
                'supervisora_obras' => ['required'],
                'num_contrato_supervisora' => ['required'],
                'obj_contrato_supervisora' => ['required'],
                'fiscal_contrato_supervisora' => ['required'],
                'situacao_contrato_supervisora' => ['required'],
            ];
        } elseif ($request['form'] === 3) {
            $rules = [
                ...$rules,
                'obs' => ['required'],
            ];
        }

        return $rules;
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
