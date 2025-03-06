<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistroRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id_ocorrencia' => ['required'],
      'arquivo'       => ['required'],
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
