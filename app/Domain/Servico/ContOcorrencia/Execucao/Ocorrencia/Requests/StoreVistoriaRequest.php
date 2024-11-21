<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVistoriaRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'ocorrencia' => ['required'],
      'vistoria' => ['required']
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
