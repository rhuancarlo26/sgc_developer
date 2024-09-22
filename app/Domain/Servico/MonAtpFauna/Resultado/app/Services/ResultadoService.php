<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Services;

use App\Domain\Servico\PMQA\Configuracao\Ponto\Imports\PMQAPontoImport;
use App\Models\AtFaunaResultado;
use App\Models\AtFaunaResultadoAnalise;
use App\Models\AtFaunaResultadoOutrasAnalise;
use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\DataManagement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaResultado::class;

    public function __construct(
        DataManagement $dataManagement,
        private readonly ResultadoCampanhaService $resultadoCampanhaService,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico, array $searchParams)
    {
        return $this->searchAllColumns(...$searchParams)
            ->select([
                'at_fauna_resultado.*',
                DB::raw('GROUP_CONCAT(fec.id) AS campanhas'),
                'relat.fk_status as fk_status_relatorio'
            ])
            ->join('at_fauna_resultado_campanha AS frc', 'frc.fk_resultado', '=', 'at_fauna_resultado.id')
            ->join('at_fauna_execucao_campanhas AS fec', 'fec.id', '=', 'frc.fk_campanha')
            ->leftJoin('at_fauna_relatorio AS relat', 'relat.fk_resultado', '=', 'at_fauna_resultado.id')
            ->where('at_fauna_resultado.fk_servico', $servico->id)
            ->groupBy('at_fauna_resultado.created_at', 'at_fauna_resultado.id')
            ->paginate();
    }

    public function store(array $request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function getResultadoAnalise(AtFaunaResultado $resultado): array
    {
        return [
            'analise' => AtFaunaResultadoAnalise::where('fk_resultado', $resultado->id)->first(),
            'tabela_registro' => $this->resultadoCampanhaService->getTabelaRegistrosIdentificados($resultado->id),
            'total_individuos' => $this->resultadoCampanhaService->getNumeroTotalIndividuos($resultado->id),
            'atropelados_campanha' => $this->resultadoCampanhaService->getAtropeladoCampanha($resultado->id),
            'atropelados_classe' => $this->resultadoCampanhaService->getAtropeladoClasse($resultado->id),
            'atropelados_especie' => $this->resultadoCampanhaService->getAtropeladoEspecie($resultado->id),
            'atropelados_mes' => $this->resultadoCampanhaService->getAtropeladoMes($resultado->id),
            'atropelados_km' => $this->resultadoCampanhaService->getAtropeladoKm($resultado->id),
            'outras_analises' => AtFaunaResultadoOutrasAnalise::where('fk_resultado', $resultado->id)->get(),
        ];
    }
}
