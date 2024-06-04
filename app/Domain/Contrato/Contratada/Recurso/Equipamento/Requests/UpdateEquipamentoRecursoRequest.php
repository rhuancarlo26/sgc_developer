<?php

namespace App\Domain\Contrato\Contratada\Recurso\Equipamento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipamentoRecursoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                => 'required|exists:recurso_equipamentos,id',
      'contrato_id'       => 'required',
      'nome'              => 'required',
      'descricao'         => 'required',
      'atividade'         => 'required',
      'observacao'        => 'required',
      'alugado'           => 'required',
      'numero_serie'      => 'required',
      'ultima_calibracao' => 'required'
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
