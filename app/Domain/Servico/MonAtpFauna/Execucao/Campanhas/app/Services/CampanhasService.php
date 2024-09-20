<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services;

use App\Models\AtFaunaExecucaoCampanha;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CampanhasService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = AtFaunaExecucaoCampanha::class;

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->getCampanhas($servico->id, $searchParams);
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function getCampanhas(int $servicoId, array $searchParams, ?array $idsCampanhas = null, bool $paginate = true): LengthAwarePaginator|Collection
    {
        return $this->searchAllColumns(...$searchParams)
            ->select([
                'at_fauna_execucao_campanhas.id',
                'at_fauna_execucao_campanhas.fk_servico_licenca',
                'at_fauna_execucao_campanhas.uf_inicial',
                'at_fauna_execucao_campanhas.uf_final',
                'at_fauna_execucao_campanhas.km_inicial',
                'at_fauna_execucao_campanhas.km_final',
                'at_fauna_execucao_campanhas.latitude_inicial',
                'at_fauna_execucao_campanhas.latitude_final',
                'at_fauna_execucao_campanhas.longitude_inicial',
                'at_fauna_execucao_campanhas.longitude_final',
                'at_fauna_execucao_campanhas.data_inicial',
                'at_fauna_execucao_campanhas.zona_inicial',
                'at_fauna_execucao_campanhas.zona_final',
                DB::raw('DATE_FORMAT(at_fauna_execucao_campanhas.data_inicial, "%d/%m/%Y") as data_inicialF'),
                'at_fauna_execucao_campanhas.data_final',
                DB::raw('DATE_FORMAT(at_fauna_execucao_campanhas.data_final, "%d/%m/%Y") as data_finalF'),
                'at_fauna_execucao_campanhas.periodo',
                'at_fauna_execucao_campanhas.observacao',
                'estado_inicial.uf AS nome_uf_inicial',
                'estado_inicial.nome AS nome_estado_inicial',
                'estado_final.nome AS nome_estado_final',
                'estado_final.uf AS nome_uf_final',
                'licencas_br.rodovia'
            ])
            ->join('servico_licenca_condicionante AS slc', 'slc.id', '=', 'at_fauna_execucao_campanhas.fk_servico_licenca')
            ->join('licencas', 'licencas.id', '=', 'slc.id_licenca')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->join('estados AS estado_inicial', 'estado_inicial.id', '=', 'at_fauna_execucao_campanhas.uf_inicial')
            ->join('estados AS estado_final', 'estado_final.id', '=', 'at_fauna_execucao_campanhas.uf_final')
            ->where('slc.id_servico', $servicoId)
            ->when($idsCampanhas, fn($query) => $query->whereIn('at_fauna_execucao_campanhas.id', $idsCampanhas))
            ->when($paginate, fn($query) => $query->paginate(), fn($query) => $query->get());
    }
}
