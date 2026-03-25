<?php

namespace App\Domain\Servico\PMQA\Configuracao\Ponto\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PMQAPontoImport implements ToCollection, WithHeadingRow, WithValidation
{
  /**
   * @param Collection $collection
   */
  public function collection(Collection $collection)
  {
    return $collection;
  }

  /**
   * Regras de validação para a planilha importada.
   * @return array
   */
  public function rules(): array
  {
    return [
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
}
