<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Services;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Strategy\Periodo\ContextStrategy;
use App\Models\ResultadoSupressao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = ResultadoSupressao::class;

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

}
