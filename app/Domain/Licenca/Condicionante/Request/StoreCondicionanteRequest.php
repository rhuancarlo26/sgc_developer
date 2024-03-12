<?php

namespace App\Domain\Licenca\Condicionante\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreCondicionanteRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'licenca_id' => 'required',
      'numero_condicionante' => 'required',
      'prazo' => 'required',
      'descricao' => 'required'
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
