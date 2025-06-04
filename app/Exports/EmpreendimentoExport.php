<?php

namespace App\Exports;
use App\Models\SgcvwEmpreendimentos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpreendimentoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SgcvwEmpreendimentos::all([
            'id',
            'created_at',
            'updated_at'
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Criado em',
            'Atualizado em'
        ];
    }
}

