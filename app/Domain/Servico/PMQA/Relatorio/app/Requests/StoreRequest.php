<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'servico_id'   => ['required'],
      'nome'         => ['required'],
      'resultado_id' => ['required'],
      'observacao'   => ['required'],
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
