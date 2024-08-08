<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Pilhas\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
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
        if($this->arquivoUtils->delete(arquivo: $arquivo)) {
            $pilha->fotos()->detach($arquivo->id);
            return true;
        }
        return false;
    }

}
