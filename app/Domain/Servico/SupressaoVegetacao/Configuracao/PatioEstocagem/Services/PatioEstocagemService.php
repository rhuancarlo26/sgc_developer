<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PatioEstocagem\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
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
use Illuminate\Support\Facades\DB;

class PatioEstocagemService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode, ShapefileHandler;

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
            'chave' => $this->getCodigo(prefix: 'PE'),
        ]);
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/patio/',
            prefixo: 'PT',
            afterSave: fn(array $fotosId) => $response['model']?->fotos()->sync($fotosId)
        );
        return $response;
    }

    public function update(array $request): array
    {
        $this->handleShapefile(request: $request);
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
        $this->arquivoUtils->handleFotos(
            fotos: $request['fotos'] ?? [],
            diretorio: 'public/uploads/supressao/patio/',
            prefixo: 'PT',
            afterSave: fn(array $fotosId) => $this->model->find($request['id'])?->fotos()->attach($fotosId)
        );
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

    public static function getPatioEstocagemServico($servicoId)
    {
        return PatioEstocagem::select([
            'patio_estocagem.*',
            'l.numero_licenca',
            'tp.nome as tipo_patio',
            DB::raw('DATE_FORMAT(patio_estocagem.created_at, "%d/%m/%Y") as dt_cadastroF'),
        ])
            ->join('licencas as l', 'l.id', '=', 'patio_estocagem.licenca_id')
            ->join('tipo_patio as tp', 'tp.id', '=', 'patio_estocagem.tipo_patio_id')
            ->where('servico_id', $servicoId)
            ->get();
    }
}
