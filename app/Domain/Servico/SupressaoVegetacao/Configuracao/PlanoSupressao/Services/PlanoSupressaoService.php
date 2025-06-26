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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanoSupressaoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = PlanoSupressao::class;

    public function __construct(
        DataManagement $dataManagement,
        private readonly LicencaShapefileService $licencaShapefileService,
        private readonly ArquivoUtils $arquivoUtils,
    ) {
        parent::__construct($dataManagement);
    }

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return PlanoSupressao::query()
            ->select([
                'id',
                'servico_id',
                'chave',
                'area_em_app',
                'area_fora_app',
                'dt_inicial',
                'dt_final',
                'local_shape_em_app',
                'local_shape_fora_app',
                'arquivo_id'
            ])
            ->with(['arquivo'])
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(Request $request): array
    {
        try {
            $dados = $request->all();

            if (!empty($dados['id'])) {
                $plano = PlanoSupressao::findOrFail($dados['id']);

                if ($request->hasFile('local_shape_em_app')) {
                    $geoJson = $this->licencaShapefileService->getFeatureCollection($request->file('local_shape_em_app'));
                    if (!$geoJson) {
                        abort(422, 'Erro ao processar o shapefile da área em APP.');
                    }
                    $dados['local_shape_em_app'] = json_decode($geoJson, true);
                } else {
                    unset($dados['local_shape_em_app']);
                }

                if ($request->hasFile('local_shape_fora_app')) {
                    $geoJson = $this->licencaShapefileService->getFeatureCollection($request->file('local_shape_fora_app'));
                    if (!$geoJson) {
                        abort(422, 'Erro ao processar o shapefile da área fora de APP.');
                    }
                    $dados['local_shape_fora_app'] = json_decode($geoJson, true);
                } else {
                    unset($dados['local_shape_fora_app']);
                }

                if ($request->hasFile('doc')) {
                    $arquivo = $this->arquivoUtils->salvar(
                        arquivo: $request->file('doc'),
                        diretorio: 'public/uploads/supressao/plano/',
                        prefixo: 'PS'
                    );
                    $dados['arquivo_id'] = $arquivo?->id;
                } else {
                    unset($dados['arquivo_id']);
                }

                unset($dados['id'], $dados['doc']);
                $plano->update($dados);

                return ['request' => 'Atualizado com sucesso!'];
            }

            if ($request->hasFile('local_shape_em_app')) {
                $geoJson = $this->licencaShapefileService->getFeatureCollection($request->file('local_shape_em_app'));
                if (!$geoJson) {
                    abort(422, 'Erro ao processar o shapefile da área em APP.');
                }
                $dados['local_shape_em_app'] = json_decode($geoJson, true);
            }

            if ($request->hasFile('local_shape_fora_app')) {
                $geoJson = $this->licencaShapefileService->getFeatureCollection($request->file('local_shape_fora_app'));
                if (!$geoJson) {
                    abort(422, 'Erro ao processar o shapefile da área fora de APP.');
                }
                $dados['local_shape_fora_app'] = json_decode($geoJson, true);
            }

            if ($request->hasFile('doc')) {
                $arquivo = $this->arquivoUtils->salvar(
                    arquivo: $request->file('doc'),
                    diretorio: 'public/uploads/supressao/plano/',
                    prefixo: 'PS'
                );
                $dados['arquivo_id'] = $arquivo?->id;
            }

            $dados['chave'] = $this->getCodigo(prefix: 'PS');
            unset($dados['doc']);

            $registroCriado = $this->dataManagement->create(entity: $this->modelClass, infos: $dados);
            return $registroCriado;
        } catch (\Throwable $e) {
            Log::error('Erro ao cadastrar/atualizar plano de supressão', [
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Erro interno. Verifique os dados e tente novamente.');
        }
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
