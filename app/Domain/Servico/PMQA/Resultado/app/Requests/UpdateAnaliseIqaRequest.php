<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnaliseIqaRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'           => ['required'],
      'resultado_id' => ['required'],
      'analise'      => ['required'],
      'imagem'       => ['required']
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
