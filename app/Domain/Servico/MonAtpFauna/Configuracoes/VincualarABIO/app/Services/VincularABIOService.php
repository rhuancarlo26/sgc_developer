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
}
