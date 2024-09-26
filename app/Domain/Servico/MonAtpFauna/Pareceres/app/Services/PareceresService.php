<?php

namespace App\Domain\Servico\MonAtpFauna\Pareceres\app\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PareceresService
{

    public function index(Servicos $servico, array $searchParams)
    {
        $query1 = DB::table('at_fauna_parecer_configuracao AS ppc')
            ->select([
                DB::raw("'Configurações' AS tipo"),
                'fk_status',
                DB::raw('parecer COLLATE utf8_general_ci AS parecer'),
                DB::raw("DATE_FORMAT(ppc.created_at, '%d/%m/%Y') as data_parecer")
            ])
            ->whereIn('fk_status', [2, 3, 4])
            ->where('fk_servico', $servico->id);

        $query2 = DB::table('at_fauna_relatorio')
            ->select([
                DB::raw("CONCAT('Relatório - ', nome_relatorio) AS tipo"),
                'fk_status',
                DB::raw('parecer_fiscal COLLATE utf8_general_ci AS parecer'),
                DB::raw("DATE_FORMAT(at_fauna_relatorio.created_at, '%d/%m/%Y') as data_parecer")
            ])
            ->whereIn('fk_status', [2, 3, 4])
            ->where('fk_servico', $servico->id);

        return $query1->unionAll($query2)->paginate();
    }

}
