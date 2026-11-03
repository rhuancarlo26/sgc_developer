<?php

namespace App\Domain\Sgc\Licencas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'condicionante_id' => ['required', 'exists:sgc_licencas_condicionantes,id'],
            'data_evento' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return ['data_evento' => 'data do evento'];
    }
}
