<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'arquivo' => ['required']
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
