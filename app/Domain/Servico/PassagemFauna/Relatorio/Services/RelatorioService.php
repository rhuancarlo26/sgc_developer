<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Services;

use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaRelatorio;
use App\Models\ServicoPassagemFaunaResultado;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaRelatorio::class;

    public function index($servico, array $searchParams): array
    {
        return [
            'relatorios' => $this->searchAllColumns(...$searchParams)
                ->with([
                    'resultado.analise',
                    'resultado.outras_analises',
                    'resultado.campanhas.abios.abio.licenca.tipo_rel',
                    'resultado.campanhas.registros.status_iunc',
                    'resultado.campanhas.registros.status_federal',
                ])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams),
            'resultados' => ServicoPassagemFaunaResultado::with(['campanhas'])->where('id_servico', '=', $servico->id)->get()
        ];
    }

    public function store(array $post): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function update(array $post): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function destroy(object $relatorio): array
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $relatorio->id);
    }
}
