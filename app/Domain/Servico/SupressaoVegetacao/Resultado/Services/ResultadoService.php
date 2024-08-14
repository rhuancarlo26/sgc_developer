<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Services;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services\PlanoSupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\CorteEspecieService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services\SupressaoService;
use App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo\ContextStrategy;
use App\Models\ResultadoSupressao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = ResultadoSupressao::class;

    public function __construct(
        DataManagement                         $dataManagement,
        private readonly SupressaoService      $supressaoService,
        private readonly CorteEspecieService   $corteEspecieService,
        private readonly PlanoSupressaoService $planoSupressaoService,
        private readonly LicencaService        $licencaService,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return $this->model
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        $periodo = (new ContextStrategy())->calculate(request: $request);

        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            ...$periodo,
            'chave' => $this->getCodigo(prefix: 'RS'),
        ]);
    }

    public function update(array $request): array
    {
        $periodo = (new ContextStrategy())->calculate(request: $request);

        return $this->dataManagement->update(entity: $this->modelClass, infos: [
            ...$request,
            ...$periodo,
        ], id: $request['id']);
    }

    public function getResultadoAnalise(Servicos $servico, ResultadoSupressao $resultado): array
    {

        $supressao = $this->supressaoService->getByPeriodo($servico, $resultado->dt_inicio, $resultado->dt_final, ['bioma:id,nome', 'licenca:id,numero_licenca', 'estagioSucessional:id,nome']);
        $cortes = $this->corteEspecieService->getSomaByAreaSupressao($supressao->pluck('id')->toArray());
        $supressaoArea = $this->supressaoService->getSumAreaByServico($servico->id);
        $planoSupressaoArea = $this->planoSupressaoService->getSumAreaByServico($servico->id);
        $licencaArea = $this->licencaService->getSumArea($supressao->pluck('licenca_id')->toArray());

        $areaTotalLicenca = $licencaArea->in_app + $licencaArea->out_app;
        $areaTotalSupressao = $supressaoArea->area_em_app + $supressaoArea->area_fora_app;

        $resumo = [
            ['desc' => 'Plano de supressão', 'area_app' => $planoSupressaoArea->area_em_app, 'area_fora' => $planoSupressaoArea->area_fora_app, 'total' => $planoSupressaoArea->area_em_app + $planoSupressaoArea->area_fora_app],
            ['desc' => 'Supressão', 'area_app' => $supressaoArea->area_em_app, 'area_fora' => $supressaoArea->area_fora_app, 'total' => $areaTotalSupressao],
            ['desc' => 'ASV', 'area_app' => $licencaArea->in_app ?? 0, 'area_fora' => $licencaArea->out_app ?? 0, 'total' => $areaTotalLicenca]
        ];

        $percentualLicenca = $areaTotalLicenca === 0 ? 0 : ($areaTotalSupressao / $areaTotalLicenca) * 100;
        $percentualSuprimido = number_format($percentualLicenca, 2, ',', ' ');

        $areaMes = $this->supressaoService->getSumAreaPerMonthByServico($servico, $resultado->dt_inicio, $resultado->dt_final);

        return compact('supressao', 'cortes', 'resumo', 'percentualSuprimido', 'areaMes');
    }

}
