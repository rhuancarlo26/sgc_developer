<?php

namespace App\Domain\Servico\PMQA\Execucao\Medir\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArquivoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                => 'required',
      'arquivo'           => 'required'
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