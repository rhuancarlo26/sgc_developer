<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Supressao\Services;

use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Models\AreaSupressao;
use App\Models\Arquivo;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use App\Shared\Traits\ShapefileHandler;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->searchAllColumns(...$searchParams)
            ->with(['bioma', 'estagioSucessional', 'fotos', 'corteEspecies', 'licenca'])
            ->where('servico_id', $servico->id)
            ->paginate()
            ->appends($searchParams);
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
            foreach ($request['corte_especies'] as $corteEspecie) {
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
        if ($this->arquivoUtils->delete(arquivo: $arquivo)) {
            $area->fotos()->detach($arquivo->id);
            return true;
        }
        return false;
    }

    public function getByPeriodo(Servicos $servico, Carbon $dtInicio, Carbon $dtFinal, array $with = [])
    {
        return $this->model
            ->where('servico_id', $servico->id)
            ->where('dt_inicial', '>=', $dtInicio->format('Y-m-d'))
            ->where('dt_final', '<=', $dtFinal->format('Y-m-d'))
            ->with($with)
            ->get();
    }

    public function getSumAreaByServico(Servicos $servico, Carbon $dtInicio, Carbon $dtFinal)
    {
        return $this->model
            ->selectRaw('SUM(area_em_app) as area_em_app, SUM(area_fora_app) as area_fora_app')
            ->where('servico_id', $servico->id)
            ->where('dt_inicial', '>=', $dtInicio->format('Y-m-d'))
            ->where('dt_final', '<=', $dtFinal->format('Y-m-d'))
            ->first();
    }

    public function getSumAreaPerMonthByServico(Servicos $servico, Carbon $dtInicio, Carbon $dtFinal)
    {
        return DB::table('area_supressao')
            ->select(DB::raw('SUM(area_supressao.area_total) as area_total'), DB::raw("
                CASE
                    WHEN MONTH(dt_inicial) = 1 THEN 'Janeiro'
                    WHEN MONTH(dt_inicial) = 2 THEN 'Fevereiro'
                    WHEN MONTH(dt_inicial) = 3 THEN 'Março'
                    WHEN MONTH(dt_inicial) = 4 THEN 'Abril'
                    WHEN MONTH(dt_inicial) = 5 THEN 'Maio'
                    WHEN MONTH(dt_inicial) = 6 THEN 'Junho'
                    WHEN MONTH(dt_inicial) = 7 THEN 'Julho'
                    WHEN MONTH(dt_inicial) = 8 THEN 'Agosto'
                    WHEN MONTH(dt_inicial) = 9 THEN 'Setembro'
                    WHEN MONTH(dt_inicial) = 10 THEN 'Outubro'
                    WHEN MONTH(dt_inicial) = 11 THEN 'Novembro'
                    WHEN MONTH(dt_inicial) = 12 THEN 'Dezembro'
                END AS mes
            "))
            ->join('tipo_biomas as b', 'area_supressao.tipo_bioma_id', '=', 'b.id')
            ->join('estagio_sucessional as e', 'area_supressao.estagio_sucessional_id', '=', 'e.id')
            ->join('licencas as l', 'area_supressao.licenca_id', '=', 'l.id')
            ->where('servico_id', $servico->id)
            ->whereDate('dt_inicial', '>=', $dtInicio)
            ->whereDate('dt_final', '<=', $dtFinal)
            ->groupBy(DB::raw("
                CASE
                    WHEN MONTH(dt_inicial) = 1 THEN 'Janeiro'
                    WHEN MONTH(dt_inicial) = 2 THEN 'Fevereiro'
                    WHEN MONTH(dt_inicial) = 3 THEN 'Março'
                    WHEN MONTH(dt_inicial) = 4 THEN 'Abril'
                    WHEN MONTH(dt_inicial) = 5 THEN 'Maio'
                    WHEN MONTH(dt_inicial) = 6 THEN 'Junho'
                    WHEN MONTH(dt_inicial) = 7 THEN 'Julho'
                    WHEN MONTH(dt_inicial) = 8 THEN 'Agosto'
                    WHEN MONTH(dt_inicial) = 9 THEN 'Setembro'
                    WHEN MONTH(dt_inicial) = 10 THEN 'Outubro'
                    WHEN MONTH(dt_inicial) = 11 THEN 'Novembro'
                    WHEN MONTH(dt_inicial) = 12 THEN 'Dezembro'
                END
            "), DB::raw('MONTH(dt_inicial)'))
            ->get();
    }

}
