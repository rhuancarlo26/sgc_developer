<?php

namespace App\Domain\Servico\SupressaoVegetacao\Configuracao\VincularASV\Services;

use App\Models\Licenca;
use App\Models\ServicoLicenca;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VincularASVService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoLicenca::class;

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return Licenca::query()
            ->with(['documento', 'tipo_rel', 'segmentos'])
            ->whereHas('licencaServicos', fn($q) => $q->where('servico_id', $servico->id))
            ->paginate();
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function deleteByLicencaAndServico(Licenca $licenca, Servicos $servicos): bool
    {
        $licencaServico = $this->modelClass::query()
            ->where('licenca_id', $licenca->id)
            ->where('servico_id', $servicos->id)
            ->first();

        return $this->delete(model: $licencaServico);
    }

    public static function getLicencaServico($servicoId)
    {
        return ServicoLicenca::select([
            'servico_licenca.id as sv_id',
            'licencas.*',
            'tipo_licencas.nome as nome_licenca',
            'tipo_licencas.sigla',
            DB::raw('DATE_FORMAT(licencas.data_emissao, "%d/%m/%Y") as data_emissaoF'),
            DB::raw('DATE_FORMAT(licencas.vencimento, "%d/%m/%Y") as vencimentoF'),
        ])
            ->join('licencas', 'licencas.id', '=', 'servico_licenca.licenca_id')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->where('servico_id', $servicoId)
            ->get();
    }

}
