<?php

namespace App\Exports;
use App\Models\SgcvwEmpreendimentos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpreendimentoExport implements FromCollection, WithHeadings
{
    protected $campos;

    public function __construct(array $campos)
    {
        $this->campos = $campos;
    }

    public function collection()
    {
        return SgcvwEmpreendimentos::select($this->campos)->get();
    }

    public function headings(): array
    {
        return array_map('ucfirst', $this->campos);
    }
}

