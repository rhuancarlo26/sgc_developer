<?php

namespace App\Domain\Servico\PassagemFauna\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnaliseRequest extends FormRequest
{
    public function rules(): array
    {
        $request = request()->all();

        $rules = [
            'form' => ['required'],
            'id_resultado' => ['required'],
        ];

        if ($request['form'] === 1) {
            $rules = [
                ...$rules,
                'registros_identificados' => ['required'],
            ];
        } elseif ($request['form'] === 2) {
            $rules = [
                ...$rules,
                'registros_classe' => ['required'],
                'graf_reg_classe' => ['required'],
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
