<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnaliseRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'resultado_id' => ['required'],
      'parametro_id' => ['required'],
      'analises'     => ['required'],
      'imagem'       => ['required'],
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