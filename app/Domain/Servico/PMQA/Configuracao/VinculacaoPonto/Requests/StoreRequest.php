<?php

namespace App\Domain\Servico\PMQA\Configuracao\VinculacaoPonto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'servico_id'          => ['required'],
      'lista'               => ['required'],
      'periodicidade'       => ['required'],
      'pontos'              => ['required'],
      'relatorio_parcial'   => ['nullable'],
      'relatorio_acomulado' => ['nullable']
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