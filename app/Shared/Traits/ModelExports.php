<?php

namespace App\Shared\Traits;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ModelExports implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison, ShouldAutoSize, WithStyles
{
  private iterable $data = [];
  private array $mapping;

  /**
   * @param iterable $data Dados a serem exportados
   * @param $mapping Colunas;Propriedades a serem exibidas
   */
  public function __construct($data, $mapping)
  {
    $this->data = $data;
    $this->mapping = $mapping;
  }

  public function collection()
  {
    return $this->data;
  }

  public function map($data): array
  {
    $mapping = [];

    foreach ($this->mapping as $col) {

      $mapping[] = eval('return $data->' . str_replace('.', '->', $col) . ';');
    }

    return $mapping;
  }

  public function headings(): array
  {
    return array_map(fn ($header) => Str::headline(ucfirst(str_replace('.', "_>_", $header))), $this->mapping);
  }

  public function styles(Worksheet $sheet): mixed
  {
    return [

      // Formata cabecalho da tabela
      1 => [
        'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
        'fill' => [
          'fillType'   => Fill::FILL_SOLID,
          'startColor' => ['argb' => Color::COLOR_DARKBLUE],
        ]
      ],
    ];
  }
}