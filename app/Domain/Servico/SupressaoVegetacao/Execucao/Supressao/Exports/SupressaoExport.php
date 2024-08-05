<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Exports;

use App\Models\AreaSupressao;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SupressaoExport implements FromCollection, WithMapping, WithHeadings, WithTitle
{

    public function collection(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        return AreaSupressao::all();
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
