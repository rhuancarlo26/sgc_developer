<?php

namespace App\Domain\Sgc\Contratada\app\Imports;

// Funções de Importação da Planílha
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use App\Models\ServicoMonitoraFaunaConfigArmadilhaMetodo;

class ArmadilhaImport implements WithMultipleSheets
{

    protected ?int $idModulo;
    /**
     * Se não receber idModulo, usa null (ou pode definir um padrão)
     */
    public function __construct(?int $idModulo = null)
    {
        $this->idModulo = $idModulo;
    }

    public function sheets(): array
    {
        return [
            'Armadilha e Métodos' => new DashboardImportFaunaMetodos($this->idModulo),
            # Adicione mais instâncias de classes de importação conforme necessário
        ];
    }
}

class DashboardImportFaunaMetodos implements ToModel, WithStartRow, WithCalculatedFormulas
{
    protected int $idModulo;

    public function __construct(?int $idModulo)
    {
        $this->idModulo = $idModulo;
    }

    public function model(array $row)
    {
        if (!in_array($row[0], [null, 'NULL', 'null', "", ' '])) {
            return new ServicoMonitoraFaunaConfigArmadilhaMetodo([
                'id_modulo'               => $this->idModulo,
                'parcela'                 => $row[0],
                'forma'                   => $row[1],
                'tipo'                    => $row[2],
                'numero_armadilha_metodo' => $row[3],
                'latitude'                => $row[4],
                'longitude'               => $row[5],
                'observacao'              => $row[6],
                // 'zona' e 'nome_id' podem ser adicionados conforme necessidade
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
