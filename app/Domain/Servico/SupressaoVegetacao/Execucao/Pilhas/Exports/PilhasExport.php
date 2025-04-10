<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Exports;

use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services\PilhasService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PilhasExport implements FromQuery, WithMapping, WithHeadings, WithTitle
{

    public function __construct(
        private readonly PilhasService $pilhasService,
        private readonly array         $searchParams,
        private readonly int           $servicoId,
    )
    {
    }

    public function query(): Builder
    {
        return $this->pilhasService
            ->searchAllColumns(...$this->searchParams)
            ->where('servico_id', $this->servicoId);
    }

    public function map($row): array
    {
        return [
            $row->chave,
            $row->created_at,
            $row->areaSupressao->chave,
            $row->licenca->numero_licenca,
            $row->patio->chave,
            $row->produto->nome,
            $row->tipo_pilha_label,
            $row->volume,
            $row->corteEspecie?->nome,
            $row->corteEspecie?->qtd_corte,
            $row->latitude,
            $row->longitude,
            $row->observacao,
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Data cadastro',
            'Área suprimida',
            'N° ASV',
            'Pátio estocagem',
            'Tipo produto',
            'Tipo pilha',
            'Volume',
            'Espécies ameaçadas',
            'Quantidade indivíduos',
            'Latitude',
            'Longitude',
            'Observações',
        ];
    }

    public function title(): string
    {
        return 'Controle de Pilhas';
    }

}
