<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Campanhas\app\Services;

use App\Models\AtFaunaExecucaoCampanha;
use App\Models\AtFaunaExecucaoCampanhaEquipamento;
use App\Models\AtFaunaExecucaoCampanhaEquipe;
use App\Models\AtFaunaExecucaoCampanhaResposavel;
use App\Models\AtFaunaExecucaoCampanhaTrechoPavimentacao;
use App\Models\AtFaunaExecucaoCampanhaVeiculo;
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
    protected string $modelClassResponsavel = AtFaunaExecucaoCampanhaResposavel::class;
    protected string $modelClassEquipe = AtFaunaExecucaoCampanhaEquipe::class;
    protected string $modelClassEquipamento = AtFaunaExecucaoCampanhaEquipamento::class;
    protected string $modelClassVeiculo = AtFaunaExecucaoCampanhaVeiculo::class;
    protected string $modelClassPavimentacao = AtFaunaExecucaoCampanhaTrechoPavimentacao::class;

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

    public function store_responsavel(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassResponsavel, infos: [
            'at_fauna_execucao_campanha_id' => $post['at_fauna_execucao_campanha_id'],
            'servico_rh_id' => $post['responsavel_id']
        ]);
    }

    public function store_equipe(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassEquipe, infos: [
            'at_fauna_execucao_campanha_id' => $post['at_fauna_execucao_campanha_id'],
            'servico_rh_id' => $post['equipe_id']
        ]);
    }

    public function store_equipamento(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassEquipamento, infos: [
            'at_fauna_execucao_campanha_id' => $post['at_fauna_execucao_campanha_id'],
            'servico_equipamento_id' => $post['equipamento_id']
        ]);
    }

    public function store_veiculo(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassVeiculo, infos: [
            'at_fauna_execucao_campanha_id' => $post['at_fauna_execucao_campanha_id'],
            'servico_veiculo_id' => $post['veiculo_id']
        ]);
    }

    public function store_trechos(array $post)
    {
        foreach ($post['trechos'] as $value) {
            $this->dataManagement->create(entity: $this->modelClassPavimentacao, infos: [
                ...$value,
                'campanha_id' => $post['campanha_id']
            ]);
        }

        return $this->modelClassPavimentacao::where('campanha_id', '=', $post['campanha_id'])->get();
    }

    public function destroy_trecho(array $post)
    {
        $this->dataManagement->delete(entity: $this->modelClassPavimentacao, id: $post['id']);

        return $this->modelClassPavimentacao::where('campanha_id', '=', $post['campanha_id'])->get();
    }

    public function delete_responsavel(array $post)
    {
        return $this->dataManagement->delete(entity: $this->modelClassResponsavel, id: $post['responsavel']['id']);
    }

    public function delete_equipe(array $post)
    {
        return $this->dataManagement->delete(entity: $this->modelClassEquipe, id: $post['equipe']['id']);
    }

    public function delete_equipamento(array $post)
    {
        return $this->dataManagement->delete(entity: $this->modelClassEquipamento, id: $post['equipamento']['id']);
    }

    public function delete_veiculo(array $post)
    {
        return $this->dataManagement->delete(entity: $this->modelClassVeiculo, id: $post['veiculo']['id']);
    }
}
