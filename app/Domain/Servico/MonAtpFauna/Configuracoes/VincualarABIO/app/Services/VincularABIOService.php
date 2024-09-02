<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Services;

use App\Models\Licenca;
use App\Models\ServicoMonAtpFaunaVincularABIO;
use App\Models\ServicoMonAtpFaunaVincularRetABIO;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Utils\ArquivoUtils;
use App\Shared\Utils\DataManagement;
use Illuminate\Support\Facades\DB;

class VincularABIOService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoMonAtpFaunaVincularABIO::class;

    public function __construct(
        DataManagement                $dataManagement,
        private readonly ArquivoUtils $arquivoUtils,
    )
    {
        parent::__construct($dataManagement);
    }


    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'vinculacoes' => $this->searchAllColumns(...$searchParams)
                ->with(['licenca.tipo', 'rets' => fn($query) => $query->orderBy('id', 'desc')])
                ->where('fk_servico', $servico->id)
                ->paginate()
                ->appends($searchParams),
            'licencas' => Licenca::get(['id', 'numero_licenca'])
        ];
    }

    public function store(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function storeRet(array $request): void
    {
        $arquivo = $this->arquivoUtils->salvar(
            arquivo: $request['arquivo_ret'],
            diretorio: 'uploads/atropelamento_fauna/rets',
            createModel: false
        );

        ServicoMonAtpFaunaVincularRetABIO::create([
            'caminho_arquivo' => $arquivo->diretorio . '/' . $arquivo->arquivo,
            'nome_arquivo' => $arquivo->nome_arquivo,
            'fk_at_config_vinculacao' => $request['fk_at_config_vinculacao'],
        ]);
    }

    public function getVinculos($servicoId)
    {
        return $this->model
            ->select([
                'at_fauna_config_vinculacao.id',
                'licencas.id as id_licenca',
                'licencas.numero_licenca',
                'licencas.empreendimento',
                'licencas.emissor',
                'licencas_br.extensao_br AS extensao',
                'licencas_br.km_fim',
                'licencas_br.km_inicio',
                'licencas.file_shape AS shapefile_licenca',
                'licencas.chave',
                DB::raw('DATE_FORMAT(licencas.data_emissao, "%d/%m/%Y") as data_emissao'),
                'licencas.vencimento',
                'tipo_licencas.sigla',
                'tipo_licencas.nome as nome_licenca',
                'licencas.processo_dnit',
                'licencas.arquivo_licenca',
                'licencas.fiscal',
                DB::raw('(SELECT id FROM at_fauna_config_vinculacao_ret WHERE fk_at_config_vinculacao = at_fauna_config_vinculacao.id ORDER BY created_at DESC LIMIT 1) as id_ret')
            ])
            ->join('licencas', 'licencas.id', '=', 'at_fauna_config_vinculacao.fk_licenca')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->where('at_fauna_config_vinculacao.fk_servico', $servicoId)
            ->get();
    }
}
