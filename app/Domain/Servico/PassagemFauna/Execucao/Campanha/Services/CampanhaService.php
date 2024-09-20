<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services;

use App\Models\ServicoPassagemFaunaConfigAbio;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaExecCampanhasAbio;
use App\Models\ServicoPassagemFaunaExecCampanhasRet;
use App\Models\ServicoPassagemFaunaExecCampanhasRh;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class CampanhaService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaExecCampanha::class;
    protected string $modelClassAbio = ServicoPassagemFaunaExecCampanhasAbio::class;
    protected string $modelClassRh = ServicoPassagemFaunaExecCampanhasRh::class;
    protected string $modelClassRet = ServicoPassagemFaunaExecCampanhasRet::class;


    public function index($servico, array $searchParams): array
    {
        return [
            'campanhas' => $this->searchAllColumns(...$searchParams)
                ->with([
                    'abios.abio.licenca',
                    'rhs.servico_rh.rh',
                    'rets.ret.abio.licenca'
                ])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create($servico): array
    {
        return [
            'abios' => ServicoPassagemFaunaConfigAbio::with(['licenca'])->where('id_servico', '=', $servico->id)->get()
        ];
    }

    public function store($servico, array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$post,
            'id_servico' => $servico->id
        ]);
    }

    public function update(array $post)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function storeAbio(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassAbio, infos: $post);
    }

    public function storeRh(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassRh, infos: $post);
    }

    public function storeRet(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClassRet, infos: $post);
    }

    public function destroy(object $campanha)
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $campanha->id);
    }

    public function deleteAbio(object $campanha_abio)
    {
        return $this->dataManagement->delete(entity: $this->modelClassAbio, id: $campanha_abio->id);
    }

    public function deleteRh(object $campanha_rh)
    {
        return $this->dataManagement->delete(entity: $this->modelClassRh, id: $campanha_rh->id);
    }

    public function deleteRet(object $campanha_ret)
    {
        return $this->dataManagement->delete(entity: $this->modelClassRet, id: $campanha_ret->id);
    }
}
