<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\Arquivo;
use App\Models\PatioEstocagem;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class PatioEstocagemService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = PatioEstocagem::class;

    public function __construct(
        DataManagement $dataManagement,
        private readonly LicencaShapefileService $licencaShapefileService,
        private readonly ArquivoUtils $arquivoUtils,
    )
    {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['fotos', 'tipo', 'licenca'])
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        $this->handleShapefile(request: $request);
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(),
        ]);
        $this->handleFotos(request: $request, afterSave: fn(array $fotosId) => $response['model']?->fotos()->sync($fotosId));
        return $response;
    }

    public function update(array $request): array
    {
        $this->handleShapefile(request: $request);
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        $this->handleFotos(request: $request, afterSave: fn(array $fotosId) => $this->model->find($request['id'])?->fotos()->attach($fotosId));
        return $response;
    }

    public function deleteFoto(Arquivo $arquivo, PatioEstocagem $patio): bool
    {
        if($this->arquivoUtils->delete(arquivo: $arquivo)) {
            $patio->fotos()->detach($arquivo->id);
            return true;
        }
        return false;
    }

    private function handleFotos(array &$request, callable $afterSave): void
    {
        if (!isset($request['fotos']) || !count($request['fotos'])) {
            return;
        }
        /** @var UploadedFile[] $fotos */
        $fotos = $request['fotos'];
        $fotosId = array_map(function($foto) {
            $arquivo = $this->arquivoUtils->salvar(arquivo: $foto, diretorio: 'public/uploads/supressao/patio/', prefixo: 'PT');
            return $arquivo?->id;
        }, $fotos);
        $afterSave($fotosId);
    }

    private function handleShapefile(array &$request): void
    {
        $shapefile = $request['shapefile'];
        if (!($shapefile instanceof UploadedFile)) return;
        $shape = $this->licencaShapefileService->getFeatureCollection(file: $shapefile);
        $request['shapefile'] = $shape;
        $path = storage_path('app' . DIRECTORY_SEPARATOR . 'file_shape' . DIRECTORY_SEPARATOR . uniqid() . '.json');
        file_put_contents($path, $shape);
        $request['local_shape'] = $path;
    }

    private function getCodigo(): string
    {
        $last = PatioEstocagem::query()->latest('id')->first()?->id;
        $key  = sprintf('%02d', $last + 1);
        return 'PE-' . $key . '/' . date('Y');
    }
}
