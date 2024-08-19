<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Exports;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services\DestinacaoService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DestinacaoExport implements FromQuery, WithMapping, WithHeadings, WithTitle
{

    public function __construct(
        private readonly DestinacaoService $destinacaoService,
        private readonly array             $searchParams,
        private readonly int               $servicoId,
    )
    {
    }

    public function query(): Builder
    {
        return $this->destinacaoService
            ->searchAllColumns(...$this->searchParams)
            ->with('pilhas:id,chave')
            ->withSum('pilhas', 'volume')
            ->where('servico_id', $this->servicoId);
    }

    public function map($row): array
    {
        return [
            $row->chave,
            $row->dt_envio,
            implode(', ', array_map(fn($item) => $item['chave'], $row->pilhas->toArray())),
            $row->uso_da_madeira,
            $row->destinatario,
            $row->pilhas_sum_volume,
            $row->observacao,
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Data cadastro',
            'Pilhas',
            'Uso madeira',
            'Destinatario',
            'Volume',
            'Observacoes',
        ];
    }

    public function title(): string
    {
        return 'Destinação de Pilha';
    }

}
