<?php

namespace App\Domain\Contrato\GestaoContrato\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'tipo_id' => 'required',
      'numero_contrato' => 'required',
      'cnpj' => 'required',
      'contratada' => 'required',
      'processo_sei' => 'required',
      'objeto' => 'required',
      'data_inicio_vigencia' => 'required',
      'data_termino_vigencia' => 'required',
      'situacao' => 'required',
      'edital' => 'required',
      'tipo_licitacao' => 'required',
      'modalidade' => 'required',
      'unidade_gestora' => 'required',
      'fiscal_contrato' => 'required',
      'preco_inicial' => 'required',
      'total_aditivo' => 'required',
      'total_reajuste' => 'required',
      'total' => 'required'
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
