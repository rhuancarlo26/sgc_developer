<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\VinculacaoPonto\Services;

use App\Models\ServicoPmqaConfiguracaoParecer;
use App\Models\ServicoPmqaParametroLista;
use App\Models\ServicoPmqaPonto;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaParametroLista;
use App\Models\SgcPmqaPonto;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class VinculacaoPontoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaParametroLista::class;

    public function index(SgcPmqa $pmqa, $searchParams): array
    {
        $vinculacoes = $this->searchAllColumns(...$searchParams)
            ->with(['pontos'])
            ->where('pmqa_id', $pmqa->id)
            ->has('pontos')
            ->paginate()
            ->appends($searchParams);
        $listas = SgcPmqaParametroLista::with(['pontos'])->where('pmqa_id', $pmqa->id)->get();
        $pontos = SgcPmqaPonto::with(['vinculado'])->where('pmqa_id', $pmqa->id)->get();

        return [
            'vinculacoes' => $vinculacoes,
            'listas' => $listas,
            'pontos' => $pontos
        ];
    }

    public function store(SgcPmqa $pmqa, array $request)
    {
        $this->updateLista($request);

        $modelLista = SgcPmqaParametroLista::findOrFail($request['lista']['id']);

        $syncData = collect($request['pontos'])
            ->mapWithKeys(fn($pontoId) => [
                $pontoId => ['pmqa_id' => $pmqa->id]
            ])
            ->toArray();

        $modelLista->pontos()->sync($syncData);

        return [
            'request' => [
                'type' => 'success',
                'content' => 'Registro salvo!',
            ]
        ];
    }


    public function updateLista(array $request): void
    {
        $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['lista']['id']);
    }

    public function deleteVinculacao(SgcPmqaParametroLista $lista)
    {
        $lista->pontos()->sync(collect([])->toArray());

        return [
            'request' => [
                'type' => 'success',
                'content' => 'Registro excluido!',
            ]
        ];
    }

    // public function enviarListaFiscal(array $post)
    // {
    //     $parecer = $this->modelClassConfigParecer::where('fk_servico', $post['fk_servico'])->first();

    //     if (empty($parecer)) {
    //         return $this->dataManagement->create(entity: $this->modelClassConfigParecer, infos: $post);
    //     }

    //     return $this->dataManagement->update(entity: $this->modelClassConfigParecer, infos: $post, id: $parecer->id);
    // }
}
