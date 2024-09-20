<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Services;

use App\Models\ServicoConOcorrOcorrenciSupervisaoExecOcorrencia;
use App\Models\ServicoConOcorrSupervisaoExecAca;
use App\Models\ServicoConOcorrSupervisaoRelatorio;
use App\Models\ServicoConOcorrSupervisaoResultado;
use App\Models\ServicoContOcorrSupervisaoConfigLote;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Carbon\Carbon;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoConOcorrSupervisaoRelatorio::class;
    protected string $modelClassResultado = ServicoConOcorrSupervisaoResultado::class;

    public function index(Servicos $servico, array $searchParams): array
    {
        $relatorios = $this->searchAllColumns(...$searchParams)
            ->with([
                'resultado'
            ])
            ->where('id_servico', $servico->id)
            ->paginate()
            ->appends($searchParams);

        return [
            'relatorios' => $relatorios,
            'resultados' => $this->modelClassResultado::where('id_servico', $servico->id)->get()
        ];
    }

    public function store(array $post)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $post);
    }

    public function update(array $post)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $post, id: $post['id']);
    }

    public function destroy(int $relatorio_id)
    {
        return $this->dataManagement->delete(entity: $this->modelClass, id: $relatorio_id);
    }

    public function getVariaveis($resultado)
    {
        $startDate = Carbon::parse($resultado->dt_inicio);
        $endDate = Carbon::parse($resultado->dt_final);

        $lotes = ServicoContOcorrSupervisaoConfigLote::with(['rodovia', 'uf'])
            ->where('id_servico', $resultado->id_servico)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $ocorrencias = ServicoConOcorrOcorrenciSupervisaoExecOcorrencia::with([
            'lote:id,nome_id,empresa',
            'registros',
            'vistorias' => function ($query) {
                $query->latest()->limit(1); // Pega apenas a última vistoria
            }
        ])->where('id_servico', $resultado->id_servico)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $roasAtendidos = $ocorrencias->where('tipo', 'ROA')->where('status', 'Atendida');
        $roasEmAberto = $ocorrencias->where('tipo', 'ROA')->where('status', 'Em aberto');
        $rncsAtendidos = $ocorrencias->where('tipo', 'RNC')->where('status', 'Atendida');
        $rncsEmAberto = $ocorrencias->where('tipo', 'RNC')->where('status', 'Em aberto');

        $acas = ServicoConOcorrSupervisaoExecAca::with([
            'rncs:id,nome_id,classificacao,local',
            'lote:id,nome_id,empresa'
        ])
            ->where('id_servico', $resultado->id_servico)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'lotes' => $lotes,
            'analise' => $resultado->analise,
            'outrasAnalises' => $resultado->outras_analises,
            'roasAtendidos' => $roasAtendidos,
            'roasEmAberto' => $roasEmAberto,
            'rncsAtendidos' => $rncsAtendidos,
            'rncsEmAberto' => $rncsEmAberto,
            'acas' => $acas,
            'ocorrencias' => $ocorrencias
        ];
    }
}
