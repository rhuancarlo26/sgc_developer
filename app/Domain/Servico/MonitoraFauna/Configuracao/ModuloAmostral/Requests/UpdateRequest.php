<?php

namespace App\Domain\Servico\MonitoraFauna\Configuracao\ModuloAmostral\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'id_servico' => ['required'],
      'data_cadastro' => ['required'],
      'tamanho_modulo' => ['required'],
      'uf' => ['required'],
      'municipio' => ['required'],
      'bioma' => ['required'],
      'fitofisionomia' => ['required'],
      'latitude_inicial' => ['required'],
      'longitude_inicial' => ['required'],
      'latitude_final' => ['required'],
      'longitude_final' => ['required'],
      'obs' => ['required'],
      'arquivo' => ['nullable']
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
