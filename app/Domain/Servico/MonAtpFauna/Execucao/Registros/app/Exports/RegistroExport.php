<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Exports;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Models\Servicos;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RegistroExport implements FromQuery, WithMapping, WithHeadings, WithTitle
{

    public function __construct(
        private readonly RegistrosService $registroService,
        private readonly array $searchParams,
        private readonly Servicos $servico,
    )
    {
    }

    public function query(): Builder
    {
        return $this->registroService->index($this->servico, $this->searchParams, false);
    }

    public function map($row): array
    {
        return [
            $row->nome_registro,
            $row->fk_campanha,
            $row->nome_grupo_amostrado,
            $row->data_registroF,
            $row->nome_estado,
            $row->rodovia,
            $row->km,
            $row->latitude,
            $row->longitude,
            $row->sentido,
            $row->margem,
            $row->classe,
            $row->ordem,
            $row->familia,
            $row->genero,
            $row->especie,
            $row->nome_comum,
            $row->sexo,
            $row->faixa_etaria,
            $row->coletado,
            $row->n_registro_tombamento,
            $row->carcaca_removida,
            $row->reducao_biologica,
            $row->n_individuos,
            $row->estadual,
            $row->federal,
            $row->iucn,
        ];
    }

    public function headings(): array
    {
        return [
            'Id',
            'Campanha',
            'Grupo amostrado',
            'Data registro',
            'Estado',
            'BR',
            'KM',
            'Latitude',
            'Longitude',
            'Sentido',
            'Margem',
            'Classe',
            'Ordem',
            'Família',
            'Gênero',
            'Espécie',
            'Nome comum',
            'Sexo',
            'Faixa etária',
            'Coletado',
            'Num tombamento',
            'Carcaca removida',
            'Redução biológica',
            'Num indivíduos',
            'Status conservação estadual',
            'Status conservação federal',
            'Status conservação iucn',
        ];
    }

    public function title(): string
    {
        return 'Registros P.F.';
    }

}
