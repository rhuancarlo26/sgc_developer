<?php

namespace App\Domain\Contrato\Contratada\Recurso\Veiculo\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVeiculoRecursoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id' => 'required',
      'contrato_id' => 'required',
      'veiculo_codigo_id' => 'required',
      'descricao' => 'required',
      'observacao' => 'required',
      'alugado' => 'required',
      'placa_veiculo' => 'required',
      'ultima_revisao' => 'required'
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
