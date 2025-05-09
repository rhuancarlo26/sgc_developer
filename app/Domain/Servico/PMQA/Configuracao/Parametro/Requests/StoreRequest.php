<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'servico_id'  => ['required'],
      'nome'        => ['required'],
      'medir_iqa'   => ['required'],
      'parametros'  => ['required']
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