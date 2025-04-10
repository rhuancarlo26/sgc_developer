<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $request = request()->all();
        $rules = [];

        if ($request['form'] === 1) {
            $rules = [
                ...$rules,
                'id' => ['nullable'],
                'rodovia' => ['required'],
                'data_ocorrencia' => ['required'],
                'km' => ['required'],
                'estaca' => ['required'],
                'lado' => ['required'],
                'latitude' => ['required'],
                'longitude' => ['required'],
                'lote' => ['required'],
                'indicio_responsabilidade' => ['required'],
                'intensidade' => ['required'],
                'tipo' => ['required'],
                'prazo' => ['required'],
                'rnc_direto' => ['required'],
            ];

            if ($request['indicio_responsabilidade'] === false) {
                $rules = [
                    ...$rules,
                    'possivel_causa' => ['required'],
                    'descricao_causa' => ['required']
                ];
            }
        } elseif ($request['form'] === 2) {
            $rules = [
                ...$rules,
                'id' => ['required'],
                'local' => ['required'],
                'classificacao' => ['required'],
                'descricao' => ['required'],
                'area_total' => ['required'],
            ];
        } elseif ($request['form'] === 3) {
            $rules = [
                ...$rules,
                'id' => ['required'],
                'acoes' => ['required'],
            ];
        } elseif ($request['form'] === 4) {
            $rules = [
                ...$rules,
                'id' => ['required'],
                'norma' => ['required'],
            ];
        } elseif ($request['form'] === 6) {
            $rules = [
                ...$rules,
                'id' => ['required'],
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
