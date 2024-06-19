<?php

namespace App\Domain\Licenca\LicencaSegmento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaSegmentoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'licenca_id'  => 'required',
      'rodovia'     => 'required',
      'uf_inicial'  => 'required',
      'uf_final'    => 'required',
      'km_inicial'  => 'required',
      'km_final'    => 'required',
      'extensao'    => 'required',
      'trecho_tipo' => 'nullable',
      'coordenada'  => 'nullable',
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
