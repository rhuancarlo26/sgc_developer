<?php

namespace App\Domain\Servico\PMQA\Resultado\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutraAnaliseRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'resultado_id' => ['required'],
      'nome'         => ['required'],
      'arquivo'      => ['required'],
      'analise'      => ['required'],
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