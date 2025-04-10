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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getByPeriodo(Servicos $servico, Carbon $dtInicio, Carbon $dtFinal): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->model
            ->select([
                'destinacaos.*',
                DB::raw('DATE_FORMAT(destinacaos.dt_envio, "%d/%m/%Y") as dt_envioF'),
                'cp.tipo_produto_id',
                DB::raw('GROUP_CONCAT(cp.chave) as pilhas'),
                DB::raw('sum(cp.volume) as volume')
            ])
            ->join('destinacao_pilhas as dp', 'destinacaos.id', '=', 'dp.destinacao_id')
            ->leftJoin('controle_pilhas as cp', 'cp.id', '=', 'dp.controle_pilha_id')
            ->where('destinacaos.servico_id', $servico->id)
            ->whereBetween('destinacaos.dt_envio', [$dtInicio, $dtFinal])
            ->groupBy([
                'destinacaos.id',
                'cp.tipo_produto_id',
            ])
            ->get();
    }

}
