<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'licenca_id'  => ['required']
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
