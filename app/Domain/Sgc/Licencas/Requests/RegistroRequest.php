<?php

namespace App\Domain\Sgc\Licencas\Requests;

use App\Domain\Sgc\Licencas\Enums\TipoRegistro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prazo = $this->route('prazo');

        return [
            'tipo' => ['required', Rule::enum(TipoRegistro::class)],
            // "data" é nome reservado no useForm do Inertia, por isso o campo chega como data_registro
            'data_registro' => ['required', 'date'],
            'numero_oficio' => ['nullable', 'string', 'max:150'],
            'numero_sei' => ['nullable', 'string', 'max:50'],
            'descricao' => ['nullable', 'required_if:tipo,' . TipoRegistro::Observacao->value . ',' . TipoRegistro::Dispensa->value, 'string'],
            'data_limite_nova' => [
                'nullable',
                'required_if:tipo,' . TipoRegistro::Prorrogacao->value,
                'date',
                'after:' . $prazo->data_limite->format('Y-m-d'),
            ],
            'arquivo' => ['nullable', 'file', 'max:51200'],
        ];
    }

    public function attributes(): array
    {
        return [
            'data_registro' => 'data',
            'numero_oficio' => 'nº do ofício',
            'numero_sei' => 'nº SEI',
            'descricao' => 'descrição',
            'data_limite_nova' => 'nova data-limite',
        ];
    }

    public function messages(): array
    {
        return [
            'data_limite_nova.after' => 'A nova data-limite deve ser posterior à data-limite atual.',
        ];
    }
}
