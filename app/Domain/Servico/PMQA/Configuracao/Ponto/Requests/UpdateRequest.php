<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                 => ['required'],
      'nome_ponto_coleta'  => ['required'],
      'lat_x'              => ['required'],
      'long_y'             => ['required'],
      'classificacao'      => ['required'],
      'classe'             => ['required'],
      'tipo_ambiente'      => ['required'],
      'UF'                 => ['required'],
      'municipio'          => ['required'],
      'bacia_hidrografica' => ['required'],
      'km_rodovia'         => ['required'],
      'estaca'             => ['required'],
      'observacoes'        => ['required']
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
