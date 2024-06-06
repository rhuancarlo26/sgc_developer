<?php

namespace App\Domain\Licenca\LicencaSegmento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaSegmentoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      // 'licenca_id'   => 'required',
      // 'rodovia_id'   => 'required',
      // 'uf_incial_id' => 'required',
      // 'uf_final_id'  => 'required',
      // 'km_inicial'   => 'required',
      // 'km_final'     => 'required',
      // 'extensao'     => 'required',
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
