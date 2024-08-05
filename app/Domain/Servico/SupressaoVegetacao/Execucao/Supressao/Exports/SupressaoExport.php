<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Exports;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupressaoExport implements FromQuery, WithMapping, WithHeadings, WithTitle
{

    public function __construct(
        private readonly SupressaoService $supressaoService,
        private readonly array $searchParams,
        private readonly int $servicoId,
    )
    {
    }

    public function query(): Builder
    {
        return $this->supressaoService
            ->searchAllColumns(...$this->searchParams)
            ->where('servico_id', $this->servicoId);
    }

    public function map($row): array
    {
        return [
            $row->chave,
            $row->dt_inicial,
            $row->dt_final,
            $row->bioma->nome,
            $row->fitofisionomia,
            $row->estagioSucessional->nome,
            $row->area_em_app,
            $row->area_fora_app,
            $row->area_total,
            $row->licenca->numero_licenca,
            $row->observacao,
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Data início',
            'Data final',
            'Bioma',
            'Fitofisionomia',
            'Estágio sucessional',
            'Área em APP',
            'Área fora APP',
            'Área total',
            'N° ASV',
            'Observações',
        ];
    }

    public function title(): string
    {
        return 'Supressão';
    }

}
