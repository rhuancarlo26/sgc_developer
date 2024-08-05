<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\AreaSupressao;
use App\Models\Arquivo;
use App\Models\PatioEstocagem;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Traits\ShapefileHandler;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class SupressaoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode, ShapefileHandler;

    protected string $modelClass = AreaSupressao::class;

    public function __construct(
        DataManagement                           $dataManagement,
        private readonly LicencaShapefileService $licencaShapefileService,
        private readonly ArquivoUtils            $arquivoUtils,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['bioma', 'estagioSucessional', 'fotos', 'corteEspecies', 'licenca'])
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        $this->handleShapefile(request: $request);
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(prefix: 'AS'),
        ]);
        if ($request['corte_especie']) {
            $response['model']?->corteEspecies()->createMany($request['corte_especies']);
        }
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/area_suprida/',
            prefixo: 'AS',
            afterSave: fn(array $fotosId) => $response['model']?->fotos()->sync($fotosId)
        );
        return $response;
    }

    public function update(array $request): array
    {
        $this->handleShapefile(request: $request);
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        /** @var AreaSupressao $supressao */
        $supressao = $this->model->find($request['id']);
        if ($request['corte_especie']) {
            foreach($request['corte_especies'] as $corteEspecie) {
                $supressao?->corteEspecies()->updateOrCreate(
                    ['id' => $corteEspecie['id'] ?? null],
                    $corteEspecie
                );
            }
        }
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/area_suprida/',
            prefixo: 'AS',
            afterSave: fn(array $fotosId) => $supressao?->fotos()->attach($fotosId)
        );
        return $response;
    }

    public function deleteFoto(Arquivo $arquivo, AreaSupressao $area): bool
    {
        if($this->arquivoUtils->delete(arquivo: $arquivo)) {
            $area->fotos()->detach($arquivo->id);
            return true;
        }
        return false;
    }

}
