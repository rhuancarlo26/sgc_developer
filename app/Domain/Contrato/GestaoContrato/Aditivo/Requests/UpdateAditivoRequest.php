<?php

namespace App\Domain\Contrato\GestaoContrato\Aditivo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAditivoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                    => 'required',
      'contrato_id'           => 'required',
      'aditivo'               => 'required',
      'valor'                 => 'required',
      'publicacao'            => 'required',
      'data_inicio_vigencia'  => 'required',
      'numero_sei'            => 'required'
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
