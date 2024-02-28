<?php

namespace App\Domain\Licenca\AppModule\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicencaRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      // 'contratos_id'       => 'required',
      // 'licenca_tipo_id'    => 'required',
      // 'status'             => 'required',
      // 'modal'              => 'required',
      // 'numero_licenca'     => 'required',
      // 'numero_sei'         => 'required',
      // 'processo_dnit'      => 'required',
      // 'data_emissao'       => 'required',
      // 'vencimento'         => 'required',
      // 'emissor'            => 'required',
      // 'empreendimento'     => 'required',
      // 'inicio_subtrecho'   => 'required',
      // 'fim_subtrecho'      => 'required',
      // 'extensao'           => 'required',
      // 'dias_renovacao'     => 'required',
      // 'requerimento'       => 'required',
      // 'renovacao'          => 'required',
      // 'fiscal'             => 'required',
      // 'observacoes'        => 'required',
      // 'data_publicacao'    => 'required',
      // 'obs_renovacao'      => 'required',
      // 'in_app'             => 'required',
      // 'out_app'            => 'required',
      // 'volume'             => 'required',
      // 'sinaflor'           => 'required',
      // 'arquivo_licenca'    => 'required',
      // 'file_shape'         => 'required',
      // 'nome_file_shape'    => 'required',
      // 'total_app'          => 'required',
      // 'local_shape'        => 'required',
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
