<?php

namespace App\Domain\Servico\ContOcorrencia\Execucao\Ocorrencia\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'id'                        => ['nullable'],
      'id_servico'                => ['nullable'],
      'num_por_servico'           => ['nullable'],
      'nome_id'                   => ['nullable'],
      'id_rodovia'                => ['nullable'],
      'id_uf'                     => ['nullable'],
      'data_ocorrencia'           => ['nullable'],
      'km'                        => ['nullable'],
      'estaca'                    => ['nullable'],
      'lado'                      => ['nullable'],
      'latitude'                  => ['nullable'],
      'longitude'                 => ['nullable'],
      'zona'                      => ['nullable'],
      'id_lote'                   => ['nullable'],
      'indicio_responsabilidade'  => ['nullable'],
      'possivel_causa'            => ['nullable'],
      'descricao_causa'           => ['nullable'],
      'intensidade'               => ['nullable'],
      'tipo'                      => ['nullable'],
      'rnc_direto'                => ['nullable'],
      'local'                     => ['nullable'],
      'classificacao'             => ['nullable'],
      'descricao'                 => ['nullable'],
      'area_total'                => ['nullable'],
      'prazo'                     => ['nullable'],
      'dias_restantes'            => ['nullable'],
      'acoes'                     => ['nullable'],
      'norma'                     => ['nullable'],
      'obs'                       => ['nullable'],
      'status'                    => ['nullable'],
      'aprovado_rnc'              => ['nullable'],
      'parecer_fiscal'            => ['nullable'],
      'envio_empresa'             => ['nullable'],
      'envio_fiscal'              => ['nullable'],
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