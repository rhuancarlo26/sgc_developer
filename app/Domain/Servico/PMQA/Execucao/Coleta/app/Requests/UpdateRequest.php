<?php

namespace App\Domain\Servico\PMQA\Execucao\Coleta\app\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    $request = (object) request()->all();
    $regras = [];

    $regras['id']                         = 'required';
    $regras['campanha_ponto_id']          = 'required';
    $regras['data_coleta']                = 'required';
    $regras['sem_coleta']                 = 'required';

    if ($request->sem_coleta === true) {
      $regras['justificativa']            = 'required';
    } else {
      $regras['numero_amostra']           = 'required';
      $regras['preservacao_amostra']      = 'required';
      $regras['acondicionamento_amostra'] = 'required';
      $regras['transporte_amostra']       = 'required';
    }

    return $regras;
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