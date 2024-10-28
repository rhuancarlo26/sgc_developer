<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\MalhaViaria\app\Services;

use App\Models\AtFaunaParecerConfiguracao;
use App\Models\AtMalhaViariaShapefile;
use App\Models\ServicoLicencaCondicionante;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use App\Shared\Traits\ShapefileHandler;

class MalhaViariaService extends BaseModelService
{
    use Searchable, Deletable, ShapefileHandler;

    protected string $modelClass = ServicoLicencaCondicionante::class;

    public function index(Servicos $servico, array $searchParams)
    {
        return $this->searchAllColumns(...$searchParams)
            ->select([
                'amvs.id',
                'servico_licenca_condicionante.id AS fk_servico_licenca',
                'amvs.shapefile',
                'amvs.local_shape',
                'tipo_licencas.sigla',
                'tipo_licencas.nome as nome_licenca',
                'licencas.id as id_licenca',
                'licencas.numero_licenca',
                'licencas.empreendimento',
                'licencas_br.extensao_br AS extensao',
                'licencas_br.km_fim',
                'licencas_br.km_inicio',
                'licencas.chave',
                'licencas.arquivo_licenca',
                'licencas.local_shape AS local_shape_licenca',
                'servico_licenca_condicionante.vigente',
                'br.rodovia',
                'estados.nome as nome_estado',
            ])
            ->join('licencas', 'licencas.id', '=', 'servico_licenca_condicionante.id_licenca')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->leftJoin('at_malha_viaria_shapefile AS amvs', 'amvs.fk_servico_licenca', '=', 'servico_licenca_condicionante.id')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->join('base_rodovia AS br', 'br.rodovia', '=', 'licencas_br.rodovia')
            ->join('estados', 'estados.id', '=', 'licencas_br.uf_inicial')
            ->where('id_servico', $servico->id)
            ->whereRaw('br.estados_id = licencas_br.uf_inicial')
            ->paginate();
    }

    public function store(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function storeShapefile(array $request): void
    {
        $this->handleShapefile(request: $request);

        AtMalhaViariaShapefile::query()->updateOrCreate(['id' => $request['id']], [
            'fk_servico_licenca' => $request['fk_servico_licenca'],
            'shapefile' => $request['shapefile'],
            'local_shape' => $request['local_shape'],
        ]);
    }

    public function sendFiscal(?int $servico): void
    {
        if (!$servico) {
            return;
        }

        AtFaunaParecerConfiguracao::query()->updateOrCreate(['fk_servico' => $servico], ['fk_status' => 1]);
    }
}
