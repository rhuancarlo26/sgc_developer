<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id_servico'                    => ['nullable'],
      'num_contrato'                  => ['nullable'],
      'nome'                          => ['nullable'],
      'rodovia'                       => ['nullable'],
      'empresa'                       => ['nullable'],
      'situacao_contrato'             => ['nullable'],
      'obj_contrato'                  => ['nullable'],
      'fiscal_contrato'               => ['nullable'],
      'km_inicial'                    => ['nullable'],
      'km_final'                      => ['nullable'],
      'estaca_inicial'                => ['nullable'],
      'estaca_final'                  => ['nullable'],
      'lat_inicial'                   => ['nullable'],
      'lat_final'                     => ['nullable'],
      'long_inicial'                  => ['nullable'],
      'long_final'                    => ['nullable'],
      'supervisora_obras'             => ['nullable'],
      'num_contrato_supervisora'      => ['nullable'],
      'obj_contrato_supervisora'      => ['nullable'],
      'fiscal_contrato_supervisora'   => ['nullable'],
      'situacao_contrato_supervisora' => ['nullable'],
      'obs'                           => ['nullable'],
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
