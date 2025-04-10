<?php

namespace App\Domain\Servico\ContOcorrencia\Resultado\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        $request = request()->all();
        
        $rules = [
            'nome' => ['required'],
            'periodo' => ['required']
        ];

        switch ($request['periodo']) {
            case 'M':
                $rules = [
                    ...$rules,
                    'mes' => ['required']
                ];
                break;
            case 'S':
                $rules = [
                    ...$rules,
                    'semestre' => ['required'],
                    'ano' => ['required']
                ];
                break;
            case 'A':
                $rules = [
                    ...$rules,
                    'ano' => ['required']
                ];
                break;
            case 'P':
                $rules = [
                    ...$rules,
                    'dt_inicio' => ['required'],
                    'dt_final' => ['required']
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
