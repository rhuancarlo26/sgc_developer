<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                => ['required'],
      'nomepontocoleta'   => ['required'],
      'lat_x'             => ['required'],
      'long_y'            => ['required'],
      'classificacao'     => ['required'],
      'classe'            => ['required'],
      'tipoambiente'      => ['required'],
      'uf'                => ['required'],
      'municipio'         => ['required'],
      'baciahidrografica' => ['required'],
      'km_rodovia'        => ['required'],
      'estaca'            => ['required'],
      'observacoes'       => ['required']
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
