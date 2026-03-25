<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Services;

use App\Models\Licenca;
use App\Models\Rodovia;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\ServicoContOcorrSupervisaoParecerConfiguracao;
use App\Models\ServicoLicencaCondicionante;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class LoteObraService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoContOcorrSupervisaoConfigLote::class;
    protected string $modelClassLoteFiscal = ServicoContOcorrSupervisaoParecerConfiguracao::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        return [
            'lotes' => $this->searchAllColumns(...$searchParams)
                ->with(['rodovia', 'uf'])
                ->where('id_servico', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create(): array
    {
        return ['rodovias' => Rodovia::with(['uf'])->get()];
    }

    public function store(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function enviarLoteFiscal(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClassLoteFiscal, infos: $post);
    }

    public function destroy(ServicoContOcorrSupervisaoConfigLote $lote)
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $lote->id);
    }
}
