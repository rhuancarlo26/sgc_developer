<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Services;

use App\Models\Destinacao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DestinacaoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = Destinacao::class;

    public function __construct(
        DataManagement                $dataManagement,
        private readonly ArquivoUtils $arquivoUtils,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->searchAllColumns(...$searchParams)
            ->withSum('pilhas', 'volume')
            ->with(['pilhas.licenca', 'pilhas.corteEspecie', 'arquivos'])
            ->where('servico_id', $servico->id)
            ->paginate()
            ->appends($searchParams);
    }

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(prefix: 'DP'),
        ]);
        if ($request['pilhas']) {
            $response['model']?->pilhas()->sync($request['pilhas']);
        }
        $this->arquivoUtils->handleFotos(
            fotos: $request['arquivos'] ?? [],
            diretorio: 'public/uploads/supressao/destinacaos/',
            prefixo: 'DP',
            afterSave: fn(array $fotosId) => $response['model']?->arquivos()->sync($fotosId)
        );
        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        /** @var Destinacao $destinacao */
        $destinacao = $this->model->find($request['id']);
        if ($request['pilhas']) {
            $destinacao->pilhas()->sync($request['pilhas']);
        }
        $this->arquivoUtils->handleFotos(
            fotos: $request['arquivos'] ?? [],
            diretorio: 'public/uploads/supressao/controle_pilhas',
            prefixo: 'CP',
            afterSave: fn(array $fotosId) => $destinacao?->arquivos()->attach($fotosId)
        );
        return $response;
    }

}
