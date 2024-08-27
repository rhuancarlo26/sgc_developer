<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\PlanoSupressao\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\PlanoSupressao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PlanoSupressaoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = PlanoSupressao::class;

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
            ->with(['arquivo'])
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        $shapeEmApp = $request['local_shape_em_app'];
        if ($shapeEmApp) {
            $request['local_shape_em_app'] = $this->licencaShapefileService->getFeatureCollection(file: $shapeEmApp);
        }

        $shapeForaApp = $request['local_shape_fora_app'];
        if ($shapeForaApp) {
            $request['local_shape_fora_app'] = $this->licencaShapefileService->getFeatureCollection(file: $shapeForaApp);
        }

        $doc = $request['doc'];
        if ($doc) {
            $arquivo = $this->arquivoUtils->salvar(arquivo: $doc, diretorio: 'public/uploads/supressao/plano/', prefixo: 'PS');
            $request['arquivo_id'] = $arquivo?->id;
            unset($request['doc']);
        }

        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(prefix: 'PS'),
        ]);
    }

    public function getSumAreaByServico(int $id)
    {
        return $this->model
            ->selectRaw('SUM(area_em_app) as area_em_app, SUM(area_fora_app) as area_fora_app')
            ->where('servico_id', $id)
            ->first();
    }

    public static function getPlanoSupressaoServico($servicoId)
    {
        return PlanoSupressao::select([
            'plano_supressao.*',
            'a.nome_arquivo',
            DB::raw('DATE_FORMAT(plano_supressao.dt_inicial, "%d/%m/%Y") as dt_inicialF'),
            DB::raw('DATE_FORMAT(plano_supressao.dt_final, "%d/%m/%Y") as dt_finalF')
        ])
            ->leftJoin('arquivos as a', 'plano_supressao.arquivo_id', '=', 'a.id')
            ->where('plano_supressao.servico_id', $servicoId)
            ->get();
    }
}
