<?php

namespace App\Domain\Servico\PMQA\Configuracao\Parametro\Services;

use App\Models\ServicoPmqaListaParametro;
use App\Models\ServicoPmqaParametro;
use App\Models\ServicoPmqaParametroLista;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class ParametroService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPmqaParametroLista::class;
    protected string $modelClassListaParametro = ServicoPmqaListaParametro::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'listas' => $this->searchAllColumns(...$searchParams)
                ->with(['parametros'])
                ->where('fk_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams),
            'parametros' => ServicoPmqaParametro::orderBy('parametro')->get()
        ];
    }

    public function store(Servicos $servico, array $request)
    {
        $response = $this->storeParametroLista([
            'fk_servico' => $servico->id,
            'nome' => $request['nome'],
            'medir_iqa' => $request['medir_iqa']
        ]);

        $this->storeListaParametros($response['model'], $request['parametros']);

        return $response['request'];
    }

    public function update(Servicos $servico, array $request)
    {
        $response = $this->updateParametroLista([
            'id' => $request['id'],
            'fk_servico' => $servico->id,
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

    private function storeListaParametros(ServicoPmqaParametroLista $parametroLista, array $request): void
    {
        $parametroLista->parametros()->sync(collect($request)->toArray());
    }

    private function updateListaParametros(array $parametroLista, array $request): void
    {
        $parametroLista = ServicoPmqaParametroLista::find($parametroLista['id']);

        $parametroLista->parametros()->sync(collect($request)->toArray());
    }

    public function destroy(ServicoPmqaParametroLista $parametroLista)
    {
        $response = $this->dataManagement->delete($this->modelClass, $parametroLista->id);

        return $response;
    }
}
