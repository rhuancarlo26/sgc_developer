<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        $request = request()->all();

        $rules = [
            'id' => ['nullable'],
            'form' => ['required'],
            'id_resultado' => ['required'],
        ];

        if ($request['form'] === 1) {
            $rules = [
                ...$rules,
                'roas_atendidos' => ['required'],
            ];
        } elseif ($request['form'] === 2) {
            $rules = [
                ...$rules,
                'registros_classe' => ['required'],
                'graf_reg_classe' => ['required'],
            ];
        } elseif ($request['form'] === 3) {
            $rules = [
                ...$rules,
                'registros_campanha' => ['required'],
                'graf_reg_campanha' => ['required'],
            ];
        } elseif ($request['form'] === 4) {
            $rules = [
                ...$rules,
                'registros_tipo' => ['required'],
                'graf_reg_tipo' => ['required'],
            ];
        } elseif ($request['form'] === 5) {
            $rules = [
                ...$rules,
                'registros_passagem' => ['required'],
                'graf_reg_passagem' => ['required'],
            ];
        } elseif ($request['form'] === 6) {
            $rules = [
                ...$rules,
                'registros_especie' => ['required'],
                'graf_reg_especie' => ['required'],
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
