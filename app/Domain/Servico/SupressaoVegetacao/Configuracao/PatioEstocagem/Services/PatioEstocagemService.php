<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
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
        $shapefile = $request['shapefile'];
        if ($shapefile) {
            $shape = $this->licencaShapefileService->getFeatureCollection(file: $shapefile);
            $request['shapefile'] = $shape;
            $path = storage_path('app' . DIRECTORY_SEPARATOR . 'file_shape' . DIRECTORY_SEPARATOR . uniqid() . '.json');
            file_put_contents($path, $shape);
            $request['local_shape'] = $path;
        }

        $response = $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(),
        ]);

        if (isset($request['fotos']) && count($request['fotos'])) {
            /** @var UploadedFile[] $fotos */
            $fotos = $request['fotos'];
            $fotosId = array_map(function($foto) {
                $arquivo = $this->arquivoUtils->salvar(arquivo: $foto, diretorio: 'public/uploads/supressao/patio/', prefixo: 'PT');
                return $arquivo?->id;
            }, $fotos);

            $response['model']?->fotos()->sync($fotosId);
        }

        return $response;
    }

//    public function update(array $request): array
//    {
//        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
//
//        return $response;
//    }

    private function getCodigo(): string
    {
        $last = PatioEstocagem::query()->latest('id')->first()?->id;
        $key  = sprintf('%02d', $last + 1);
        return 'PE-' . $key . '/' . date('Y');
    }
}
