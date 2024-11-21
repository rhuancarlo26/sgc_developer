<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RegistroImport implements ToCollection, WithHeadingRow, WithValidation
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
      'fk_grupo_amostrado' => ['nullable'],
      'uf_final' => ['nullable'],
      'estado' => ['nullable'],
      'nome_registro' => ['nullable'],
      'data_registro' => ['nullable'],
      'km' => ['nullable'],
      'latitude' => ['nullable'],
      'longitude' => ['nullable'],
      'zona' => ['nullable'],
      'sentido' => ['nullable'],
      'margem' => ['nullable'],
      'classe' => ['nullable'],
      'ordem' => ['nullable'],
      'familia' => ['nullable'],
      'genero' => ['nullable'],
      'especie' => ['nullable'],
      'nome_comum' => ['nullable'],
      'reducao_biologica' => ['nullable'],
      'sexo' => ['nullable'],
      'coletado' => ['nullable'],
      'faixa_etaria' => ['nullable'],
      'n_registro_tombamento' => ['nullable'],
      'estadual' => ['nullable'],
      'federal' => ['nullable'],
      'iucn' => ['nullable'],
      'n_individuos' => ['nullable'],
      'carcaca_removida' => ['nullable'],
      'fotografia' => ['nullable'],
      'hora_registro' => ['nullable'],
    ];
  }
}