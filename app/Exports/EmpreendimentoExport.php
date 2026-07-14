<?php

namespace App\Exports;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcvwEstudos;
use App\Models\SgcvwSubprodutos;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpreendimentoExport implements FromCollection, WithHeadings
{
    protected $campos;
    protected $tabela;
    protected $ordenarpor;
    protected $ordem;

    public function __construct(array $campos, $tabela = 'sgcvw_empreendimentos', $ordenarpor = null, $ordem = 'asc')
    {
        $this->campos = $campos;
        $this->tabela = $tabela;
        $this->ordenarpor = $ordenarpor;
        $this->ordem = $ordem;
    }

    public function collection()
    {
        $query = null;
        switch ($this->tabela) {
            case 'sgcvw_empreendimentos':
                $query = SgcvwEmpreendimentos::select($this->campos); break;
            case 'sgcvw_estudos':
                $query = SgcvwEstudos::select($this->campos); break;
            case 'sgcvw_subprodutos':
                $query = SgcvwSubprodutos::select($this->campos); break;
            default:
                throw new \Exception("Tabela inválida: {$this->tabela}");
        }

        if ($this->ordenarpor) {
            $query->orderBy($this->ordenarpor, $this->ordem);
        }

        return $query->get();
        // return SgcvwEmpreendimentos::select($this->campos)->get();
    }

    public function headings(): array
    {
        return array_map('ucfirst', $this->campos);
    }
}

