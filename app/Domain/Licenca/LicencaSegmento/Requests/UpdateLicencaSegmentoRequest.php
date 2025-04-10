<?php

namespace App\Domain\Licenca\LicencaSegmento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicencaSegmentoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'idlicenca_br' => 'required',
      'licenca_id'   => 'required',
      'rodovia'      => 'required',
      'uf_inicial'   => 'required',
      'uf_final'     => 'required',
      'km_inicio'    => 'required',
      'km_fim'       => 'required',
      'extensao_br'  => 'required',
      'trecho_tipo'  => 'nullable',
      'coordenada'   => 'nullable',
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