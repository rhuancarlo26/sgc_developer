<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseRequest extends FormRequest
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
                'roas_em_aberto' => ['required'],
            ];
        } elseif ($request['form'] === 3) {
            $rules = [
                ...$rules,
                'rncs_atendidos' => ['required'],
            ];
        } elseif ($request['form'] === 4) {
            $rules = [
                ...$rules,
                'rncs_em_aberto' => ['required'],
            ];
        } elseif ($request['form'] === 5) {
            $rules = [
                ...$rules,
                'ocorr_por_intensidade' => ['required'],
                'graf_reg_intensidade' => ['required']
            ];
        } elseif ($request['form'] === 6) {
            $rules = [
                ...$rules,
                'ocorr_por_local' => ['required'],
                'graf_reg_local' => ['required']
            ];
        } elseif ($request['form'] === 7) {
            $rules = [
                ...$rules,
                'ocorr_por_classificacao' => ['required'],
                'graf_reg_classificacao' => ['required']
            ];
        } elseif ($request['form'] === 7) {
            $rules = [
                ...$rules,
                'ocorr_por_lote' => ['required'],
                'graf_reg_lote' => ['required']
            ];
        } elseif ($request['form'] === 8) {
            $rules = [
                ...$rules,
                'aca_gerados' => ['required']
            ];
        };

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
