<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                => ['required'],
      'campanha_ponto_id' => ['required'],
      'sem_coleta'        => ['nullable'],
      'iqa'               => ['nullable'],
      'parametros'        => ['nullable'],
      'justificativa'     => ['nullable'],
    ];
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