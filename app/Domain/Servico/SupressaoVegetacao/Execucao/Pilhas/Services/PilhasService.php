<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Enums\TipoPilha;
use App\Models\AreaSupressao;
use App\Models\Arquivo;
use App\Models\ControlePilha;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Traits\ShapefileHandler;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PilhasService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode, ShapefileHandler;

    protected string $modelClass = ControlePilha::class;

    public function __construct(
        DataManagement                           $dataManagement,
        private readonly LicencaShapefileService $licencaShapefileService,
        private readonly ArquivoUtils            $arquivoUtils,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->searchAllColumns(...$searchParams)
            ->with(['areaSupressao', 'fotos', 'licenca', 'patio', 'produto', 'corteEspecie'])
            ->where('servico_id', $servico->id)
            ->paginate()
            ->appends($searchParams);
    }

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(prefix: 'CP'),
        ]);
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/controle_pilhas/',
            prefixo: 'CP',
            afterSave: fn(array $fotosId) => $response['model']?->fotos()->sync($fotosId)
        );
        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/controle_pilhas',
            prefixo: 'CP',
            afterSave: fn(array $fotosId) => $this->model->find($request['id'])?->fotos()->attach($fotosId)
        );
        return $response;
    }

    public function deleteFoto(Arquivo $arquivo, ControlePilha $pilha): bool
    {
        if ($this->arquivoUtils->delete(arquivo: $arquivo)) {
            $pilha->fotos()->detach($arquivo->id);
            return true;
        }
        return false;
    }

    public function getByPeriodo(Servicos $servico, Carbon $dtInicio, Carbon $dtFinal): array
    {
        $query = $this->model
            ->select([
                'controle_pilhas.*',
                'l.numero_licenca',
                'aas.chave as area_supressao',
                'pe.chave as patio',
                'tp.nome as produto',
                'ce.nome as corte',
                'ce.qtd_corte as qtd_cortes',
            ])
            ->join('licencas as l', 'l.id', '=', 'controle_pilhas.licenca_id')
            ->join('area_supressao as aas', 'aas.id', '=', 'controle_pilhas.area_supressao_id')
            ->leftJoin('patio_estocagem as pe', 'pe.id', '=', 'controle_pilhas.patio_estocagem_id')
            ->leftJoin('corte_especie as ce', 'ce.id', '=', 'controle_pilhas.corte_especie_id')
            ->leftJoin('tipo_produto as tp', 'tp.id', '=', 'controle_pilhas.tipo_produto_id')
            ->where('controle_pilhas.servico_id', $servico->id);
        $query2 = clone $query;
        return [
            'pilhas' => $query
                ->where('aas.dt_inicial', '>=', $dtInicio)
                ->where('aas.dt_final', '<=', $dtFinal)
                ->get(),
            'pilhasProtegidas' => $query2
                ->where('controle_pilhas.tipo_pilha', TipoPilha::ESPECIE_AMEACADA_PROTEGIDA)
                ->where('controle_pilhas.created_at', '>=', $dtInicio)
                ->where('controle_pilhas.created_at', '>=', $dtFinal)
                ->get()
        ];
    }

    public function getTotalEstocado(Servicos $servico): Model
    {
        return $this->model
            ->selectRaw('SUM(volume) as volume')
            ->where('servico_id', $servico->id)
            ->first();
    }

}
