<?php

namespace App\Domain\Servico\PMQA\Execucao\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'            => ['required'],
      'servico_id'    => ['required'],
      'nome'          => ['required'],
      'data_inicio'   => ['required'],
      'data_termino'  => ['required'],
      'pontos'        => ['required']
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