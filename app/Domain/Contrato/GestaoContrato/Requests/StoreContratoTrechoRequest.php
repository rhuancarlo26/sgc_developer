<?php

namespace App\Domain\Contrato\GestaoContrato\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoTrechoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'contrato_id' => 'required',
      'tipo_contrato' => 'required',
      'uf' => 'required',
      'rodovia' => 'required',
      'km_inicial' => 'required',
      'km_final' => 'required',
      'tipo_trecho' => 'required'
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
