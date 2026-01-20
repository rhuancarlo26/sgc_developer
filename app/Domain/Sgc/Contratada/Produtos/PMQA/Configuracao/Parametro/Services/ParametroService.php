<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services;

use App\Models\ServicoPmqaListaParametro;
use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaParametroLista;
use App\Models\Servicos;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaListaParametro;
use App\Models\SgcPmqaParametroLista;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ParametroService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaParametroLista::class;
    protected string $modelClassListaParametro = SgcPmqaListaParametro::class;

    public function index(SgcPmqa $pmqa, array $searchParams): array
    {
        $query = $this->searchAllColumns(
            $searchParams['columns'] ?? null,
            $searchParams['value'] ?? null
        );

        return [
            'listas' => $query
                ->with(['parametros'])
                ->where('pmqa_id', $pmqa->id)   // ou fk_servico, depende da sua tabela
                ->paginate()
                ->appends($searchParams),

            'parametros' => ServicoPmqaParametro::orderBy('parametro')->get(),
        ];
    }

    public function store(SgcPmqa $pmqa, array $request)
    {
        $response = $this->storeParametroLista([
            'pmqa_id' => $pmqa->id,
            'nome' => $request['nome'],
            'medir_iqa' => $request['medir_iqa']
        ]);

        $this->storeListaParametros($response['model'], $request['parametros']);

        return $response['request'];
    }

    public function update(SgcPmqa $pmqa, array $request)
    {
        $response = $this->updateParametroLista([
            'id' => $request['id'],
            'pmqa_id' => $pmqa->id,
            'nome' => $request['nome'],
            'medir_iqa' => $request['medir_iqa']
        ]);

        $this->updateListaParametros($request, $request['parametros']);

        return $response['request'];
    }

    private function storeParametroLista(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return $response;
    }

    private function updateParametroLista(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return $response;
    }

    private function storeListaParametros(SgcPmqaParametroLista $parametroLista, array $request): void
    {
        $parametroLista->parametros()->sync(collect($request)->toArray());
    }

    private function updateListaParametros(array $parametroLista, array $request): void
    {
        $parametroLista = SgcPmqaParametroLista::find($parametroLista['id']);

        $parametroLista->parametros()->sync(collect($request)->toArray());
    }

    public function destroy(SgcPmqaParametroLista $parametroLista)
    {
        $response = $this->dataManagement->delete($this->modelClass, $parametroLista->id);

        return $response;
    }
}
